<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/4/17
 * Time: 14:06.
 */

namespace HughCube\Laravel\Swoole\Mutex;

use Illuminate\Support\Facades\Facade;
use Swoole\Lock as SwooleLock;

/**
 * Class Mutex.
 *
 * @method static SwooleLock connection(string $name = null)
 * @method static integer bootstrap()
 */
class Mutex extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.mutex';
    }
}
