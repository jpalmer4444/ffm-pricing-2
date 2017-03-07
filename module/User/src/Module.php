<?php

namespace User;

use Application\Controller\IndexController;
use Application\Entity\UserSession;
use User\Controller\AuthController;
use User\Service\AuthManager;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
class Module {

    /**
     * This method returns the path to module.config.php file.
     */
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners. 
     */
    public function onBootstrap(MvcEvent $event) {
        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method. 
        $sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user 
     * to the login page.
     */
    public function onDispatch(MvcEvent $event) {

        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();

        //Get Controller name
        $controllerName = $event->getRouteMatch()->getParam('controller', null);

        //Get raw actionName
        $actionNameDashed = $event->getRouteMatch()->getParam('action', null);

        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionNameDashed, '-')));

        // Get the instance of AuthManager service.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        // Get the instance of logger.
        $logger = $event->getApplication()->getServiceManager()->get('Zend\Log\Logger');

        // need to login user by sessionId when AuthService has no identity. Which happens
        // in a load-balanced environment when subsequent requests go to a different server.
        $authenticationService = $event->getApplication()->
                        getServiceManager()->get('Zend\Authentication\AuthenticationService');

        // Get the instance of UserService.
        $userService = $event->getApplication()->
                        getServiceManager()->get('Application\Service\UserService');
        
        // Get the instance of UserSessionService.
        $userSessionService = $event->getApplication()->
                        getServiceManager()->get('Application\Service\UserSessionService');

        // Get the instance of SessionManager.
        $sessionManager = $event->getApplication()->
                        getServiceManager()->get('Zend\Session\SessionManager');

        // check if $authenticationService has a logged-in user.
        // this is where we handle authentication across requests
        // in a multiple server environment. Normally PHP will serialize
        // any Session objects and store the result in a text file on the 
        // server keyed by SESSION_ID, but this obviously breaks down when 
        // the application runs in a multiple server environment. This is where
        // we deal with that problem. 
        // When we get to this point and there is no logged-in user - it might be 
        // because the user has not logged in or it might be the user has logged in
        // we have no real way of knowing at this point - so we check the SESSION_ID
        // on the users Browser by way of PHPs PHPSESSION cookie. If the cookie exists
        // then we look in the DB for a user that belongs to that cookie. If we find
        // that user - we log them in with balanceLogin method and then we let the Request
        // complete.
        if (empty($authenticationService->getIdentity())) {
            //attempt to lookup User by sessionId.
            $sessionId = $sessionManager->getId();
            $this->balanceLogin($sessionId, $userService, $userSessionService, $authenticationService, $logger);
        } else {
            $this->log(Logger::INFO, "Authentication Service has logged-in user.", $logger);
        }

        // Execute the access filter on every controller except AuthController
        // and IndexController/index and IndexController/about
        // (to avoid infinite redirect).
        // Check to see if we need to bypass this Request because it is not for a 
        // protected resource.
        // check if this request is for index/index
        $bypass = ($controllerName == IndexController::class && ($actionName == 'index')) ||
                // or this request is for AuthController.
                strcmp($controllerName, AuthController::class) == 0;

        if (!$bypass && !$authManager->filterAccess($controllerName, $actionName)) {

            // Remember the URL of the page the user tried to access. We will
            // redirect the user to that URL after successful login.
            $uri = $event->getApplication()->getRequest()->getUri();
            // Make the URL relative (remove scheme, user info, host name and port)
            // to avoid redirecting to other domain by a malicious user.
            $uri->setScheme(null)
                    ->setHost(null)
                    ->setPort(null)
                    ->setUserInfo(null);
            $redirectUrl = $uri->toString();

            // Redirect the user to the "Login" page.
            return $controller->redirect()->toRoute('login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
        }
    }

    private function balanceLogin($sessionId, $userService, $userSessionService, $authenticationService, $logger) {
        if (!empty($sessionId)) {
            $userSession = $userSessionService->getEntityManager()->getRepository(UserSession::class)
                ->findOneBy(['sessionId' =>$sessionId]);
            if(!empty($userSession)){
                $user = $userService->find($userSession->getUserId());
            }else{
                $this->log(Logger::ERR, "UserSession not found by session id", $logger);
            }
            if (!empty($user)) {
                // Authenticate with login/password.
                $authAdapter = $authenticationService->getAdapter();
                $authAdapter->setEmail($user->getEmail());
                $authAdapter->setPassword($user->getPassword());
                $authAdapter->setUseBcrypt(FALSE);
                $authenticationService->authenticate();
            }
        } else {
            $this->log(Logger::ERR, "Session Id empty", $logger);
        }
    }

    private function log($level, $message, $logger) {
        $logger->log($level, '(User\Module.php): ' . $message);
    }

}
