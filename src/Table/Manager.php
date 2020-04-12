<?php

namespace HughCube\Laravel\Swoole\Table;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Swoole\Table as SwooleTable;

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
     * @var SwooleTable[]
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
     * @return SwooleTable
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
     * @return SwooleTable
     * @throws \InvalidArgumentException
     *
     */
    protected function resolve($name = null)
    {
        $name = null == $name ? 'default' : $name;

        if (!isset($this->config[$name])) {
            throw new InvalidArgumentException("Table [{$name}] not configured.");
        }

        $size = Arr::get($this->config[$name], 'size');
        $conflictProportion = Arr::get($this->config[$name], 'conflict_proportion', 0.2);
        $columns = Arr::get($this->config[$name], 'columns', []);

        $table = new SwooleTable($size, $conflictProportion);

        foreach ($columns as $column) {
            if (isset($column['size'])) {
                $table->column($column['name'], $column['type'], $column['size']);
            } else {
                $table->column($column['name'], $column['type']);
            }
        }

        $table->create();

        return $table;
    }

    /**
     * 创建所有的table.
     *
     * @return int
     */
    public function bootstrapCreate()
    {
        foreach ($this->config as $name => $table) {
            $this->connection($name);
        }

        return count($this->config);
    }
}
