<?php

namespace User\Session;

use Zend\Session\Validator\RemoteAddr;

/**
 * Description of BalancedRemoteAddr
 *
 * @author jasonpalmer
 */
class BalancedRemoteAddr extends RemoteAddr{
    
    const METADATA_ELB = "(ELB)";
    const METADATA_REMOTE_ADDR = "(REMOTE_ADDR)";
    
    protected function getIpAddress() {
        //Just get the headers if we can or else use the SERVER global
        if ( function_exists( 'apache_request_headers' ) ) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }
        //Get the forwarded IP if it exists
        if ( array_key_exists( 'X-Forwarded-For', $headers ) && 
                filter_var( $headers['X-Forwarded-For'], 
                        FILTER_VALIDATE_IP, 
                        FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && 
                filter_var( $headers['HTTP_X_FORWARDED_FOR'], 
                        FILTER_VALIDATE_IP, 
                        FILTER_FLAG_IPV4 )
        ) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {
            if(array_key_exists('REMOTE_ADDR', $_SERVER)){
                $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], 
                    FILTER_VALIDATE_IP, 
                    FILTER_FLAG_IPV4 );
            }else{
                $the_ip = '';
            }
            
        }
        return $the_ip;
    }

    public function isValid()
    {
        return ($this->getIpAddress() === $this->getData());
    }
    
    private function getServer($key){
        return isset($_SERVER[$key])
                   ? $_SERVER[$key]
                   : null;
    }

    
}
