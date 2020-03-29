<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\WorkerStopEvent;

class WorkerStopListener extends Listener
{
    public function handle(WorkerStopEvent $event, array $payload)
    {

    }
}
