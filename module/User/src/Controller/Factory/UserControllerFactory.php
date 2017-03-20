<?php
namespace User\Controller\Factory;

use Application\Controller\Factory\BaseFactory;
use Interop\Container\ContainerInterface;
use User\Controller\UserController;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory extends BaseFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get('User\Service\UserManager');
        $authManager = static::getAuthManager($container);
        $dbAdapter = $container->get('Zend\Db\Adapter\Adapter');
        $logger = $container->get('Zend\Log\Logger');
        $config = $container->get('Config');
        $sspJoin = $container->get('Application\Datatables\SSPJoin');
        
        // Instantiate the controller and inject dependencies
        return new UserController($entityManager, $userManager, $authManager, $dbAdapter, $logger, $config, $sspJoin);
    }
}