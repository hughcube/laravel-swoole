<?php

return [
    'http' => [
        'handle'  => 'HughCube\Laravel\Swoole\Contracts\Listeners\HttpListener',
        'setting' => [

        ],
        'protocol'     => 'http',
        'listen_port'  => env('SOCKET_LISTEN_PORT', 5200),
        'listen_ip'    => env('SOCKET_listen_ip', '0.0.0.0'),
        'socket_type'  => env('SOCKET_SOCKET_TYPE', 'SWOOLE_SOCK_TCP'),
        'swoole_model' => env('SWOOLE_TYPE', 'SWOOLE_PROCESS'),
    ],
];
