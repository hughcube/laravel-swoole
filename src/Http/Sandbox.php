<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:14
 */

namespace HughCube\Laravel\Swoole;

use HughCube\Laravel\Swoole\Exceptions\UnknownApplicationException;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Application as LumenApplication;
use Symfony\Component\HttpKernel\Kernel;

class Sandbox
{
    /**
     * @var LumenApplication|LaravelApplication
     */
    protected $app;

    public function __construct($app, Request $request)
    {
    }

    /**
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws UnknownApplicationException
     */
    public function handleRequest(Request $request)
    {
        if ($this->app instanceof LumenApplication) {
            /** @var Kernel $kernel */
            $kernel = $this->app->make(Kernel::class);

            return $kernel->handle($request);
        }

        if ($this->app instanceof LaravelApplication) {
            return $this->app->dispatch($request);
        }

        throw new UnknownApplicationException();
    }
}
