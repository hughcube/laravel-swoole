<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 16:39.
 */

namespace HughCube\Laravel\Swoole;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class Manager
{
    const DEFAULT_CONNECTION_NAME = 'http';

    /**
     * The application instance.
     *
     * @var LaravelApplication|LumenApplication
     */
    protected $app;

    /**
     * The swoole server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The acm connections.
     *
     * @var Server[]
     */
    protected $connections;

    /**
     * Manager constructor.
     *
     * @param LaravelApplication|LumenApplication $app
     * @param array                               $config
     */
    public function __construct($app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Get a server by name.
     *
     * @param string|null $name
     *
     * @return Server
     */
    public function connection($name = null)
    {
        $name = null == $name ? static::DEFAULT_CONNECTION_NAME : $name;

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
     * @return Server
     */
    public function resolve($name = null)
    {
        $name = null == $name ? static::DEFAULT_CONNECTION_NAME : $name;

        if (isset($this->config[$name])) {
            return new Server($this->app, $this->config[$name]);
        }

        throw new \InvalidArgumentException("Swoole server connection [{$name}] not configured.");
    }
}
