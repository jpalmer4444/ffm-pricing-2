<?php

namespace Application\Controller;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use Zend\Log\Logger;

class IndexController extends BaseController
{
    
    private $entityManager;
    
    private $logger;
    
    public function __construct(EntityManager $entityManager, Logger $logger) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    
    public function indexAction()
    {
        
        return $this->getView();
    }
    
    /**
     * The "settings" action displays the info about currently logged in user.
     */
    public function settingsAction()
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->identity());
        
        if ($user==null) {
            throw new Exception('Not found user with such email');
        }
        
        return $this->getView([
            'user' => $user
        ]);
    }

}
