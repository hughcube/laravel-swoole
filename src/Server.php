<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 17:13.
 */

namespace HughCube\Laravel\Swoole;

use HughCube\Laravel\Swoole\Counter\Manager as CounterManager;
use HughCube\Laravel\Swoole\Events\BufferEmptyEvent;
use HughCube\Laravel\Swoole\Events\BufferFullEvent;
use HughCube\Laravel\Swoole\Events\ConnectEvent;
use HughCube\Laravel\Swoole\Events\Event;
use HughCube\Laravel\Swoole\Events\FinishEvent;
use HughCube\Laravel\Swoole\Events\HandShakeEvent;
use HughCube\Laravel\Swoole\Events\InitServerEvent;
use HughCube\Laravel\Swoole\Events\ManagerStartEvent;
use HughCube\Laravel\Swoole\Events\ManagerStopEvent;
use HughCube\Laravel\Swoole\Events\MessageEvent;
use HughCube\Laravel\Swoole\Events\OpenEvent;
use HughCube\Laravel\Swoole\Events\PacketEvent;
use HughCube\Laravel\Swoole\Events\PipeMessageEvent;
use HughCube\Laravel\Swoole\Events\ReceiveEvent;
use HughCube\Laravel\Swoole\Events\RequestEvent;
use HughCube\Laravel\Swoole\Events\RunServerEvent;
use HughCube\Laravel\Swoole\Events\ShutdownEvent;
use HughCube\Laravel\Swoole\Events\StartEvent;
use HughCube\Laravel\Swoole\Events\TaskEvent;
use HughCube\Laravel\Swoole\Events\WorkerErrorEvent;
use HughCube\Laravel\Swoole\Events\WorkerExitEvent;
use HughCube\Laravel\Swoole\Events\WorkerStartEvent;
use HughCube\Laravel\Swoole\Events\WorkerStopEvent;
use HughCube\Laravel\Swoole\Exceptions\UnknownProtocolException;
use HughCube\Laravel\Swoole\Http\LaravelRequestListener;
use HughCube\Laravel\Swoole\Listeners\BufferEmptyListener;
use HughCube\Laravel\Swoole\Listeners\BufferFullListener;
use HughCube\Laravel\Swoole\Listeners\ConnectListener;
use HughCube\Laravel\Swoole\Listeners\FinishListener;
use HughCube\Laravel\Swoole\Listeners\HandShakeListener;
use HughCube\Laravel\Swoole\Listeners\ManagerStartListener;
use HughCube\Laravel\Swoole\Listeners\ManagerStopListener;
use HughCube\Laravel\Swoole\Listeners\MessageListener;
use HughCube\Laravel\Swoole\Listeners\OpenListener;
use HughCube\Laravel\Swoole\Listeners\PacketListener;
use HughCube\Laravel\Swoole\Listeners\PipeMessageListener;
use HughCube\Laravel\Swoole\Listeners\ReceiveListener;
use HughCube\Laravel\Swoole\Listeners\RequestListener;
use HughCube\Laravel\Swoole\Listeners\ShutdownListener;
use HughCube\Laravel\Swoole\Listeners\StartListener;
use HughCube\Laravel\Swoole\Listeners\TaskListener;
use HughCube\Laravel\Swoole\Listeners\WorkerErrorListener;
use HughCube\Laravel\Swoole\Listeners\WorkerExitListener;
use HughCube\Laravel\Swoole\Listeners\WorkerStartListener;
use HughCube\Laravel\Swoole\Listeners\WorkerStopListener;
use HughCube\Laravel\Swoole\Mutex\Manager as MutexManager;
use HughCube\Laravel\Swoole\Process\Manager as ProcessManager;
use HughCube\Laravel\Swoole\Table\Manager as TableManager;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Lumen\Application as LumenApplication;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

class Server
{
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
     * @var SwooleServer
     */
    protected $swooleServer;

    /**
     * @var TableManager
     */
    protected $tableManager;

    /**
     * @var CounterManager
     */
    protected $counterManager;

    /**
     * @var MutexManager
     */
    protected $mutexManager;

    /**
     * Server events.
     *
     * @var array
     */
    protected $swooleServerEvents = [
        'start'        => StartEvent::class,
        'shutDown'     => ShutdownEvent::class,
        'workerStart'  => WorkerStartEvent::class,
        'workerStop'   => WorkerStopEvent::class,
        'workerExit'   => WorkerExitEvent::class,
        'connect'      => ConnectEvent::class,
        'packet'       => PacketEvent::class,
        'bufferFull'   => BufferFullEvent::class,
        'bufferEmpty'  => BufferEmptyEvent::class,
        'task'         => TaskEvent::class,
        'finish'       => FinishEvent::class,
        'pipeMessage'  => PipeMessageEvent::class,
        'workerError'  => WorkerErrorEvent::class,
        'managerStart' => ManagerStartEvent::class,
        'managerStop'  => ManagerStopEvent::class,
        'request'      => RequestEvent::class,
        'receive'      => ReceiveEvent::class,
        'handShake'    => HandShakeEvent::class,
        'open'         => OpenEvent::class,
        'message'      => MessageEvent::class,
    ];

