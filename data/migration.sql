use customer_pricing_20170419T183023Z;

# DIRECTIONS
# 1. Run the SQL between (STEP ONE START|END).
#       At this point we have the basic DB structure in place as well as:
#       users, roles, user_role, permissions, and role_permission tables populated.
#
# 2. Now we need to let the web-app populate our products, customers, user_product_preferences, 
#    user_customer, customer_product, and item_table_checkbox tables.
#       A. Now open a browser and navigate to:
#           https://pricingv2.ffmalpha.com
#       
#       B. If you are forwarded to Salespeople or Customers page please click top-right User icon --> click Logout.
#          This will force the correct session id to be written to DB to avoid mismatches because we just
#          dropped and recreated all tables but your browser might have an old cookie and I want to avoid that.
#       
#       C. Login with production credentials for an admin --> You will be forwarded to Salespeople page.
#
#       D. Click on every salesperson navigating back and forth between Salespeople and Customers until you 
#          have viewed all Customers for each Salesperson.
#
#       E. Now we need to run the SQL between (STEP TWO START|END).
#          The first 2 queries will identify any missed Products that still need to be populated for the migration to work.

# STEP ONE START.

DROP TABLE IF EXISTS `error_log`;
DROP TABLE IF EXISTS `item_table_checkbox`;
DROP TABLE IF EXISTS `item_price_override`;
DROP TABLE IF EXISTS `pricing_override_report`;
DROP TABLE IF EXISTS `customer_added_product`;
DROP TABLE IF EXISTS `user_product_preferences`;
DROP TABLE IF EXISTS `user_customer`;
DROP TABLE IF EXISTS `customer_product`;
DROP TABLE IF EXISTS `user_products`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `added_product`;
DROP TABLE IF EXISTS `row_plus_items_page`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `user_role`;
drop table if exists `role_permission`;
drop table if exists `permissions`;
drop table if exists `roles`;
DROP TABLE IF EXISTS `user_sessions`;
DROP TABLE IF EXISTS `user_role_xref`;
DROP TABLE IF EXISTS `users`;


CREATE TABLE `users` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `version` INTEGER DEFAULT 1,
  `email` VARCHAR(128) NOT NULL,
  `username` VARCHAR(128) NOT NULL,
  `full_name` VARCHAR(512) NOT NULL,
  `password` VARCHAR(256) NOT NULL,
  `status` BOOLEAN NOT NULL,
  `date_created` TIMESTAMP NOT NULL,
  `pwd_reset_token` VARCHAR(32) DEFAULT NULL,
  `pwd_reset_token_creation_date` datetime DEFAULT NULL,
  `salespersonname` VARCHAR(100) DEFAULT NULL,
  `phone1` varchar(100) NOT NULL,
  `sales_attr_id` INTEGER DEFAULT NULL,
  `last_login` TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `KEY_USERS_SALESPERSONNAME` (`salespersonname`),
  UNIQUE KEY `UNIQKEY_USERS_SALES_ATTR_ID` (`sales_attr_id`),
  UNIQUE KEY `UNIQKEY_USERS_EMAIL` (`email`),
  UNIQUE KEY `UNIQKEY_USERS_USERNAME` (`username`)
);

CREATE TABLE `user_sessions` (
  `user_id` int(11) NOT NULL,
  `version` int(11) DEFAULT '1',
  `session_id` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`session_id`,`user_agent`),
  KEY `KEY_USER_SESSIONS_SESSION` (`session_id`),
  KEY `KEY_USER_SESSIONS_USER_AGENT` (`user_agent`),
  CONSTRAINT `FK_USER_SESSIONS_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQKEY_ROLES_NAME` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `KEY_USER_ROLE_USER` (`user_id`),
  KEY `KEY_USER_ROLE_ROLE` (`role_id`),
  CONSTRAINT `FK_USER_ROLE_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_USER_ROLE_ROLE` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQKEY_PERMISSIONS_NAME` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `KEY_ROLE_PERMISSIONS_ROLE` (`role_id`),
  KEY `KEY_ROLE_PERMISSIONS_PERMISSION` (`permission_id`),
  CONSTRAINT `FK_ROLE_PERMISSIONS_ROLE` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ROLE_PERMISSIONS_PERMISSION` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# dhError table
