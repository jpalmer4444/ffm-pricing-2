<?php

namespace Application;

return [
    // Doctrine config
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'datetime_functions' => [
                    'date' => 'Application\Doctrine\DQL\Date',
                ]
            ]
        ]
    ],
    'router' => include __DIR__ . '/router.config.php',
    'service_manager' => include __DIR__ . '/services.config.php',
    'controllers' => [
        'factories' => [
            'Application\Controller\IndexController' => 'Application\Controller\Factory\IndexControllerFactory'
        ],
    ],
    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed 
            // under the 'access_filter' config key, and access is denied to any not listed 
            // action for not logged in users. In permissive mode, if an action is not listed 
            // under the 'access_filter' key, access to it is permitted to anyone (even for 
            // not logged in users. Restrictive mode is more secure and recommended to use.
            'mode' => 'restrictive'
        ],
        'controllers' => [
            'Application\Controller\IndexController' => [
                // Allow anyone to visit "index" and "about" actions
                ['actions' => ['index', 'about'], 'allow' => '*'],
                // Allow authorized users to visit "settings" action
                ['actions' => ['settings'], 'allow' => '@']
            ],
        ]
    ],
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
    
    'view_helpers' => [
        'factories' => [
            'Application\View\Helper\Menu' => 'Application\View\Helper\Factory\MenuFactory',
            'Application\View\Helper\Breadcrumbs' => 'Zend\ServiceManager\Factory\InvokableFactory',
        ],
        'aliases' => [
            'mainMenu' => 'Application\View\Helper\Menu',
            'pageBreadcrumbs' => 'Application\View\Helper\Breadcrumbs',
        ],
        'invokables' => [
            'translate' => 'Zend\I18n\View\Helper\Translate'
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    // The following key allows to define custom styling for FlashMessenger view helper.
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
];
