<?php
/**
 * @copyright  Copyright (c) 2017 Fulton Inc.
 * @author     Jason Palmer <jpalmer@meadedigital.com>
 */

namespace Application\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_products")
 */
class UserProduct {
    
    public function __construct()
    {
        $this->created=new DateTime();
    }
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Customer", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="customer", referencedColumnName="id")
     */
    protected $customer;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    protected $product;
    
    /** 
     * @ORM\Column(type="integer") 
     * @ORM\Version 
     */
    protected $version;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $comment;
    
    /**
     * @ORM\Column(name="`option`", type="string", length=255, nullable=true)
     */
    protected $option;
    
    /**
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;
    
    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;
    
    public function getCustomer() {
        return $this->customer;
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

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
        return $this;
    }

    public function setProduct($product) {
        $this->product = $product;
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
