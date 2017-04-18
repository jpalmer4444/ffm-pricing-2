<?php

namespace Application\Controller\Factory;

use Application\Controller\ProductController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of IndexControllerFactory
 *
 * @author jasonpalmer
 */
class ProductControllerFactory extends BaseFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $logger = $container->get('Zend\Log\Logger');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $restService = $container->get('Application\Service\RestService');
        $customerService = $container->get('Application\Service\CustomerService');
        $userService = $container->get('Application\Service\UserService');
        $checkboxService = $container->get('Application\Service\CheckboxService');
        $priceOverrideService = $container->get('Application\Service\PriceOverrideService');
        $sspJoin = $container->get('Application\Datatables\SSPJoin');
        $authManager = static::getAuthManager($container);
        $config = $container->get('Config');
        return new ProductController($entityManager, $logger, $config, $authManager, $sspJoin, $restService, $customerService, $userService, $checkboxService, $priceOverrideService);
    }

}
