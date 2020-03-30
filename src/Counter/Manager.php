<?php

namespace HughCube\Laravel\Swoole\Counter;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Swoole\Atomic as SwooleAtomic;

/**
 * Class Manager
 * @package HughCube\Laravel\Swoole\Table
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
    public function atomic($name = null)
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
     * @return SwooleAtomic
     * @throws \InvalidArgumentException
     *
     */
    protected function resolve($name = null)
    {
        $name = null == $name ? 'default' : $name;;

        if (!isset($this->config[$name])) {
            throw new InvalidArgumentException("Counter [{$name}] not configured.");
        }

        $value = Arr::get($this->config[$name], 0);

        $atomic = new SwooleAtomic();
        $atomic->set($value);

        return $atomic;
    }

    /**
     * 创建所有
     *
     * @return int
     */
    public function bootstrapCreate()
    {
        foreach ($this->config as $name => $item) {
            $this->atomic($name);
        }

        return count($this->config);
    }
}
