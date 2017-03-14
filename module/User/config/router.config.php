<?php

return [
    'routes' => [
            'login' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => 'User\Controller\AuthController',
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => 'User\Controller\AuthController',
                        'action'     => 'logout',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => 'User\Controller\UserController',
                        'action'     => 'resetPassword',
                    ],
                ],
            ],
            'set-password' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/set-password',
                    'defaults' => [
                        'controller' => 'User\Controller\UserController',
                        'action'     => 'setPassword',
                    ],
                ],
            ],
            'users-table' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/users-table',
                    'defaults' => [
                        'controller' => 'User\Controller\UserController',
                        'action'     => 'usersTable',
                    ],
                ],
            ],
            'users-table-update-status' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/users-table-update-status',
                    'defaults' => [
                        'controller' => 'User\Controller\UserController',
                        'action'     => 'usersTableUpdateStatus',
                    ],
                ],
            ],
            'users' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'User\Controller\UserController',
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
];
