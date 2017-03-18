<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered users session ids for various browsers.
 * @ORM\Entity()
 * @ORM\Table(name="user_sessions")
 */
class UserSession 
{
    // User status constants.
    
    /**
     * @ORM\Id
     * @ORM\Column(name="session_id")
     */
    protected $sessionId;

    /**
     * @ORM\Id
     * @ORM\Column(name="user_agent")
     */
    protected $userAgent;
    
    /** 
     * @ORM\Column(type="integer") 
     * @ORM\Version 
     */
    protected $version;
    
    /**
     * @ORM\Column(name="user_id")
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $userId;
    
    public function getSessionId() {
        return $this->sessionId;
    }

    public function getUserAgent() {
        return $this->userAgent;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
    }

    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

}



