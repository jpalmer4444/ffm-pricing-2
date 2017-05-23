<?php

namespace Application\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
/**
 * @copyright  Copyright (c) 2017 Fulton Inc.
 * @author     Jason Palmer <jpalmer@meadedigital.com>
 */

/** 
 * @ORM\Entity()
 * @ORM\Table(name="customers")
 */
class Customer
{
    
    public function __construct()
    {
        $this->created=new DateTime();
        $this->updated = new DateTime();
        $this->products = new ArrayCollection();
        $this->addedProducts = new ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;
    
    /** 
     * Used internally by Doctrine - Do not touch or manipulate.
     * @ORM\Column(type="integer") 
     * @ORM\Version 
     */
    protected $version;
    
    /**
     * @var ArrayCollection|Product[]
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="customers", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinTable(name="customer_product",
     *      joinColumns={@ORM\JoinColumn(name="customer", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $products;
    
    /**
     * @var ArrayCollection|AddedProduct[]
     * @ORM\OneToMany(targetEntity="AddedProduct", mappedBy="customer")
     */
    protected $addedProducts;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $company;
    
    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;
    
    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;
    
    public function getId() {
        return $this->id;
    }

    public function getVersion() {
        return $this->version;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }

    public function getCompany() {
        return $this->company;
    }
    
    public function getCreated() {
        return $this->created;
    }
    
    public function getUpdated() {
        return $this->updated;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setCompany($company) {
        $this->company = $company;
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
    
    public function getProducts() {
        return $this->products;
    }

    public function setProducts($products) {
        $this->products = $products;
        return $this;
    }

    public function getAddedProducts() {
        return $this->addedProducts;
    }

    public function setAddedProducts($addedProducts) {
        $this->addedProducts = $addedProducts;
        return $this;
    }

    /*
     * Filters
     */
    
    public function getAllProducts() {

    return array_merge($this->getProducts(), $this->getAddedProducts()); 
  }
  
  public function getActiveAddedProducts(){
      $activeAdded = [];
      foreach($this->getAddedProducts() as $added){
          if($added->getActive()){
              $activeAdded [] = $added;
          }
      }
      return $activeAdded;
  }
  
  public function getFilteredProducts($criteria) {
        
    //$criteria = Criteria::create()->where(Criteria::expr()->in("id", $ids));

    return $this->getProducts()->matching($criteria); 
  }
  
  public function getFilteredAddedProducts($criteria) {
        
    //$criteria = Criteria::create()->where(Criteria::expr()->in("id", $ids));

    return $this->getAddedProducts()->matching($criteria); 
  }
    
}
