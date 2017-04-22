<?php

return [
    
    "columns" => array(
        array('db' => 'checked', 'dt' => 0),
        array('db' => 'id', 'dt' => 1),
        array('db' => 'productname', 'dt' => 2),
        array('db' => 'description', 'dt' => 3),
        array('db' => 'comment', 'dt' => 4),
        array('db' => 'option', 'dt' => 5),
        array('db' => 'wholesale', 'dt' => 6),
        array('db' => 'retail', 'dt' => 7),
        array('db' => 'overrideprice', 'dt' => 8),
        array('db' => 'uom', 'dt' => 9),
        array('db' => 'status', 'dt' => 10),
        array('db' => 'saturdayenabled', 'dt' => 11),
        array('db' => 'sku', 'dt' => 12),
        array('db' => 'id', 'dt' => 13),
    ),
    
    "columnsPre" => array(
        array('db' => '`item_table_checkbox`.`checked`', 'dt' => 0),
        array('db' => '`products`.`id`', 'dt' => 1),
        array('db' => '`products`.`productname`', 'dt' => 2),
        array('db' => '`products`.`description`', 'dt' => 3),
        array('db' => '`user_product_preferences`.`comment`', 'dt' => 4),
        array('db' => '`user_product_preferences`.`option`', 'dt' => 5),
        array('db' => '`products`.`wholesale`', 'dt' => 6),
        array('db' => '`products`.`retail`', 'dt' => 7),
        array('db' => '`item_price_override`.`overrideprice`', 'dt' => 8),
        array('db' => '`products`.`uom`', 'dt' => 9),
        array('db' => '`products`.`status`', 'dt' => 10),
        array('db' => '`products`.`saturdayenabled`', 'dt' => 11),
        array('db' => '`products`.`sku`', 'dt' => 12),
        array('db' => 'null', 'dt' => 13),
    ),
    
    'columnsPost' => array(
        array('db' => '`item_table_checkbox`.`checked`', 'dt' => 0),
        array('db' => '`added_product`.`id`', 'dt' => 1),
        array('db' => '`added_product`.`productname`', 'dt' => 2),
        array('db' => '`added_product`.`description`', 'dt' => 3),
        array('db' => '`added_product`.`comment`', 'dt' => 4),
        array('db' => 'null', 'dt' => 5), //this would be option, but it doesnt exist in this query
        array('db' => 'null', 'dt' => 6), //this would be wholesale, but it doesnt exist in this query
        array('db' => 'null', 'dt' => 7), //this would be retail, but it doesnt exist in this query
        array('db' => '`added_product`.`overrideprice`', 'dt' => 8),
        array('db' => '`added_product`.`uom`', 'dt' => 9),
        array('db' => '`added_product`.`status`', 'dt' => 10),
        array('db' => 'null', 'dt' => 11), //this would be saturdayenabled, but it doesnt exist in this query
        array('db' => '`added_product`.`sku`', 'dt' => 12),
        array('db' => 'null', 'dt' => 13),
    ),
    
    'selectPre' => "SELECT "
    . "         IFNULL(item_table_checkbox.checked, 0) 		as 'checked', "
    . "         CONCAT('P', products.id) 			as 'id', "
    . "         products.sku					as 'sku', "
    . "         products.productname				as 'productname', "
    . "         products.description				as 'description', "
    . "         products.wholesale				as 'wholesale', "
    . "         products.retail					as 'retail', "
    . "         products.uom					as 'uom', "
    . "         products.`status`				as 'status', "
    . "         products.saturdayenabled			as 'saturdayenabled', "
    . "         item_price_override.overrideprice		as 'overrideprice', "
    . "         `user_product_preferences`.`comment`		as 'comment', "
    . "         `user_product_preferences`.`option`		as 'option' "
    . "             FROM item_table_checkbox "
    . "                 RIGHT JOIN products ON item_table_checkbox.product = products.id "
    . "                 LEFT JOIN customer_product ON products.id = customer_product.product "
    . "                 LEFT JOIN item_price_override "
    . "                     ON (products.id = item_price_override.product AND "
    . "                         customer_product.customer = item_price_override.customer AND "
    . "                         item_price_override.salesperson = item_table_checkbox.salesperson AND "
    . "                         item_price_override.active = 1) "
    . "                 LEFT JOIN user_customer "
    . "                     ON (customer_product.customer = user_customer.customer_id) "
    . "                 LEFT JOIN user_product_preferences "
    . "                     ON (products.id = user_product_preferences.product_id AND "
    . "                         user_customer.user_id = user_product_preferences.user_id AND "
    . "                         user_customer.customer_id = user_product_preferences.customer_id) ",
    
    'selectCountPre' => "SELECT count(DISTINCT(products.id)) "
    . "     FROM item_table_checkbox "
    . "         RIGHT JOIN products ON item_table_checkbox.product = products.id "
    . "         LEFT JOIN customer_product ON products.id = customer_product.product "
    . "         LEFT JOIN user_customer ON (customer_product.customer = user_customer.customer_id) "
    . "         LEFT JOIN user_product_preferences "
    . "                     ON (products.id = user_product_preferences.product_id AND "
    . "                         user_customer.user_id = user_product_preferences.user_id AND "
    . "                         user_customer.customer_id = user_product_preferences.customer_id) "
    . "         LEFT JOIN item_price_override "
    . "             ON ( products.id = item_price_override.product AND "
    . "             customer_product.customer = item_price_override.customer AND "
    . "             item_price_override.salesperson = item_table_checkbox.salesperson AND "
    . "             item_price_override.active = 1) ",
    
    'selectPost' => "
            SELECT
                IFNULL(`item_table_checkbox`.`checked`, 0)      as 'checked',
                CONCAT('A', `added_product`.`id`)         as 'id',
                `added_product`.`sku`                     as 'sku',
                `added_product`.`productname`             as 'productname',
                `added_product`.`description`             as 'description',
                (select null)                               as 'wholesale',
                (select null)                               as 'retail',
                `added_product`.`uom`                     as 'uom',
                `added_product`.`status`                as 'status',
                (select 1)                                  as 'saturdayenabled',
                `added_product`.`overrideprice`           as 'overrideprice',
                `added_product`.`comment`              as 'comment',
                (select null)                               as 'option'
            FROM `item_table_checkbox`
                RIGHT JOIN `added_product`
                ON `added_product`.`id` = `item_table_checkbox`.`added_product` ",
    
    'selectCountPost' => "SELECT count(DISTINCT(item_table_checkbox.id)) "
                . "     FROM item_table_checkbox "
                . "         RIGHT JOIN added_product "
                . "             ON added_product.id = item_table_checkbox.added_product",
    
    'skuSelect' => "SELECT `products`.`sku` as sku, `products`.`uom` as uom, `products`.`productname` as product FROM `item_table_checkbox` RIGHT JOIN `products` ON `item_table_checkbox`.`product` = `products`.`id`  LEFT JOIN `customer_product` ON `products`.`id` = `customer_product`.`product` LEFT JOIN `item_price_override` ON (`products`.`id` = `item_price_override`.`product` AND `customer_product`.`customer` = `item_price_override`.`customer` AND `item_price_override`.`salesperson` = `item_table_checkbox`.`salesperson` AND `item_price_override`.`active` = 1) LEFT JOIN `user_customer` ON (`customer_product`.`customer` = `user_customer`.`customer_id`) LEFT JOIN `user_product_preferences` ON (`products`.`id` = `user_product_preferences`.`product_id` AND `user_customer`.`user_id` = `user_product_preferences`.`user_id` AND `user_customer`.`customer_id` = `user_product_preferences`.`customer_id`) WHERE `customer_product`.`customer` = ? AND `user_customer`.`user_id` = ?  UNION ALL SELECT `added_product`.`sku` as sku, `added_product`.`uom` as uom, `added_product`.`productname` as product FROM `item_table_checkbox` RIGHT JOIN `added_product` ON `added_product`.`id` = `item_table_checkbox`.`added_product` WHERE `added_product`.`customer` = ? AND `added_product`.`active` = 1"
];
