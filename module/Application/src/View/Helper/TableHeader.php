<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Description of TableHeader
 *
 * @author jasonpalmer
 */
class TableHeader extends AbstractHelper{
    
    /**
     <div class="table-header">
        <div class="pull-left">
            <header>
                <h4 class="pull-left">
                    // ${TABLE_NAME} ( ${TOTAL_ROWS} )
                    // Modal Popup Button Control
                    // RENDER IF ${TABLE_TITLE_CONTROL = TRUE}
                    <span data-toggle="modal" data-target="#UsersModal">
                        &nbsp;
                        <a class="ion-plus" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Add"></a>
                    </span>
                </h4>
            </header>
        </div>

        <div class="pull-right">
            <!--<div class="table-header-text info addBarcode hidden">Adding</div>-->
            <!--<div class="table-header-text info subtractBarcode hidden">Subtracting</div>-->
            //RENDER IF ${TABLE_HAS_FILTERS}
            <a title="Reset Filters" class="table-header-btn reset-filters-btn btn-default">
                Reset all filters<i class="ion-close"></i>
            </a>
            //ITEMS PER PAGE CONTROL
            <select class="itemPerPage form-control selectpicker bs-select-hidden">
                <option >
                    5
                </option>
                <option >
                    10      
                </option>
                <option selected="selected">
                    20
                </option>
                <option >
                    50
                </option>
                <option >
                    100
                </option>
            </select>
            <div class="btn-group bootstrap-select itemPerPage form-control">
                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" title="20">
                    <span class="filter-option pull-left"></span>
                    &nbsp;
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu open">
                    <ul class="dropdown-menu inner" role="menu">
                        <li data-original-index="0">
                            <a tabindex="0" class="" style="" data-tokens="null">
                                <span class="text">5</span>
                                <span class="glyphicon glyphicon-ok check-mark"></span>
                            </a>
                        </li>
                        <li data-original-index="1">
                            <a tabindex="0" class="" style="" data-tokens="null">
                                <span class="text">10</span>
                                <span class="glyphicon glyphicon-ok check-mark"></span>
                            </a>
                        </li>
                        <li data-original-index="2" class="selected">
                            <a tabindex="0" class="" style="" data-tokens="null">
                                <span class="text">20</span>
                                <span class="glyphicon glyphicon-ok check-mark"></span>
                            </a>
                        </li>
                        <li data-original-index="3">
                            <a tabindex="0" class="" style="" data-tokens="null">
                                <span class="text">50</span>
                                <span class="glyphicon glyphicon-ok check-mark"></span>
                            </a>
                        </li>
                        <li data-original-index="4">
                            <a tabindex="0" class="" style="" data-tokens="null">
                                <span class="text">100</span>
                                <span class="glyphicon glyphicon-ok check-mark"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="table-header-text">Items</div>
            <div class="dataTables_paginate paging_bootstrap pagination pull-right">
                <div class="divider"></div>
                    <ul class="pagination">
                        <li class="disabled">
                            <a data-page="">‹</a>
     *                  </li>
                        <!-- Numbered page links -->
                        <li class="active">
                            <a data-page="1">1</a>
                        </li>
                        <li>
                           <a data-page="2">2</a>
                        </li>
                        <!-- Next page link -->
                        <li>
                            <a data-page="2">›</a>
                       </li>
                    </ul>
                </div>
            </div>
        </div>
     */
    
}
