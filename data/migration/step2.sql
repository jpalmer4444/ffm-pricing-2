
# Must allow web-app to populate DB products. I know this step is frustrating, but it is necessary because 
# we do not have enough information in V1 to satisfy V2 data structure. This is caused by the fact that we used
# to call the Web Service every time the Products page rendered in V1. This meant we always knew which Customers
# belonged to which Salespeople. In V2 - we do not call the Web Service everytime the table renders - instead we 
# only call the Web Service when the page initially renders - then we call the DB by page after that - thereby making 
# it necessary to keep more information in the DB.

# Now Browse webapp until these queries return zero rows:
SELECT itc.salesperson as 'Salesperson', c.name as 'Customer Name', p.productname as 'Product Name' 
    FROM customer_pricing.products p
    LEFT JOIN customer_pricing.item_table_checkbox itc ON p.id = itc.product
    LEFT JOIN customer_pricing.customers c ON itc.customerid = c.id
    WHERE itc.product NOT IN (SELECT id FROM customer_pricing_20170419T183023Z.products) order by itc.salesperson;
    
SELECT por.salesperson as 'Salesperson', c.name as 'Customer Name', p.productname as 'Product Name' 
    FROM customer_pricing.products p
    LEFT JOIN customer_pricing.pricing_override_report por ON p.id = por.product
    LEFT JOIN customer_pricing.customers c ON por.customerid = c.id
    WHERE por.product NOT IN (SELECT id FROM customer_pricing_20170419T183023Z.products) order by por.salesperson;
    
SELECT rpip.salesperson as 'Salesperson', c.name as 'Customer Name' 
    FROM customer_pricing.row_plus_items_page rpip
    LEFT JOIN customer_pricing.customers c ON rpip.customerid = c.id
    WHERE rpip.customerid NOT IN (SELECT id FROM customer_pricing_20170419T183023Z.customers) order by rpip.salesperson;
