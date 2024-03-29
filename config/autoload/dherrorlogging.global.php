<?php
/**
 * DhErrorLogging Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$config = [

    /**
     * Set if error logging should be enabled.
     */
    'enabled' => true,

    /**
     * Set what error types should be logged
     */
    'error_types' => [
        // Exceptions (those other than within dispatch or render phase)
        'exceptions' => true,
        // Native PHP errors (those other than within dispatch or render phase)
        'errors' => true,
        // Dispatch errors, triggered in case of a problem or exception anywhere during dispatch
        // lifecycle (unknown controller, exception thrown inside of controller,...)
        'dispatch' => true,
        // Router no match  (route not found = 404).
        'dispatch\router_no_match' => true,
        // Render errors, triggered in case of a problem during the render process (no renderer found...).
        'render' => true,
        // Fatal errors that halt execution of further code
        'fatal' => true,
    ],
    /**
     * filter out some of the exception types (i.e. \Exception\UnauthorizedException)
     */
    'exception_filter' => [
        '\Exception\UnauthorizedException' // 403
    ],

   /**
    * Set writers to be used.
    * You can either add new config array for some of the the standard writers
    * that don't need injection of other objects
    * (stream, chromephp, 'fingerscrossed', 'firephp', 'mail', 'mock', 'null', 'syslog', 'zendmonitor')
    * or identifier of registered log writer factory (registered in general config section ['log_writers']).
    */
    'log_writers' => [

        /**
         * Writing logs into file
         *
         * Make sure the specified file exists and it is writable
         */
            'stream' => array(
                'name' => 'stream',
                'options' => array(
                    'stream' => __DIR__ . '/../../data/log/error.log',
                    'log_separator' => "\n"
                ),
            
            ),

        /**
         * Recording logs into database
         *
         * Make sure and have table "error_log" with correct schema in your database
         */
        'db' => [
            'name' => 'DhErrorLogging\DbWriter',
            'options' => [
                'table_name' => 'error_log',
                'table_map' => [
//                   'timestamp' => 'creation_time',
                   'type' => 'type',
                   'priorityName' => 'priority',
                   'message' => 'message',
                   'reference'  => 'reference',
                   'file'  => 'file',
                   'line'  => 'line',
                   'trace' => 'trace',
                   'xdebug' => 'xdebug',
                   'uri' => 'uri',
                   'request' => 'request',
                   'ip' => 'ip',
                   'session_id' => 'session_id'
                ]
            ]
        ]
    ],

    /**
     * Paths of templates to be used for output of errors
     */

    'templates' => [
         // error during dispatch lifecycle. comment out if you want to use the default zend skeleton one.
//         'dispatch' => __DIR__ . '/../view/error/dispatch.phtml',
         // error during render lifecycle. comment out if you want to use the default zend skeleton one.
//         'render' => __DIR__ . '/../view/error/index.phtml',
         // exceptions outside of mvc (i.e. while in module bootstrap,..)
//         'exception' => __DIR__ . '/../view/error/exception.html',
         // native errors while outside of mvc (i.e. while in module bootstrap,..)
//         'error' => __DIR__ . '/../view/error/error.html',
         // fatal errors while outside of mvc (i.e. while in module bootstrap,..)
//         'fatal' => __DIR__ . '/../view/error/fatal.html',
         // error output while in CLI
//         'console' => __DIR__ . '/../view/error/console.txt',
         // error output when json response requested
//         'json' => __DIR__ . '/../view/error/json.js'
    ],


    /**
     * Zend\Db\Adapter\Adapter DI Alias
     *
     * Please specify the DI alias for the configured Zend\Db\Adapter\Adapter
     * instance that DhErrorLogging should use. Applicable only if you want to log into database via Zend Db.
     */
//     'zend_db_adapter' => 'Zend\Db\Adapter\Adapter',

];


return [
    'dherrorlogging' => $config,
    'service_manager' => [
        'aliases' => [
            // set db adapter
            'dherrorlogging_zend_db_adapter'
                => (isset($config['zend_db_adapter'])) ? $config['zend_db_adapter']: 'Zend\Db\Adapter\Adapter',
        ],
    ],
];
