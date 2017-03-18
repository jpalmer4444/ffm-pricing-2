<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Log\Logger;

class CustomerController extends BaseController
{
    
    private $logger;
    
    private $entityManager;
    
    public function __construct(EntityManager $entityManager, Logger $logger, array $config, AuthenticationService $authenticationService) {
        parent::__construct($authenticationService, $config);
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }
    
    public function indexAction() {

        $this->serveNgPage();
    }
    
    public function viewAction() {
        
        $id = (int) $this->params()->fromRoute('id', -1);
        if ($id < 1) {
            //no valid id - 404 Error.
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        

        $this->serveNgPage();
        
        
    }
    
    public function customerTableAction(){
        
        $tableRows = [];
        
        //lookup rows based on parameters.
        
        return $this->jsonResponse($tableRows);
    }

}
