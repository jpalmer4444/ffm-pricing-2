<?php

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

/**
 * Description of LogServiceFactory
 *
 * @author jasonpalmer
 */
class LogServiceFactory {
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $logger = new Logger;
        $writer = new Stream(__DIR__ . '/../../../../../data/log/error.log');
        $logger->addWriter($writer);
        return $logger;
    }
    
}
