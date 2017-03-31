<?php

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
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    // Session configuration.
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 24, // Session cookie will expire in 24 hours.
        'gc_maxlifetime' => 60 * 60 * 24 * 30, // How long to store session data on server (for 1 month).    
        'cookie_secure' => true
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            'User\Session\BalancedRemoteAddr',
            'User\Session\BalancedHttpUserAgent',
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => 'Zend\Session\Storage\SessionArrayStorage'
    ],
    'ngSettings' => [
    ],
    'queries' => [
        "Application\Controller\ProductController" => [
            'actions' => [
                'productTableAction' => include __DIR__ . '/../queries/productTableAction.php'
            ]
        ],
        "Application\Controller\CustomerController" => [
            'actions' => [
                'customerTableAction' => include __DIR__ . '/../queries/customerTableAction.php'
            ]
        ]
    ],
];
