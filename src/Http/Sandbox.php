<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:14.
 */

namespace HughCube\Laravel\Swoole\Http;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Http\Response as IlluminateResponse;
use Laravel\Lumen\Application as LumenApplication;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Sandbox
{
    /**
     * @var LumenApplication|LaravelApplication
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param SwooleRequest  $request
     * @param SwooleResponse $swooleResponse
     */
    public function handle(SwooleRequest $request, SwooleResponse $swooleResponse)
    {
        /** @var IlluminateResponse $response */
        $response = $this->app->dispatch(Request::createFromSwoole($this->app, $request));

        $this->sendIlluminateResponseHeaders($response, $swooleResponse);
        $swooleResponse->end($response->getContent());
    }

    /**
     * @param SymfonyResponse $symfonyResponse
     * @param SwooleResponse  $swooleResponse
     */
    public function sendIlluminateResponseHeaders(
        SymfonyResponse $symfonyResponse,
        SwooleResponse $swooleResponse
    ) {
        // headers
        foreach ($symfonyResponse->headers->allPreserveCaseWithoutCookies() as $name => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($name, $value);
            }
        }

        // cookies
        $hasIsRaw = null;
        foreach ($symfonyResponse->headers->getCookies() as $cookie) {
            if ($hasIsRaw === null) {
                $hasIsRaw = method_exists($cookie, 'isRaw');
            }

            $setCookie = $hasIsRaw && $cookie->isRaw() ? 'rawcookie' : 'cookie';
            $swooleResponse->{$setCookie}(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }

        $swooleResponse->status($symfonyResponse->getStatusCode());
    }
}
