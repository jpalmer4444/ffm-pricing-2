<?php

namespace User\Controller\Factory;

use Application\Controller\Factory\BaseFactory;
use Interop\Container\ContainerInterface;
use User\Controller\AuthController;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory extends BaseFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authManager = $container->get('User\Service\AuthManager');
        $authService = static::getAuthenticationService($container);
        $userManager = $container->get('User\Service\UserManager');
        $logger = $container->get('Zend\Log\Logger');
        
        return new AuthController($entityManager, $authManager, $authService, $userManager, $logger);
    }
}
