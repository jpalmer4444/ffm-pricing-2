<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Log\Logger;

class ProductController extends BaseController
{
    
    private $logger;
    
    private $entityManager;
    
    public function __construct(
            EntityManager $entityManager, 
            Logger $logger, 
            array $config, 
            AuthenticationService $authenticationService) {
        parent::__construct($authenticationService, $config);
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }
    
    public function indexAction() {

        $this->serveNgPage();
    }
    
    public function productTableAction(){
        
        $tableRows = [];
        
        //lookup rows based on parameters.
        
        return $this->jsonResponse($tableRows);
    }

}
