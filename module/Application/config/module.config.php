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
            // Configures the default SessionManager instance
            'Zend\Session\ManagerInterface' => 'Zend\Session\Service\SessionManagerFactory',
            // Provides session configuration to SessionManagerFactory
            'Zend\Session\Config\ConfigInterface' => 'Zend\Session\Service\SessionConfigFactory',
            'Application\Controller\IndexController' => 'Application\Controller\Factory\IndexControllerFactory',
            'Application\Controller\SalespeopleController' => 'Application\Controller\Factory\SalespeopleControllerFactory',
            'Application\Controller\CustomerController' => 'Application\Controller\Factory\CustomerControllerFactory',
            'Application\Controller\ProductController' => 'Application\Controller\Factory\ProductControllerFactory',
        ],
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
            'Application\View\Helper\Permissions' => 'Application\View\Helper\Factory\PermissionsFactory',
        ],
        'aliases' => [
            'mainMenu' => 'Application\View\Helper\Menu',
            'pageBreadcrumbs' => 'Application\View\Helper\Breadcrumbs',
            'permissions' => 'Application\View\Helper\Permissions',
        ],
        'invokables' => [
            'translate' => 'Zend\I18n\View\Helper\Translate'
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/403' => __DIR__ . '/../view/error/403.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/unauthorized' => __DIR__ . '/../view/error/unauthorized.phtml',
            'partial/users-table-header-tplt' => __DIR__ . '/../../User/view/user/partial/users-table-header-tplt.phtml',
            'partial/customer-table-header-tplt' => __DIR__ . '/../view/application/partial/customer-table-header-tplt.phtml',
            'partial/product-table-header-tplt' => __DIR__ . '/../view/application/partial/product-table-header-tplt.phtml',
            'partial/salespeople-table-header-tplt' => __DIR__ . '/../view/application/partial/salespeople-table-header-tplt.phtml',
            //global modals for all angular pages.
            'partial/warning-modal-tplt' => __DIR__ . '/../view/application/partial/warning-modal-tplt.phtml',
            'partial/confirmation-modal-tplt' => __DIR__ . '/../view/application/partial/confirmation-modal-tplt.phtml',
            //add Salesperson modal
            'partial/add-salesperson-modal-tplt' => __DIR__ . '/../view/application/partial/add-salesperson-modal-tplt.phtml',
            //add Product modal
            'partial/add-product-modal-tplt' => __DIR__ . '/../view/application/partial/add-product-modal-tplt.phtml',
            //override price modal
            'partial/override-price-modal-tplt' => __DIR__ . '/../view/application/partial/override-price-modal-tplt.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ],
    // The following key allows to define custom styling for FlashMessenger view helper.
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format' => '<div%s><ul><li>',
            'message_close_string' => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
];
