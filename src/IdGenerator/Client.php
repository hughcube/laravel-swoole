<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/31
 * Time: 21:03
 */

namespace HughCube\Laravel\Swoole\IdGenerator;

use Carbon\Carbon;
use HughCube\Laravel\Swoole\Server;
use Illuminate\Support\Arr;
use Swoole\Lock as SwooleLock;
use Swoole\Process as SwooleProcess;
use Swoole\Table as SwooleTable;

/**
 * Class IdGeneratorService
 * @package App\Services
 *
 * 总共 64 - 1 位
 * 时间戳预留41位, 也就是2199023255551毫秒, 69年
 * workId预留10位, 也就是, 0 - 1023
 * 每秒sequence预留12位, 也就是 0 - 4091
 */
class Client
{
    const KEY_WORK_ID = 'WORK_ID';
    const KEY_WORK_ID_EXPIRES = 'WORK_ID_EXPIRES';

    /**
     * 最大保留的时间计数器, 也就是会保留半个小时的数据
     * ?
     * 1: 可以防止时间回拨
     * 2: 可以如果当前毫秒顺序号不够可以往前借位
     *
     * @var int
     */
    protected $maxKeepTimestampSequence = 10;

    /**
     * 限制的每毫秒最大id生成数
     *
     * @var int
     */
    protected $maxSequence = 4091;

    /**
     * 限制的最大workId
     *
     * @var int
     */
    protected $maxWorkId = 1023;

    /**
     * 限制的每秒最大id生成数
     *
     * @var int
     */
    protected $maxSequenceBinLength;

    /**
     * 限制的最大workId
     *
     * @var int
     */
    protected $maxWorkIdBinLength;

    /**
     * @var SwooleLock
     */
    protected $mutex;

    /**
     * @var SwooleTable
     */
    protected $table;

    /**
     * @var array
     */
    protected $config;

    /**
     * IdGeneratorService constructor.
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->maxSequenceBinLength = strlen($this->baseConvert($this->maxSequence, 10, 2));
        $this->maxWorkIdBinLength = strlen($this->baseConvert($this->maxWorkId, 10, 2));

        $this->bootstrapCreateMutex();
        $this->bootstrapCreateTable();
        $this->bootstrapGcHandler();
    }

    /**
     * @return integer
     * @throws
     */
    public function getId()
    {
        $millisecond = Carbon::now()->getPreciseTimestamp(3);

        /**
         * 通过读锁来确保, 在生成id的时候workId不会发生变化
         */
        $this->mutex->lock_read();
        while (true) {
            $workId = $this->getWorkId($millisecond);
            if (false === $workId) {
                break;
            }

            $sequence = $this->table->incr($millisecond, 'value', 1);
            if ($sequence <= $this->maxSequence) {
                break;
            }
            $millisecond++;
        }
        $this->mutex->unlock();

        /**
         * 如果workID不存在, 需要重新处理
         */
        if (
            (null !== $this->maxWorkId && $workId > $this->maxWorkId)
            || false === $workId
        ) {
            return null;
        }

        /**
         * 时间 2020-01-01 00:00:00  起
         */
        $binTimestamp = $this->baseConvert(strval($millisecond - 1577836800000), 10, 2);

        /**
         * WorkId
         */
        $binWorkId = $this->baseConvert(strval(intval($workId)), 10, 2);
        $binWorkId = str_pad($binWorkId, $this->maxWorkIdBinLength, '0', STR_PAD_LEFT);

        /**
         * 顺序号
         */
        $binSequence = $this->baseConvert(strval($sequence), 10, 2);
        $binSequence = str_pad($binSequence, $this->maxSequenceBinLength, '0', STR_PAD_LEFT);

        /**
         * 拼接
         */
        $binId = "{$binTimestamp}{$binWorkId}{$binSequence}";

        /**
         * 返回10进制的id
         */
        return $this->baseConvert($binId, 2, 10);
    }

    /**
     * 获取workId
     *
     * @return integer
     * @throws \Exception
     */
    public function getWorkId($millisecond = null)
    {
        $millisecond = null === $millisecond ? (Carbon::now()->getPreciseTimestamp(3)) : $millisecond;

        $id = $this->table->get(static::KEY_WORK_ID, 'value');
        if (empty($id)) {
            return null;
        }

        $expires = $this->table->get(static::KEY_WORK_ID_EXPIRES, 'value');
        if ($millisecond > $expires) {
            return false;
        }

        return $id;
    }

    /**
     * 设置workid
     *
     * @param integer $id
     * @param integer $expires
     * @return bool|mixed
     * @throws \Exception
     */
    public function setWorkId($id, $expires)
    {
        if ($id == $this->getWorkId()) {
            return $this->table->set(static::KEY_WORK_ID_EXPIRES, ['value' => $expires]);
        }

        $this->mutex->lock();
        $this->table->set(static::KEY_WORK_ID, ['value' => $id]);
        $this->table->set(static::KEY_WORK_ID_EXPIRES, ['value' => $expires]);
        $this->mutex->unlock();

        return true;
    }

    /**
     * 调整表
     */
    public function gc()
    {
        if ($this->maxKeepTimestampSequence >= $this->table->count()) {
            return;
        }

        $timestamps = [];
        foreach ($this->table as $key => $row) {
            if (!is_numeric($key) || !ctype_digit(strval($key))) {
                continue;
            }

            $timestamps[] = $key;
        }

        rsort($timestamps, SORT_NUMERIC);
        foreach (array_slice($timestamps, $this->maxKeepTimestampSequence) as $timestamp) {
            $this->table->del($timestamp);
            usleep(10);
        }
    }

    /**
     * 进制转换
     *
     * @param string $number
     * @param integer $frombase
     * @param integer $tobase
     * @return string
     */
    protected function baseConvert($number, $frombase, $tobase)
    {
        return base_convert($number, $frombase, $tobase);
    }

    /**
     * @return integer
     */
    public function getMaxTableSize()
    {
        return Arr::get($this->config, 'table_size', 100000);
    }

    /**
     * 创建所有.
     *
     * @return int
     */
    public function bootstrapCreateMutex()
    {
        $this->mutex = new SwooleLock(SWOOLE_RWLOCK);
    }

    /**
     * 创建所有.
     *
     * @return int
     */
    public function bootstrapCreateTable()
    {
        $table = new SwooleTable($this->getMaxTableSize(), 1);
        $table->column('value', SwooleTable::TYPE_INT, 8);
        $table->create();

        $this->table = $table;
    }

    /**
     * 创建所有.
     *
     * @return void
     */
    public function bootstrapGcHandler()
    {
        if (!preg_match("/cli/i", php_sapi_name())) {
            return;
        }

        /** @var Server $server */
        $server = app()->make(Server::class);

        $process = new SwooleProcess(function (SwooleProcess $process) {
            for ($i = 1; $i <= 100; $i++) {
                $this->gc();

                sleep(ceil($this->maxKeepTimestampSequence / 1000 * 0.5));
            }
        });

        $server->getSwooleServer()->addProcess($process);
    }
}
