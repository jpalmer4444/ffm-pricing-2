<?php

namespace Application\Controller;

class IndexController extends BaseController
{
    
    public function __construct() {
        
    }
    
    public function indexAction()
    {
        
        return $this->getView();
    }
    
    /**
     * This is the "about" action. It is used to display the "About" page.
     */
    public function aboutAction() 
    {              
        $appName = 'User Demo';
        $appDescription = 'This demo shows how to implement user management with Zend Framework 3';
        
        // Return variables to view script with the help of
        // ViewObject variable container
        return $this->getView([
            'appName' => $appName,
            'appDescription' => $appDescription
        ]);
    }  
    
    /**
     * The "settings" action displays the info about currently logged in user.
     */
    public function settingsAction()
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->identity());
        
        if ($user==null) {
            throw new \Exception('Not found user with such email');
        }
        
        return $this->getView([
            'user' => $user
        ]);
    }

}
