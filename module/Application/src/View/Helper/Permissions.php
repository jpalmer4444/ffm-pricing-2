<?php

namespace Application\View\Helper;

use User\Service\AuthManager;
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Description of Permissions
 *
 * @author jasonpalmer
 */
class Permissions extends AbstractHelper{
    
    protected $authManager;
    
    public function __construct(AuthManager $authManager) {
        $this->authManager = $authManager;
    }
    
    public function isGranted($controllerName, $actionName, AssertionInterface $assertion = null) {
        return $this->authManager->isGranted($controllerName, $actionName, $assertion);
    }
    
}
