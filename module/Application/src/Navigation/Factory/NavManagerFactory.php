<?php

namespace Application\Navigation\Factory;

use Application\Controller\Factory\BaseFactory;
use Application\Navigation\NavManager;
use Interop\Container\ContainerInterface;

/**
 * This is the factory class for NavManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class NavManagerFactory extends BaseFactory
{
    /**
     * This method creates the NavManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $userService = $container->get('Application\Service\UserService');
        $config = $container->get('Config');
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');
        $breadcrumbs = $viewHelperManager->get('pageBreadcrumbs');
        $authManager = static::getAuthManager($container);
        
        return new NavManager($authManager, $userService, $config, $breadcrumbs, $urlHelper);
    }
}
