<?php

namespace User\Service;

use Application\Entity\UserSession;
use Application\Service\UserService;
use Application\Service\UserSessionService;
use DateTime;
use ReflectionClass;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Log\Logger;
use Zend\Session\SessionManager;
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Role as ZendRole;

/**
 * The AuthManager service is responsible for user's login/logout and RBAC access 
 * filtering. The access filtering feature checks whether the current visitor 
 * is allowed to see the given page or not. The AuthManager is also responsible for
 * persisting sessionId to the DB for load-balanced session management.
 */
class AuthManager {

    /**
     * Logger service
     * @var Logger
     */
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
     * UserSessionService.
     * @var UserSessionService
     */
    private $userSessionService;

    /**
     * Contents of the 'access_filter' config key.
     * @var array 
     */
    private $config;

    /**
     * Constructs the service.
     */
    public function __construct(
    UserService $userService, UserSessionService $userSessionService, AuthenticationService $authService, SessionManager $sessionManager, Logger $logger, $config
    ) {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->userService = $userService;
        $this->userSessionService = $userSessionService;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     */
    public function login($username, $password, $rememberMe) {
        // Check if user has already logged in. If so, do not allow to log in 
        // twice.
        if ($this->authService->getIdentity() != null) {
            $this->logMessage('The user is already logged in!', Logger::INFO);
        }
        // Authenticate with login/password.
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setUsername($username);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();
        // If user wants to "remember him", we will make session to expire in 
        // one month. By default session expires in 1 hour (as specified in our 
        // config/global.php file).
        if ($result->getCode() == Result::SUCCESS) {
            //save sessionId in DB
            $this->setSessionId($username, $this->sessionManager->getId());
            if ($rememberMe) {
                // Session cookie will expire in 1 month (30 days).
                $this->sessionManager->rememberMe(60 * 60 * 24 * 30);
            } else {
                // Session cookie will expire in 1 month (1 days).
                $this->sessionManager->rememberMe(60 * 60 * 24);
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
            $this->logMessage('The user is not logged in!', Logger::INFO);
        }
        //pass username of logged-in user and NULL to clear sessionId in DB.
        $this->setSessionId($this->authService->getIdentity(), NULL);
        // Remove identity from session.
        $this->authService->clearIdentity();
    }
    
    public function getLoggedInUser(){
        if (!$this->authService->hasIdentity()) {
            return false;
        }
        return $this->userService->getRepository()->findOneByUsername($this->authService->getIdentity());
    }
    
    public function isAdmin(){
        $user = $this->getLoggedInUser();
        foreach($user->getRoles() as $role){
            $name = $role->getName();
            if(strcmp('admin', $name) == 0){
                return true;
            }
        }
        return FALSE;
    }

    public function isGranted($controllerName, $actionName, AssertionInterface $assertion = null) {
        if (!$this->authService->hasIdentity()) {
            return false;
        }
        $user = $this->userService->getRepository()->findOneByUsername($this->authService->getIdentity());
        $roles = !empty($user) ? $user->getRoles() : [];
        $rbac = new Rbac();
        $controllerReflection = new ReflectionClass($controllerName);
        $controllerTrimmed = lcfirst(str_replace("Controller", "", $controllerReflection->getShortName()));
        $permissionString = $controllerTrimmed . "/" . $actionName;
        foreach ($roles as $role) {
            $zendRole = new ZendRole($role->getName());
            $rbac->addRole($zendRole);
            $permissions = $role->getPermissions();
            if (!empty($permissions)) {
                foreach ($permissions as $permission) {
                    $zendRole->addPermission($permission->getName());
                    //we want to keep iterating if this permission does not allow
                    if (!empty($assertion) && $rbac->isGranted($role->getName(), $permissionString, $assertion)) {
                        return true;
                    } else if (empty($assertion) && $rbac->isGranted($role->getName(), $permissionString)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Username must always be non-null, but sessionId should be null
     * when logging out the User.
     * @param type $email
     * @param type $sessionId
     */
    private function setSessionId($username, $sessionId) {
        //get user agent string
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $user = $this->userService->findByUsername($username);
        //if username is not null
        if (!empty($sessionId)) {
            //check if we already have a UserSession record for this User and this Browser.
            $userSession = $this->userSessionService->getEntityManager()->getRepository(UserSession::class)
                    ->findOneBy(['userId' => $user->getId(), 'userAgent' => $userAgent]);
            if (empty($userSession)) {
                $userSession = new UserSession();
                $userSession->setUserAgent($userAgent);
                $userSession->setUserId($user->getId());
            }
            $userSession->setSessionId($sessionId);
            $user->setLastLogin(new DateTime());
            $this->userSessionService->save($userSession);
        } else if (!empty($email)) {
            $userSession = $this->userSessionService->getEntityManager()->getRepository(UserSession::class)
                    ->findOneBy(['userId' => $user->getId(), 'userAgent' => $userAgent]);
            if (!empty($userSession)) {
                $userSession->setSessionId($sessionId);
                $this->userSessionService->save($userSession);
            }
        } else {
            $this->logMessage("SessionId and Username passed were empty! RuntimeError! Please Fix");
        }
    }

    private function logMessage($message, $level = Logger::INFO) {
        $this->logger->log($level, $message);
    }

}
