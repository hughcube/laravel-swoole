<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/30
 * Time: 23:26
 */

namespace HughCube\Laravel\Swoole;


use Laravel\Lumen\Application as LumenApplication;

class PreResolved
{
    /**
     * The application instance.
     *
     * @var LaravelApplication|LumenApplication
     */
    protected $app;

    /**
     * @var array
     */
    protected $resolves = [
        'view',
        'files',
        'session',
        'session.store',
        'routes',
        'db',
        'db.factory',
        'cache',
        'cache.store',
        'config',
        'cookie',
        'encrypter',
        'hash',
        'router',
        'translator',
        'url',
        'log',
    ];

    //'Illuminate\Contracts\Foundation\Application' => 'app',
    //'Illuminate\Contracts\Auth\Factory' => 'auth',
    //'Illuminate\Contracts\Auth\Guard' => 'auth.driver',
    //'Illuminate\Contracts\Cache\Factory' => 'cache',
    //'Illuminate\Contracts\Cache\Repository' => 'cache.store',
    //'Illuminate\Contracts\Config\Repository' => 'config',
    //'Illuminate\Container\Container' => 'app',
    //'Illuminate\Contracts\Container\Container' => 'app',
    //'Illuminate\Database\ConnectionResolverInterface' => 'db',
    //'Illuminate\Database\DatabaseManager' => 'db',
    //'Illuminate\Contracts\Encryption\Encrypter' => 'encrypter',
    //'Illuminate\Contracts\Events\Dispatcher' => 'events',
    //'Illuminate\Contracts\Filesystem\Factory' => 'filesystem',
    //'Illuminate\Contracts\Filesystem\Filesystem' => 'filesystem.disk',
    //'Illuminate\Contracts\Filesystem\Cloud' => 'filesystem.cloud',
    //'Illuminate\Contracts\Hashing\Hasher' => 'hash',
    //'log' => 'Psr\Log\LoggerInterface',
    //'Illuminate\Contracts\Queue\Factory' => 'queue',
    //'Illuminate\Contracts\Queue\Queue' => 'queue.connection',
    //'Illuminate\Redis\RedisManager' => 'redis',
    //'Illuminate\Contracts\Redis\Factory' => 'redis',
    //'Illuminate\Redis\Connections\Connection' => 'redis.connection',
    //'Illuminate\Contracts\Redis\Connection' => 'redis.connection',
    //'request' => 'Illuminate\Http\Request',
    //'Laravel\Lumen\Routing\Router' => 'router',
    //'Illuminate\Contracts\Translation\Translator' => 'translator',
    //'Laravel\Lumen\Routing\UrlGenerator' => 'url',
    //'Illuminate\Contracts\Validation\Factory' => 'validator',
    //'Illuminate\Contracts\View\Factory' => 'view',

    public function bootstrap()
    {
        foreach ($this->resolves as $abstract) {
            if ($this->app->offsetExists($abstract)) {
                continue;
            }

            if ($this->app->isAlias($abstract) || class_exists($abstract)) {
                $this->app->make($abstract);
            }
        }

        $this->app->forgetInstance();
    }


}
