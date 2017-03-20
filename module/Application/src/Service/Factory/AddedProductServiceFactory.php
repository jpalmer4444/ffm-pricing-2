<?php

namespace Application\Service\Factory;

use Application\Service\AddedProductService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of AddedProductServiceFactory
 *
 * @author jasonpalmer
 */
class AddedProductServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new AddedProductService($entityManager, $config, $logger);
    }
    
}