CREATE TABLE `error_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference` varchar(6) COLLATE utf8_unicode_ci DEFAULT '',
  `type` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'ERROR',
  `priority` varchar(6) COLLATE utf8_unicode_ci DEFAULT 'DEBUG',
  `message` text COLLATE utf8_unicode_ci,
  `file` text COLLATE utf8_unicode_ci,
  `line` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `trace` text COLLATE utf8_unicode_ci,
  `xdebug` text COLLATE utf8_unicode_ci,
  `uri` text COLLATE utf8_unicode_ci,
  `request` text COLLATE utf8_unicode_ci,
  `ip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `version` int(11) DEFAULT '1',
  `email` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `company` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `KEY_CUSTOMERS_CUSTOMERS` (`email`),
  KEY `KEY_CUSTOMERS_NAME` (`name`),
  KEY `KEY_CUSTOMERS_COMPANY` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*
*/
CREATE TABLE `user_customer` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`customer_id`),
  KEY `KEY_USER_CUSTOMER_USER` (`user_id`),
  KEY `KEY_USER_CUSTOMER_CUSTOMER` (`customer_id`),
  CONSTRAINT `FK_USER_CUSTOMER_USER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_USER_CUSTOMER_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `version` int(11) DEFAULT '1',
  `sku` varchar(25) DEFAULT NULL,
  `productname` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `wholesale` decimal(22,2) DEFAULT NULL,
  `retail` decimal(22,2) DEFAULT NULL,
  `uom` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `saturdayenabled` tinyint(1) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_PRODUCTS_SKU` (`sku`),
  KEY `FK_PRODUCTS_PRODUCTNAME` (`productname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `item_price_override` (
  `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
  `version` INTEGER DEFAULT 1,
  `product` INTEGER NOT NULL,
  `overrideprice` decimal(22,2) DEFAULT NULL,
  `active` BOOLEAN DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer` INTEGER NOT NULL,
  `salesperson` INTEGER NOT NULL,
  KEY `KEY_ITEM_PRICE_OVERRIDE_SALESPERSON` (`salesperson`),
  KEY `KEY_ITEM_PRICE_OVERRIDE_PRODUCT` (`product`),
  KEY `KEY_ITEM_PRICE_OVERRIDE_CUSTOMER` (`customer`),
  CONSTRAINT `FK_ITEM_PRICE_OVERRIDE_SALESPERSON` FOREIGN KEY (`salesperson`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_ITEM_PRICE_OVERRIDE_PRODUCT` FOREIGN KEY (`product`) REFERENCES `products` (`id`),
  CONSTRAINT `FK_ITEM_PRICE_OVERRIDE_CUSTOMER` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `added_product` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `version` INTEGER DEFAULT 1,
  `overrideprice` decimal(22,2) DEFAULT NULL,
  `active` BOOLEAN DEFAULT NULL,
  `sku` VARCHAR(25) DEFAULT NULL,
  `productname` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `comment` VARCHAR(255) DEFAULT NULL,
  `uom` VARCHAR(100) NOT NULL,
  `status` BOOLEAN DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer` INTEGER NOT NULL,
  `salesperson` INTEGER NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_ADDED_PRODUCT_SALESPERSON` (`salesperson`),
  KEY `KEY_ADDED_PRODUCT_CUSTOMER` (`customer`),
  CONSTRAINT `FK_ADDED_PRODUCT_SALESPERSON` FOREIGN KEY (`salesperson`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_ADDED_PRODUCT_CUSTOMER` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `item_table_checkbox` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `version` INTEGER DEFAULT 1,
  `product` INTEGER DEFAULT NULL,
  `added_product` INTEGER DEFAULT NULL,
  `checked` BOOLEAN DEFAULT '0',
  `customer` INTEGER NOT NULL,
  `salesperson` INTEGER NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `KEY_ITEM_TABLE_CHECKBOX_PRODUCT` (`product`),
  KEY `KEY_ITEM_TABLE_CHECKBOX_ADDED_PRODUCT` (`added_product`),
  KEY `KEY_ITEM_TABLE_CHECKBOX_SALESPERSON` (`salesperson`),
  KEY `KEY_ITEM_TABLE_CHECKBOX_CUSTOMER` (`customer`),
  CONSTRAINT `FK_ITEM_TABLE_CHECKBOX_PRODUCT` FOREIGN KEY (`product`) REFERENCES `products` (`id`),
  CONSTRAINT `FK_ITEM_TABLE_CHECKBOX_ADDED_PRODUCT` FOREIGN KEY (`added_product`) REFERENCES `added_product` (`id`),
  CONSTRAINT `FK_ITEM_TABLE_CHECKBOX_SALESPERSON` FOREIGN KEY (`salesperson`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_ITEM_TABLE_CHECKBOX_CUSTOMER` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `pricing_override_report` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `version` INTEGER DEFAULT 1,
  `product` INTEGER DEFAULT NULL,
  `added_product` INTEGER DEFAULT NULL,
  `overrideprice` decimal(22,2) DEFAULT NULL,
  `retail` decimal(22,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer` INTEGER NOT NULL,
  `salesperson` INTEGER NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_PRICING_OVERRIDE_REPORT_SALESPERSON` (`salesperson`),
  KEY `KEY_PRICING_OVERRIDE_REPORT_PRODUCT` (`product`),
  KEY `KEY_PRICING_OVERRIDE_REPORT_CUSTOMER` (`customer`),
  CONSTRAINT `FK_KEY_PRICING_OVERRIDE_REPORT__SALESPERSON` FOREIGN KEY (`salesperson`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_KEY_PRICING_OVERRIDE_REPORT__PRODUCT` FOREIGN KEY (`product`) REFERENCES `products` (`id`),
  CONSTRAINT `FK_KEY_PRICING_OVERRIDE_REPORT__CUSTOMER` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `customer_product` (
  `customer` INTEGER NOT NULL DEFAULT '0',
  `product` INTEGER NOT NULL DEFAULT '0',
  PRIMARY KEY (`customer`,`product`),
  CONSTRAINT `FK_CUSTOMER_PRODUCT_PRODUCT` FOREIGN KEY (`product`) REFERENCES `products` (`id`),
  CONSTRAINT `FK_CUSTOMER_PRODUCT_CUSTOMER` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `customer_added_product` (
  `customer` INTEGER NOT NULL,
  `added_product` INTEGER NOT NULL,
  PRIMARY KEY (`customer`,`added_product`),
  CONSTRAINT `FK_CUSTOMER_ADDED_PRODUCT_ADDED_PRODUCT` FOREIGN KEY (`added_product`) REFERENCES `added_product` (`id`),
  CONSTRAINT `FK_CUSTOMER_ADDED_PRODUCT_CUSTOMER` FOREIGN KEY (`customer`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_product_preferences` (
  `user_id` INTEGER NOT NULL DEFAULT '0',
  `product_id` INTEGER NOT NULL DEFAULT '0',
  `version` INTEGER DEFAULT '1',
  `comment` VARCHAR(255) DEFAULT NULL,
  `option` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`product_id`),
  CONSTRAINT `FK_USER_PRODUCT_PREFERENCES_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `FK_USER_PRODUCT_PREFERENCES_CUSTOMER` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (1, 'jpalmer',24,'$2y$10$BaoRbZVUPtpZlhRJxd2dYeXEGf71LshO2AFWs6xlfYqKb6v5DgTjC',null,'jpalmer@fultonfishmarket.com','630-999-0139',null,'2017-03-03 18:44:10','2016-12-06 13:09:50', 1, 'Jason Palmer');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (2, 'foobarx',1,'$2y$10$BaoRbZVUPtpZlhRJxd2dYeXEGf71LshO2AFWs6xlfYqKb6v5DgTjC','Foo Bar X','foobarx@fultonfishmarket.com','802-233-9957',247,'2016-12-06 13:09:50','2016-12-06 13:09:50', 1, 'Foobar X');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (3, 'dtanzer',16,'$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', null,'dtanzer@fultonfishmarket.com','802-233-9957',null,'2017-03-01 22:24:39','2016-12-06 13:09:50', 1, 'David Tanzer');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (4, 'jdowns',32,'$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO',null,'jdowns@fultonfishmarket.com','802-238-1452',null,'2017-04-11 18:03:28','2016-12-06 13:09:50', 1, 'Jeff Downs');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (5, 'cmetallo',4,'$2y$10$oYayre7c1Ls9mMqNx4Cm0uJs5Dy9f1wESsD4aP2pKBzKNG8WrXVle','Cyndi Metallo','cmetallo@fultonfishmarket.com','847-809-6512',183,'2017-02-22 21:41:51','2016-12-06 13:09:50', 1, 'Cyndi Metallo');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (6, 'mspindler',1,'$2y$10$wMxYn7DCLOuW4Yyv48i1queAk5MjYBvDzM11uCF42qKUuQGVVEytW',null,'mspindler@fultonfishmarket.com','847-809-6512',null,'2016-12-06 13:09:50','2016-12-06 13:09:50', 1, 'Mike Spindler');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (7, 'bzakrinski',1,'$2y$10$gSZ7jGyuSHBeOrGwE.Pa5uuDlzpdp/VSXU5ObHNaGvhhRrI3I13hK','Bill Zakrinski','bzak@fultonfishmarket.com','347-680-2772',206,'2016-12-06 13:09:51','2016-12-06 13:09:51', 1, 'Bill Zakrinski');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (8, 'iderfler',1,'$2y$10$jTgKbfE6bqcivt4fqdVmFufvLoEX0mgtAKbg8g9ejBUnhKB2/GBxW','Iris Derfler','iderfler@fultonfishmarket.com','847-606-2555',181,'2016-12-06 13:09:51','2016-12-06 13:09:51', 1, 'Iris Derfler');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (9, 'jmeade',1024,'$2y$10$e5On29MiGz.ctu8zFMVz9.kPx98ZarMlG11ub4O2ilKpjppkBxnHm','Jody Meade','jody@fultonfishmarket.com','570-335-6484',180,'2017-04-18 18:32:56','2016-12-06 13:09:51', 1, 'Jody Meade');
INSERT INTO `users` (`id`, `username`,`version`,`password`,`salespersonname`,`email`,`phone1`,`sales_attr_id`,`last_login`,`date_created`, `status`, `full_name`) VALUES (10, 'dbacon',378,'$2y$10$IaYd4efN4b.lyxRP1dIwq.qNYpnwgqNCPjt.oTB5NI6HUZO2kjkCm','David Bacon','dbacon@fultonfishmarket.com','',250,'2017-04-17 23:07:13','2016-12-21 00:42:56', 1, 'David Bacon');


# ROLES
INSERT INTO `roles` (`id`, `name`) VALUES(1, 'admin');
INSERT INTO `roles` (`id`, `name`) VALUES(2, 'user');

# USER_ROLE
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 1);# admin jpalmer
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 3);# admin dtanzer
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 4);# admin jdowns
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 6);# admin mspindler
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 5);# admin cmetallo
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 7);# user bzakrinsky
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 2);# user foobarx
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 8);# user iderfler
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 9);# user jmeade
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 10);# user dbacon

