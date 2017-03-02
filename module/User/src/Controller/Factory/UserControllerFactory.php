<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\UserController;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get('User\Service\UserManager');
        $dbAdapter = $container->get('Zend\Db\Adapter\Adapter');
        $logger = $container->get('Zend\Log\Logger');
        
        // Instantiate the controller and inject dependencies
        return new UserController($entityManager, $userManager, $dbAdapter, $logger);
    }
}