<?php

namespace Application\Controller;

use Application\Datatables\Server;
use Application\Datatables\SSPJoin;
use Application\Entity\User;
use Application\Form\SalespersonForm;
use Application\Service\RestService;
use Application\Service\UserService;
use Doctrine\ORM\EntityManager;
use User\Service\AuthManager;
use User\Service\UserManager;
use Zend\Log\Logger;

class SalespeopleController extends BaseController {

    /**
     *
     * @var Logger $logger
     */
    private $logger;
    
    /**
     *
     * @var EntityManager $entityManager
     */
    private $entityManager;
    
    /**
     *
     * @var RestService
     */
    private $restService;
    
    /**
     *
     * @var SSPJoin
     */
    private $sspJoin;
    
    /**
     *
     * @var User\Service\UserManager
     */
    private $userManager;
    
    /**
     *
     * @var Application\Server\UserService
     */
    private $userService;

    public function __construct(
        EntityManager $entityManager, 
            Logger $logger, 
            array $config, 
            RestService $restService, 
            AuthManager $authManager, 
            SSPJoin $sspJoin, 
            UserManager $userManager, 
            UserService $userService
    ) {

        parent::__construct($authManager, $config);

        $this->logger = $logger;

        $this->entityManager = $entityManager;

        $this->restService = $restService;

        $this->sspJoin = $sspJoin;

        $this->userManager = $userManager;

        $this->userService = $userService;
    }

    /**
     * Serves base salespeople page. (Angular Page)
     */
    public function indexAction() {

        //setup angular dependencies
        $this->serveNgPage();
    }

