<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Events;

use Swoole\Server as SwooleServer;

/**
 * Class WorkerErrorEvent
 * @package HughCube\Laravel\Swoole\Events
 * @see https://wiki.swoole.com/#/server/events?id=onworkererror
 */
class WorkerErrorEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var integer
     */
    protected $worker_id;

    /**
     * @var integer
     */
    protected $worker_pid;

    /**
     * @var integer
     */
    protected $exit_code;

    /**
     * @var integer
     */
    protected $signal;

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
    public function getWorkerId(): int
    {
        return $this->worker_id;
    }

    /**
     * @return int
     */
    public function getWorkerPid(): int
    {
        return $this->worker_pid;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exit_code;
    }

    /**
     * @return int
     */
    public function getSignal(): int
    {
        return $this->signal;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->worker_id = isset($parameters[1]) ? $parameters[1] : null;
        $this->worker_pid = isset($parameters[2]) ? $parameters[2] : null;
        $this->exit_code = isset($parameters[3]) ? $parameters[3] : null;
        $this->signal = isset($parameters[4]) ? $parameters[4] : null;
    }
}
