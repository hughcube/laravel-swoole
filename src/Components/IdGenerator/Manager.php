<?php

namespace HughCube\Laravel\Swoole\Components\IdGenerator;

use InvalidArgumentException;

/**
 * Class Manager.
 */
class Manager
{
    /**
     * The IdGenerator server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The connections.
     *
     * @var Client[]
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
     * @return Client
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
     * @return Client
     */
    protected function resolve($name = null)
    {
        $name = null == $name ? 'default' : $name;

        if (!isset($this->config[$name])) {
            throw new InvalidArgumentException("IdGenerator [{$name}] not configured.");
        }

        $idGenerator = new Client($this->config[$name]);

        return $idGenerator;
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
