<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Http;

use HughCube\Laravel\Swoole\Events\RequestEvent;
use HughCube\Laravel\Swoole\Listeners\Listener;

class LaravelRequestListener extends Listener
{
    /**
     * @param RequestEvent $event
     *
     * @throws \Exception
     */
    public function handle(RequestEvent $event)
    {
        if ($event->isSend()) {
            return;
        }

        $sandbox = new Sandbox($event->getServer()->getApp());

        $sandbox->handle($event->getRequest(), $event->getResponse());
    }
}
