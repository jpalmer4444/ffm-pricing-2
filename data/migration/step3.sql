use customer_pricing_20170419T183023Z;

# STEP THREE Start.

# INSERT ALL ITEM_PRICE_OVERRIDE rows from V1.
# Column name changed from customerid to customer.
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]
INSERT IGNORE INTO `customer_pricing_20170419T183023Z`.`item_price_override` (
        `version`, `product`, `overrideprice`, `active`, `created`, `customer`, `salesperson`
) (
	SELECT 
            `item_price_override`.`version`, 
            `item_price_override`.`product`,
            `item_price_override`.`overrideprice`, 
            `item_price_override`.`active`, 
            `item_price_override`.`created`, 
            `item_price_override`.`customerid`, 
            (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = `item_price_override`.`salesperson`
                )
            ) 
        FROM `customer_pricing`.`item_price_override` WHERE `item_price_override`.`active` = 1
);

# Table name has changed from row_plus_items_page to added_product
# Column name changed from customerid to customer.
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]
INSERT INTO `customer_pricing_20170419T183023Z`.`added_product` (
        `id`, `version`, `overrideprice`, `active`, `sku`, `productname`, `description`, 
        `comment`, `uom`, `status`, `created`, `customer`, `salesperson`
    ) (
	SELECT 
            `row_plus_items_page`.`id`, `row_plus_items_page`.`version`, `row_plus_items_page`.`overrideprice`, 
            `row_plus_items_page`.`active`, `row_plus_items_page`.`sku`, `row_plus_items_page`.`productname`, 
            `row_plus_items_page`.`description`, `row_plus_items_page`.`comment`, `row_plus_items_page`.`uom`, 
            `row_plus_items_page`.`status`, `row_plus_items_page`.`created`, `row_plus_items_page`.`customerid`, 
            (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = `row_plus_items_page`.`salesperson`
                )
            ) 
            FROM `customer_pricing`.`row_plus_items_page`
);

# New Table
INSERT INTO `customer_pricing_20170419T183023Z`.`customer_added_product` (
        `customer`, `added_product`
    ) (
	SELECT 
            `row_plus_items_page`.`customerid`, `row_plus_items_page`.`id` 
            FROM `customer_pricing`.`row_plus_items_page`
);

# Now we update our checkboxes
# Important Note - big difference between V1 and V2 V2 creates a checkbox row for every item when the item is created.
# whereas V1 only creates the checkbox row after the Product has been "checked" one time and not before. That is why we leave the 
# item_table_checkbox rows that are generated from browsing the web-app then we UPDATE any matching rows from V1.
UPDATE `customer_pricing_20170419T183023Z`.`item_table_checkbox` NITC, 
	`customer_pricing`.`item_table_checkbox` OITC
	SET NITC.`checked` = OITC.`checked`
		WHERE NITC.`product` = OITC.`product` AND 
			NITC.`customer` = OITC.`customerid` AND
			NITC.`salesperson` = (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = OITC.`salesperson`
                )
            );

# INSERT all rows from pricing_override_report
# Column name changed from customerid to customer
# Column name changed from row_plus_items_page_id to added_product
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]
INSERT INTO `customer_pricing_20170419T183023Z`.`pricing_override_report` (
        `version`, `product`, `added_product`, `overrideprice`, `retail`, `customer`, `salesperson`, `created`
    ) (
	SELECT 
            `pricing_override_report`.`version`, 
            `pricing_override_report`.`product`,
            `pricing_override_report`.`row_plus_items_page_id`, 
            `pricing_override_report`.`overrideprice`, 
            `pricing_override_report`.`retail`, 
            `pricing_override_report`.`customerid`, 
            (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = `pricing_override_report`.`salesperson`
                )
            ), 
            `pricing_override_report`.`created` 
            FROM `customer_pricing`.`pricing_override_report`
);
# STEP THREE End.

# Now you navigate to production V1:
# https://pricing.fultonfishmarket.com/

# navigate to staging
# https://pricingv2.ffmalpha.com/

# Compare matching products checkbox selection. Because production is a live changing environment the 
# number of Products on the 2 lists can be different, but if you compare any matching products - if it
# is "checked" in production - it will now be checked in staging.