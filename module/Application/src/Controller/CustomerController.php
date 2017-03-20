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
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\Query\Expr\Select;
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
    EntityManager $entityManager, Logger $logger, array $config, AuthManager $authManager, SSPJoin $sspJoin, CustomerService $customerService, RestService $restService, UserService $userService
    ) {

        parent::__construct($authManager, $config);

        $this->logger = $logger;

        $this->entityManager = $entityManager;

        $this->sspJoin = $sspJoin;

        $this->customerService = $customerService;

        $this->restService = $restService;

        $this->userService = $userService;
    }

    public function indexAction() {

        $this->serveNgPage();

        $this->logger->log(Logger::INFO, "Querying Customers.");

        //$id is sales_attr_id to identify salesperson
        $sales_attr_id = (int) $this->params()->fromRoute('id', -1);
        $this->logger->log(Logger::INFO, "Querying Customers.");
        if ($sales_attr_id < 1) {
            //no valid id - 404 Error.
            $this->logger->log(Logger::INFO, "Sales attr id < 1.");
            $this->getResponse()->setStatusCode(404);
            throw new \Exception($sales_attr_id ? "Post with id=$sales_attr_id could not be found" : "No id found in request");
        }

        $assertion = $this->getSalesAttrIdMustMatchAssertion($sales_attr_id);

        //restrict access if DynamicAssertion fails.
        if (!$this->authManager->isGranted(CustomerController::class, "index", $assertion)) {
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

        //if ($inDb < $inSvc) {
        //remove every matching row in DB and rewrite them all to guarantee we have latest data
        //in theory this should flush everything out and keep records up-to-date over time.
        $some = false;
        foreach ($json['customers'] as $customer) {
            //lookup item with id
            $cdb = $this->findById($dbcustomers, $customer['id']);
            if (!empty($cdb)) {
                //update existing record
                if (strcmp($customer['email'], $cdb->getEmail()) != 0) {
                    $cdb->setEmail($customer['email']);
                    $some = TRUE;
                }

                if (strcmp($customer['name'], $cdb->getName()) != 0) {
                    $cdb->setName($customer['name']);
                    $some = TRUE;
                }

                if (strcmp($customer['company'], $cdb->getCompany()) != 0) {
                    $cdb->setCompany($customer['company']);
                    $some = TRUE;
                }

                if ($some) {
                    $cdb->setUpdated(new DateTime());
                    $this->logger->log(Logger::INFO, "Updating DB Record ID: " . $cdb->getId());
                    $this->entityManager->merge($cdb);
                }
            } else {
                //insert record because it doesn't exist.
                $cdb = new Customer();
                $customers = $user->getCustomers();
                $customers [] = $cdb;
                $user->setCustomers($customers);
                $cdb->setId($customer['id']);
                $this->logger->log(Logger::INFO, "Creating DB Record ID: " . $cdb->getId());
                $cdb->setEmail($customer['email']);
                $cdb->setName($customer['name']);
                $cdb->setCompany($customer['company']);
                $cdb->setCreated(new DateTime());
                $cdb->setUpdated($cdb->getCreated());
                $this->entityManager->persist($cdb);
                $some = true;
            }
        }
        if ($some) {
            $this->entityManager->flush();
        }
        //}
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
        
    }

    public function customerTableAction() {

        $jsonData = json_decode($this->params()->fromPost('jsonData'));

        $sales_attr_id = $this->params()->fromQuery('zff_sales_attr_id');

        //because this is not always the logged-in user sales_attr_id
        //we must lookup the user associated with the passed sales_attr_id
        $userForQuery = $this->userService->findBySalesperson($sales_attr_id);

        $table = 'customers';

        $primaryKey = 'id';

        /*
         * id, companyName, Name, email, created, updated
         */

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
                $jsonArgs, $this->sspJoin->pluckColumnIndex($columns, 'created'), $this->params()->fromQuery('zff_updated')
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
        $joinStatement = "SELECT `customers`.`id`, `customers`.`company`, `customers`.`name`, `customers`.`email`, `customers`.`created`, `customers`.`updated`,`customers`.`id` FROM `user_customer` LEFT OUTER JOIN `customers` ON `user_customer`.`customer_id` = `customers`.`id` ";
        $joinCountStatement = "SELECT COUNT(`customers`.`id`) FROM `user_customer` LEFT OUTER JOIN `customers` ON `user_customer`.`customer_id` = `customers`.`id` ";
        $this->sspJoin->setJoinStatement($joinStatement);
        $this->sspJoin->setJoinCountStatement($joinCountStatement);
        $this->sspJoin->setAndWhere(' `user_customer`.`user_id` = ' . $userForQuery->getId() . " ");
        $this->sspJoin->setDebug(TRUE);
        $this->sspJoin->setLogger($this->logger);

        $response = $this->sspJoin->simple($jsonArgs, $sql_details, $table, $primaryKey, $columns);

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

    private function rest($url, $method = "GET", $params = []) {
        return $this->restService->rest($url, $method, $params);
    }

}
