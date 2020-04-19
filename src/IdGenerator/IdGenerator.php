<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/4/17
 * Time: 13:58
 */

namespace HughCube\Laravel\Swoole\IdGenerator;

use Illuminate\Support\Facades\Facade;

/**
 * Class IdGenerator
 * @package HughCube\Laravel\Swoole\IdGenerator
 *
 * @method static Client connection(string $name = null)
 * @method static integer bootstrap()
 */
class IdGenerator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.idGenerator';
    }
}
