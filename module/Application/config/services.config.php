<?php

use Application\Service\Factory\NavManagerFactory;
use Application\Service\NavManager;

return [
    'aliases' => [
    ],
    'abstract_factories' => [
        'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        'Zend\Log\LoggerAbstractServiceFactory',
        'Zend\Navigation\Service\NavigationAbstractServiceFactory'
    ],
    'factories' => [
        'Zend\Validator\Translator\TranslatorInterface' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        'UserService' => 'Application\Service\Factory\UserServiceFactory',
        // Configures the default SessionManager instance
        'Zend\Session\ManagerInterface' => 'Zend\Session\Service\SessionManagerFactory',
        // Provides session configuration to SessionManagerFactory
        'Zend\Session\Config\ConfigInterface' => 'Zend\Session\Service\SessionConfigFactory',
        NavManager::class => NavManagerFactory::class,
    ],
];
