# CURRENT BAD SQL
(SELECT          
        IFNULL(`item_table_checkbox`.`checked`, 0) 		as 'checked',          
        CONCAT('P', `products`.`id`)                            as 'id',          
        `products`.`sku`					as 'sku',          
        `products`.`productname`				as 'productname',          
        `products`.`description`				as 'description',          
        `products`.`wholesale`                                  as 'wholesale',          
        `products`.`retail`					as 'retail',          
        `products`.`uom`					as 'uom',          
        `products`.`status`                                     as 'status',          
        `products`.`saturdayenabled`                            as 'saturdayenabled',          
        `item_price_override`.`overrideprice`                   as 'overrideprice',          
        `user_product_preferences`.`comment`                    as 'comment',          
        `user_product_preferences`.`option`                     as 'option'               
            FROM `products`                  
                LEFT JOIN `item_table_checkbox`                      
                    ON (                         
                        `item_table_checkbox`.`product` = `products`.`id` AND                          
                        `item_table_checkbox`.`customer` = 1910 AND                          
                        `item_table_checkbox`.`salesperson` = 10                         
                        )                  
                LEFT JOIN `customer_product`                      
                    ON (                         
                        `products`.`id` = `customer_product`.`product` AND                          
                        1910 = `customer_product`.`customer`                         
                        )                  
                LEFT JOIN `item_price_override`                      
                    ON (                         
                        `products`.`id` = `item_price_override`.`product` AND                          
                        `customer_product`.`customer` = `item_price_override`.`customer` AND                          
                        `item_price_override`.`salesperson` = `item_table_checkbox`.`salesperson` AND                          
                        `item_price_override`.`active` = 1                         
                        )                  
                LEFT JOIN `user_customer`                      
                    ON (                         
                        `customer_product`.`customer` = `user_customer`.`customer_id` AND                          
                        10 = `user_customer`.`user_id`                         
                        )                  
                LEFT JOIN `user_product_preferences`                      
                    ON (                         
                        `products`.`id` = `user_product_preferences`.`product_id` AND                          
                        `user_customer`.`user_id` = `user_product_preferences`.`user_id` AND                          
                        `user_customer`.`customer_id` = `user_product_preferences`.`customer_id`                         
                        )  
                
                    WHERE `customer_product`.`customer` = 1910 AND `user_customer`.`user_id` = 10
) 

UNION 

(SELECT                
        IFNULL(`item_table_checkbox`.`checked`, 0)              as 'checked',                
        CONCAT('A', `added_product`.`id`)                       as 'id',                
        `added_product`.`sku`                                   as 'sku',                
        `added_product`.`productname`                           as 'productname',                
        `added_product`.`description`                           as 'description',                
        (select null)                                           as 'wholesale',                
        (select null)                                           as 'retail',                
        `added_product`.`uom`                                   as 'uom',                
        `added_product`.`status`                                as 'status',                
        (select 1)                                              as 'saturdayenabled',                
        `added_product`.`overrideprice`                         as 'overrideprice',                
        `added_product`.`comment`                               as 'comment',                
        (select null)                                           as 'option'             
            FROM `added_product`                
                LEFT JOIN `item_table_checkbox`                    
                    ON (                        
                        `added_product`.`id` = `item_table_checkbox`.`added_product` AND                         
                        `added_product`.`salesperson` = 10     
                        )  
                        
                        WHERE `added_product`.`customer` = 1910 AND `added_product`.`active` = 1 
) 

ORDER BY `productname` ASC LIMIT 0, 250

# The problem with the preceding SQL is that IF the item_price_override table has multiple ACTIVE rows for a 
# product - it returns multiple rows for the same id. The SQL shoud be:


