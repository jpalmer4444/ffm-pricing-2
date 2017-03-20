<?php

namespace Application\Service\Factory;

use Application\Service\ProductService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of ProductServiceFactory
 *
 * @author jasonpalmer
 */
class ProductServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new ProductService($entityManager, $config, $logger);
    }
    
}