    protected $swooleServerEventListeners = [
        StartEvent::class        => [StartListener::class],
        ShutdownEvent::class     => [ShutdownListener::class],
        WorkerStartEvent::class  => [WorkerStartListener::class],
        WorkerStopEvent::class   => [WorkerStopListener::class],
        WorkerExitEvent::class   => [WorkerExitListener::class],
        ConnectEvent::class      => [ConnectListener::class],
        PacketEvent::class       => [PacketListener::class],
        BufferFullEvent::class   => [BufferFullListener::class],
        BufferEmptyEvent::class  => [BufferEmptyListener::class],
        TaskEvent::class         => [TaskListener::class],
        FinishEvent::class       => [FinishListener::class],
        PipeMessageEvent::class  => [PipeMessageListener::class],
        WorkerErrorEvent::class  => [WorkerErrorListener::class],
        ManagerStartEvent::class => [ManagerStartListener::class],
        ManagerStopEvent::class  => [ManagerStopListener::class],
        RequestEvent::class      => [RequestListener::class, LaravelRequestListener::class],
        ReceiveEvent::class      => [ReceiveListener::class],
        HandShakeEvent::class    => [HandShakeListener::class],
        OpenEvent::class         => [OpenListener::class],
        MessageEvent::class      => [MessageListener::class],
    ];

    public function __construct($app, $config)
    {
        $this->app = $app;
        $this->config = $config;

        $this->bootstrapCreateSwoole();
        $this->bootstrapCreateTable();
        $this->bootstrapCreateCounter();

        $this->app->make('events')->dispatch(new InitServerEvent($this));

        $this->app->instance(static::class, $this);
    }

    /**
     * 创建swoole对象
     */
    protected function bootstrapCreateSwoole()
    {
        if ('cli' !== php_sapi_name()) {
            return;
        }

        $protocol = Arr::get($this->config, 'protocol', 'http');
        $ip = Arr::get($this->config, 'listen_ip', '0.0.0.0');
        $port = Arr::get($this->config, 'listen_port', 1123);
        $socketType = Arr::get($this->config, 'socket_type', SWOOLE_SOCK_TCP);
        $model = Arr::get($this->config, 'swoole_model', SWOOLE_PROCESS);

        if ('http' === $protocol) {
            $this->swooleServer = new SwooleHttpServer($ip, $port, $model, $socketType);
        } elseif ('websocket' === $protocol) {
            $this->swooleServer = new SwooleWebSocketServer($ip, $port, $model, $socketType);
        } elseif ('tcp' === $protocol || 'udp' === $protocol) {
            $this->swooleServer = new SwooleServer($ip, $port, $model, $socketType);
        } else {
            throw new UnknownProtocolException();
        }
    }

    /**
     * 注册swoole的事件.
     */
    protected function bootstrapRegisterSwooleEvent()
    {
        $swooleServer = $this->getSwooleServer();

        foreach ($this->swooleServerEvents as $eventName => $eventClass) {
            /** @var Event $event */
            $event = new $eventClass($this);

            if (false === $event->canDispatch($swooleServer)) {
                continue;
            }

            $swooleServer->on($eventName, function () use ($event) {
                $event->receiveSwooleEventParameters(func_get_args());

                /** @var \Illuminate\Events\Dispatcher $events */
                $events = $this->app->make('events');

                $events->dispatch($event);
            });
        }
    }

    /**
     * 注册swoole的时间监听.
     */
    protected function bootstrapRegisterSwooleEventListener()
    {
        foreach ($this->swooleServerEventListeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                /** @var \Illuminate\Events\Dispatcher $events */
                $events = $this->app->make('events');

                $events->listen($event, $listener);
            }
        }
    }

    /**
     * 创建所有的table.
     *
     * @return int
     */
    public function bootstrapCreateTable()
    {
        $this->tableManager = new TableManager(Arr::get($this->config, 'tables', []));

        return $this->tableManager->bootstrapCreate();
    }

    /**
     * 创建所有的计数器.
     *
     * @return int
     */
    public function bootstrapCreateCounter()
    {
        $this->counterManager = new CounterManager(Arr::get($this->config, 'counters', []));

        return $this->counterManager->bootstrapCreate();
    }

    /**
     * 创建所有的互斥锁
     *
     * @return int
     */
    public function bootstrapCreateMutex()
    {
        $this->mutexManager = new MutexManager(Arr::get($this->config, 'mutex', []));

        return $this->mutexManager->bootstrapCreate();
    }

    /**
     * 创建进程.
     *
     * @return void
     */
    public function bootstrapCreateProcess()
    {
        $manager = new ProcessManager(Arr::get($this->config, 'processes', []));
        $manager->bootstrapCreate($this);
    }

    /**
     * 注册swoole的时间监听.
     */
    protected function bootstrapSwooleConfig()
    {
    }

    /**
     * 启动swoole服务
     */
    public function run()
    {
        $this->app->make('events')->dispatch(new RunServerEvent($this));

        $this->bootstrapSwooleConfig();
        $this->bootstrapRegisterSwooleEvent();
        $this->bootstrapRegisterSwooleEventListener();
        $this->bootstrapCreateProcess();

        $this->getSwooleServer()->start();
    }

    /**
     * @return SwooleServer
     */
    public function getSwooleServer()
    {
        return $this->swooleServer;
    }

    /**
     * @return TableManager
     */
    public function getTableManager()
    {
        return $this->tableManager;
    }

    /**
     * @return CounterManager
     */
    public function getCounterManager()
    {
        return $this->counterManager;
    }

    /**
     * @return MutexManager
     */
    public function getMutexManager()
    {
        return $this->mutexManager;
    }

    /**
     * Set process name.
     *
     * @codeCoverageIgnore
     *
     * @param string $process
     */
    public function setProcessName($process)
    {
        // MacOS doesn't support modifying process name.
        if (Str::contains(Str::substr(Str::lower(PHP_OS), 0, 3), 'dar')) {
            return;
        }

        $processName = Arr::get($this->config, 'process_name', 'swoole');
        $appName = $this->app->make('config')->get('app.name', 'Swoole');

        swoole_set_process_name(sprintf('%s: %s for %s', $processName, $process, $appName));
    }

    protected function getEventDispatcher()
    {
    }

    /**
     * @return LaravelApplication|LumenApplication
     */
    public function getApp()
    {
        return $this->app;
    }
}