# PERMISSIONS - Set them up.
# AuthController IS NOT PROTECTED.
# UserController->{add,changePassword,resetPassword,setPassword,view,edit,index,message}
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(1, 'user/add', 'Add User');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(2, 'user/changePassword', 'Change Password');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(3, 'user/resetPassword', 'Reset Password');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(4, 'user/setPassword', 'Set Password');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(5, 'user/view', 'View User');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(6, 'user/edit', 'Edit User');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(7, 'user/index', 'List Users');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(8, 'user/message', 'Message User');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(9, 'user/usersTable', 'Ajax Users Table');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(13, 'user/usersTableUpdateStatus', 'Users Table Update Status');

# IndexController
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(10, 'index/about', 'Index About Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(11, 'index/index', 'Index Index Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(12, 'index/settings', 'Index Settings Action');

# CustomerController
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(20, 'customer/view', 'Customer View Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(15, 'customer/customerTable', 'Customer Customer Table');

# SalespeopleController
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(16, 'salespeople/index', 'Salespeople Index Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(21, 'salespeople/add', 'Salespeople Add Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(17, 'salespeople/salespeopleTable', 'Salespeople Salespeople Table');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(27, 'salespeople/validateAddSalesperson', 'Salespeople ValidateAddSalesperson Validation');

# ProductController
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(18, 'product/view', 'Product View Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(22, 'product/checked', 'Product Checked Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(23, 'product/product', 'Product Product Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(25, 'product/report', 'Product Report Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(24, 'product/override', 'Product Override Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(19, 'product/productTable', 'Product Product Table');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(26, 'product/productFormTypeahead', 'Product ProductForm Typeahead');

# Admin Permissions
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 1); # admin user/add (Add User)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 2); # admin user/changePassword (Change Password)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 3); # admin user/resetPassword (Reset Password)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 4); # admin user/setPassword (Set Password)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 5); # admin user/view (View User)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 6); # admin user/edit (Edit User)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 7); # admin user/index (List Users)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 8); # admin user/message (Message User)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 9); # admin user/usersTable (Ajax Users Table Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 10); # admin index/about (About Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 11); # admin index/index (Index Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 12); # admin index/settings (Settings Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 13); # admin user/usersTableUpdateStatus (Users Table Update Status)

INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 20); # admin customer/view (Customer View Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 15); # admin customer/customerTable (Customer Customer Table)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 16); # admin salespeople/index (Salespeople Index Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 21); # admin salespeople/add (Salespeople Add Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 17); # admin salespeople/salespeopleTable (Salespeople Salespeople Table)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 18); # admin product/view (Product View Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 22); # admin product/checked (Product Checked Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 23); # admin product/product (Product Product Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 24); # admin product/override (Product Override Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 25); # admin product/report (Product Report Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 19); # admin product/productTable (Product Product Table)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 26); # admin product/productFormTypeahead (Product ProductForm Typeahead)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 27); # admin salespeople/validateAddSalesperson (Salespeople ValidateAddSalesperson Validation)

# User Permissions
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 9);    # sales user/usersTable (Ajax Users Table Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 10);   # sales index/about (About Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 11);   # sales index/index (Index Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 12);   # sales index/settings (Settings Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 6);    # sales user/edit (Edit User)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 20);   # sales customer/view (Customer View Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 15);   # sales customer/customerTable (Customer Customer Table)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 18);   # sales product/view (Product View Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 22);   # sales product/checked (Product Checked Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 23);   # sales product/product (Product Product Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 24);   # sales product/override (Product Override Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 25);   # sales product/report (Product Report Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 19);   # sales product/productTable (Product Product Table)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 26);   # sales product/productFormTypeahead (Product ProductForm Typeahead)

# STEP ONE End.
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
# STEP TWO Start.
# Now Browse webapp until these queres return zero rows:
SELECT itc.salesperson as 'Salesperson', c.name as 'Customer Name', p.productname as 'Product Name' 
    FROM customer_pricing.products p
    LEFT JOIN customer_pricing.item_table_checkbox itc ON p.id = itc.product
    LEFT JOIN customer_pricing.customers c ON itc.customerid = c.id
    WHERE itc.product NOT IN (SELECT id FROM customer_pricing_20170419T183023Z.products);
    
SELECT por.salesperson as 'Salesperson', c.name as 'Customer Name', p.productname as 'Product Name' 
    FROM customer_pricing.products p
    LEFT JOIN customer_pricing.pricing_override_report por ON p.id = por.product
    LEFT JOIN customer_pricing.customers c ON por.customerid = c.id
    WHERE por.product NOT IN (SELECT id FROM customer_pricing_20170419T183023Z.products);
    
SELECT rpip.salesperson as 'Salesperson', c.name as 'Customer Name' 
    FROM customer_pricing.row_plus_items_page rpip
    LEFT JOIN customer_pricing.customers c ON rpip.customerid = c.id
    WHERE rpip.customerid NOT IN (SELECT id FROM customer_pricing_20170419T183023Z.customers);

# STEP TWO End. 
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
# STEP THREE Start.
# When the above queries are returning zero rows

# INSERT ALL ITEM_PRICE_OVERRIDE rows from V1.
# Column name changed from customerid to customer.
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]
use `customer_pricing_20170419T183023Z`;
INSERT INTO `customer_pricing_20170419T183023Z`.`item_price_override` (
        `id`, `version`, `product`, `overrideprice`, `active`, `created`, `customer`, `salesperson`
) (
	SELECT 
            `item_price_override`.`id`, `item_price_override`.`version`, (
                SELECT id FROM `customer_pricing_20170419T183023Z`.`products` WHERE sku = (
                    SELECT sku FROM `customer_pricing`.`products` WHERE id = `item_price_override`.`product`
                ) 
            ), 
            `item_price_override`.`overrideprice`, `item_price_override`.`active`, `item_price_override`.`created`, 
            `item_price_override`.`customerid`, 
            (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = `item_price_override`.`salesperson`
                )
            )
        FROM `customer_pricing`.`item_price_override`
);

# INSERT all row_plus_items_page rows into added_product table.
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

# When Products are populated from the DB an item_table_checkbox row is created for each one. We must DELETE those 
# we are going to override. At this point - we have browsed the web-app and forced the rows to be inserted into 
# item_table_checkbox. In V2 every product belonging to every customer has an item_table_checkbox row, in V1 there are 
# only item_table_checkbox rows for Products that have been "checked" at least once. In order to deal with this mismatch 
# we need to delete any rows that exist in the item_table_checkbox table from V1.
# DELETE ITC2.* FROM `customer_pricing_20170419T183023Z`.`item_table_checkbox` ITC2 
#    LEFT JOIN `customer_pricing`.`item_table_checkbox` ITC1
#        ON  ITC2.product = ITC1.product
#            AND ITC2.salesperson = (
#                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
#                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = ITC1.salesperson
#                )
#            )

#           AND ITC2.customer = ITC1.customerid
#           AND ITC2.added_product = ITC1.row_plus_items_page_id;

# drop any existing temp tables from earlier in the session
# DROP TABLE IF EXISTS `item_table_checkbox_temp`;

# temp table to hold all the rows that are left in the item_table_checkbox table. After we have inserted all the rows found in
# item_table_checkbox V1 - we will need to iterate this temporary table and re-insert them. This was how I chose to deal with
# DUPLICATE key problems I had when I was just leaving the rows in the item_table_checkbox table. This way when we iterate the 
# rows in the temp table - we INSERT them one by one into the truncated item_table_checkbox table, but we DO NOT include the 
# AUTO-INCREMENT id column - which allows the DB to re-generate the id column. And this does not hurt us in any way because id
# column from item_table_checkbox IS NOT used as FK in any table.
# CREATE TEMPORARY TABLE IF NOT EXISTS `item_table_checkbox_temp` AS (
#    SELECT * FROM `customer_pricing_20170419T183023Z`.`item_table_checkbox`
# );

# now truncate the table
DELETE FROM `customer_pricing_20170419T183023Z`.`item_table_checkbox`;

# INSERT all rows from V1 item_table_checkbox into truncated V2 item_table_checkbox
# Column name changed from customerid to customer
# Column name changed from row_plus_items_page_id to added_product
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]


