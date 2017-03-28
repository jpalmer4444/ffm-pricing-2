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
    }
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", fetch="LAZY")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * Used internally by Doctrine - Do not touch or manipulate.
     * @ORM\Column(type="integer") 
     * @ORM\Version 
     */
    private $version;

    /**
     * @ORM\Column(type="string", name="`comment`", length=255, nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", name="`option`", length=255, nullable=true)
     */
    protected $option;
    
    public function getUser() {
        return $this->user;
    }

    public function getProduct() {
        return $this->product;
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

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function setProduct($product) {
        $this->product = $product;
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
}
