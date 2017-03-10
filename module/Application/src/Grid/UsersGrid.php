<?php

namespace Application\Grid;

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
class UsersGrid extends AbstractGrid {

    /**
     * @var bool
     */
    protected $canDelete;

    /**
     * config
     * @var array
     */
    protected $config = array(
        'name' => 'Users',
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
        'username' => [
            'tableAlias' => 'u',
            'title' => 'Actions',
        ],
    );

    public function init() {
        
        //file_put_contents('/u/local/jasonpalmer/ffm-skeleton-app/data/log/error.log', "RenderasHtml"."\n", FILE_APPEND);

        /**
         * Render a hrefs for button clicks in table.
         */
        $this->addAttr('id', 'usersTable');

        $this->addClass('display');

        if (empty($this->headers)) {
            $this->setHeaders($this->headers);
        }

        /*
        $this->getHeader('username')->getCell()->addDecorator('closure', array(
            'closure' => function($context, $record) {
                if (is_object($record)) {
                    $html = '   <div class="around-table-actions">';
                    $html .= '       <a class="btn btn-default btn-square btn-transparent" title="Edit User" href="users/edit/%s">';
                    $html .= '           <i class="ion ion-edit spin-logo"></i>';
                    $html .= '       </a>';
                    $html .= '       <a class="btn btn-default  btn-square btn-transparent" title="Change User Password" href="users/change-password/%s">';
                    $html .= '           <i class="ion ion-locked spin-logo"></i>';
                    $html .= '       </a>';
                    $html .= '   </div>';
                    return sprintf($html, $record->getId(), $record->getId());
                } else {
                    return '';
                }
            }
        ));

        $actionsTDHtml = '   <div class="around-table-actions">';
        $actionsTDHtml .= '       <a class="btn btn-default btn-square btn-transparent" title="Edit User" href="users/edit/%s">';
        $actionsTDHtml .= '           <i class="ion ion-edit spin-logo"></i>';
        $actionsTDHtml .= '       </a>';
        $actionsTDHtml .= '       <a class="btn btn-default  btn-square btn-transparent" title="Change User Password" href="users/change-password/%s">';
        $actionsTDHtml .= '           <i class="ion ion-locked spin-logo"></i>';
        $actionsTDHtml .= '       </a>';
        $actionsTDHtml .= '   </div>';

        $this->getHeader('username')->getCell()->addDecorator('template', array(
            'template' => $actionsTDHtml,
            'vars' => array('id')
        ));
         */
    }

    protected function initFilters($query) {
        
    }

}
