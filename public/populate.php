<?php

$file = 'migrate_step_2.log';
$current = file_get_contents($file);
$current = '';
$host = "https://svc3.ffmalpha.com";
$productIds = [];

$servername = "ffm-db3b.ctmgcvu7ktrr.us-east-1.rds.amazonaws.com";
$username = "jpalmer";
$password = "basbun";
$dbname = "customer_pricing_20170419T183023Z";

/*
  ['247' =>'foobarx'],
  ['183' =>'cmetallo'],
  ['206' =>'bzakrinski'],
  ['181' =>'iderfler'],
  ['180' =>'jmeade'],
  ['250' =>'dbacon']

 */

$salesMap = [
    '2' => 247,
    '5' => 183,
    '7' => 206,
    '8' => 181,
    '9' => 180,
    '10' => 250
];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

function rest($method, $host, $url, $data = false) {
    $curl = curl_init();
    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // Optional Authentication:
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_URL, $host . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function insertCustomer($customer, $current, $conn) {

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        logme($current, "Connection Failed: " . $conn->connect_error);
        exit("Connection Failed!");
    } else {

        $sql = "INSERT IGNORE INTO customers (id, version, email, name, company)
            VALUES ('" . $customer["id"] . "', '1', '" . $customer["email"] . "', '" . $conn->real_escape_string($customer["name"]) . "', '" . $conn->real_escape_string($customer["company"]) . "')";

        if ($conn->query($sql) === TRUE) {
            //echo "customer record created successfully";
            echo "customer[customer=".$customer["id"].", name=".$customer["name"]."]\n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
    }
}

function insertUserCustomer($customer, $user_id, $current, $conn) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        logme($current, "Connection Failed: " . $conn->connect_error);
        exit("Connection Failed!");
    } else {

        $sql = "INSERT IGNORE INTO user_customer (user_id, customer_id)
            VALUES ('" . $user_id . "', '" . $customer["id"] . "')";

        if ($conn->query($sql) === TRUE) {
            //echo "user_customer record created successfully";
            echo "user_customer[user=".$user_id.", customer=".$customer["id"]."]\n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
    }
}

function insertProduct($product, $current, $conn) {

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        logme($current, "Connection Failed: " . $conn->connect_error);
        exit("Connection Failed!");
    } else {

        $status = ($product['status'] == 'Enabled' ? 1 : 0);

        $sql = "INSERT IGNORE INTO products (id, version, sku, productname, description, wholesale, retail, uom, status, saturdayenabled)
            VALUES ('" . $product["id"] . "', '1', '" . $product["sku"] . "', '" . $conn->real_escape_string($product["productname"]) . "', '" . $conn->real_escape_string($product["shortescription"]) . "', '" . $product["wholesale"] . "', '" . $product["retail"] . "', '" . $product["uom"] . "', '" . $status . "', '" . $product["saturdayenabled"] . "')";

        if ($conn->query($sql) === TRUE) {
            //echo "product record created successfully";
            echo "product[product=".$product["id"].", sku=".$product["sku"].", name=".$product["productname"]."]\n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
    }
}

function insertUserProductPreference($customerid, $product, $salespersonid, $current, $conn) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        logme($current, "Connection Failed: " . $conn->connect_error);
        exit("Connection Failed!");
    } else {

        $status = ($product['status'] == 'Enabled' ? 1 : 0);

        $sql = "INSERT IGNORE INTO user_product_preferences (user_id, product_id, customer_id, version, `comment`, `option`)
            VALUES ('" . $salespersonid . "', '" . $product["id"] . "', '" . $customerid . "', '1', '" . $conn->real_escape_string($product["comment"]) . "', '" . $conn->real_escape_string($product["option"]) . "')";

        if ($conn->query($sql) === TRUE) {
            //echo "product record created successfully";
            echo "user_product_preference[product=".$product["id"].", salesperson=".$salespersonid."]\n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
    }
}

