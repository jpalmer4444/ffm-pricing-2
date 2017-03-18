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
            'customer' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/customer[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'Application\Controller\CustomerController',
                        'action'        => 'index',
                    ],
                ],
            ],
            'product' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/product[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'Application\Controller\ProductController',
                        'action'        => 'index',
                    ],
                ],
            ],
            'salespeople' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/salespeople[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'Application\Controller\SalespeopleController',
                        'action'        => 'index',
                    ],
                ],
            ],
            'customer-table' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/customer-table',
                    'defaults' => [
                        'controller' => 'Application\Controller\CustomerController',
                        'action'     => 'customerTable',
                    ],
                ],
            ],
            'salespeople-table' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/salespeople-table',
                    'defaults' => [
                        'controller' => 'Application\Controller\SalespeopleController',
                        'action'     => 'salespeopleTable',
                    ],
                ],
            ],
            'product-table' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/product-table',
                    'defaults' => [
                        'controller' => 'Application\Controller\ProductController',
                        'action'     => 'productTable',
                    ],
                ],
            ],
        ],
];
