<?php
namespace Application\View\Grid;
/**
 * ZfTable ( Module for Zend Framework 2)
 *
 * @copyright Copyright (c) 2017 Jason Palmer jpalmer@meadedigital.com
 */

use ZfTable\Options\ModuleOptions as ZfModuleOptions;

/**
 * Class ModuleOptions
 * @package Application\View\Grid
 */
class ModuleOptions extends ZfModuleOptions
{
    /**
     * Show or hide total number
     * @var boolean
     */
    protected $showTotalNumber = false;

    /**
     * @var string
     */
    protected $typeForPagination = 'Items';

    /**
     * @var bool
     */
    protected $showAddButton = false;

    /**
     * @var bool
     */
    protected $showDate = false;

    /**
     * @var bool
     */
    protected $showOpenMoreBtn = false;

    /**
     * @var bool
     */
    protected $showSearchBoxBtn = false;

    /**
     * @var bool
     */
    protected $date = false;

    /**
     * @var bool
     */
    protected $showReloadItemsBtn = false;

    public function __construct($options = null)
    {
        $this->templateMap = [
            'paginator-slide' =>
                __DIR__ . '/../../../view/application/zftable/templates/slide-paginator.phtml',
            'default-params' =>
                __DIR__ . '/../../../view/application/zftable/templates/default/default-params.phtml',
            'container' => __DIR__ . '/../../../view/application/zftable/templates/container-b3-new.phtml',
            'data-table-init' =>
                __DIR__ . '/../../../view/application/zftable/templates/default/data-table-init.phtml',
            'custom-b2' => __DIR__ . '/../../../view/application/zftable/templates/default/custom-b2.phtml',
            'custom-b3' => __DIR__ . '/../../../view/application/zftable/templates/default/custom-b3.phtml',
        ];

        if (null !== $options) {
            $this->setFromArray($options);
        }
    }

    /**
     * @return boolean
     */
    public function getShowTotalNumber()
    {
        return $this->showTotalNumber;
    }

    /**
     * @param boolean $showTotalNumber
     */
    public function setShowTotalNumber($showTotalNumber)
    {
        $this->showTotalNumber = $showTotalNumber;
    }

    /**
     * @return string
     */
    public function getTypeForPagination()
    {
        return $this->typeForPagination;
    }

    /**
     * @param string $typeForPagination
     */
    public function setTypeForPagination($typeForPagination)
    {
        $this->typeForPagination = $typeForPagination;
    }

    /**
     * @return boolean
     */
    public function getShowAddButton()
    {
        return $this->showAddButton;
    }

    /**
     * @param boolean $showAddButton
     */
    public function setShowAddButton($showAddButton)
    {
        $this->showAddButton = $showAddButton;
    }

    /**
     * @return boolean
     */
    public function getShowDate()
    {
        return $this->showDate;
    }

    /**
     * @param boolean $showDate
     */
    public function setShowDate($showDate)
    {
        $this->showDate = $showDate;
    }

    /**
     * @return boolean
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param boolean $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    

    /**
     * @return boolean
     */
    public function getShowSearchBoxBtn()
    {
        return $this->showSearchBoxBtn;
    }

    /**
     * @param boolean $showSearchBoxBtn
     */
    public function setShowSearchBoxBtn($showSearchBoxBtn)
    {
        $this->showSearchBoxBtn = $showSearchBoxBtn;
    }

    /**
     * @return boolean
     */
    public function getShowReloadItemsBtn()
    {
        return $this->showReloadItemsBtn;
    }

    /**
     * @param boolean $showReloadItemsBtn
     */
    public function setShowReloadItemsBtn($showReloadItemsBtn)
    {
        $this->showReloadItemsBtn = $showReloadItemsBtn;
    }
}
