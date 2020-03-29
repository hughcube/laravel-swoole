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
 * Class StartEvent.
 *
 * @see https://wiki.swoole.com/#/server/events?id=onstart
 */
class StartEvent extends Event
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

    /**
     * {@inheritdoc}
     */
    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
    }
}
