<?php

namespace User\Session;

use Zend\Session\Validator\HttpUserAgent;

/**
 * Description of BalancedHttpUserAgent
 *
 * @author jasonpalmer
 */
class BalancedHttpUserAgent extends HttpUserAgent {

    public function isValid() {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        $same = strcmp($userAgent, $this->getData()) == 0;
        if (empty($same)) {
            //log here
        }
        return $same;
    }

}
