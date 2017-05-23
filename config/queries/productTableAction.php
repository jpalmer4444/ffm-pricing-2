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
        array('db' => 'ITEM.`overrideprice`', 'dt' => 8),
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
    )
    ];
