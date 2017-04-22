<?php

namespace Application\Controller;

use Application\Assert\SalesAttrIdMustMatchAssertion;
use Application\Datatables\Server;
use Application\Datatables\SSPJoin;
use Application\Entity\Customer;
use Application\Service\CustomerService;
use Application\Service\RestService;
use Application\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManager;
use User\Service\AuthManager;
use Zend\Log\Logger;

class CustomerController extends BaseController {

    private $logger;
    private $entityManager;
    private $sspJoin;
    private $customerService;
    private $restService;
    private $userService;

    public function __construct(
            EntityManager $entityManager, 
            Logger $logger, 
            array $config, 
            AuthManager $authManager, 
            SSPJoin $sspJoin, 
            CustomerService $customerService, 
            RestService $restService, 
            UserService $userService
    ) {

        parent::__construct($authManager, $config);

        $this->logger = $logger;

        $this->entityManager = $entityManager;

        $this->sspJoin = $sspJoin;

        $this->customerService = $customerService;

        $this->restService = $restService;

        $this->userService = $userService;
    }

    private function findById($customers, $id) {
        foreach ($customers as $customer) {
            if ($id == $customer->getId()) {
                return $customer;
            }
        }
        return FALSE;
    }

    public function viewAction() {
        
        $this->serveNgPage();
        
    }

