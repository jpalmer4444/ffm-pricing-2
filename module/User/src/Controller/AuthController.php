<?php

namespace User\Controller;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Form\LoginForm;
use User\Service\AuthManager;
use User\Service\UserManager;
use Zend\Authentication\AuthenticationService;
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
     * Auth service.
     * @var AuthenticationService
     */
    private $authService;

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
    EntityManager $entityManager, AuthManager $authManager, AuthenticationService $authService, UserManager $userManager, Logger $logger
    ) {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->authService = $authService;
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

        // Create login form
        $form = new LoginForm();

        $form->get('redirect_url')->setValue($redirectUrl);

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
                $result = $this->authManager->login($data['username'], $data['password'], $data['remember_me']);

                // Check result.
                if ($result->getCode() == Result::SUCCESS) {

                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');

                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack 
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost() != null)
                            throw new Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if (empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    foreach ($result->getMessages() as $msg) {
                        $this->plugin('flashmessenger')->addMessage($msg);
                    }

                    $isLoginError = true;
                }
            } else {
                $isLoginError = true;
            }
        }

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);
    }

    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction() {
        $this->authManager->logout();

        return $this->redirect()->toRoute('login');
    }

}
