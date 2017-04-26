<?php

return [
    'abstract_factories' => [
        'Zend\Log\LoggerAbstractServiceFactory',
    ],
    'factories' => [
        'Zend\Authentication\AuthenticationService' => 'User\Service\Factory\AuthenticationServiceFactory',
        'User\Service\AuthAdapter' => 'User\Service\Factory\AuthAdapterFactory',
        'User\Service\AuthManager' => 'User\Service\Factory\AuthManagerFactory',
        'User\Service\UserManager' => 'User\Service\Factory\UserManagerFactory',
        'Zend\Log\Logger' => 'User\Service\Factory\LogServiceFactory',
    ],
];
