<?php

namespace Application\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="item_price_override")
 */
class PriceOverride {
    
    public function __construct()
    {
        $this->created=new DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** 
     * Used internally by Doctrine - Do not touch or manipulate.
     * @ORM\Column(type="integer") 
     * @ORM\Version 
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="Product", fetch="LAZY")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    protected $product;
    
    /**
     * @ORM\Column(type="decimal")
     */
    protected $overrideprice;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;
    
    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\ManyToOne(targetEntity="Customer", fetch="LAZY")
     * @ORM\JoinColumn(name="customer", referencedColumnName="id")
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="User", fetch="LAZY")
     * @ORM\JoinColumn(name="salesperson", referencedColumnName="id")
     */
    protected $salesperson;

    /*
     * Hydration
     */

    public function exchangeArray($data) {
        $this->overrideprice = (isset($data['overrideprice'])) ? $data['overrideprice'] : null;
    }

    // Add the following method:
    public function getArrayCopy() {
        return get_object_vars($this);
    }

    /*
     * Accessors and Mutators.
     */

    public function getId() {
        return $this->id;
    }

    public function getProduct() {
        return $this->product;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function getOverrideprice() {
        return $this->overrideprice;
    }

    public function getActive() {
        return $this->active;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getSalesperson() {
        return $this->salesperson;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
        return $this;
    }

    public function setOverrideprice($overrideprice) {
        $this->overrideprice = $overrideprice;
        return $this;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    public function setCreated($created) {
        $this->created = $created;
        return $this;
    }

    public function setSalesperson($salesperson) {
        $this->salesperson = $salesperson;
        return $this;
    }

}
