<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Exception;
use User\Entity\User;

class IndexController extends BaseController
{
    
    private $entityManager;
    
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
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
