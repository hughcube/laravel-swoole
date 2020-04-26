<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/4/17
 * Time: 13:45.
 */

namespace HughCube\Laravel\Swoole\Components\Counter;

use Illuminate\Support\Facades\Facade;
use Swoole\Atomic as SwooleAtomic;
use Swoole\Atomic\Long as SwooleLongAtomic;

/**
 * Class Counter.
 *
 * @method static SwooleAtomic|SwooleLongAtomic connection(string $name = null)
 * @method static integer bootstrap()
 */
class Counter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.counter';
    }
}
