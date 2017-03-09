<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Log\Logger;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use function date_default_timezone_set;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {

    const VERSION = '3.0.2dev';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $config = $app->getConfig();
        if (isset($config['phpSettings']) && isset($config['phpSettings']['date.timezone'])) {
            if (isset($config['phpSettings']['date.timezone'])) {
                date_default_timezone_set($config['phpSettings']['date.timezone']);
            }
        }
        
        //setup error handler
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleError'));
        
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'handleError'));
    }
    
    public function handleError(MvcEvent $event) {
        $controller = $event->getController();
        $error = $event->getParam('error');
        $exception = $event->getParam('exception');
        $message = sprintf(' Error dispatching controller "%s". Error was: "%s"', $controller, $error);
        if ($exception instanceof \Exception) {
            $message .= ', Exception(' . $exception->getMessage() . '): ' . $exception->getTraceAsString();
        }
        $logger = $event->getApplication()->getServiceManager()->get('Zend\Log\Logger');
        $logger->log(Logger::ERR, $message);
    }

    public function getAutoloaderConfig() {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
                ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ],
                ],
            ];
    }

}
