<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller\Factory;

use Application\Controller\SalespeopleController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of IndexControllerFactory
 *
 * @author jasonpalmer
 */
class SalespeopleControllerFactory extends BaseFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $logger = $container->get('Zend\Log\Logger');
        $entityManager = $container->get('Doctrine\ORM\EntityManager');
        
        $authenticationService = static::getAuthenticationService($container);
        $restService = $container->get('Application\Service\RestService');
        $userManager = $container->get('User\Service\UserManager');
        $userService = $container->get('Application\Service\UserService');
        $config = $container->get('Config');
        $sspJoin = $container->get('Application\Datatables\SSPJoin');
        return new SalespeopleController(
                $entityManager, 
                $logger, 
                $config, 
                $restService, 
                $authenticationService, 
                $sspJoin, 
                $userManager, 
                $userService
                );
    }

}
