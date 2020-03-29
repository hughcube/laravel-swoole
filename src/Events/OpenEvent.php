<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Events;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class RequestEvent
 * @package HughCube\Laravel\Swoole\Events
 * @see https://wiki.swoole.com/#/websocket_server?id=onopen
 */
class OpenEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->request = isset($parameters[0]) ? $parameters[0] : null;
        $this->response = isset($parameters[1]) ? $parameters[1] : null;
    }
}
