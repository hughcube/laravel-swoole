<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/4/17
 * Time: 14:10
 */

namespace HughCube\Laravel\Swoole\Table;

use Illuminate\Support\Facades\Facade;
use Swoole\Table as SwooleTable;

/**
 * Class Table
 * @package HughCube\Laravel\Swoole\Table
 *
 * @method static SwooleTable connection(string $name = null)
 * @method static integer bootstrap()
 */
class Table extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.table';
    }
}
