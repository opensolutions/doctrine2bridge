<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Doctrine2 Cache
    |--------------------------------------------------------------------------
    |
    | Cache configuration. Implemented caches include:
    |     - MemcacheCache
    |     - ArrayCache
    |
    | Any cache such as ArrayCache requiring no configuration can be named below.
    | Caches requiring configuration will require updating:
    |     src/Doctrine2Bridge/Doctrine2CacheBridgeServiceProvider.php
    |
    | It should be fairly trivial to do this. Please open a pull request when you do!
    |
    */
    'cache' => array(
        'namespace' => 'Doctrine2',
        'type'      => 'ArrayCache', // 'MemcacheCache'

        'memcache'  => array(
            'servers' => array(
                array(
                    'host'       => '127.0.0.1',
                    // 'port'       = '11211',
                    // 'persistent' = false,
                    // 'weight'     = 1,
                    // 'timeout'    = 15
                    // 'retry_int'  = 15
                ),
                // more hosts...
            ),
        ),

        // CLI commands such as schema generation can react badly to older cached metadata.
        // If you want the cache cleared on each CLI command, set the following to true
        'flushForCli' => true
    ),

    /*
    |--------------------------------------------------------------------------
    | Doctrine2
    |--------------------------------------------------------------------------
    |
    */

    'doctrine' => array(

        // connection parameters
        'connection' => array(
            'driver'   => 'pdo_mysql',
            'dbname'   => 'test',
            'user'     => 'test',
            'password' => '',
            'host'     => '127.0.0.1',
            'charset'  => 'utf8'
        ),

        // Paths for models, proxies, repositories, etc.
        'paths' => array(
            'models'       => app_path()  . '/models',          // '/Entities' added by default
            'proxies'      => app_path()  . '/models/Proxies',
            'repositories' => app_path()  . '/models',          // '/Repositories' added by default
            'xml_schema'   => base_path() . '/doctrine/schema'
        ),

        // set to true to have Doctrine2 generate proxies on the fly. Not recommended in a production system.
        'autogen_proxies'        => false,

        // Namespaces for entities, proxies and repositories.
        'namespaces' => array(
            'models'       => 'Entities',
            'proxies'      => 'Proxies',
            'repositories' => 'Repositories'
        ),

        // Doctrine2Bridge includes an implementation of Doctrine\DBAL\Logging\SQLLogger which
        // just calls the Laravel Log facade. If you wish to log your SQL queries (and execution 
        // time), just set enabled in the following to true.
        'sqllogger' => array(
            'enabled' => false,
            'level'   => 'debug'   // one of debug, info, notice, warning, error, critical, alert
        ),
    )
);
