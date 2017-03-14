<?php

namespace Application\Controller;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Log\Logger;

class IndexController extends BaseController
{
    
    private $entityManager;
    
    private $logger;
    
    public function __construct(
            EntityManager $entityManager, 
            Logger $logger, 
            array $config, 
            AuthenticationService $authenticationService
            ) {
        parent::__construct($authenticationService, $config);
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    
    public function indexAction()
    {
        
        $this->serveNgPage();
        
        return $this->getView();
    }
    
    /**
     * The "settings" action displays the info about currently logged in user.
     */
    public function settingsAction()
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByUsername($this->identity());
        
        if ($user==null) {
            throw new Exception('Not found user with such username');
        }
        
        return $this->getView([
            'user' => $user
        ]);
    }

}
