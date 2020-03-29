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
 * Class PipeMessageEvent.
 *
 * @see https://wiki.swoole.com/#/server/events?id=onpipemessage
 */
class PipeMessageEvent extends Event
{
    /**
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var int
     */
    protected $src_worker_id;

    /**
     * @var mixed
     */
    protected $message;

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
    public function getSrcWorkerId(): int
    {
        return $this->src_worker_id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->swooleServer = isset($parameters[0]) ? $parameters[0] : null;
        $this->src_worker_id = isset($parameters[1]) ? $parameters[1] : null;
        $this->message = isset($parameters[2]) ? $parameters[2] : null;
    }
}
