<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Datatables\Factory;

use Application\Datatables\SSPJoin;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Description of IndexControllerFactory
 *
 * @author jasonpalmer
 */
class SSPJoinFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
        $logger = $container->get('Zend\Log\Logger');
        return new SSPJoin($logger);
    }

}
