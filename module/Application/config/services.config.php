<?php

return [
    'aliases' => [
    ],
    'abstract_factories' => [
        'Zend\Log\LoggerAbstractServiceFactory',
    ],
    'factories' => [
        'Zend\Validator\Translator\TranslatorInterface' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        'Application\Service\NavManager' => 'Application\Service\Factory\NavManagerFactory',
    ],
];
