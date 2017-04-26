<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Zend\Navigation',
    'DhErrorLogging',
    'Zend\ServiceManager\Di',
    'Zend\Cache',
    'Zend\Paginator',
    'Zend\I18n',
    'Zend\InputFilter',
    'Zend\Filter',
    'Zend\Hydrator',
    'Zend\Session',
    'Zend\Db',
    'Zend\Mvc\Plugin\Identity',
    'Zend\Mvc\Plugin\FlashMessenger',
    'Zend\Form',
    'Zend\Log',
    'Zend\Router',
    'DoctrineModule',
    'DoctrineORMModule',
    'Application',
    'User',
];
