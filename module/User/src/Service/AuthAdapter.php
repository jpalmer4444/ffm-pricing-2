<?php
namespace User\Service;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\SessionManager;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns its identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 */
class AuthAdapter implements AdapterInterface
{
    
    private $useBcrypt = TRUE;
    
    /**
     * User email.
     * @var string 
     */
    private $email;
    
    /**
     * Password
     * @var string 
     */
    private $password;
    
    /**
     * Entity manager.
     * @var EntityManager 
     */
    private $entityManager;
    
    /**
     * Session manager.
     * @var SessionManager 
     */
    private $sessionManager;
        
    /**
     * Constructor.
     */
    public function __construct(EntityManager $entityManager, SessionManager $sessionManager)
    {
        $this->entityManager = $entityManager;
        $this->sessionManager = $sessionManager;
    }
    
    /**
     * Sets user email.     
     */
    public function setEmail($email) 
    {
        $this->email = $email;        
    }
    
    /**
     * Sets password.     
     */
    public function setPassword($password) 
    {
        $this->password = (string)$password;        
    }
    
    /**
     * Sets useBcrypt.     
     */
    public function setUseBcrypt($useBcrypt) 
    {
        $this->useBcrypt = $useBcrypt;        
    }
    
    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {                
        // Check the database if there is a user with such email.
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->email);
        
        // If there is no such user, return 'Identity Not Found' status.
        if ($user == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND, 
                null, 
                ['Invalid credentials.']);        
        }   
        
        // If the user with such email exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->getStatus()==User::STATUS_RETIRED) {
            return new Result(
                Result::FAILURE, 
                null, 
                ['User is retired.']);        
        }
        
        // Now we need to calculate hash based on user-entered password and compare
        // it with the password hash stored in database.
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();
        
        if (!empty($this->useBcrypt) && $bcrypt->verify($this->password, $passwordHash)) {
            // Great! The password hash matches. Return user identity (email) to be
            // saved in session for later use.
            return new Result(
                    Result::SUCCESS, 
                    $this->email, 
                    ['Authenticated successfully.']);     
            
            // 2nd case used for auto-login across servers.
        }else if(empty($this->useBcrypt)){
            
            return new Result(
                    Result::SUCCESS, 
                    $this->email, 
                    ['Authenticated successfully.']); 
        }           
        
        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(
                Result::FAILURE_CREDENTIAL_INVALID, 
                null, 
                ['Invalid credentials.']);        
    }
}


