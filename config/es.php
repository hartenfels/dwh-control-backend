<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Elasticsearch Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the Elasticsearch connections below you wish
    | to use as your default connection for all work.
    |
    */
    'default' => env('ELASTIC_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the Elasticsearch connections setup for your application.
    | Of course, examples of configuring each Elasticsearch platform.
    |
    */
    'connections' => [
        'etl_executions_automic' => [
            'servers' => [
                [
                    'host' => env('ELASTIC_HOST_ETL_EXECUTIONS_AUTOMIC', '127.0.0.1'),
                    'port' => env('ELASTIC_PORT_ETL_EXECUTIONS_AUTOMIC', 9200),
                    'user' => env('ELASTIC_USER_ETL_EXECUTIONS_AUTOMIC', ''),
                    'pass' => env('ELASTIC_PASS_ETL_EXECUTIONS_AUTOMIC', ''),
                    'scheme' => env('ELASTIC_SCHEME_ETL_EXECUTIONS_AUTOMIC', 'http'),
                ],
            ],
            'index' => 'proc-automic_executions-2021'
        ],
    ],
];
