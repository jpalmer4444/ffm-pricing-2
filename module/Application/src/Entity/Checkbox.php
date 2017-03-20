<?php

namespace Application\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="item_table_checkbox")
 */
class Checkbox {
    
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
     * @ORM\JoinColumn(name="row_plus_items_page", referencedColumnName="id")
     */
    protected $rowPlusItemsPage;
    
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
    
     /**
     * @ORM\Column(name="checked", type="boolean")
     */
    protected $checked;
    
    public function getId() {
        return $this->id;
    }
    
    public function getChecked() {
        return $this->checked;
    }

    public function getCustomer() {
        return $this->customer;
    }
    
    public function getRowPlusItemsPage() {
        return $this->rowPlusItemsPage;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getSalesperson() {
        return $this->salesperson;
    }
    
    public function getProduct() {
        return $this->product;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }
    
    public function setRowPlusItemsPage($rowPlusItemsPage) {
        $this->rowPlusItemsPage = $rowPlusItemsPage;
        return $this;
    }
    
    public function setChecked($checked) {
        $this->checked = $checked;
        return $this;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
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
