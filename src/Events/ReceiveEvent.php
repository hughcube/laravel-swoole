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
 * Class ReceiveEvent
 * @package HughCube\Laravel\Swoole\Events
 * @see https://wiki.swoole.com/#/server/events?id=onreceive
 */
class ReceiveEvent extends Event
{

    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var integer
     */
    public $fd;

    /**
     * @var integer
     */
    public $reactorId;

    /**
     * @var string
     */
    public $data;

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
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return int
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
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
        $this->fd = isset($parameters[1]) ? $parameters[1] : null;
        $this->reactorId = isset($parameters[2]) ? $parameters[2] : null;
        $this->data = isset($parameters[3]) ? $parameters[3] : null;
    }
}
