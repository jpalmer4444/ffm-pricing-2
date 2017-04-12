<?php

namespace Application\Controller;

use Application\Datatables\Server;
use Application\Datatables\SSPJoin;
use Application\Datatables\SSPUnion;
use Application\Entity\AddedProduct;
use Application\Entity\Checkbox;
use Application\Entity\Customer;
use Application\Entity\Preferences;
use Application\Entity\PriceOverride;
use Application\Entity\PriceOverrideReport;
use Application\Entity\Product;
use Application\Entity\User;
use Application\Form\OverridePriceForm;
use Application\Form\ProductForm;
use Application\Service\CheckboxService;
use Application\Service\CustomerService;
use Application\Service\RestService;
use Application\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Select;
use Exception;
use User\Service\AuthManager;
use Zend\Log\Logger;

class ProductController extends BaseController {

    private $logger;
    private $entityManager;
    private $restService;
    private $sspJoin;
    private $customerService;
    private $userService;

    /** @var $checkboxService \Application\Service\CheckboxService */
    private $checkboxService;

    public function __construct(
    EntityManager $entityManager, Logger $logger, array $config, AuthManager $authManager, SSPJoin $sspJoin, RestService $restService, CustomerService $customerService, UserService $userService, CheckboxService $checkboxService
    ) {

        parent::__construct($authManager, $config);

        $this->logger = $logger;

        $this->entityManager = $entityManager;

        $this->sspJoin = $sspJoin;

        $this->restService = $restService;

        $this->customerService = $customerService;

        $this->userService = $userService;

        $this->checkboxService = $checkboxService;
    }

    public function viewAction() {

        $this->serveNgPage();
    }