    public function customerTableAction() {
        
        if((int)$this->params()->fromQuery('zff_sync') == 1){
            $this->logger->log(Logger::INFO, "Syncing DB. Customer Controller");
            $this->syncDB();
        }else{
            $this->logger->log(Logger::INFO, "DB Sync Skipped on subsequent ajax.");
        }
        
        $jsonData = json_decode($this->params()->fromPost('jsonData'));

        $sales_attr_id = $this->params()->fromQuery('zff_sales_attr_id');
        
        //because this is not always the logged-in user sales_attr_id
        //we must lookup the user associated with the passed sales_attr_id
        $userForQuery = $this->userService->findBySalesperson($sales_attr_id);
        
        $salespersonphone = $userForQuery->getPhone1();
        
        $salespersonemail = $userForQuery->getEmail();

        $table = 'customers';

        $primaryKey = 'id';

        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'company', 'dt' => 1),
            array('db' => 'name', 'dt' => 2),
            array('db' => 'email', 'dt' => 3),
            array(
                'db' => 'created',
                'dt' => 4,
                'formatter' => function( $d, $row ) {
                    return date('m/d/Y', strtotime($d));
                }
            ),
            array(
                'db' => 'updated',
                'dt' => 5,
                'formatter' => function( $d, $row ) {
                    return date('m/d/Y', strtotime($d));
                }
            ),
            array('db' => 'id', 'dt' => 6),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => $this->config['doctrine']['connection']['orm_default']['params']['user'],
            'pass' => $this->config['doctrine']['connection']['orm_default']['params']['password'],
            'db' => $this->config['doctrine']['connection']['orm_default']['params']['dbname'],
            'host' => $this->config['doctrine']['connection']['orm_default']['params']['host']
        );

        $jsonArgs = Server::buildArrayFromJson($jsonData);

        //merge company

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'company'), $this->params()->fromQuery('zff_company')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'name'), $this->params()->fromQuery('zff_name')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'email'), $this->params()->fromQuery('zff_email')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'created'), $this->params()->fromQuery('zff_created')
        );

        $this->sspJoin->setColumnSearchValue(
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'updated'), $this->params()->fromQuery('zff_updated')
        );

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
        $joinStatement = "SELECT `customers`.`id`, `customers`.`company`, `customers`.`name`, `customers`.`email`, `customers`.`created`, `customers`.`updated`, `customers`.`id` FROM `user_customer` LEFT OUTER JOIN `customers` ON `user_customer`.`customer_id` = `customers`.`id` ";
        $joinCountStatement = "SELECT COUNT(`customers`.`id`) FROM `user_customer` LEFT OUTER JOIN `customers` ON `user_customer`.`customer_id` = `customers`.`id` ";
        $this->sspJoin->setJoinStatement($joinStatement);
        $this->sspJoin->setJoinCountStatement($joinCountStatement);
        $this->sspJoin->setAndWhere(' `user_customer`.`user_id` = ' . $userForQuery->getId() . " ");
        $this->sspJoin->setDebug(TRUE);
        $this->sspJoin->setLogger($this->logger);

        $response = $this->sspJoin->simple($jsonArgs, $sql_details, $table, $primaryKey, $columns);
        
        $response['salesperson_phone'] = $salespersonphone;
        $response['salesperson_email'] = $salespersonemail;

        return $this->jsonResponse(
                        $response
        );
    }

    private function getSalesAttrIdMustMatchAssertion($sales_attr_id) {

        //retrieve the User
        $loggedInUser = $this->authManager->getLoggedInUser();

        //Guarantees that User is either an Admin or is indeed the User they are requesting.
        $assertion = new SalesAttrIdMustMatchAssertion($loggedInUser);

        //set the id parameter as pageId
        $assertion->setPageId($sales_attr_id);

        return $assertion;
    }
    
    private function syncDB(){
        
        $this->logger->log(Logger::INFO, "Querying Customers.");

        //$id is sales_attr_id to identify salesperson
        $sales_attr_id = (int) $this->params()->fromQuery('zff_sales_attr_id', -1);
        $this->logger->log(Logger::INFO, "Querying Customers.");
        if ($sales_attr_id < 1) {
            //no valid id - 404 Error.
            $this->logger->log(Logger::INFO, "Sales attr id < 1.");
            $this->getResponse()->setStatusCode(404);
            throw new \Exception($sales_attr_id ? "Post with id=$sales_attr_id could not be found" : "No id found in request");
        }

        $assertion = $this->getSalesAttrIdMustMatchAssertion($sales_attr_id);

        //restrict access if DynamicAssertion fails.
        if (!$this->authManager->isGranted(CustomerController::class, "view", $assertion)) {
            //do not render the page.
            $this->logger->log(Logger::INFO, "Assertion Failed.");
            $this->getResponse()->setStatusCode(404);
            return;
        }

        //query WebService
        //setup params from configuration
        $params = [
            "id" => $this->config['pricing_config']['by_sku_userid'],
            "pw" => $this->config['pricing_config']['by_sku_password'],
            "object" => $this->config['pricing_config']['by_sku_object_users_controller'],
            "salespersonid" => $sales_attr_id
        ];

        //setup url from configuration
        $url = $this->config['pricing_config']['by_sku_base_url'];

        //setup method from configuration
        $method = $this->config['pricing_config']['by_sku_method'];

        //execute Web Service call
        $json = $this->rest($url, $method, $params);

        $user = $this->userService->findBySalesperson($sales_attr_id);

        $dbcustomers = $user->getCustomers();

        $inDb = count($dbcustomers);
        $inSvc = count($json['customers']);

        $this->logger->log(Logger::INFO, "inDB: " . $inDb . " inWS: " . $inSvc);

        $some = false;
        foreach ($json['customers'] as $customer) {
            //lookup item with id
            $customerid = $customer['id'];
            $customerObj = $this->findById($dbcustomers, $customer['id']);
            if (!empty($customerObj)) {
                //update existing record
                if (strcmp($customer['email'], $customerObj->getEmail()) != 0) {
                    $customerObj->setEmail($customer['email']);
                    $some = TRUE;
                }

                if (strcmp($customer['name'], $customerObj->getName()) != 0) {
                    $customerObj->setName($customer['name']);
                    $some = TRUE;
                }

                if (strcmp($customer['company'], $customerObj->getCompany()) != 0) {
                    $customerObj->setCompany($customer['company']);
                    $some = TRUE;
                }

                if ($some) {
                    $customerObj->setUpdated(new DateTime());
                    $this->logger->log(Logger::INFO, "Updating DB Record ID: " . $customerObj->getId());
                    $this->entityManager->merge($customerObj);
                }
            } else {
                //insert record because it doesn't exist.
                $customerObj = new Customer();
                $customers = $user->getCustomers();
                $customers [] = $customerObj;
                $user->setCustomers($customers);
                $customerObj->setId($customerid);
                $this->logger->log(Logger::INFO, "Creating DB Record ID: " . $customerObj->getId());
                $customerObj->setEmail($customer['email']);
                $customerObj->setName($customer['name']);
                $customerObj->setCompany($customer['company']);
                $customerObj->setCreated(new DateTime());
                $customerObj->setUpdated($customerObj->getCreated());
                $this->entityManager->persist($customerObj);
                $some = true;
            }
        }
        if ($some) {
            $this->entityManager->flush();
        }
    }

    private function rest($url, $method = "GET", $params = []) {
        return $this->restService->rest($url, $method, $params);
    }

}
