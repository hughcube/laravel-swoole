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
 * Class CloseEvent.
 *
 * @see https://wiki.swoole.com/#/server/events?id=onclose
 */
class CloseEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var int
     */
    protected $reactorId;

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

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->fd = isset($parameters[1]) ? $parameters[1] : null;
        $this->reactorId = isset($parameters[2]) ? $parameters[2] : null;
    }
}
