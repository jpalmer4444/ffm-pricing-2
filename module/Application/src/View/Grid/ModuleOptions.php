<?php
/**
 * ZfTable ( Module for Zend Framework 2)
 *
 * @copyright Copyright (c) 2013 Piotr Duda dudapiotrek@gmail.com
 * @license   MIT License
 */

namespace Application\View\Grid;

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
    protected $showEndOfDayBtn = false;

    /**
     * @var bool
     */
    protected $isDayClosed = false;

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
     * @var bool|int
     */
    protected $futureItemsCount = false;

    /**
     * @var bool|string
     */
    protected $futureItemsUrl = false;

    /**
     * @var bool|string
     */
    protected $receivingUrl = false;

    /**
     * @var bool
     */
    protected $showSendTrackingNrsBtn = false;

    /**
     * @var bool
     */
    protected $showDeviceStatsBtn = false;

    /**
     * @var bool|string
     */
    protected $deviceStatsUrl = false;

    /**
     * @var bool
     */
    protected $showDailyReportBtn = false;

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
    public function getShowEndOfDayBtn()
    {
        return $this->showEndOfDayBtn;
    }

    /**
     * @param boolean $showEndOfDayBtn
     */
    public function setShowEndOfDayBtn($showEndOfDayBtn)
    {
        $this->showEndOfDayBtn = $showEndOfDayBtn;
    }

    /**
     * @return boolean
     */
    public function getIsDayClosed()
    {
        return $this->isDayClosed;
    }

    /**
     * @param boolean $isDayClosed
     */
    public function setIsDayClosed($isDayClosed)
    {
        $this->isDayClosed = $isDayClosed;
    }

    /**
     * @return boolean
     */
    public function getShowOpenMoreBtn()
    {
        return $this->showOpenMoreBtn;
    }

    /**
     * @param boolean $showOpenMoreBtn
     */
    public function setShowOpenMoreBtn($showOpenMoreBtn)
    {
        $this->showOpenMoreBtn = $showOpenMoreBtn;
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
     * @return bool|int
     */
    public function getFutureItemsCount()
    {
        return $this->futureItemsCount;
    }

    /**
     * @param bool|int $futureItemsCount
     */
    public function setFutureItemsCount($futureItemsCount)
    {
        $this->futureItemsCount = $futureItemsCount;
    }

    /**
     * @return bool|string
     */
    public function getFutureItemsUrl()
    {
        return $this->futureItemsUrl;
    }

    /**
     * @param bool|string $futureItemsUrl
     */
    public function setFutureItemsUrl($futureItemsUrl)
    {
        $this->futureItemsUrl = $futureItemsUrl;
    }

    /**
     * @return bool|string
     */
    public function getReceivingUrl()
    {
        return $this->receivingUrl;
    }

    /**
     * @param bool|string $receivingUrl
     */
    public function setReceivingUrl($receivingUrl)
    {
        $this->receivingUrl = $receivingUrl;
    }

    /**
     * @return boolean
     */
    public function getShowSendTrackingNrsBtn()
    {
        return $this->showSendTrackingNrsBtn;
    }

    /**
     * @param boolean $showSendTrackingNrsBtn
     */
    public function setShowSendTrackingNrsBtn($showSendTrackingNrsBtn)
    {
        $this->showSendTrackingNrsBtn = $showSendTrackingNrsBtn;
    }

    /**
     * @return boolean
     */
    public function getShowDeviceStatsBtn()
    {
        return $this->showDeviceStatsBtn;
    }

    /**
     * @param boolean $showDeviceStatsBtn
     */
    public function setShowDeviceStatsBtn($showDeviceStatsBtn)
    {
        $this->showDeviceStatsBtn = $showDeviceStatsBtn;
    }

    /**
     * @return boolean
     */
    public function getDeviceStatsUrl()
    {
        return $this->deviceStatsUrl;
    }

    /**
     * @param boolean $deviceStatsUrl
     */
    public function setDeviceStatsUrl($deviceStatsUrl)
    {
        $this->deviceStatsUrl = $deviceStatsUrl;
    }

    /**
     * @return boolean
     */
    public function getShowDailyReportBtn()
    {
        return $this->showDailyReportBtn;
    }

    /**
     * @param boolean $showDailyReportBtn
     */
    public function setShowDailyReportBtn($showDailyReportBtn)
    {
        $this->showDailyReportBtn = $showDailyReportBtn;
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