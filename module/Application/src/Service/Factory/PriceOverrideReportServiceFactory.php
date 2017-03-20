<?php

namespace Application\Service\Factory;

use Application\Service\PriceOverrideReportService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of PricingOverrideReportServiceFactory
 *
 * @author jasonpalmer
 */
class PriceOverrideReportServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new PriceOverrideReportService($entityManager, $config, $logger);
    }
    
}
