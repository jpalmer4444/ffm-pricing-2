<?php

namespace Application\Service\Factory;

use Application\Service\CustomerService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of CustomerServiceFactory
 *
 * @author jasonpalmer
 */
class CustomerServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new CustomerService($entityManager, $config, $logger);
    }
    
}
