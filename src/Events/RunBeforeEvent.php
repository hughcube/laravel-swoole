<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Events;

use Swoole\Server as SwooleServer;

class RunBeforeEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->swooleServer;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
    }
}
