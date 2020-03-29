<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Events;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class RequestEvent.
 *
 * @see https://wiki.swoole.com/#/http_server?id=on
 */
class RequestEvent extends Event
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
     * @var bool
     */
    protected $isSend = false;

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

    /**
     * @return bool
     */
    public function isSend(): bool
    {
        return $this->isSend;
    }

    /**
     * @param bool $isSend
     */
    public function setIsSend(bool $isSend)
    {
        $this->isSend = $isSend;
    }

    public function receiveSwooleEventParameters(array $parameters)
    {
        $this->request = isset($parameters[0]) ? $parameters[0] : null;
        $this->response = isset($parameters[1]) ? $parameters[1] : null;
    }
}
