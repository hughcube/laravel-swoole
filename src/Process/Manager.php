<?php

namespace HughCube\Laravel\Swoole\Process;

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
    protected $config;

    /**
     * Manager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 创建所有.
     *
     * @return void
     */
    public function bootstrapCreate(Server $server)
    {
        foreach ($this->config as $handler) {
            $process = new SwooleProcess(function (SwooleProcess $process) use ($handler, $server) {
                /** @var Handler $handler */
                $handler = new $handler($server, $process);

                $handler->handle();
            });

            $server->getSwooleServer()->addProcess($process);
        }
    }
}
