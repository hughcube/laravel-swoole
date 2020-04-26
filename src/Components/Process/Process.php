<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/4/24
 * Time: 18:24
 */

namespace HughCube\Laravel\Swoole\Components\Process;

use Illuminate\Support\Facades\Facade;

/**
 * Class Table.
 *
 * @method static Manager addProcess($process)
 * @method static integer bootstrap()
 */
class Process extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.process';
    }
}
