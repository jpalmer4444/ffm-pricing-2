<?php

use User\Session\BalancedHttpUserAgent;
use User\Session\BalancedRemoteAddr;
use Zend\Session\Storage\SessionArrayStorage;
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    // Session configuration.
    'session_config' => [
        'cookie_lifetime'     => 60*60*24, // Session cookie will expire in 24 hours.
        'gc_maxlifetime'      => 60*60*24*30, // How long to store session data on server (for 1 month).        
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            BalancedRemoteAddr::class,
            BalancedHttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    'ngSettings' => [
        'loginUrl' => '/login',
        'usersTableAjax' => '/users-table',
        'usersTableUpdateStatusAjax' => '/users-table-update-status',
    ],
];
