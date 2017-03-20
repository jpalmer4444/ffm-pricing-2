<?php

namespace Application\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_product_preferences")
 */
class Preferences {

    public function __construct() {
        $this->created = new DateTime();
        $this->updated = new DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;
    
    /**
     * @ORM\Id
     * @ORM\Column(name="product_id", type="integer")
     */
    protected $productId;

    /**
     * Used internally by Doctrine - Do not touch or manipulate.
     * @ORM\Column(type="integer") 
     * @ORM\Version 
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $option;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;
    
    public function getUserId() {
        return $this->userId;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getOption() {
        return $this->option;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    public function setProductId($productId) {
        $this->productId = $productId;
        return $this;
    }

    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    public function setComment($comment) {
        $this->comment = $comment;
        return $this;
    }

    public function setOption($option) {
        $this->option = $option;
        return $this;
    }

    public function setCreated($created) {
        $this->created = $created;
        return $this;
    }

    public function setUpdated($updated) {
        $this->updated = $updated;
        return $this;
    }

}
