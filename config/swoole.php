<?php

return [
    'http' => [
        'protocol'     => 'http',
        'listen_port'  => env('SOCKET_LISTEN_PORT', 5200),
        'listen_ip'    => env('SOCKET_listen_ip', '0.0.0.0'),
        'socket_type'  => SWOOLE_SOCK_TCP,
        'swoole_model' => SWOOLE_PROCESS,
        'setting'      => [
            'log_level' => SWOOLE_LOG_TRACE,
        ],

        //'processes' => [
        //    'HughCube\Laravel\Swoole\Process\Handler'
        //],

        //'mutex' => [
        //    'mutexName' => ['type' => SWOOLE_RWLOCK],
        //],

        //'counters' => [
        //    'countName' => 0,
        //],

        //'tables' => [
        //    'default' => [
        //        'size' => 10,
        //        'conflict_proportion' => 0.2,
        //        'columns' => [
        //            ['name' => 'column_name', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 1024],
        //        ]
        //    ]
        //]
    ],
];
