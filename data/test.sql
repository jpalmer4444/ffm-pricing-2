# Main Join for rows to display in table
(SELECT          
    IFNULL(item_table_checkbox.checked, 0) 		as 'checked',          
    CONCAT('P', products.id)                            as 'id',          
    products.sku					as 'sku',          
    products.productname				as 'productname',          
    products.description				as 'description',          
    products.wholesale                                  as 'wholesale',          
    products.retail					as 'retail',          
    products.uom					as 'uom',          
    products.`status`                                   as 'status',          
    products.saturdayenabled                            as 'saturdayenabled',          
    item_price_override.overrideprice                   as 'overrideprice',          
    `user_product_preferences`.`comment`		as 'comment',          
    `user_product_preferences`.`option`                 as 'option'              
        FROM products                  
            LEFT JOIN item_table_checkbox 
                ON item_table_checkbox.product = products.id                  
            LEFT JOIN customer_product ON products.id = customer_product.product                  
            LEFT JOIN item_price_override                      
                ON (products.id = item_price_override.product AND                          
                    customer_product.customer = item_price_override.customer AND                          
                    item_price_override.salesperson = item_table_checkbox.salesperson AND                          
                    item_price_override.active = 1)                  
            LEFT JOIN user_customer                      
                ON (customer_product.customer = user_customer.customer_id)                  
            LEFT JOIN user_product_preferences                      
                ON (products.id = user_product_preferences.product_id AND                          
                    user_customer.user_id = user_product_preferences.user_id AND                          
                    user_customer.customer_id = user_product_preferences.customer_id)  
                WHERE `customer_product`.`customer` = 203 AND `user_customer`.`user_id` = 9) 
UNION 
(SELECT                
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
        FROM `added_product`                
            LEFT JOIN `item_table_checkbox`                
                ON `added_product`.`id` = `item_table_checkbox`.`added_product`  
            WHERE `added_product`.`customer` = 203 AND `added_product`.`active` = 1 ) 
    ORDER BY `productname` ASC LIMIT 0, 250;

# Count Query to count total Filtered rows.
SELECT (SELECT count(DISTINCT(products.id))      FROM item_table_checkbox          RIGHT JOIN products ON item_table_checkbox.product = products.id          LEFT JOIN customer_product ON products.id = customer_product.product          LEFT JOIN user_customer ON (customer_product.customer = user_customer.customer_id)          LEFT JOIN user_product_preferences                      ON (products.id = user_product_preferences.product_id AND                          user_customer.user_id = user_product_preferences.user_id AND                          user_customer.customer_id = user_product_preferences.customer_id)          LEFT JOIN item_price_override              ON ( products.id = item_price_override.product AND              customer_product.customer = item_price_override.customer AND              item_price_override.salesperson = item_table_checkbox.salesperson AND              item_price_override.active = 1)  WHERE `customer_product`.`customer` = 203 AND `user_customer`.`user_id` = 9)+(SELECT count(DISTINCT(item_table_checkbox.id))      FROM item_table_checkbox          RIGHT JOIN added_product              ON added_product.id = item_table_checkbox.added_product WHERE `added_product`.`customer` = 203 AND `added_product`.`active` = 1 )

# Count Query to count total Unfiltered rows.
SELECT (SELECT count(DISTINCT(products.id))      FROM item_table_checkbox          RIGHT JOIN products ON item_table_checkbox.product = products.id          LEFT JOIN customer_product ON products.id = customer_product.product          LEFT JOIN user_customer ON (customer_product.customer = user_customer.customer_id)          LEFT JOIN user_product_preferences                      ON (products.id = user_product_preferences.product_id AND                          user_customer.user_id = user_product_preferences.user_id AND                          user_customer.customer_id = user_product_preferences.customer_id)          LEFT JOIN item_price_override              ON ( products.id = item_price_override.product AND              customer_product.customer = item_price_override.customer AND              item_price_override.salesperson = item_table_checkbox.salesperson AND              item_price_override.active = 1)  WHERE `customer_product`.`customer` = 203 AND `user_customer`.`user_id` = 9)+(SELECT count(DISTINCT(item_table_checkbox.id))      FROM item_table_checkbox          RIGHT JOIN added_product              ON added_product.id = item_table_checkbox.added_product WHERE `added_product`.`customer` = 203 AND `added_product`.`active` = 1 )