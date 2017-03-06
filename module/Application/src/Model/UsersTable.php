<?php

namespace Application\Model;

use ZfTable\AbstractTable;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsersTable
 *
 * @author jasonpalmer
 */
class UsersTable extends AbstractTable {

    protected $config = array(
        'name' => 'Users Table',
        'showPagination' => true,
        'showQuickSearch' => false,
        'showItemPerPage' => true,
        'showColumnFilters' => true,
    );
    //Definition of headers
    protected $headers = array(
        'id' => array(
            'tableAlias' => 'u',
            'title' => 'Id',
            'width' => '50'
        ),
        'email' => array(
            'tableAlias' => 'u',
            'title' => 'Email'
        ),
        'fullName' => array(
            'tableAlias' => 'u',
            'title' => 'Full Name'
        ),
        'dateCreated' => array(
            'tableAlias' => 'u',
            'title' => 'Date Created'
        ),
        'status' => array(
            'tableAlias' => 'u',
            'title' => 'Active'
        ),
        'actions' => array(
            'title' => 'Actions'
        ),
    );

    public function init() {

        /**
         * Render a hrefs for button clicks in table.
         */
        $this->addAttr('id', 'usersTable');
        $this->addClass('display');
    }

    protected function initFilters($query) {

        
    }

}