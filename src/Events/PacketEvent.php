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
 * Class PacketEvent.
 *
 * @see https://wiki.swoole.com/#/server/events?id=onpacket
 */
class PacketEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var string
     */
    public $data;

    /**
     * @var array
     */
    public $clientInfo;

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->swooleServer;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getClientInfo(): array
    {
        return $this->clientInfo;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->data = isset($parameters[1]) ? $parameters[1] : null;
        $this->clientInfo = isset($parameters[2]) ? $parameters[2] : null;
    }
}
