<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\ShutdownEvent;

class ShutdownListener extends Listener
{
    public function handle(ShutdownEvent $event, array $payload)
    {

    }
}
