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
 * Class WorkerStopEvent
 * @package HughCube\Laravel\Swoole\Events
 * @see https://wiki.swoole.com/#/server/events?id=onworkerstop
 */
class WorkerStopEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var integer
     */
    public $workerId;

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
        return $this->workerId;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->workerId = isset($parameters[1]) ? $parameters[1] : null;
    }
}
