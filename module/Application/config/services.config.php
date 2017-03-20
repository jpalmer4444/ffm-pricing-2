<?php

return [
    'aliases' => [
    ],
    'abstract_factories' => [
        'Zend\Log\LoggerAbstractServiceFactory',
    ],
    'factories' => [
        'Zend\Validator\Translator\TranslatorInterface' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        'Application\Navigation\NavManager' => 'Application\Navigation\Factory\NavManagerFactory',
        //services (model compliant)
        'Application\Service\PermissionService' => 'Application\Service\Factory\PermissionServiceFactory',
        'Application\Service\RolesService' => 'Application\Service\Factory\RolesServiceFactory',
        'Application\Service\UserService' => 'Application\Service\Factory\UserServiceFactory',
        'Application\Service\UserSessionService' => 'Application\Service\Factory\UserSessionServiceFactory',
        'Application\Service\CustomerService' => 'Application\Service\Factory\CustomerServiceFactory',
        'Application\Service\PriceOverrideService' => 'Application\Service\Factory\PriceOverrideServiceFactory',
        'Application\Service\CheckboxService' => 'Application\Service\Factory\CheckboxServiceFactory',
        'Application\Service\PriceOverrideReportService' => 'Application\Service\Factory\PriceOverrideReportServiceFactory',
        'Application\Service\ProductService' => 'Application\Service\Factory\ProductServiceFactory',
        'Application\Service\AddedProductService' => 'Application\Service\Factory\AddedProductServiceFactory',
        
        //services (NOT model compliant)
        'Application\Service\RestService' => 'Application\Service\Factory\RestServiceFactory',
        
        //non-shared (new instance everytime)
        'Application\Datatables\SSPJoin' => 'Application\Datatables\Factory\SSPJoinFactory',
    ],
    'shared' => [
        'Application\Datatables\SSPJoin' => false,
    ]
];
