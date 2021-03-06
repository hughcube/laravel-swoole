<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\StartEvent;

class StartListener extends Listener
{
    public function handle(StartEvent $event)
    {
        $event->getServer()->setProcessName('master process');
    }
}
