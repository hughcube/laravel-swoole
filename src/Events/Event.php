<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Events;

use HughCube\Laravel\Swoole\Server;
use Swoole\Server as SwooleServer;

class Event
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function canDispatch(SwooleServer $swooleServer)
    {
        return true;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
    }
}
