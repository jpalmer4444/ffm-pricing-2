<?php

namespace Application\Service\Factory;

use Application\Service\CheckboxService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of CheckboxServiceFactory
 *
 * @author jasonpalmer
 */
class CheckboxServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new CheckboxService($entityManager, $config, $logger);
    }
    
}
