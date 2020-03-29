<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\ManagerStartEvent;

class ManagerStartListener extends Listener
{
    public function handle(ManagerStartEvent $event, array $payload)
    {
        $event->getServer()->setProcessName('manager process');
    }
}
