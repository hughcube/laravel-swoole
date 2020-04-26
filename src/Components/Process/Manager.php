<?php

namespace HughCube\Laravel\Swoole\Components\Process;

use HughCube\Laravel\Swoole\Events\RunBeforeEvent;
use HughCube\Laravel\Swoole\Server;
use Swoole\Process as SwooleProcess;

/**
 * Class Manager.
 */
class Manager
{
    /**
     * The acm server configurations.
     *
     * @var array
     */
    protected $processes;

    /**
     * Manager constructor.
     *
     * @param array $config
     */
    public function __construct(array $processes)
    {
        $this->processes = $processes;
    }

    public function addProcess($process)
    {
        $this->processes[] = $process;

        return $this;
    }

    /**
     * 创建所有.
     *
     * @return void
     */
    public function bootstrap()
    {
        /** @var Server $server */
        $server = app()->make(Server::class);

        /** @var \Illuminate\Events\Dispatcher $eventDispatcher */
        $eventDispatcher = $server->getApp()->make('events');

        $eventDispatcher->listen(RunBeforeEvent::class, function () use ($server) {
            foreach ($this->processes as $process) {
                if (is_string($process)) {
                    $callable = [$server->getApp()->make($process), 'handle'];
                } else {
                    $callable = $process;
                }

                $process = new SwooleProcess(function (SwooleProcess $process) use ($callable, $server) {
                    $server->setProcessName('process');
                    $callable();
                });

                $server->getSwooleServer()->addProcess($process);
            }
        });
    }
}
