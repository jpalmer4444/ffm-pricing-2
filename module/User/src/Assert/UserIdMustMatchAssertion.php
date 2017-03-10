<?php

namespace User\Assert;

use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

/**
 * This class is responsible for checking if a User either matches the passed in pageId (== user->getId()).
 */
class UserIdMustMatchAssertion implements AssertionInterface {

    protected $userId;
    protected $roles;
    protected $pageId;

    public function __construct($user) {
        if ($user) {
            $this->userId = $user->getId();
        }
        if ($user) {
            $this->roles = $user->getRoles();
        }
    }

    public function setPageId($pageId) {
        $this->pageId = $pageId;
    }

    public function assert(Rbac $rbac) {
        if (! $this->pageId || ! $this->userId) {
            return false;
        }

        //if user is admin - allow edit.
        if ($this->atLeastOneRoleAdmin()) {
            return true;
        }

        return ($this->userId === $this->pageId);
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
