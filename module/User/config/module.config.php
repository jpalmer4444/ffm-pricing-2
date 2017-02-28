<?php

namespace User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use User\Controller\AuthController;
use User\Controller\Factory\AuthControllerFactory;
use User\Controller\Factory\UserControllerFactory;
use User\Controller\UserController;
use Zend\Authentication\AuthenticationService;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => UserController::class,
                        'action'     => 'resetPassword',
                    ],
                ],
            ],
            'set-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/set-password',
                    'defaults' => [
                        'controller' => UserController::class,
                        'action'     => 'setPassword',
                    ],
                ],
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => UserController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AuthController::class => AuthControllerFactory::class,
            UserController::class => UserControllerFactory::class,            
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            UserController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone.
                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
                // Give access to "index", "add", "edit", "view", "changePassword" actions to authorized users only.
                ['actions' => ['index', 'add', 'edit', 'view', 'changePassword'], 'allow' => '@']
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
