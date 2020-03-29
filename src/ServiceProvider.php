<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/22
 * Time: 02:51.
 */

namespace HughCube\Laravel\Swoole;

use HughCube\Laravel\Swoole\Commands\StartCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

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
            $source = realpath($raw = __DIR__.'/../config/swoole.php') ?: $raw;
            $this->publishes([$source => config_path('swoole.php')]);
        }

        if ($this->app instanceof LumenApplication) {
            $this->app->configure('swoole');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Manager::class, function ($app) {
            /** @var LaravelApplication|LumenApplication $app */
            $config = $app->make('config')->get('swoole', []);

            return new Manager($app, $config);
        });

        if ($this->app->runningInConsole()) {
            $this->registerCommand();
        }
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