# FIXED JOIN that does not pick up any duplicate rows from item_price_override
(SELECT          
	IFNULL(`item_table_checkbox`.`checked`, 0) 		as 'checked',          
	CONCAT('P', `products`.`id`)                            as 'id',          
        `products`.`sku`					as 'sku',          
        `products`.`productname`				as 'productname',          
        `products`.`description`                                as 'description',          
        `products`.`wholesale`                                  as 'wholesale',          
        `products`.`retail`					as 'retail',          
        `products`.`uom`					as 'uom',          
        `products`.`status`                                     as 'status',          
        `products`.`saturdayenabled`                            as 'saturdayenabled',          
        ITEM.`overrideprice`                                    as 'overrideprice',          
        `user_product_preferences`.`comment`                    as 'comment',          
        `user_product_preferences`.`option`                     as 'option'               
		FROM `products`                  
			LEFT JOIN `item_table_checkbox`                      
				ON 
				(
                                    `item_table_checkbox`.`product` = `products`.`id` AND                          
                                    `item_table_checkbox`.`customer` = 1910 AND                          
                                    `item_table_checkbox`.`salesperson` = 10                         
                                )                  
			LEFT JOIN `customer_product`                      
				ON 
                                (                         
					`products`.`id` = `customer_product`.`product` AND                          
                                        1910 = `customer_product`.`customer`                         
                                )                          
                        LEFT JOIN (
            
                            SELECT ipo.id,
                                   ipo.product,
                                   ipo.customer,
                                   ipo.salesperson,
                                   ipo.overrideprice,
                                   ipo.active
                                        FROM item_price_override ipo
                                            ORDER BY ipo.created DESC LIMIT 1
         
                                ) ITEM
				ON 
                                (                         
                                    `products`.`id` = ITEM.`product` AND                          
                                    `customer_product`.`customer` = ITEM.`customer` AND                          
                                    ITEM.`salesperson` = `item_table_checkbox`.`salesperson` AND                          
                                    ITEM.`active` = 1                         
                                )                  
			LEFT JOIN `user_customer`                      
				ON 
                                (                         
					`customer_product`.`customer` = `user_customer`.`customer_id` AND                          
                                        10 = `user_customer`.`user_id`                         
                                )                  
			LEFT JOIN `user_product_preferences`                      
				ON 
                                (                         
					`products`.`id` = `user_product_preferences`.`product_id` AND                          
                                        `user_customer`.`user_id` = `user_product_preferences`.`user_id` AND                          
                                        `user_customer`.`customer_id` = `user_product_preferences`.`customer_id`                         
                                )  
                    
                            WHERE `customer_product`.`customer` = 1910 AND `user_customer`.`user_id` = 10
) 
    UNION 
(SELECT                
	IFNULL(`item_table_checkbox`.`checked`, 0)              as 'checked',                
        CONCAT('A', `added_product`.`id`)                       as 'id',                
        `added_product`.`sku`                     		as 'sku',                
        `added_product`.`productname`             		as 'productname',                
        `added_product`.`description`             		as 'description',                
        (select null)                                           as 'wholesale',                
        (select null)                                           as 'retail',                
        `added_product`.`uom`                     		as 'uom',                
        `added_product`.`status`                		as 'status',                
        (select 1)                                              as 'saturdayenabled',                
        `added_product`.`overrideprice`           		as 'overrideprice',                
        `added_product`.`comment`              			as 'comment',                
        (select null)                                           as 'option'             
		FROM `added_product`                
			LEFT JOIN `item_table_checkbox`                    
				ON 
                                (                        
                                    `added_product`.`id` = `item_table_checkbox`.`added_product` AND                         
                                    `added_product`.`salesperson` = 10     
				)  
                
                                WHERE `added_product`.`customer` = 1910 AND `added_product`.`active` = 1 
) 
                
                ORDER BY `productname` ASC LIMIT 0, 250

# AND the Item Price Override modal dialog should also be adjusted to never create more than one ACTIVE item_price_override to be safe. 
# This is especially important because I am pretty sure there are other places where we assume there is only 1 ACTIVE item_price_override 
# at a time.