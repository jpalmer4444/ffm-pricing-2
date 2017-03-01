<?php

namespace User\Service;

use Application\Service\UserService;
use Exception;
use ReflectionClass;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Session\SessionManager;

/**
 * The AuthManager service is responsible for user's login/logout and simple access 
 * filtering. The access filtering feature checks whether the current visitor 
 * is allowed to see the given page or not. The AuthManager is also responsible for
 * persisting sessionId to the DB for load-balanced session management.
 */
class AuthManager {
    
    private $logger;

    /**
     * Authentication service.
     * @var AuthenticationService
     */
    private $authService;

    /**
     * Session manager.
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * UserService.
     * @var UserService
     */
    private $userService;

    /**
     * Contents of the 'access_filter' config key.
     * @var array 
     */
    private $config;

    /**
     * Constructs the service.
     */
    public function __construct(UserService $userService, AuthenticationService $authService, SessionManager $sessionManager, $config) {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->userService = $userService;
        $this->config = $config;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     */
    public function login($email, $password, $rememberMe) {
        // Check if user has already logged in. If so, do not allow to log in 
        // twice.
        if ($this->authService->getIdentity() != null) {
            throw new Exception('Already logged in');
        }

        // Authenticate with login/password.
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        // If user wants to "remember him", we will make session to expire in 
        // one month. By default session expires in 1 hour (as specified in our 
        // config/global.php file).
        if ($result->getCode() == Result::SUCCESS) {
            
            //save sessionId in DB
            $this->setSessionId($email, $this->sessionManager->getId());
            
            if($rememberMe){
                // Session cookie will expire in 1 month (30 days).
                $this->sessionManager->rememberMe(60 * 60 * 24 * 30);
            }
        }

        return $result;
    }

    /**
     * Performs user logout.
     */
    public function logout() {
        // Allow to log out only when user is logged in.
        if ($this->authService->getIdentity() == null) {
            $this->logMessage('The user is not logged in!', Logger::ERR);
        }
        
        //pass email of logged-in user and NULL to clear sessionId in DB.
        $this->setSessionId($this->authService->getIdentity(), NULL);

        // Remove identity from session.
        $this->authService->clearIdentity();
    }

    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     * 
     * This method uses RBAC from DB.
     */
    public function filterAccess($controllerName, $actionName) {
        
        if (!$this->authService->hasIdentity()) {
            return false;
        }

        $user = $this->userService->getRepository()->findOneByEmail($this->authService->getIdentity());
        
        $roles = !empty($user) ? $user->getRoles() : [];
        
        foreach ($roles as $role) {
            $permissions = $role->getPermissions();
            if (!empty($permissions)) {
                foreach ($permissions as $permission) {
                    
                    //$controllerName is Controllers shortName.
                    //checks name=user/index 
                    //cuts to slash (user) uppercases user (User) concatenates Controller 
                    //(UserController) - then compare to name of controller called.
                    if ($this->checkControllerName($permission->getName(), $controllerName)) {
                        
                        //ControllerName has matched.
                        //check if action name matches and allow if true.
                        if ($this->checkActionName($permission->getName(), $actionName)) {
                            return true;
                        }
                    }
                }
            }
        }

        // Do not permit access to this page.
        return false;
    }

    /**
     * Chops permissionValue with substr() to the first slash. Then uppercases
     * the first character then concatenates Controller, leaving us with a parseable
     * Controller shortName which is then compared to the Controller shortName passed in as
     * 2nd parameter.
     * @param type $name (Permission->name user/index)
     * @param type $controllerName (Controller shortName)
     * @return boolean
     */
    private function checkControllerName($name, $controllerName) {
        
        if (strpos(ucwords(substr($name, 0, strpos($name, '/'))) . "Controller", (new ReflectionClass($controllerName))->getShortName()) !== FALSE) {
            return true;
        }
        return false;
    }

    /**
     * Cuts permissionValue with substr() from the first slash to the end of the value 
     * which is then compared to the 2nd paramter passed in.
     * @param type $name
     * @param type $actionName
     * @return boolean
     */
    private function checkActionName($name, $actionName) {
        if (strpos(substr($name, strrpos($name, '/')), $actionName) !== FALSE) {
            return true;
        }
        return false;
    }
    
    /**
     * Email must always be non-null, but sessionId should be null
     * when logging out the User.
     * @param type $email
     * @param type $sessionId
     */
    private function setSessionId($email, $sessionId) {
        //if email is not null
        if(!empty($sessionId)){
            
            $user = $this->userService->findByEmail($email);
            $user->setSessionId($sessionId);
            $this->userService->save($user);
            
        }else if (!empty($email)){
            
            $user = $this->userService->findByEmail($email);
            $user->setSessionId($sessionId);
            $this->userService->save($user);
            
        }else {
            
            $this->logMessage("SessionId and Email passed were empty! RuntimeError! Please Fix");
            
        }
        
        
    }
    
    protected function logMessage($message, $level = Logger::INFO) {
        
        if (!$this->logger) {
            $this->logger = new Logger;
            $writer = new Stream(__DIR__ . '/../../../../data/log/error.out');
            $this->logger->addWriter($writer);
        }
        $this->logger->log($level, $message);
    }

}
