<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller\Factory;

use Application\Controller\CustomerController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of CustomerControllerFactory
 *
 * @author jasonpalmer
 */
class CustomerControllerFactory extends BaseFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $logger = $container->get('Zend\Log\Logger');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        $authenticationService = static::getAuthenticationService($container);
        $config = $container->get('Config');
        return new CustomerController($entityManager, $logger, $config, $authenticationService);
    }

}