function insertCustomerProduct($product, $customer, $current, $conn) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        logme($current, "Connection Failed: " . $conn->connect_error);
        exit("Connection Failed!");
    } else {

        $sql = "INSERT IGNORE INTO customer_product (customer, product)
            VALUES ('" . $customer["id"] . "', '" . $product["id"] . "')";

        if ($conn->query($sql) === TRUE) {
            //echo "customer_product record created successfully";
            echo "customer_product[product=".$product["id"].", customer=".$customer["id"]."]\n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
    }
}

function insertItemTableCheckbox($product, $customer, $user_id, $current, $conn) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        logme($current, "Connection Failed: " . $conn->connect_error);
        exit("Connection Failed!");
    } else {

        $status = ($product['status'] == 'Enabled' ? 1 : 0);

        $sql = "INSERT IGNORE INTO item_table_checkbox (version, product, checked, customer, salesperson)
            VALUES ('1', '" . $product["id"] . "', '0', '" . $customer["id"] . "', '" . $user_id . "')";

        if ($conn->query($sql) === TRUE) {
            echo "item_table_checkbox[product=".$product["id"].", customer=".$customer["id"].", user=".$user_id."]\n";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "\n";
        }
    }
}

function logme(& $cur, $msg = "") {
    $cur .= $msg . "\n";
}

//https://svc.localhost/bySKU.php?id=jpalmer&pw=goodbass&object=salespeople
//https://svc.localhost/bySKU.php?id=jpalmer&pw=goodbass&object=customers&salespersonid=206
//https://svc.localhost/bySKU.php?id=jpalmer&pw=goodbass&object=customerlistitems&customerid=1153

$result = rest('GET', $host, "/bySKU.php?id=jpalmer&pw=goodbass&object=salespeople");

logme($current, "Retrieving Salespeople from Web Service");
logme($current);
logme($current, print_r($result, TRUE));

$jsonResults = json_decode($result, true);

logme($current);
logme($current, "Retrieved " . count($jsonResults['salespeople']) . " Salespeople from Web Service");

logme($current, "Retrieving Customers from Web Service");
logme($current);

foreach ($jsonResults['salespeople'] as $salesperson) {

    //each salesperson we must lookup their Customers
    logme($current, "Retrieving Customers from Web Service for Salesperson " . $salesperson['salesperson']);
    logme($current);
    $customerResults = rest('GET', $host, "/bySKU.php?id=jpalmer&pw=goodbass&object=customers&salespersonid=" . $salesperson['id']);
    //logme($current);
    //logme($current, print_r($customerResults, TRUE));
    $jsonCustomerResults = json_decode($customerResults, true);

    foreach ($jsonCustomerResults['customers'] as $customer) {

        insertCustomer($customer, $current, $conn);
        insertUserCustomer($customer, array_search($salesperson['id'], $salesMap), $current, $conn);
        //customer, product, user_customer, user_product_preferences, item_table_checkbox, and customer_product


        logme($current, "Retrieving CustomerListItems from Web Service for Customer " . $customer['name'] . " with ID: " . $customer['id']);
        logme($current);
        $customerListItemResults = rest('GET', $host, "/bySKU.php?id=jpalmer&pw=goodbass&object=customerlistitems&customerid=" . $customer['id']);
        //logme($current);
        //logme($current, print_r($customerListItemResults, TRUE));
        $jsonCustomerListItemResults = json_decode($customerListItemResults, true);
        $products = [];
        foreach ($jsonCustomerListItemResults['customerlistitems'] as $product) {

            //if (!array_key_exists($product['id'], $products)) {
                insertProduct($product, $current, $conn);
                
                insertItemTableCheckbox($product, $customer, array_search($salesperson['id'], $salesMap), $current, $conn);
                
                insertUserProductPreference($customer['id'], $product, array_search($salesperson['id'], $salesMap), $current, $conn);
                
                insertCustomerProduct($product, $customer, $current, $conn);
            //}
        }
    }
}

logme($current, "Finished Inserting rows.");
logme($current);


file_put_contents($file, $current);

$conn->close();
?>
