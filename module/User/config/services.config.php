<?php

use User\Service\AuthAdapter;
use User\Service\AuthManager;
use User\Service\Factory\AuthAdapterFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use User\Service\Factory\AuthManagerFactory;
use User\Service\Factory\UserManagerFactory;
use User\Service\UserManager;
use Zend\Authentication\AuthenticationService;

return [
    'abstract_factories' => [
        'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        'Zend\Log\LoggerAbstractServiceFactory',
    ],
    'factories' => [
        AuthenticationService::class => AuthenticationServiceFactory::class,
        AuthAdapter::class => AuthAdapterFactory::class,
        AuthManager::class => AuthManagerFactory::class,
        UserManager::class => UserManagerFactory::class,
    ],
];
