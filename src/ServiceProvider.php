<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/22
 * Time: 02:51.
 */

namespace HughCube\Laravel\Swoole;

use HughCube\Laravel\Swoole\Commands\StartCommand;
use HughCube\Laravel\Swoole\Components\Counter\Counter;
use HughCube\Laravel\Swoole\Components\Counter\Manager as CounterManager;
use HughCube\Laravel\Swoole\Components\IdGenerator\IdGenerator;
use HughCube\Laravel\Swoole\Components\IdGenerator\Manager as IdGeneratorManager;
use HughCube\Laravel\Swoole\Components\Mutex\Manager as MutexManager;
use HughCube\Laravel\Swoole\Components\Mutex\Mutex;
use HughCube\Laravel\Swoole\Components\Process\Manager as ProcessManager;
use HughCube\Laravel\Swoole\Components\Process\Process;
use HughCube\Laravel\Swoole\Components\Table\Manager as TableManager;
use HughCube\Laravel\Swoole\Components\Table\Table;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $source = realpath($raw = __DIR__ . '/../config/swoole.php') ?: $raw;
            $this->publishes([$source => config_path('swoole.php')]);
        }

        if ($this->app instanceof LumenApplication) {
            $this->app->configure('swoole');
        }

        $this->app->make(Server::class);

        Counter::bootstrap();
        IdGenerator::bootstrap();
        Mutex::bootstrap();
        Table::bootstrap();
        Process::bootstrap();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommand();
        }

        /**
         * swoole 进程.
         */
        $this->app->singleton(Server::class, function ($app) {
            /** @var LaravelApplication|LumenApplication $app */
            $config = $app->make('config')->get('swoole', []);

            return new Server($app, $config);
        });

        /**
         * swoole计数器.
         */
        $this->app->singleton('swoole.counter', function ($app) {
            $config = $app->make('config')->get('swoole.counters', []);

            return new CounterManager($config);
        });

        /**
         * ID生成器.
         */
        $this->app->singleton('swoole.idGenerator', function ($app) {
            $config = $app->make('config')->get('swoole.idGenerators', []);

            return new IdGeneratorManager($config);
        });

        /**
         * 全局锁
         */
        $this->app->singleton('swoole.mutex', function ($app) {
            $config = $app->make('config')->get('swoole.mutex', []);

            return new MutexManager($config);
        });

        /**
         * 表存储.
         */
        $this->app->singleton('swoole.table', function ($app) {
            $config = $app->make('config')->get('swoole.tables', []);

            return new TableManager($config);
        });

        /**
         * 进程
         */
        $this->app->singleton('swoole.process', function ($app) {
            $config = $app->make('config')->get('swoole.processes', []);

            return new ProcessManager($config);
        });
    }

    protected function registerCommand()
    {
        $commands = [
            StartCommand::class,
        ];

        foreach ($commands as $command) {
            $this->app->singleton($command, function () use ($command) {
                return new $command();
            });
            $this->commands([$command]);
        }
    }
}
