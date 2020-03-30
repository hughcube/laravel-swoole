<?php

namespace HughCube\Laravel\Swoole\Mutex;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Swoole\Lock as SwooleLock;

/**
 * Class Manager
 * @package HughCube\Laravel\Swoole\Lock
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
     * @var SwooleLock[]
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
     * @return SwooleLock
     */
    public function lock($name = null)
    {
        $name = null == $name ?: 'default';

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
     * @return SwooleLock
     * @throws \InvalidArgumentException
     *
     */
    protected function resolve($name = null)
    {
        $name = null == $name ? 'default' : $name;;

        if (!isset($this->config[$name])) {
            throw new InvalidArgumentException("Mutex [{$name}] not configured.");
        }

        $type = Arr::get($this->config[$name], 'type');
        $lockfile = Arr::get($this->config[$name], 'lockfile');

        if (empty($lockfile)) {
            $lock = new SwooleLock($type);
        } else {
            $lock = new SwooleLock($type, $lockfile);
        }

        return $lock;
    }

    /**
     * 创建所有的table
     *
     * @return int
     */
    public function bootstrapCreate()
    {
        foreach ($this->config as $name => $table) {
            $this->lock($name);
        }

        return count($this->config);
    }
}
