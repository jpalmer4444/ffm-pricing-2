<?php

namespace Application\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="products")
 */
class Product {

    public function __construct() {
        $this->created = new DateTime();
        $this->updated = new DateTime();
        $this->checkboxes = new ArrayCollection();
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
     * @ORM\Column(type="decimal")
     */
    protected $wholesale;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $uom;

    /**
     * @ORM\Column(type="string", length=25)
     */
    protected $sku;

    /**
     * @ORM\Column(type="decimal")
     */
    protected $retail;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    /**
     * @ORM\Column(name="saturdayenabled", type="boolean")
     */
    protected $saturdayenabled;
    
    /**
     * One Product has Many Checkboxes (By Customer).
     * @ORM\OneToMany(targetEntity="Checkbox", mappedBy="product")
     */
    protected $checkboxes;
    
    public function getCheckboxes() {
        return $this->checkboxes;
    }

    public function setCheckboxes($checkboxes) {
        $this->checkboxes = $checkboxes;
        return $this;
    }

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

    public function getWholesale() {
        return $this->wholesale;
    }

    public function getUom() {
        return $this->uom;
    }

    public function getSku() {
        return $this->sku;
    }

    public function getRetail() {
        return $this->retail;
    }

    public function get_created() {
        return $this->created;
    }

    public function get_updated() {
        return $this->updated;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getSaturdayenabled() {
        return $this->saturdayenabled;
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

    public function setWholesale($wholesale) {
        $this->wholesale = $wholesale;
        return $this;
    }

    public function setUom($uom) {
        $this->uom = $uom;
        return $this;
    }

    public function setSku($sku) {
        $this->sku = $sku;
        return $this;
    }

    public function setRetail($retail) {
        $this->retail = $retail;
        return $this;
    }

    public function set_created($_created) {
        $this->created = $_created;
        return $this;
    }

    public function set_updated($_updated) {
        $this->updated = $_updated;
        return $this;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setSaturdayenabled($saturdayenabled) {
        $this->saturdayenabled = $saturdayenabled;
        return $this;
    }

}
