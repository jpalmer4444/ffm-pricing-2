<?php

namespace Application\Service\Factory;

use Application\Service\PermissionService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PermissionServiceFactory
 *
 * @author jasonpalmer
 */
class PermissionServiceFactory implements FactoryInterface{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $config = $container->get('Config');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $logger = $container->get('Zend\Log\Logger');
        return new PermissionService($entityManager, $config, $logger);
    }
    
}
