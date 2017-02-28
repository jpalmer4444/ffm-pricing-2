<?php

return [
    'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\IndexController',
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller'    => 'Application\Controller\IndexController',
                        'action'        => 'index',
                    ],
                ],
            ],
            'about' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/about',
                    'defaults' => [
                        'controller' => 'Application\Controller\IndexController',
                        'action'     => 'about',
                    ],
                ],
            ],          
        ],
];
