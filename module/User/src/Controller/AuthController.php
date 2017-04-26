<?php

namespace User\Controller;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Form\LoginForm;
use User\Service\AuthManager;
use User\Service\UserManager;
use Zend\Authentication\Result;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Uri\Uri;
use Zend\View\Model\ViewModel;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController {

    /**
     * Entity manager.
     * @var EntityManager 
     */
    private $entityManager;

    /**
     * Auth manager.
     * @var User\Service\AuthManager 
     */
    private $authManager;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;
    private $logger;

    /**
     * Constructor.
     */
    public function __construct(
    EntityManager $entityManager, AuthManager $authManager, UserManager $userManager, Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->userManager = $userManager;
        $this->logger = $logger;
    }

    /**
     * Authenticates user given username and password credentials.     
     */
    public function loginAction() {
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string) $this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl) > 2048) {
            throw new Exception("Too long redirectUrl argument passed");
        }
        
        $loginErrorMessage = NULL;

        // Create login form
        $form = new LoginForm();

        // Store login status.
        $isLoginError = false;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if ($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Perform login attempt.
                $result = $this->authManager->login($data['username'], $data['password'], 1);

                // Check result.
                if ($result->getCode() == Result::SUCCESS) {
                    
                    $user = $this->authManager->getLoggedInUser();
                    setcookie('guid_id', $_COOKIE['PHPSESSID'], time() + 3600, '/');

                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack 
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost() != null)
                            throw new Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if (empty($redirectUrl) || strcmp('/', $redirectUrl) == 0) {
                        if ($this->authManager->isAdmin()) {
                            return $this->redirect()->toRoute('salespeople');
                        } else {
                            
                            return $this->redirect()->toRoute('customer', array('action' => 'view', 'id' => $user->getSales_attr_id));
                        }
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    foreach ($result->getMessages() as $msg) {
                        $this->plugin('flashmessenger')->addMessage($msg);
                        //$this->logger->log(Logger::INFO, "Adding Login Error: $msg");
                        $loginErrorMessage = $msg;
                    }
                    $isLoginError = true;
                    
                }
            } else {
                $isLoginError = true;
            }
        } else {
            //if we are logged-in here - we should log out the User 
            //or the page shows logged in details and is wrong. So we redirect to logout
            //taking care to preserve any redirectUrl parameter then we redirect back to this
            //page where we will no longer be logged-in and the 2nd time this test is encountered
            //it passes and we are logged-out, for page flow and logic.
            if (!empty($this->authManager->getLoggedInUser()) || !empty($this->identity())) {
                if ($redirectUrl) {
                    return $this->redirect()->toRoute('logout', [], ['query' => ['redirectUrl' => $redirectUrl]]);
                } else {
                    return $this->redirect()->toRoute('logout');
                }
            }
        }
        
        $messageFromQuery = $this->params()->fromQuery('message');
        
        if(!empty($messageFromQuery)){
            $isLoginError = true;
            $this->plugin('flashmessenger')->addMessage($messageFromQuery);
            
        }

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'loginErrorMessage' => $loginErrorMessage,
            'redirectUrl' => $redirectUrl
        ]);
    }

    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction() {
        $this->authManager->logout();
        //$redirectUrl = (string) $this->params()->fromQuery('redirectUrl', '');
        //if ($redirectUrl) {
            //return $this->redirect()->toRoute('login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
        //} else {
            //return $this->redirect()->toRoute('login');
        //}
        return $this->redirect()->toRoute('login');
    }

}
