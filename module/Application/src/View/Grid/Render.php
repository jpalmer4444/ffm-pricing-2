<?php
namespace Application\View\Grid;

use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use ZfTable\Options\ModuleOptions;
use ZfTable\Render;

/**
 * @copyright Copyright (c) 2017 Jason Palmer jpalmer@meadedigital.com
 */

class Render extends Render
{
    /**
     * @var int
     */
    protected $totalItemCount;

    /**
     * Rendering filters
     *
     * @return string
     */
    public function renderFilters()
    {
        
        $headers = $this->getTable()->getHeaders();
        $render = '';

        foreach ($headers as $name => $params) {

            if (isset($params['filters'])) {
                $value = $this->getTable()->getParamAdapter()->getValueOfFilter($name);
                $id = 'zff_'.$name;

                if (is_string($params['filters'])) {

                    $element = new Text($id);

                    if ($params['filters'] == 'date') {

                        $element = new Text($id);
                        $element->setAttribute('class', 'form_datetime filter form-control');
                        $element->setAttribute('readonly', true);

                    } else {
                        $element->setAttribute('class', 'filter form-control');
                    }
                } else {
                    $element = new Select($id);
                    $element->setValueOptions($params['filters']);
                    $element->setAttribute('class', 'selectpicker filter form-control');
                    $element->setAttribute('data-container', 'body');

                    if (isset($params['search']) && $params['search']) {
                        $element->setAttribute('data-live-search', true);
                    }
                }

                $element->setValue($value);

                $item = $this->getRenderer()->formRow($element);
                if ($params['filters'] == 'date') {
                    $item =  '<label class="cursor-pointer date-picker-table">'
                        .$item.'<i class="ion-calendar"></i></label>';
                }

                $render .= sprintf('<th>%s</th>', $item);
            } else {
                $render .= '<th></th>';
            }
        }
        return sprintf('<tr class="no-padding">%s</tr>', $render);
    }

    /**
     * Rendering table
     *
     * @return string
     */
    public function renderTableAsHtml()
    {
        
        $render = '';
        /** @var ModuleOptions $tableConfig */
        $tableConfig = $this->getTable()->getOptions();

        if ($tableConfig->getShowColumnFilters()) {
            $render .= $this->renderFilters();
        }

        $render .= $this->renderHead();
        $render = sprintf('<thead>%s</thead>', $render);
        $render .= $this->getTable()->getRow()->renderRows();
        $table = sprintf('<table %s>%s</table>', $this->getTable()->getAttributes(), $render);

        $view = new ViewModel();
        $view->setTemplate('container');
        $view->setVariable('table', $table);
        $view->setVariable('totalNumber', $this->getTotalItemCount());
        $view->setVariable('paginator', $this->renderPaginator());
        $view->setVariable('paramsWrap', $this->renderParamsWrap());
        $view->setVariable('itemCountPerPage', $this->getTable()->getParamAdapter()->getItemCountPerPage());
        $view->setVariable('quickSearch', $this->getTable()->getParamAdapter()->getQuickSearch());
        $view->setVariable('name', $tableConfig->getName());
        $view->setVariable('itemCountPerPageValues', $tableConfig->getValuesOfItemPerPage());
        $view->setVariable('showQuickSearch', $tableConfig->getShowQuickSearch());
        $view->setVariable('showPagination', $tableConfig->getShowPagination());
        $view->setVariable('showItemPerPage', $tableConfig->getShowItemPerPage());
        $view->setVariable('showExportToCSV', $tableConfig->getShowExportToCSV());
        $view->setVariable('showTotalNumber', $tableConfig->getShowTotalNumber());
        $view->setVariable('typeForPagination', $tableConfig->getTypeForPagination());
        $view->setVariable('showAddButton', $tableConfig->getShowAddButton());
        $view->setVariable('date', $tableConfig->getDate());
        $view->setVariable('showDate', $tableConfig->getShowDate());
        $view->setVariable('showEndOfDayBtn', $tableConfig->getShowEndOfDayBtn());
        $view->setVariable('isDayClosed', $tableConfig->getIsDayClosed());
        $view->setVariable('showOpenMoreBtn', $tableConfig->getShowOpenMoreBtn());
        $view->setVariable('showSearchBoxBtn', $tableConfig->getShowSearchBoxBtn());
        $view->setVariable('futureItemsCount', $tableConfig->getFutureItemsCount());
        $view->setVariable('futureItemsUrl', $tableConfig->getFutureItemsUrl());
        $view->setVariable('receivingUrl', $tableConfig->getReceivingUrl());
        $view->setVariable('showDailyReportBtn', $tableConfig->getShowDailyReportBtn());
        $view->setVariable('showDeviceStatsBtn', $tableConfig->getShowDeviceStatsBtn());
        $view->setVariable('deviceStatsUrl', $tableConfig->getDeviceStatsUrl());
        $view->setVariable('showReloadItemsBtn', $tableConfig->getShowReloadItemsBtn());
        $today = (new DateTime())->format('m/d/Y');
        $showSendTrackingNrsBtn = false;
        if ($tableConfig->getShowSendTrackingNrsBtn()) {
            if (getenv('IS_PROD')) {
                if ($today == $tableConfig->getDate()) {
                    $showSendTrackingNrsBtn = true;
                }
            } else {
                $showSendTrackingNrsBtn = true;
            }
        }

        $view->setVariable('showSendTrackingNrsBtn', $showSendTrackingNrsBtn);

        return $this->getRenderer()->render($view);
    }

    /**
     * @return int
     */
    public function getTotalItemCount()
    {
        if (is_null($this->totalItemCount)) {
            /** @var Paginator $paginator */
            $paginator = $this->getTable()->getSource()->getPaginator();

            $this->totalItemCount = $paginator->getTotalItemCount();
        }

        return $this->totalItemCount;
    }

    /**
     * @param $count
     */
    public function setTotalItemCount($count)
    {
        $this->totalItemCount = $count;
    }
}
