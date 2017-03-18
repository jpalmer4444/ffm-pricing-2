<?php
/**
 * @copyright  Copyright (c) 2017 Fulton Inc.
 * @author     Jason Palmer <jpalmer@meadedigital.com>
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="pricing_override_report")
 */
class PricingOverrideReport {
    
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
     * @ORM\ManyToOne(targetEntity="Product", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="product", referencedColumnName="id")
     */
    protected $product;
    
    /**
     * @ORM\ManyToOne(targetEntity="RowPlusItemsPage", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="row_plus_items_page_id", referencedColumnName="id")
     */
    protected $rowPlusItemsPage;
    
    /**
     * @ORM\Column(type="decimal")
     */
    protected $retail;
    
    /**
     * @ORM\Column(type="decimal")
     */
    protected $overrideprice;
    
    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="Customer", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="customerid", referencedColumnName="id")
     */
    protected $customerid;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="salesperson", referencedColumnName="id")
     */
    protected $salesperson;
    
    public function getId() {
        return $this->id;
    }

    public function getProduct() {
        return $this->product;
    }
    public function getVersion() {
        return $this->version;
    }

    public function getRowPlusItemsPage() {
        return $this->rowPlusItemsPage;
    }

    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    public function setRowPlusItemsPage($rowPlusItemsPage) {
        $this->rowPlusItemsPage = $rowPlusItemsPage;
        return $this;
    }

        public function getRetail() {
        return $this->retail;
    }
    
    public function getCustomerid() {
        return $this->customerid;
    }

    public function getOverrideprice() {
        return $this->overrideprice;
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
    
    public function setCustomer($customer) {
        $this->customerid = $customer;
        return $this;
    }
    
    public function setRetail($retail) {
        $this->retail = $retail;
        return $this;
    }

    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }

    public function setOverrideprice($overrideprice) {
        $this->overrideprice = $overrideprice;
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