# INSERT all rows from V2 item_table_checkbox_temp into V2 item_table_checkbox.
# Drop the id column to allow DB to regenerate AUTOINCREMENT column id to avoid
# DUPLICATE key errors.
# INSERT INTO `customer_pricing_20170419T183023Z`.`item_table_checkbox` (
#        `version`, `product`, `added_product`, `checked`, `customer`, `salesperson`, `created`
#    ) (
#	SELECT 
#            `item_table_checkbox_temp`.`version`, 
#            `item_table_checkbox_temp`.`product`,
#            `item_table_checkbox_temp`.`added_product`, 
#            `item_table_checkbox_temp`.`checked`, 
#            `item_table_checkbox_temp`.`customer`, 
#            `item_table_checkbox_temp`.`salesperson`, 
#            `item_table_checkbox_temp`.`created` 
#            FROM `customer_pricing_20170419T183023Z`.`item_table_checkbox_temp`
);

# INSERT all rows from pricing_override_report
# Column name changed from customerid to customer
# Column name changed from row_plus_items_page_id to added_product
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]
INSERT INTO `customer_pricing_20170419T183023Z`.`pricing_override_report` (
        `version`, `product`, `added_product`, `overrideprice`, `retail`, `customer`, `salesperson`, `created`
    ) (
	SELECT 
            `pricing_override_report`.`version`, (SELECT id FROM `customer_pricing_20170419T183023Z`.`products` WHERE sku = (SELECT sku FROM `customer_pricing`.`products` WHERE id = `pricing_override_report`.`product`) ), 
            `pricing_override_report`.`row_plus_items_page_id`, `pricing_override_report`.`overrideprice`, 
            # We need to select the ID of the salesperson that holds sales_attr_id that matches
            # `pricing_override_report`.`retail`, `pricing_override_report`.`customerid`, (
            #    SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE username = (`pricing_override_report`.`salesperson`)
            # ), 
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

INSERT INTO `customer_pricing_20170419T183023Z`.`item_table_checkbox` (
        `version`, `product`, `added_product`, `checked`, `customer`, `salesperson`, `created`
    ) (
	SELECT 
            `item_table_checkbox`.`version`, 
            (
                SELECT id FROM `customer_pricing_20170419T183023Z`.`products` WHERE sku = (
                    SELECT sku FROM `customer_pricing_20170419T183023Z`.`products` WHERE id = `item_table_checkbox`.`product`) 
            ), 
            (
                SELECT id FROM `customer_pricing_20170419T183023Z`.`added_product` WHERE productname = (
                    SELECT productname FROM `customer_pricing`.`row_plus_items_page` WHERE id = `item_table_checkbox`.`row_plus_items_page_id`) 
            ),
            `item_table_checkbox`.`checked`, 
            `item_table_checkbox`.`customerid`, 
            (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = `item_table_checkbox`.`salesperson`
                )
            ),
            `item_table_checkbox`.`created` 
            FROM `customer_pricing`.`item_table_checkbox`
);


# STEP THREE End.