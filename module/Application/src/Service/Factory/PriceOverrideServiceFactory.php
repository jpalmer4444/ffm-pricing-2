<?php

namespace Application\Service\Factory;

use Application\Service\PriceOverrideService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of PriceOverrideServiceFactory
 *
 * @author jasonpalmer
 */
class PriceOverrideServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new PriceOverrideService($entityManager, $config, $logger);
    }
    
}
