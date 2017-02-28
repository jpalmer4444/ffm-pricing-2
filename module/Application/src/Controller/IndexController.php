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

}
