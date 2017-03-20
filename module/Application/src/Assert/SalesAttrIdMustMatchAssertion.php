<?php

namespace Application\Assert;

use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

/**
 * This class is responsible for checking if a User either matches the passed in pageId (== user->getSales_attr_id()).
 */
class SalesAttrIdMustMatchAssertion implements AssertionInterface {

    protected $sales_attr_id;
    protected $roles;
    protected $pageId;

    public function __construct($user) {
        if ($user) {
            $this->sales_attr_id = $user->getSales_attr_id();
        }
        if ($user) {
            $this->roles = $user->getRoles();
        }
    }

    public function setPageId($pageId) {
        $this->pageId = $pageId;
    }

    public function assert(Rbac $rbac) {
        if (! $this->pageId) {
            return false;
        }

        //if user is admin - allow edit.
        if ($this->atLeastOneRoleAdmin()) {
            return true;
        }
        
        if (! $this->sales_attr_id) {
            return false;
        }

        return ($this->sales_attr_id === $this->pageId);
    }

    private function atLeastOneRoleAdmin() {
        foreach ($this->roles as $role) {
            if (strcmp($role->getName(), "admin") == 0) {
                return true;
            }
        }
        return false;
    }

}
