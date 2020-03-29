<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28.
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\WorkerStartEvent;

/**
 * Class WorkerStartListener.
 *
 * @see https://wiki.swoole.com/#/server/events?id=onstart
 */
class WorkerStartListener extends Listener
{
    /**
     * @param WorkerStartEvent $event
     */
    public function handle(WorkerStartEvent $event)
    {
        if ($event->getServer()->getSwooleServer()->taskworker) {
            $event->getServer()->setProcessName('task process');
        } else {
            $event->getServer()->setProcessName('worker process');
        }
    }
}
