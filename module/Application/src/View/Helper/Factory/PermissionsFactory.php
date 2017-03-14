<?php
namespace Application\View\Helper\Factory;

use Application\View\Helper\Permissions;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class PermissionsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authManager = $container->get('User\Service\AuthManager');
        
        // Instantiate the helper.
        return new Permissions($authManager);
    }
}

