<?php

namespace Application\Controller;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Service\AuthManager;
use Zend\Log\Logger;

class IndexController extends BaseController
{
    
    private $entityManager;
    
    private $logger;
    
    public function __construct(
            EntityManager $entityManager, 
            Logger $logger, 
            array $config, 
            AuthManager $authManager
            ) {
        parent::__construct($authManager, $config);
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    
    public function indexAction()
    {
        
        $user = $this->authManager->getLoggedInUser();
        
        $isAdmin = $this->authManager->isAdmin();
        
        $sales_attr_id = $user->getSales_attr_id();
        
        if(!empty($sales_attr_id) && !$isAdmin){
            return $this->redirect()->toRoute('customer', ['action' => 'view', 'id' => $sales_attr_id]);
        }else{
            return $this->redirect()->toRoute('salespeople', ['action' => 'index']);
        }
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
