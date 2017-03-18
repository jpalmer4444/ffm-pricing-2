# Begin joinStatement SSPJoin
SELECT
          IFNULL(item_table_checkbox.checked, 0) 				as 				'checked',
          CONCAT('P', products.id) 			   			as 				'id',
          products.sku								as				'sku',
          products.productname							as				'productname',
          products.description							as				'description',
          products.wholesale							as				'wholesale',
          products.retail							as				'retail',
          products.uom								as				'uom',
          products.`status`							as				'status',
          products.saturdayenabled						as				'saturdayenabled',
          item_price_override.overrideprice					as				'overrideprice'
          FROM item_table_checkbox
          RIGHT JOIN products
          ON item_table_checkbox.product = products.id
          LEFT JOIN user_products
          ON products.id = user_products.product
          LEFT JOIN item_price_override
          ON (
          products.id = item_price_override.product AND
          user_products.customer = item_price_override.customerid AND
          item_price_override.salesperson = item_table_checkbox.salesperson AND
          item_price_override.active = 1
          )
# End joinStatement SSPJoin

            WHERE user_products.customer = 1153


          UNION

          SELECT
          IFNULL(item_table_checkbox.checked, 0) 				as 				'checked',
          CONCAT('A', row_plus_items_page.id)                                   as 				'id',
          row_plus_items_page.sku						as 				'sku',
          row_plus_items_page.productname					as 				'productname',
          row_plus_items_page.description					as 				'description',
          (select null) 							as 				'wholesale',
          (select null)                                                         as 				'retail',
          row_plus_items_page.uom						as 				'uom',
          row_plus_items_page.`status`						as 				'status',
          (select 1) 								as 				'saturdayenabled',
          row_plus_items_page.overrideprice					as 				'overrideprice'
          FROM item_table_checkbox
          RIGHT JOIN row_plus_items_page
          ON row_plus_items_page.id = item_table_checkbox.row_plus_items_page_id
          
            WHERE row_plus_items_page.customerid = 1153 and row_plus_items_page.active = 1




# Count Query (Union)
# Begin joinStatementCount SSPJoin
SELECT COUNT(products.id)
          FROM item_table_checkbox
          RIGHT JOIN products
          ON item_table_checkbox.product = products.id
          LEFT JOIN user_products
          ON products.id = user_products.product
          LEFT JOIN item_price_override
          ON (
          products.id = item_price_override.product AND
          user_products.customer = item_price_override.customerid AND
          item_price_override.salesperson = item_table_checkbox.salesperson AND
          item_price_override.active = 1
          )
# End joinStatementCount SSPJoin

            WHERE user_products.customer = 1153


          
# Begin joinStatementCountUnion SSPJoin
          SELECT COUNT(row_plus_items_page.id) 
          FROM item_table_checkbox
          RIGHT JOIN row_plus_items_page
          ON row_plus_items_page.id = item_table_checkbox.row_plus_items_page_id
# End joinStatementCountUnion SSPJoin
          
            WHERE row_plus_items_page.customerid = 1153 and row_plus_items_page.active = 1

