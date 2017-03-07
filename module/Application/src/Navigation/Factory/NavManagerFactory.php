<?php

namespace Application\Navigation\Factory;

use Interop\Container\ContainerInterface;
use Application\Navigation\NavManager;

/**
 * This is the factory class for NavManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class NavManagerFactory
{
    /**
     * This method creates the NavManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $authService = $container->get('Zend\Authentication\AuthenticationService');
        $userService = $container->get('Application\Service\UserService');
        $config = $container->get('Config');
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');
        $breadcrumbs = $viewHelperManager->get('pageBreadcrumbs');
        
        return new NavManager($authService, $userService, $config, $breadcrumbs, $urlHelper);
    }
}
