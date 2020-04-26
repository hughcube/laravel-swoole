<?php

namespace HughCube\Laravel\Swoole\Components\Counter;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Swoole\Atomic as SwooleAtomic;
use Swoole\Atomic\Long as SwooleLongAtomic;

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
     * The connections.
     *
     * @var SwooleAtomic[]
     */
    protected $connections = [];

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
     * Get a connection by name.
     *
     * @param string|null $name
     *
     * @return SwooleAtomic
     */
    public function connection($name = null)
    {
        $name = null == $name ? 'default' : $name;

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        return $this->connections[$name] = $this->resolve($name);
    }

    /**
     * Resolve the given connection by name.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return SwooleAtomic
     */
    protected function resolve($name = null)
    {
        $name = null == $name ? 'default' : $name;

        if (!isset($this->config[$name])) {
            throw new InvalidArgumentException("Counter [{$name}] not configured.");
        }

        $type = Arr::get($this->config[$name], 'type');
        $value = Arr::get($this->config[$name], 'value');

        $atomic = 'long' === $type ? new SwooleLongAtomic() : new SwooleAtomic();
        $atomic->set((null == $value ? 0 : $value));

        return $atomic;
    }

    /**
     * 创建所有.
     *
     * @return int
     */
    public function bootstrap()
    {
        foreach ($this->config as $name => $item) {
            $this->connection($name);
        }

        return count($this->config);
    }
}
