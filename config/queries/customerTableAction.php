<?php

return [
    "sql" => "SELECT 
            `customers`.`email`, 
            `customers`.`name`, 
            `customers`.`company` 
                FROM user_customer 
                    LEFT OUTER JOIN `customers` 
			ON user_customer.customer_id = customers.id 
				WHERE user_customer.user_id = ?"
];
