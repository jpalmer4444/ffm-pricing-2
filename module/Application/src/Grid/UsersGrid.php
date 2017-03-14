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
            'title' => 'Email', 'filters'=> 'text'
        ),
        'fullName' => array(
            'tableAlias' => 'u',
            'title' => 'Full Name'
        ),
        'dateCreated' => array(
            'tableAlias' => 'u',
            'title' => 'Date Created', 'filters'  => 'date'
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

        $actionsTDHtml = '   <div class="around-table-actions">';
        $actionsTDHtml .= '       <a class="btn btn-default btn-square btn-transparent" title="Edit User" href="users/edit/%s">';
        $actionsTDHtml .= '           <i class="ion ion-edit spin-logo"></i>';
        $actionsTDHtml .= '       </a>';
        $actionsTDHtml .= '       <a class="btn btn-default  btn-square btn-transparent" title="Change User Password" href="users/change-password/%s">';
        $actionsTDHtml .= '           <i class="ion ion-locked spin-logo"></i>';
        $actionsTDHtml .= '       </a>';
        $actionsTDHtml .= '   </div>';

        
    }

    protected function initFilters($query) {
        
        if ($value = $this->getParamAdapter()->getValueOfFilter('email')) {
            $query->where("u.email like '%".$value."%' ");
        }
        
         $creationDate = $this->getParamAdapter()->getValueOfFilter('dateCreated');
        if ($creationDate != '') {
            $creationDate = \DateTime::createFromFormat('m/d/Y', $creationDate);
            if ($creationDate) {
                $query->andWhere($query->expr()->like('u.dateCreated', '?6'))
                ->setParameter('6', '%'.$creationDate->format('Y-m-d').'%');
            }
        }
        
    }

}
