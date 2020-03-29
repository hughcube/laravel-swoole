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
 * Class FinishEvent
 * @package HughCube\Laravel\Swoole\Events
 * @see https://wiki.swoole.com/#/server/events?id=onfinish
 */
class FinishEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var integer
     */
    protected $task_id;

    /**
     * @var string
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
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->task_id = isset($parameters[1]) ? $parameters[1] : null;
        $this->data = isset($parameters[2]) ? $parameters[2] : null;
    }
}