    /**
     * 
     * @return XMLHttpRequest (JSON)
     */
    public function addAction() {

        if ($this->getRequest()->isXmlHttpRequest()) {
            
            $pw = $this->params()->fromPost('password');
            
            $pwv = $this->params()->fromPost('password_verify');
            
            //$pw $pwv
            $passwordRequired = !empty($pw) && !empty($pwv) && (strcmp($pw, $pwv) == 0);

            $form = new SalespersonForm($passwordRequired, $this->params()->fromPost('scenario'), $this->entityManager, $this->authManager->getLoggedInUser());

            if ($this->getRequest()->isPost()) {
                
                $form->setData($this->params()->fromPost());

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    //either save or update based on scenario.
                    if ($this->params()->fromPost('scenario') == 'create') {
                        // Add user.
                        //add role to data to create and associate the role
                        $data['role'] = $this->params()->fromPost('role');
                        //pass along non-form data salesAttrId.
                        $data['salesAttrId'] = $this->params()->fromPost('salesAttrId');
                        //$this->logger->log(Logger::INFO, $data);
                        $user = $this->userManager->addUser($data);
                        
                    } else {
                        // Edit user.
                        $user = $this->userService->find($this->params()->fromPost('id'));
                        if (!empty($user)) {
                            $this->userManager->updateUser($user, $data);
                            if ($this->isDebug()) {
                                $this->logger->log(Logger::INFO, "User Updated");
                            }
                        } else {
                            if (empty($data['id'])) {
                                $this->log("UpdateUser is scenario, but no ID was passed in \$data");
                            } else {
                                $this->log("UpdateUser is scenario, but the ID: " . $data['id'] . " did not match any users");
                            }
                        }
                    }
                    return $this->jsonResponse(['success' => true]);
                } else {
                    $msgs = [];
                    $this->log("User Data: " . $this->my_print_r($data));
                    foreach ($form->getMessages() as $msg) {
                        $this->log("Form Invalid - Message: " . $this->my_print_r($msg));
                        $msgs [] = $this->my_print_r($msg);
                    }
                    $this->log("salespeople/add form not valid.");
                    return $this->jsonResponse(['success' => false, 'messages' => $msgs]);
                }
            } else {
                $this->log("salespeople/add called with AJAX - BUT it was NOT a POST and therefore ignored.");
                return $this->jsonResponse(['success' => false, 'messages' => ['Request was not a POST Request']]);
            }
        } else {
            $this->log("salespeople/add called BUT was not an AJAX call - ignoring.");
            return $this->jsonResponse(['success' => false, 'messages' => ['Request was not an XHR Request']]);
        }
    }

    /**
     * Ajax Datatables Call
     * @return XMLHttpRequest (JSON)
     */
    public function salespeopleTableAction() {

        $results = $this->serviceCall();

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
            array('db' => 'sales_attr_id', 'dt' => 7),
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
        $this->sspJoin->setDebug($this->isDebug());
        $this->sspJoin->setAndWhere(" sales_attr_id IS NOT NULL AND status = 1 ");

        $response = $this->sspJoin->simple($jsonArgs, $sql_details, $table, $primaryKey, $columns);
        //inform page of sync_scenario to display appropriate buttons in table header
        $response['missingFromDBSalespeople'] = $results['missingFromDBSalespeople'];
        $response['missingFromWebServiceSalespeople'] = $results['missingFromWebServiceSalespeople'];

        return $this->jsonResponse(
                        $response
        );
    }
    
    /**
     * Pre-Validates form fields via ajax
     */
    public function validateAddSalespersonAction() {

        if ($this->getRequest()->isXmlHttpRequest()) {

            if ($this->getRequest()->isPost()) {
                
                $validationcase = $this->params()->fromPost("validationcase");
                $scenario = $this->params()->fromPost("scenario");
                $id = $this->params()->fromPost("id");
                
                switch($validationcase){
                    
                    case "email" : {
                        
                        if($scenario == 'create'){
                            
                            $userEmail = $this->userService->findByEmail($this->params()->fromPost('value'));
                            if(!empty($userEmail)){
                                
                                return $this->jsonResponse(['success' => false, 'messages' => ['Email already in use']]);
                                
                            }else{
                                
                                return $this->jsonResponse(['success' => true]);
                            }
                            
                        }else if($scenario == 'edit'){
                            
                            $user = $this->userService->find($this->params()->fromPost('id'));
                            $userEmail = $this->userService->findByEmail($this->params()->fromPost('value'));
                            if(!empty($userEmail) && $user->getId() != $userEmail->getId()){
                                
                                return $this->jsonResponse(['success' => false, 'messages' => ['Email already in use']]);
                                
                            }else{
                                
                                return $this->jsonResponse(['success' => true]);
                            }
                            
                        }else{
                            //scenario should not be possible!
                            return $this->jsonResponse(['success' => false, 'messages' => ['Validation Scenario not found. Please contact IT']]);
                        }
                    }
                    
                    case "username" : {
                        
                        if($scenario == 'create'){
                            
                            $userUsername = $this->userService->findByUsername($this->params()->fromPost('value'));
                            if(!empty($userUsername)){
                                
                                return $this->jsonResponse(['success' => false, 'messages' => ['Username already in use']]);
                                
                            }else{
                                
                                return $this->jsonResponse(['success' => true]);
                            }
                            
                        }else if($scenario == 'edit'){
                            
                            //here we should have an ID.
                            $user = $this->userService->find($this->params()->fromPost('id'));
                            $userUsername = $this->userService->findByUsername($this->params()->fromPost('value'));
                            if(!empty($userUsername) && $user->getId() != $userUsername->getId()){
                                
                                return $this->jsonResponse(['success' => false, 'messages' => ['Username already in use']]);
                                
                            }else{
                                
                                return $this->jsonResponse(['success' => true]);
                            }
                            
                        }else{
                            //scenario should not be possible!
                            return $this->jsonResponse(['success' => false, 'messages' => ['Validation Scenario not found. Please contact IT']]);
                        }
                    }
                    
                    default : {
                        
                        return $this->jsonResponse(['success' => false, 'messages' => ['Validation Scenario not found. Please Contact IT']]);
                    }
                }
                
            } else {
                $this->log("salespeople/validateAddSalesperson called with AJAX - BUT it was NOT a POST and therefore ignored.");
                return $this->jsonResponse(['success' => false, 'messages' => ['Request was not a POST Request']]);
            }
        } else {
            $this->log("salespeople/validateAddSalesperson called BUT was not an AJAX call - ignoring.");
            return $this->jsonResponse(['success' => false, 'messages' => ['Request was not an XHR Request']]);
        }
        
    }

    /**
     * Queries matches rows from Web Service against rows from DB.
     * @return void
     */
    private function serviceCall() {

        $results = array('missingFromDBSalespeople' => array(), 'missingFromWebServiceSalespeople' => array());

        if($this->isDebug()){
            $this->logger->log(Logger::INFO, "Retrieving Salespeople from Web Service.");
        }

        //we have 4 possible scenarios
        // 0. Web Service returns no Salespeople. (Display Warning Modal explaining Web Service has returned zero Salespeople)
        // 1. Web Service returns same number as DB. (Do nothing - we're good to go)
        // 2. Web Service returns more than DB. (Render Add Salesperson Button)
        // 3. Web Service returns less than DB. (Render Manage Users Button in table header)
        //get a reference to pricing_config array
        
        $pricingconfig = $this->getConfig()['pricing_config'];

        if (empty($pricingconfig)) {

            $this->logger->log('pricing_config not found! Error!', Logger::ERR);
        }

        //log request
        if($this->isDebug()){
            $this->logger->log(Logger::INFO, 'Retrieving ' . $pricingconfig['by_sku_object_sales_controller'] . '.');
        }
        
        //create parameters for Web Service call.
        $params = [
            "id" => $pricingconfig['by_sku_userid'],
            "pw" => $pricingconfig['by_sku_password'],
            "object" => $pricingconfig['by_sku_object_sales_controller']
        ];

        $url = $pricingconfig['by_sku_base_url'];
        $method = $pricingconfig['by_sku_method'];

        $json = $this->rest($url, $method, $params);

        $numberSalespeopleFromWebService = count($json['salespeople']);

        if ($numberSalespeopleFromWebService) {

            $msg = "Retrieved " . $numberSalespeopleFromWebService . " salespeople from web service";

            $this->log($msg);

            $users = $this->entityManager->getRepository(User::class)->findBy(['status' => '1']);

            $salespeople = array();

            foreach ($users as $user) {
                //only removed for development
                //must make sure to remove non-sales users.
                if (!empty($user->getSales_attr_id())) {
                    $salespeople [] = $user;
                }
            }
            
            $usersInactive = $this->entityManager->getRepository(User::class)->findBy(['status' => '0']);

            $salespeopleInactive = array();

            foreach ($usersInactive as $inactiveuser) {
                //only removed for development
                //must make sure to remove non-sales users.
                if (!empty($inactiveuser->getSales_attr_id())) {
                    $salespeopleInactive [] = $inactiveuser;
                }
            }

            $salespeopleCountFromDB = count($salespeople);

            $this->log("Retrieved " . $salespeopleCountFromDB . " salespeople from DB");

            //look for salespeople missing from Web Service.
            foreach ($salespeople as $salesperson2) {
                if (!$this->salespeople($json['salespeople'], $salesperson2->getSales_attr_id())) {
                    $arr = [
                        'salesAttrId' => $salesperson2->getSales_attr_id(),
                        'id' => $salesperson2->getId(),
                        'email' => $salesperson2->getEmail(),
                        'phone1' => $salesperson2->getPhone1(),
                        'username' => $salesperson2->getUsername(),
                        'status' => $salesperson2->getStatus(),
                        'full_name' => $salesperson2->getFullName(),
                        'last_login' => $salesperson2->getLastLogin() ? date_format($salesperson2->getLastLogin(), 'm/d/Y g:iA') : '',
                        'date_created' => $salesperson2->getDateCreated() ? date_format($salesperson2->getDateCreated(), 'm/d/Y g:iA') : '',
                        'roles' => $this->getRoleArray($salesperson2)
                    ];
                    $results['missingFromWebServiceSalespeople'][] = $arr;
                    if ($this->isDebug()) {
                        $this->log(PHP_EOL . "MissingFromWebService: " . $salesperson2->getId());
                    }
                }
            }
            
            //look for salespeople missing from DB.
            foreach ($json['salespeople'] as $salesperson1) {
                if (!$this->salespeople($salespeople, $salesperson1['id'])) {
                    //if we find this salesperson in Inactive array then title should be
                    //Full Name inactive salesperson returned by Web Service
                    $title = $this->getMissingFromDBTitle($salespeopleInactive, $salesperson1['id'], $salesperson1['salesperson']);
                    $id = $this->getMissingFromDBID($salespeopleInactive, $salesperson1['id']);
                    $arr = [
                        'salesAttrId' => $salesperson1['id'],
                        'full_name' => $salesperson1['salesperson'],
                        'title' => $title
                    ];
                    if(!empty($id)){
                        $email = $this->getMissingFromDBEmail($salespeopleInactive, $salesperson1['id']);
                        $phone1 = $this->getMissingFromDBPhone($salespeopleInactive, $salesperson1['id']);
                        $username = $this->getMissingFromDBUsername($salespeopleInactive, $salesperson1['id']);
                        $arr['id'] = $id;
                        $arr['email'] = $email;
                        $arr['phone1'] = $phone1;
                        $arr['username'] = $username;
                    }
                    $results['missingFromDBSalespeople'][] = $arr;
                    if ($this->isDebug()) {
                        $this->log(PHP_EOL . "MissingFromDB: " . $salesperson1['id']);
                    }
                }
            }
            
        } else {
            $msg = "No salespeople returned from Web Service -> ERROR!";
            $this->log($msg);
        }

        $this->log('Salespeople REST/DB Sync Test Results: ' . $scenario);

        return $results;
    }
    
    private function getMissingFromDBTitle(array $salespeopleInactive, $id, $fullname){
        foreach($salespeopleInactive as $salesperson){
            if($salesperson->getSales_attr_id() == $id){
                return $fullname  . ' Inactive in Database';
            }
        }
        return $fullname  . ' not found in Database';
    }
    
    private function getMissingFromDBID(array $salespeopleInactive, $id){
        foreach($salespeopleInactive as $salesperson){
            if($salesperson->getSales_attr_id() == $id){
                return $salesperson->getId();
            }
        }
        return FALSE;
    }
    
    private function getMissingFromDBEmail(array $salespeopleInactive, $id){
        foreach($salespeopleInactive as $salesperson){
            if($salesperson->getSales_attr_id() == $id){
                return $salesperson->getEmail();
            }
        }
        return FALSE;
    }
    
    private function getMissingFromDBPhone(array $salespeopleInactive, $id){
        foreach($salespeopleInactive as $salesperson){
            if($salesperson->getSales_attr_id() == $id){
                return $salesperson->getPhone1();
            }
        }
        return FALSE;
    }
    
    private function getMissingFromDBUsername(array $salespeopleInactive, $id){
        foreach($salespeopleInactive as $salesperson){
            if($salesperson->getSales_attr_id() == $id){
                return $salesperson->getUsername();
            }
        }
        return FALSE;
    }

    private function salespeople($salespeople, $salespersonid) {
        foreach ($salespeople as $salesperson) {
            if (is_array($salesperson)) {
                if (strcmp(strval($salesperson['id']), strval($salespersonid)) == 0) {
                    return true;
                }
            } else {
                if (strcmp(strval($salesperson->getSales_attr_id()), strval($salespersonid)) == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getRoleArray($salesperson) {
        $roles = array();
        foreach ($salesperson->getRoles() as $role) {
            $roles [] = $role->getName();
        }
        return $roles;
    }

    private function rest($url, $method = "GET", $params = []) {
        return $this->restService->rest($url, $method, $params);
    }

    private function log($msg, $info = Logger::INFO) {
        $this->logger->log($info, $msg);
    }

    private function my_print_r($x) {
        return str_replace(PHP_EOL, '', print_r($x, TRUE));
    }

}
