<?php

namespace Application\Controller;

use Application\Datatables\Server;
use Doctrine\ORM\EntityManager;
use User\Service\AuthManager;
use Zend\Log\Logger;

class ProductController extends BaseController
{
    
    private $logger;
    
    private $entityManager;
    
    public function __construct(
            EntityManager $entityManager, 
            Logger $logger, 
            array $config, 
            AuthManager $authManager) {
        parent::__construct($authManager, $config);
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }
    
    public function indexAction() {

        $this->serveNgPage();
    }
    
    public function productTableAction(){
        
        $jsonData = json_decode($this->params()->fromPost('jsonData'));

        $table = 'users';

        $primaryKey = 'id';

        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'username', 'dt' => 1),
            array('db' => 'email', 'dt' => 2),
            array('db' => 'status', 'dt' => 3),
            array('db' => 'full_name', 'dt' => 4),
            array(
                'db' => 'date_created',
                'dt' => 5,
                'formatter' => function( $d, $row ) {
                    return date('m/d/Y', strtotime($d));
                }
            ),
            array(
                'db' => 'last_login',
                'dt' => 6,
                'formatter' => function( $d, $row ) {
                    return date('m/d/Y', strtotime($d));
                }
            ),
            array('db' => 'id', 'dt' => 7),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => $this->config['doctrine']['connection']['orm_default']['params']['user'],
            'pass' => $this->config['doctrine']['connection']['orm_default']['params']['password'],
            'db' => $this->config['doctrine']['connection']['orm_default']['params']['dbname'],
            'host' => $this->config['doctrine']['connection']['orm_default']['params']['host']
        );

        $jsonArgs = Server::buildArrayFromJson($jsonData);

        //merge username
        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'username'), $this->params()->fromQuery('zff_username')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'email'), $this->params()->fromQuery('zff_email')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'full_name'), $this->params()->fromQuery('zff_fullname')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'date_created'), $this->params()->fromQuery('zff_createddate')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'last_login'), $this->params()->fromQuery('zff_lastlogindate')
        );

        $zff_status = $this->params()->fromQuery('zff_status');

        if ($zff_status == 1 || $zff_status == 0 || $zff_status == '1' || $zff_status == '0') {

            $this->sspJoin->setColumnSearchValue(
                    $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'status'), $zff_status
            );
        }

        $zff_length = $this->params()->fromQuery('zff_length');

        if (!empty($zff_length)) {

            $jsonArgs['length'] = $zff_length;

            //check page now
            $zff_page = $this->params()->fromQuery('zff_page');

            if (empty($zff_page)) {
                $zff_page = 1;
            }

            $zff_page--; //make zero based

            $jsonArgs['start'] = $zff_page ? ($zff_page * $zff_length) : ($zff_page);
        }

        $this->sspJoin->reset();
        $this->sspJoin->setLogger($this->logger);
        
        $response = $this->sspJoin->simple($jsonArgs, $sql_details, $table, $primaryKey, $columns);
        
        return $this->jsonResponse(
                        $response
        );
    }

}
