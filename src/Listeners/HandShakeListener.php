<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\HandShakeEvent;

/**
 * Class BufferEmptyListener
 * @package HughCube\Laravel\Swoole\Listeners
 * @see
 */
class HandShakeListener extends Listener
{
    public function handle(HandShakeEvent $event)
    {

    }
}