    public function overrideAction() {

        if ($this->getRequest()->isXmlHttpRequest()) {

            $form = new OverridePriceForm();

            if ($this->getRequest()->isPost()) {

                $form->setData($this->params()->fromPost());

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    $salesperson = $this->userService->findBySalesperson($this->params()->fromPost('sales_attr_id'));

                    $customer = $this->customerService->find($this->params()->fromPost('customer_id'));

                    //create or delete based on override data being empty 

                    $product = $this->entityManager->getRepository(Product::class)->find(substr($this->params()->fromPost('product_id'), 1));

                    $overridePrice = $this->editCreateOrDeleteOverridePrice($data, $customer, $salesperson, $product);

                    return $this->jsonResponse(['success' => true, 'id' => $overridePrice->getId()]);
                } else {

                    $this->log("Form Invalid Form Data: " . print_r($data, TRUE));

                    foreach ($form->getMessages() as $msg) {

                        $this->log("Form Invalid - Message: " . print_r($msg, TRUE));
                    }

                    $this->log("product/override form not valid.");
                    return $this->jsonResponse(['success' => false]);
                }
            } else {

                $this->log("product/override called with AJAX - BUT it was NOT a POST and therefore ignored.");
                return $this->jsonResponse(['success' => false]);
            }
        } else {

            $this->log("product/override called BUT was not an AJAX call - ignoring.");
            return $this->jsonResponse(['success' => false]);
        }
    }

    public function reportAction() {

        if ($this->getRequest()->isXmlHttpRequest()) {

            if ($this->getRequest()->isPost()) {

                $customerid = $this->params()->fromPost('customer_id');
                $sales_attr_id = $this->params()->fromPost('sales_attr_id');
                $rows = $this->params()->fromPost('rows');
                $rowcount = count($rows);

                if (empty($customerid) || empty($sales_attr_id) || empty($rowcount)) {

                    $msg = "One of customerid: $customerid, sales_attr_id: $sales_attr_id, or rowcount: $rowcount is empty.  ERROR!";
                    $this->logger->log(Logger::INFO, $msg);
                    $this->getResponse()->setStatusCode(404);
                    throw new Exception($msg);
                } else {

                    //update DB with Report Rows.
                    foreach ($rows as $row) {

                        $id = $row[0];
                        $retail = $row[1];
                        $override = $row[2];

                        $this->createReportProduct($id, $customerid, $sales_attr_id, $override, $retail);
                    }

                    $this->entityManager->flush();

                    return $this->jsonResponse(['success' => true]);
                }
            } else {

                $this->log("product/report called with AJAX - BUT it was NOT a POST and therefore ignored.");
                return $this->jsonResponse(['success' => false]);
            }
        } else {

            $this->log("product/report called BUT was not an AJAX call - ignoring.");
            return $this->jsonResponse(['success' => false]);
        }
    }

    public function productAction() {

        if ($this->getRequest()->isXmlHttpRequest()) {

            //first check if this is a delete request - whereas we dont use the form at all
            if (strcmp($this->params()->fromPost('scenario'), 'delete') == 0) {

                //just delete the Added Product.
                $addedProduct = $this->entityManager->getRepository(AddedProduct::class)->find(substr($this->params()->fromPost('product_id'), 1));
                $addedProduct->setActive(0);
                $this->entityManager->merge($addedProduct);
                $this->entityManager->flush();
                return $this->jsonResponse(['success' => true]);
            }

            $form = new ProductForm();

            if ($this->getRequest()->isPost()) {

                $form->setData($this->params()->fromPost());

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    $salesperson = $this->userService->findBySalesperson($this->params()->fromPost('sales_attr_id'));

                    $customer = $this->customerService->find($this->params()->fromPost('customer_id'));

                    //create an Added Product
                    $addedProduct = $this->createAddedProduct($data, $customer, $salesperson);

                    return $this->jsonResponse(['success' => true, 'id' => $addedProduct->getId()]);
                } else {

                    $this->log("Form Invalid Form Data: " . print_r($data, TRUE));

                    foreach ($form->getMessages() as $msg) {

                        $this->log("Form Invalid - Message: " . print_r($msg, TRUE));
                    }

                    $this->log("product/product form not valid.");
                    return $this->jsonResponse(['success' => false]);
                }
            } else {

                $this->log("product/product called with AJAX - BUT it was NOT a POST and therefore ignored.");
                return $this->jsonResponse(['success' => false]);
            }
        } else {

            $this->log("product/product called BUT was not an AJAX call - ignoring.");
            return $this->jsonResponse(['success' => false]);
        }
    }

    public function checkedAction() {

        $type = $this->params()->fromQuery('myaction');

        if (empty($type)) {

            //no valid id - 404 Error.
            $this->logger->log(Logger::INFO, "checkedAction called with no myaction paramter! ERROR!");
            $this->getResponse()->setStatusCode(404);
            throw new Exception("No myaction paramter found in request");
        }

        //id will be preceded by P or A
        //P for Products
        //A for AddedProducts

        $this->logger->log(Logger::INFO, "product/checked called with type: $type");

        switch ($type) {

            case 'selectall' :
            case 'deselectall' :

                $sales_attr_id = $this->params()->fromPost('salesperson');

                $customerid = $this->params()->fromPost('customer');

                if (empty($sales_attr_id) || empty($customerid)) {

                    $this->logger->log(Logger::ERR, "Insufficient Parameters Sent! Required POST vars: salesperson=$sales_attr_id, customer=$customerid");

                    $this->jsonResponse([
                        'success' => FALSE,
                        'salesperson' => $sales_attr_id,
                        'customer' => $customerid
                    ]);
                }
                
                $entityManager = $this->entityManager;

                $lookup = function($sales_attr_id, $customerid) use (& $entityManager) {
                    $salesperson = $entityManager->getRepository(User::class)->
                            findOneBy(['sales_attr_id' => $sales_attr_id]);

                    return $entityManager->getRepository(Checkbox::class)
                                    ->findBy([
                                        'salesperson' => $salesperson->getId(),
                                        'customer' => $customerid
                    ]);
                };

                $checkboxes = $lookup($sales_attr_id, $customerid);

                foreach ($checkboxes as $checkbox) {

                    /** @var $checkbox \Application\Entity\Checkbox */
                    $checkbox->setChecked($type == 'selectall' ? 1 : 0);

                    $this->entityManager->merge($checkbox);
                }

                $this->entityManager->flush();

                return $this->jsonResponse([
                            'success' => TRUE
                ]);

            case 'deselect' :
            case 'select' :
            default :

                $id = $this->params()->fromPost('id');

                $sales_attr_id = $this->params()->fromPost('salesperson');

                $customerid = $this->params()->fromPost('customer');

                if (empty($id) || empty($sales_attr_id) || empty($customerid)) {

                    $this->logger->log(Logger::ERR, "Insufficient Parameters Sent! Required POST vars: id=$id, salesperson=$sales_attr_id, customer=$customerid");

                    $this->jsonResponse([
                        'success' => FALSE,
                        'id' => $id,
                        'salesperson' => $sales_attr_id,
                        'customer' => $customerid
                    ]);
                }
                
                $entityManager = $this->entityManager;

                $lookup = function ($id, $sales_attr_id, $customerid) use (& $entityManager) {
                    $salesperson = $entityManager->getRepository(User::class)->
                            findOneBy(['sales_attr_id' => $sales_attr_id]);

                    return $entityManager->getRepository(Checkbox::class)
                                    ->findOneBy([
                                        $id[0] == 'P' ? 'product' : 'addedProduct' => substr($id, 1),
                                        'salesperson' => $salesperson->getId(),
                                        'customer' => $customerid
                    ]);
                };

                /** @var \Application\Entity\Checkbox $checkbox */
                $checkbox = $lookup($id, $sales_attr_id, $customerid);

                
                $checkbox->setChecked($type == 'select' ? 1 : 0);

                $this->entityManager->merge($checkbox);
                $this->entityManager->flush();

                return $this->jsonResponse([
                            'success' => TRUE
                ]);
        }
    }

    public function productTableAction() {
        
        (int) $zff_sync = $this->params()->fromPost('zff_sync');
        
        $this->logger->log(Logger::INFO, "zff_sync: " . $zff_sync);
        
        if ($zff_sync == 1) {
            $this->logger->log(Logger::INFO, "Syncing DB. Products Controller");
            $this->syncDB();
        } else {
            $this->logger->log(Logger::INFO, "DB Sync Skipped on subsequent ajax.");
        }

        $jsonArgs = $this->params()->fromPost();
        
        if($jsonArgs['order'][0]['column'] == 0){
            $jsonArgs['order'][0]['column'] = 2;
        }

        $columns = $this->configValue('columns');

        $columnsPre = $this->configValue('columnsPre');

        $columnsPost = $this->configValue('columnsPost');

        // SQL server connection information
        $sql_details = array(
            'user' => $this->config['doctrine']['connection']['orm_default']['params']['user'],
            'pass' => $this->config['doctrine']['connection']['orm_default']['params']['password'],
            'db' => $this->config['doctrine']['connection']['orm_default']['params']['dbname'],
            'host' => $this->config['doctrine']['connection']['orm_default']['params']['host']
        );

        $cust_id = $this->params()->fromQuery('zff_customer_id');

        $sales_user = $this->userService->findBySalesperson($this->params()->fromQuery('zff_sales_attr_id'));

        $sales_user_id = $sales_user->getId();

        $selectPre = $this->configValue('selectPre');

        $selectCountPre = $this->configValue('selectCountPre');

        $andWherePre = "`customer_product`.`customer` = $cust_id AND `user_customer`.`user_id` = $sales_user_id";

        $andWherePost = "`added_product`.`customer` = $cust_id AND `added_product`.`active` = 1 ";

        $selectPost = $this->configValue('selectPost');

        $selectCountPost = $this->configValue('selectCountPost');

        $sql = $this->configValue('skuSelect');

        $stmt = $this->entityManager->getConnection()->executeQuery(
                $sql, [
            $cust_id,
            $sales_user_id,
            $cust_id
                ]
        );

        $skus = [];
        $productnames = [];
        $uoms = [];

        while ($row = $stmt->fetch()) {

            $skus[] = $row['sku'];
            $productnames[] = $row['product'];
            $uoms[] = $row['uom'];
        }

        $response = SSPUnion::union($jsonArgs, $sql_details, $columns, $columnsPre, $columnsPost, $selectPre, $selectPost, $selectCountPre, $selectCountPost, $andWherePre, $andWherePost, $this->logger);

        $response['skus'] = $skus;
        $response['products'] = $productnames;
        $response['uoms'] = $uoms;

        return $this->jsonResponse(
                        $response
        );
    }
    
    public function productFormTypeaheadAction() {
        
        $search = '%' . $this->params()->fromPost('term') . '%';

        $sql = 'SELECT `products`.`productname` as \'productname\', `products`.`description` as \'description\', `products`.`sku` as \'sku\', `products`.`uom` as \'uom\', `products`.`retail` as \'retail\' FROM products WHERE `products`.`productname` LIKE ? ORDER BY `products`.`productname` ASC LIMIT 0, 25';

        $stmt = $this->entityManager->getConnection()->executeQuery(
                $sql, [
                    $search
                ]
        );

        $results = [];

        while ($row = $stmt->fetch()) {

            $results[] = [
                    'productname' => $row['productname'],
                    'description' => $row['description'],
                    'sku' => $row['sku'],
                    'uom' => $row['uom'],
                    'retail' => $row['retail']
            ];
        }
        
        return $this->jsonResponse($results);
    }

    private function createReportProduct($id, $customerid, $sales_attr_id, $override, $retail) {
        $isAddedProduct = $id[0] == 'A';
        $_id = substr($id, 1);
        $report = new PriceOverrideReport();
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerid);
        $report->setCustomer($customer);
        $report->setOverrideprice($override);
        $report->setRetail($retail);
        $salesperson = $this->userService->findBySalesperson($sales_attr_id);
        $report->setSalesperson($salesperson);
        if ($isAddedProduct) {
            $addedProduct = $this->entityManager->getRepository(AddedProduct::class)->find($_id);
            $report->setAddedProduct($addedProduct);
        } else {
            $product = $this->entityManager->getRepository(Product::class)->find($_id);
            $report->setProduct($product);
        }
        $this->entityManager->persist($report);
    }

    private function syncDB() {

        $customerid = $this->params()->fromQuery('zff_customer_id');
        $sales_attr_id = $this->params()->fromQuery('zff_sales_attr_id');

        $params = [
            "id" => $this->config['pricing_config']['by_sku_userid'],
            "pw" => $this->config['pricing_config']['by_sku_password'],
            "object" => $this->config['pricing_config']['by_sku_object_items_controller'],
            "customerid" => $customerid
        ];

        $method = $this->config['pricing_config']['by_sku_method'];
        $json = $this->rest($this->config['pricing_config']['by_sku_base_url'], $method, $params);

        $restItemCount = !empty($json) ?
                count($json[$this->config['pricing_config']['by_sku_object_items_controller']]) :
                0;

        if ($restItemCount == 0) {

            $this->logger->log(Logger::INFO, "No products returned from the Web Service!");
            return;
        } else {

            $this->logger->log(Logger::INFO, "Web Service (Products) #: " . $restItemCount);
        }

        //retrieve customer
        $dql_customer = 'SELECT customer FROM Application\Entity\Customer customer WHERE customer.id = :customerid';

        $customer = $this->customerService->find(
                $customerid
        );

        if (is_array($customer)) {
            $customer = $customer[0];
        }

        $this->logger->log(Logger::INFO, "Customer: " . ($customer ? $customer->getId() : "No Customer Found!!!"));

        $dql_salesperson = 'SELECT user FROM Application\Entity\User user WHERE user.sales_attr_id = :sales_attr_id';

        $user = $this->userService->findEager(
                //eagerly instantiate Preferences on User objects.
                ['Application\Entity\Preferences' => 'preferences'],
                //pass in parameters for the DQL query
                ['sales_attr_id' => $sales_attr_id],
                //pass in DQL
                $dql_salesperson
        );

        if (is_array($user)) {
            $user = $user[0];
        }

        $preferences = !empty($user) ? $user->getPreferences() : [];

        $this->logger->log(Logger::INFO, "Preferences by user: " . count($preferences));

        $products = $this->getAgnosticProducts($json);

        $this->logger->log(Logger::INFO, "Products: " . count($products));

        $productsInserted = 0;
        $preferencesInserted = 0;

        /*
         * Test if we have results returned from the Web Service.
         */
        if ($json &&
                array_key_exists($this->config['pricing_config']['by_sku_object_items_controller'], $json)) {

            $restItems = $json[$this->config['pricing_config']['by_sku_object_items_controller']];

            $productMap = [];

            /*
             * Iterate Web Service Results.
             */
            $some = FALSE;
            foreach ($restItems as $restItem) {

                /*
                 * There are duplicates from the Web Service. 
                 * Use "continue" to skip Products already added.
                 */
                if (!array_key_exists($restItem['id'], $productMap)) {

                    $this->logger->log(Logger::INFO, "PROCESSING: Rest Item ID: {$restItem['id']} SKU: {$restItem['sku']} PRODUCTNAME: {$restItem['productname']}");
                    $productMap[$restItem['id']] = $restItem['id'];
                    
                } else {
                    $this->logger->log(Logger::INFO, "SKIPPING: Rest Item ID: {$restItem['id']} SKU: {$restItem['sku']} PRODUCTNAME: {$restItem['productname']}");
                    continue;
                }

                /*
                 * Test if this product already lives in the DB.
                 */
                $product = $this->find($products, $restItem['id'], 'id');

                /*
                 * If the product does not live in the DB.
                 * Create a new Product, If it DOES live in DB,
                 * Then Update it to guarantee latest data.
                 */
                if (!$product) {

                    $product = $this->createProduct($restItem, $customer, $user);
                    $productsInserted++;
                    $some = TRUE;
                    
                } else {

                    $some = $this->updateProduct($some, $product, $restItem);
                }

                /*
                 * Should be impossible to not have a Product here
                 * but test anyway.
                 */
                $productid = $product->getId() ? $product->getId() : -1;

                /*
                 * Log if we have no Product because
                 * it should NEVER happen.
                 */
                if ($productid == -1) {
                    $this->logger->log(Logger::ERR, "No Product! This should NEVER happen. Please investigate immediately.");
                }

                /*
                 * Test if we have an existing Preference for this User/Product combo.
                 * If not, create one - If so, compare values and update if necessary.
                 */
                $preference = $this->find($preferences, $productid, 'product');

                if (!$preference) {
                    $preference = $this->createPreference($restItem, $product, $user);
                    $preferencesInserted++;
                    $some = TRUE;
                } else {
                    $some = $this->updatePreference($some, $preference, $restItem);
                }
            }

            //next iterate over items returned from the DB
            //NOT Products, BUT Preferences - because Products are owned across Users.
            //iterate over DB any not found in Web Service - DELETE or SET INACTIVE

            /*
             * Iterate over Preferences returned from the DB for this User. Lookup the associated
             * Product for every Preference, this is unavoidable as we need the Product ID from the 
             * Product instance to see if there is a corresponding Product returned from the Web Service, 
             * If the Product ID is not found in the Web Service results - it means the Product has fallen
             * off this Customers list and the Preference needs to be removed. This is also why we have no
             * Cascade attribute on associations for Preferences - so we can safely remove a Preference for 
             * a Product without removing the associated Product. Which is necessary because the Product still 
             * exists and is surely referenced by other accounts, so remove the Preference and leave the Product.
             */
            foreach ($preferences as $preference) {

                $product = $preference->getProduct();

                $jsonItem = $this->find($restItems, $product->getId(), 'json');
                if (empty($jsonItem)) {

                    $this->logger->log(Logger::INFO, 'Deleting Preference[product=' . $preference->getProduct()->getId() . ',user=' . $preference->getUser()->getUsername() . ']');
                    $this->entityManager->remove($preference);
                    $some = TRUE;
                }
            }

            if ($some) {

                $this->entityManager->flush();
                $this->logger->log(Logger::INFO, "Created $productsInserted products");
                $this->logger->log(Logger::INFO, "Created $preferencesInserted preferences");
            }
        } else {

            $this->logger->log(Logger::INFO, 'No ' . $this->config['pricing_config']['by_sku_object_items_controller'] . ' Products found.');
        }
    }

    private function updatePreference($some, $preference, $restItem) {
        $thisOne = FALSE;
        if (strcmp($preference->getComment(), $restItem['comment']) != 0) {
            $preference->setComment($restItem['comment']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($preference->getOption(), $restItem['option']) != 0) {
            $preference->setOption($restItem['option']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if ($thisOne) {
            $this->entityManager->merge($preference);
        }
        $this->logger->log(Logger::INFO, "Updating Preference[ "
                . "product =  {$preference->getProduct()->getId()} , "
                . "sku =  {$preference->getProduct()->getSku()} , "
                . "user = {$preference->getUser()->getUsername()}"
                . "]");
        return $some;
    }

    private function createPreference($restItem, $product, $user) {
        $preference = new Preferences();
        $preference->setComment($restItem['comment']);
        $preference->setOption($restItem['option']);
        $preference->setProduct($product);
        $preference->setUser($user);
        $userPreferences = $user->getPreferences();
        if (empty($userPreferences)) {
            $userPreferences = new ArrayCollection();
        }
        $userPreferences->add($preference);
        $user->setPreferences($userPreferences);
        $this->entityManager->merge($user);
        $this->entityManager->persist($preference);
        $this->logger->log(Logger::INFO, "Creating Preference[ "
                . "product =  {$product->getId()} , "
                . "sku =  {$product->getSku()} , "
                . "user = {$user->getUsername()}"
                . "]");
        return $preference;
    }

    private function getAgnosticProducts(array $json) {
        $some = FALSE;
        $qb = $this->entityManager->getRepository('Application\Entity\Product')->createQueryBuilder('product');
        $qb->add('select', new Select(array('product')))
                ->add('from', new From('Application\Entity\Product', 'product'));
        $arr = [];
        foreach ($json[$this->config['pricing_config']['by_sku_object_items_controller']] as $product) {
            $some = TRUE;
            $arr [] = $qb->expr()->eq('product.id', "'" . utf8_encode($product['id']) . "'");
        }
        $qb->add('where', $qb->expr()->orX(
                        implode(" OR ", $arr)
        ));
        $query = $qb->getQuery();

        return $query->getResult();
    }

    private function find($array, $id, $type) {
        if (!empty($array) && $id != -1) {
            foreach ($array as $model) {
                switch ($type) {
                    case "id" : {
                            if ($model->getId() == $id) {
                                return $model;
                            }else{
                                break;
                            }
                        }
                    case "json" : {
                            if ($model['id'] == $id) {
                                return $model;
                            }else{
                                break;
                            }
                        }
                    case "product" :
                    default : {
                            if ($model->getProduct()->getId() == $id) {
                                return $model;
                            }
                        }
                }
            }
            if ($type == 'json') {
                $this->logger->log(Logger::INFO, "find(type=json) not found = $id");
            }
        }
        return FALSE;
    }

    private function configValue($key) {
        return $this->config['queries'][self::class]['actions']['productTableAction'][$key];
    }

    private function updateProduct($some, $product, $restItem) {
        //check fields
        $thisOne = FALSE;
        if (strcmp($product->getDescription(), $restItem['shortescription']) != 0) {
            $product->setDescription($restItem['shortescription']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getProductname(), $restItem['productname']) != 0) {
            $product->setProductname($restItem['productname']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getQty(), $restItem['qty']) != 0) {
            $product->setQty($restItem['qty']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getRetail(), $restItem['retail']) != 0) {
            $product->setRetail($restItem['retail']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getSaturdayenabled(), $restItem['saturdayenabled']) != 0) {
            $product->setSaturdayenabled($restItem['saturdayenabled']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getSku(), $restItem['sku']) != 0) {
            $product->setSku($restItem['sku']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getStatus(), $restItem['status'] ? 'Enabled' : 'Disabled') != 0) {
            $product->setStatus($restItem['shortescription'] ? true : false);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getUom(), $restItem['uom']) != 0) {
            $product->setUom($restItem['uom']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if (strcmp($product->getWholesale(), $restItem['wholesale']) != 0) {
            $product->setWholesale($restItem['wholesale']);
            $some = TRUE;
            $thisOne = TRUE;
        }
        if ($thisOne) {
            $this->entityManager->merge($product);
        }
        if ($this->isDebug() && $some) {
            $this->logger->log(Logger::INFO, "Updating Product[ "
                    . "id = {$product->getId()}, "
                    . "sku = {$product->getSku()}, "
                    . "]");
        }
        return $some;
    }

    private function createAddedProduct(array $formData, Customer $customer, User $salesperson) {

        //create the Product
        /** @var AddedProduct $addedProduct */
        $addedProduct = new AddedProduct();
        $addedProduct->setDescription($formData['description']);
        $addedProduct->setProductname($formData['product']);
        $addedProduct->setComment($formData['comment']);
        $addedProduct->setOverridePrice($formData['overrideprice']);
        $addedProduct->setSku($formData['sku']);
        $addedProduct->setStatus(true);
        $addedProduct->setActive(true);
        $addedProduct->setSalesperson($salesperson);
        $addedProduct->setUom($formData['uom']);
        $addedProduct->setCustomer($customer);

        //create Checkbox
        $checkbox = new Checkbox();
        $checkbox->setChecked(FALSE);
        $checkbox->setCustomer($customer);
        $checkbox->setAddedProduct($addedProduct);

        //associate Checkbox with Salesperson
        $checkbox->setSalesperson($salesperson);

        //associate Checkbox with Product
        $checkboxes = $addedProduct->getCheckboxes();
        $checkboxes->add($checkbox);
        $addedProduct->setCheckboxes($checkboxes);


        $userAddedProducts = $customer->getAddedProducts();

        if (empty($userAddedProducts)) {
            $userAddedProducts = new ArrayCollection();
        }

        $userAddedProducts->add($addedProduct);

        $customer->setAddedProducts($userAddedProducts);

        if ($this->isDebug()) {
            $this->logger->log(Logger::INFO, "Creating AddedProduct[ "
                    . "sku = {$addedProduct->getSku()}, "
                    . "customer.company = {$customer->getCompany()}, "
                    . "customer.name = {$customer->getName()}"
                    . "version = {$addedProduct->getVersion()}, "
                    . "productname = {$addedProduct->getProductname()}, "
                    . "description = {$addedProduct->getDescription()}, "
                    . "sku = {$addedProduct->getSku()}, "
                    . "uom = {$addedProduct->getUom()}, "
                    . "status = {$addedProduct->getStatus()}, "
                    . "comment = {$addedProduct->getComment()}, "
                    . "active = {$addedProduct->getActive()}, "
                    . "overrideprice = {$addedProduct->getOverrideprice()}, "
                    . "salesperson.username = {$salesperson->getUsername()}"
                    . "]");
        }

        try {
            $this->entityManager->merge($customer);
            $this->entityManager->merge($salesperson);
            $this->entityManager->persist($addedProduct);
            $this->entityManager->persist($checkbox);
            $this->entityManager->flush();
        } catch (Exception $exc) {
            $this->logger->log(Logger::ERR, "Message: {$exc->getMessage()} Code: {$exc->getCode()} File: {$exc->getFile()} Line: {$exc->getLine()}");
            $this->logger->log(Logger::ERR, $exc->getTraceAsString());
        }

        return $addedProduct;
    }

    private function editCreateOrDeleteOverridePrice(array $restItem, Customer $customer, User $salesperson, Product $product) {

        //based on scenario we know if this is a new override price - or edit existing
        $scenario = $this->params()->fromPost("scenario");

        switch ($scenario) {

            case "create" : {

                    $overridePrice = new PriceOverride();
                    $overridePrice->setActive(1);
                    $overridePrice->setCustomer($customer);
                    $overridePrice->setOverrideprice($restItem['overrideprice']);
                    $overridePrice->setProduct($product);
                    $overridePrice->setSalesperson($salesperson);

                    $this->entityManager->merge($customer);
                    $this->entityManager->merge($product);
                    $this->entityManager->merge($salesperson);
                    $this->entityManager->persist($overridePrice);
                    $this->entityManager->flush();

                    break;
                }

            case "edit" : {

                    $params = [
                        'customer' => $customer->getId(),
                        'product' => $product->getId(),
                        'salesperson' => $salesperson->getId()
                    ];

                    $overridePrice = $this->entityManager->getRepository(PriceOverride::class)->findOneBy($params);

                    $updated = false;

                    if (empty($restItem['overrideprice'])) {

                        //delete it
                        $this->entityManager->remove($overridePrice);
                        $updated = true;
                    } else {

                        //edit with new price
                        if (strcmp($overridePrice->getOverrideprice(), $restItem['overrideprice']) != 0) {


                            $overridePrice->setOverrideprice($restItem['overrideprice']);

                            $this->entityManager->merge($overridePrice);
                            $updated = true;
                        }
                    }

                    if ($updated) {

                        $this->entityManager->flush();
                    }

                    break; //not necessary, but good practice.
                }
            default : //fall-through
        }

        return $overridePrice;
    }

    private function createProduct(array $restItem, Customer $customer, User $salesperson) {

        //create the Product
        $product = new Product();
        $product->setId($restItem['id']);
        $product->setDescription($restItem['shortescription']);
        $product->setProductname($restItem['productname']);
        $product->setQty($restItem['qty']);
        $product->setRetail($restItem['retail']);
        $product->setSaturdayenabled($restItem['saturdayenabled'] ? true : false);
        $product->setSku($restItem['sku']);
        $product->setStatus($restItem['status'] ? true : false);
        $product->setUom($restItem['uom']);
        $product->setWholesale($restItem['wholesale']);

        //create Checkbox
        $checkbox = new Checkbox();
        $checkbox->setChecked(FALSE);
        $checkbox->setCustomer($customer);
        $checkbox->setProduct($product);

        //associate Checkbox with Salesperson
        $checkbox->setSalesperson($salesperson);

        //associate Checkbox with Product
        $checkboxes = $product->getCheckboxes();
        $checkboxes->add($checkbox);
        $product->setCheckboxes($checkboxes);

        $userProducts = $customer->getProducts();

        if (empty($userProducts)) {
            $userProducts = new ArrayCollection();
        }
        
        $userProducts->add($product);

        $customer->setProducts($userProducts);

        if ($this->isDebug()) {
            $this->logger->log(Logger::INFO, "Creating Product[ "
                    . "id = {$product->getId()}, "
                    . "sku = {$product->getSku()}, "
                    . "customer.company = {$customer->getCompany()}, "
                    . "customer.name = {$customer->getName()}"
                    . "]");
        }
        $this->entityManager->merge($customer);
        $this->entityManager->merge($salesperson);
        $this->entityManager->persist($product);
        $this->entityManager->persist($checkbox);
        return $product;
    }

    private function rest($url, $method = "GET", $params = []) {
        return $this->restService->rest($url, $method, $params);
    }

    private function log($msg, $info = Logger::INFO) {
        $this->logger->log($info, $msg);
    }

}
