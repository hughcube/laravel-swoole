<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:28
 */

namespace HughCube\Laravel\Swoole\Listeners;

use HughCube\Laravel\Swoole\Events\BufferEmptyEvent;

/**
 * Class BufferEmptyListener
 * @package HughCube\Laravel\Swoole\Listeners
 * @see
 */
class BufferEmptyListener extends Listener
{
    public function handle(BufferEmptyEvent $event)
    {

    }
}
