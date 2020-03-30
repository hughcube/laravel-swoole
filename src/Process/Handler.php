<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/30
 * Time: 22:56
 */

namespace HughCube\Laravel\Swoole\Process;

use HughCube\Laravel\Swoole\Server;
use Swoole\Process as SwooleProcess;

class Handler
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var SwooleProcess
     */
    protected $process;

    final public function __construct(Server $server, SwooleProcess $process)
    {
        $this->server = $server;
        $this->process = $process;
    }

    /**
     * @return Server
     */
    final public function getServer()
    {
        return $this->server;
    }

    /**
     * @return SwooleProcess
     */
    final public function getProcess()
    {
        return $this->process;
    }

    public function handle()
    {
        echo date('Y-m-d H:i:s  '), static::class, PHP_EOL;
        sleep(1);
    }
}
