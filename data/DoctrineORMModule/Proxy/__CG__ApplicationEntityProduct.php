<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Product extends \Application\Entity\Product implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'id', '' . "\0" . 'Application\\Entity\\Product' . "\0" . 'version', 'productname', 'description', '' . "\0" . 'Application\\Entity\\Product' . "\0" . 'qty', 'wholesale', 'uom', 'sku', 'retail', 'created', 'updated', 'status', 'saturdayenabled', 'checkboxes'];
        }

        return ['__isInitialized__', 'id', '' . "\0" . 'Application\\Entity\\Product' . "\0" . 'version', 'productname', 'description', '' . "\0" . 'Application\\Entity\\Product' . "\0" . 'qty', 'wholesale', 'uom', 'sku', 'retail', 'created', 'updated', 'status', 'saturdayenabled', 'checkboxes'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Product $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getCheckboxes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCheckboxes', []);

        return parent::getCheckboxes();
    }

    /**
     * {@inheritDoc}
     */
    public function setCheckboxes($checkboxes)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCheckboxes', [$checkboxes]);

        return parent::setCheckboxes($checkboxes);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getVersion', []);

        return parent::getVersion();
    }

    /**
     * {@inheritDoc}
     */
    public function getProductname()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProductname', []);

        return parent::getProductname();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', []);

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function getQty()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getQty', []);

        return parent::getQty();
    }

    /**
     * {@inheritDoc}
     */
    public function getWholesale()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getWholesale', []);

        return parent::getWholesale();
    }

    /**
     * {@inheritDoc}
     */
    public function getUom()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUom', []);

        return parent::getUom();
    }

    /**
     * {@inheritDoc}
     */
    public function getSku()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSku', []);

        return parent::getSku();
    }

    /**
     * {@inheritDoc}
     */
    public function getRetail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRetail', []);

        return parent::getRetail();
    }

    /**
     * {@inheritDoc}
     */
    public function get_created()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'get_created', []);

        return parent::get_created();
    }

    /**
     * {@inheritDoc}
     */
    public function get_updated()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'get_updated', []);

        return parent::get_updated();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function getSaturdayenabled()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSaturdayenabled', []);

        return parent::getSaturdayenabled();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function setVersion($version)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setVersion', [$version]);

        return parent::setVersion($version);
    }

    /**
     * {@inheritDoc}
     */
    public function setProductname($productname)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProductname', [$productname]);

        return parent::setProductname($productname);
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($description)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDescription', [$description]);

        return parent::setDescription($description);
    }

    /**
     * {@inheritDoc}
     */
    public function setQty($qty)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setQty', [$qty]);

        return parent::setQty($qty);
    }

    /**
     * {@inheritDoc}
     */
    public function setWholesale($wholesale)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setWholesale', [$wholesale]);

        return parent::setWholesale($wholesale);
    }

    /**
     * {@inheritDoc}
     */
    public function setUom($uom)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUom', [$uom]);

        return parent::setUom($uom);
    }

    /**
     * {@inheritDoc}
     */
    public function setSku($sku)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSku', [$sku]);

        return parent::setSku($sku);
    }

    /**
     * {@inheritDoc}
     */
    public function setRetail($retail)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRetail', [$retail]);

        return parent::setRetail($retail);
    }

    /**
     * {@inheritDoc}
     */
    public function set_created($_created)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'set_created', [$_created]);

        return parent::set_created($_created);
    }

    /**
     * {@inheritDoc}
     */
    public function set_updated($_updated)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'set_updated', [$_updated]);

        return parent::set_updated($_updated);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function setSaturdayenabled($saturdayenabled)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSaturdayenabled', [$saturdayenabled]);

        return parent::setSaturdayenabled($saturdayenabled);
    }

}
