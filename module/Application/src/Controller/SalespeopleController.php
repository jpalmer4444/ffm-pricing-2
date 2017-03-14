<?php

namespace Application\Controller;

use Zend\Log\Logger;

class SalespeopleController extends BaseController
{
    
    private $logger;
    
    public function __construct(Logger $logger, array $config, AuthenticationService $authenticationService) {
        parent::__construct($authenticationService, $config);
        $this->logger = $logger;
    }
    
    public function indexAction()
    {
        
        return $this->getView();
    }
    
    public function datatablesAjaxAction(){
        
        $tableRows = [];
        
        //lookup rows based on parameters.
        
        return $this->jsonResponse($tableRows);
    }

}
