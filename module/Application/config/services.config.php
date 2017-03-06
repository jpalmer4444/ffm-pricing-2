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
        //services
        'Application\Service\PermissionService' => 'Application\Service\Factory\PermissionServiceFactory',
        'Application\Service\RolesService' => 'Application\Service\Factory\RolesServiceFactory',
        'Application\Service\UserService' => 'Application\Service\Factory\UserServiceFactory',
        'Application\Service\UserSessionService' => 'Application\Service\Factory\UserSessionServiceFactory',
    ],
];
