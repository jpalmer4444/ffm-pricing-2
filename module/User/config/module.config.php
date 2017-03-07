<?php

namespace User;

return [
    'router' => include __DIR__ . '/router.config.php',
    'controllers' => [
        'factories' => [
            'User\Controller\AuthController' => 'User\Controller\Factory\AuthControllerFactory',
            'User\Controller\UserController' => 'User\Controller\Factory\UserControllerFactory',
        ],
    ],
    'service_manager' => include __DIR__ . '/services.config.php',
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../../Application/src/Entity'
                    ]
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
