<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\AuthManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory class for AuthManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class AuthManagerFactory implements FactoryInterface
{
    /**
     * This method creates the AuthManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        // Instantiate dependencies.
        $authenticationService = $container->get('Zend\Authentication\AuthenticationService');
        $sessionManager = $container->get('Zend\Session\SessionManager');
        $userService = $container->get('Application\Service\UserService');
        $userSessionService = $container->get('Application\Service\UserSessionService');
        $logger = $container->get('Zend\Log\Logger');
        
        // Get contents of 'access_filter' config key (the AuthManager service
        // will use this data to determine whether to allow currently logged in user
        // to execute the controller action or not.
        $config = $container->get('Config');
        if (isset($config['access_filter']))
            $config = $config['access_filter'];
        else
            $config = [];
                        
        // Instantiate the AuthManager service and inject dependencies to its constructor.
        return new AuthManager($userService, $userSessionService, $authenticationService, $sessionManager, $logger, $config);
    }
}
