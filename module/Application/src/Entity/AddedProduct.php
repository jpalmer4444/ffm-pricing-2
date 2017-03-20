<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="row_plus_items_page")
 */
class AddedProduct {
    
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
     * @ORM\Column(type="string", length=255)
     */
    protected $productname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $description;
    
    /**
     * @ORM\Column(type="string", length=25, columnDefinition="VARCHAR(25) DEFAULT NULL")
     */
    protected $sku;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $uom;
    
    /**
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $comment;

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
     * @ORM\ManyToOne(targetEntity="Customer", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="customer", referencedColumnName="id")
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="salesperson", referencedColumnName="id")
     */
    protected $salesperson;

    /*
     * Accessors and Mutators.
     */

    public function getId() {
        return $this->id;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getProductname() {
        return $this->productname;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getUom() {
        return $this->uom;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getComment() {
        return $this->comment;
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

    public function getCustomer() {
        return $this->customer;
    }

    public function getSalesperson() {
        return $this->salesperson;
    }
    public function getSku() {
        return $this->sku;
    }

    public function setSku($sku) {
        $this->sku = $sku;
        return $this;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    public function setProductname($productname) {
        $this->productname = $productname;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function setUom($uom) {
        $this->uom = $uom;
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setComment($comment) {
        $this->comment = $comment;
        return $this;
    }

    public function setOverrideprice($overrideprice) {
        $this->overrideprice = $overrideprice;
        return $this;
    }

    public function setActive($_active) {
        $this->active = $_active;
        return $this;
    }

    public function setCreated($_created) {
        $this->created = $_created;
        return $this;
    }

    public function setCustomer(Customer $customer) {
        $this->customer = $customer;
        return $this;
    }

    public function setSalesperson($_salesperson) {
        $this->salesperson = $_salesperson;
        return $this;
    }



}
