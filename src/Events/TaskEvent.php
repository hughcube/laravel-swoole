<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Events;

use Swoole\Server as SwooleServer;

/**
 * Class TaskEvent.
 *
 * @see https://wiki.swoole.com/#/server/events?id=ontask
 */
class TaskEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var int
     */
    protected $task_id;

    /**
     * @var int
     */
    protected $src_worker_id;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->swooleServer;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->task_id;
    }

    /**
     * @return int
     */
    public function getSrcWorkerId(): int
    {
        return $this->src_worker_id;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->task_id = isset($parameters[1]) ? $parameters[1] : null;
        $this->src_worker_id = isset($parameters[2]) ? $parameters[2] : null;
        $this->data = isset($parameters[3]) ? $parameters[3] : null;
    }
}
