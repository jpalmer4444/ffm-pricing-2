<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Controller\Factory;

/**
 * Description of BaseFactory
 *
 * @author jasonpalmer
 */
class BaseFactory {

    public static function getAuthenticationService($container) {
        $sessionManager = $container->get('Zend\Session\SessionManager');
        if (!$sessionManager->isValid()) {
            $sessionManager->destroy();
            $sessionManager->regenerateId();
        }
        $authenticationService = $container->get('Zend\Authentication\AuthenticationService');
        return $authenticationService;
    }

}
