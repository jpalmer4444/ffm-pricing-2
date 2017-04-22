use customer_pricing_20170419T183023Z;

# DIRECTIONS
# 1. Run the SQL between (STEP ONE START|END).
#       At this point we have the basic DB structure in place as well as:
#       users, roles, user_role, permissions, and role_permission tables populated.
#

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
    `customer_id` INTEGER NOT NULL DEFAULT '0',
  `version` INTEGER DEFAULT '1',
  `comment` VARCHAR(255) DEFAULT NULL,
  `option` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`product_id`, `customer_id`),
  CONSTRAINT `FK_USER_PRODUCT_PREFERENCES_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `FK_USER_PRODUCT_PREFERENCES_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
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

# This Product is no longer being returned from the Web Service.
# For the Web-App this is not a problem, but for the migration we need it
# for referential integrity. Here - we are removing any references. This should not be
# a problem because the Product is no longer returned and if it pops up again - it will
# be populated automatically.

# STEP ONE End.
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
#          IMPORTANT - You must view the Products page at least once for any Customer before running the next step
#          If you do not - then the queries give incorrect results.
#
#       E. Now we need to run the SQL between (STEP TWO START|END).
#          The first 2 queries will identify any missed Products that still need to be populated for the migration to work.
#          I found it very easy to order by Salesperson then choose the Salesperson you want in the webapp. Then 
#          you can see in your query results what Customers products are missing from the DB (based on the query results).
#          you MUST click on each Customer and view their Products. You will need to keep going back and forth in the 
#          browser working your way through the query results. Because Products are re-used for all Customers - after you
#          have browsed back and forth and viewed Products for a few of the Customers - you should re-execute the query to
#          update the results and see which Products are still missing. This process took me about 20 minutes. Once you have 
#          both of the first 2 queries returning zero results - you can move onto the 3rd query, which just verifies that we 
#          have all of our Customers needed.
#

use `customer_pricing_20170419T183023Z`;

# Iterate customers
INSERT INTO `customer_pricing_20170419T183023Z`.`customers` (id, version, email, `name`, company, created, updated) (
    SELECT 
            `customers`.`id`, 
            `customers`.`version`,
            `customers`.`email`, 
            `customers`.`name`, 
            `customers`.`company`, 
            `customers`.`created`, 
            `customers`.`updated`
        FROM `customer_pricing`.`customers`
);

# Iterate products
INSERT INTO `customer_pricing_20170419T183023Z`.`products` (
id, version, sku, `productname`, description, qty, wholesale, retail, uom, `status`, saturdayenabled, created, updated) (
    SELECT 
            `products`.`id`, 
            `products`.`version`,
            `products`.`sku`, 
            `products`.`productname`, 
            `products`.`description`, 
            `products`.`qty`, 
            `products`.`wholesale`,
            `products`.`retail`,
            `products`.`uom`,
            `products`.`status`,
            `products`.`saturdayenabled`,
            `products`.`created`,
            `products`.`updated`
        FROM `customer_pricing`.`products`
);

INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,46);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,391);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,397);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,398);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,399);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,400);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,401);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,567);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,568);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,581);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,606);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,694);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,698);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,713);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,743);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,746);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,758);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,833);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,842);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,944);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,989);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1019);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1036);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1053);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1057);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1062);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1063);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1064);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1065);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1073);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1172);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1193);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1216);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1239);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1243);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1463);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1466);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1469);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1493);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1577);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1628);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1663);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1683);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1761);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1838);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1879);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,1996);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,2080);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,2151);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (5,2210);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,849);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1122);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1123);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1124);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1126);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1127);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1128);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1130);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1131);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1133);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1135);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1136);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1138);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1139);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1144);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1151);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1153);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1154);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1164);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1175);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1176);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1184);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1185);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1186);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1188);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1204);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1214);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1215);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1238);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1259);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1270);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1280);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1351);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1360);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1402);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1514);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1630);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1632);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1651);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1825);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1844);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,1935);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,2017);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,2020);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,2056);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,2057);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (7,2283);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,49);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,51);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,154);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,159);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,192);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,220);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,258);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,278);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,286);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,295);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,330);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,335);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,337);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,342);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,367);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,380);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,395);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,419);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,423);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,434);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,440);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,479);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,493);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,500);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,531);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,548);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,555);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,561);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,562);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,564);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,565);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,566);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,598);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,602);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,607);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,608);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,615);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,616);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,621);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,622);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,623);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,630);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,640);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,641);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,642);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,643);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,652);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,658);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,666);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,667);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,677);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,684);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,686);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,688);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,692);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,704);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,706);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,718);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,727);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,733);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,734);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,736);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,737);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,742);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,744);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,750);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,759);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,763);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,764);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,781);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,796);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,797);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,806);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,810);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,831);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,838);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,839);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,840);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,847);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,851);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,852);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,855);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,861);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,868);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,872);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,882);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,884);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,919);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,920);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,927);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,935);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,938);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,945);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,948);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,960);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,962);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,965);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,971);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,992);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,994);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1014);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1021);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1041);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1046);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1054);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1055);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1084);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1103);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1113);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1117);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1134);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1147);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1149);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1157);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1160);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1161);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1173);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1174);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1182);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1183);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1209);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1212);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1242);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1344);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1345);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1374);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1377);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1384);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1417);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1461);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1490);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1545);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1569);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1598);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1619);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1629);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1631);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1650);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1658);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1661);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1687);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1924);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1930);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1955);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1969);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,1974);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,2001);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,2053);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,2074);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (8,2077);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,47);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,53);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,117);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,203);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,237);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,242);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,243);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,244);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,309);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,445);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,483);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,507);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,528);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,532);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,536);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,545);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,552);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,560);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,563);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,569);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,578);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,579);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,587);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,589);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,590);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,591);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,592);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,594);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,595);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,596);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,597);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,599);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,600);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,604);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,605);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,620);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,624);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,625);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,644);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,646);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,647);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,657);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,665);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,668);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,678);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,685);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,689);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,700);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,703);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,707);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,712);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,714);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,715);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,716);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,719);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,721);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,722);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,723);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,728);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,730);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,731);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,735);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,741);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,747);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,748);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,749);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,760);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,773);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,774);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,775);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,776);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,814);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,815);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,816);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,817);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,818);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,822);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,824);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,825);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,827);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,830);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,832);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,834);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,835);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,836);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,856);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,859);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,860);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,865);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,866);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,874);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,892);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,893);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,894);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,896);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,897);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,898);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,913);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,924);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,925);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,928);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,929);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,933);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,952);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,954);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,955);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,957);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,963);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,964);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,966);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,972);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,974);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,980);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,981);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,985);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,986);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1004);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1035);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1059);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1075);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1093);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1094);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1095);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1096);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1107);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1112);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1159);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1179);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1205);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1207);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1217);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1231);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1232);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1235);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1236);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1248);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1261);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1265);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1305);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1311);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1313);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1383);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1458);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1460);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1518);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1547);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1641);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1647);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1689);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1690);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1726);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1730);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1896);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1905);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1917);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1940);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,1942);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2012);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2016);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2023);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2046);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2047);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2049);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2106);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2107);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2113);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2114);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2125);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2128);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2130);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2131);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2132);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2133);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2178);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2217);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (9,2221);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1649);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1675);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1695);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1704);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1707);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1724);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1735);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1767);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1768);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1776);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1779);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1781);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1786);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1810);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1834);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1836);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1845);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1878);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1893);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1910);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1916);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1922);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1928);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1929);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1933);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1936);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1938);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1956);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1972);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,1985);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2008);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2013);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2018);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2029);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2048);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2054);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2092);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2093);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2172);
INSERT INTO `user_customer` (`user_id`,`customer_id`) VALUES (10,2280);

# Iterate user_products renamed to customer_product
# INSERT INTO `customer_pricing_20170419T183023Z`.`customer_product` (
#    customer, product) (
#    SELECT 
#            `user_products`.`customer`, 
#            `user_products`.`product`
#        FROM `customer_pricing`.`user_products`
#);



# INSERT ALL ITEM_PRICE_OVERRIDE rows from V1.
# Column name changed from customerid to customer.
# Column salesperson datatype changed VARCHAR(100) [M:1 users.username] to INTEGER [M:1 users.id]
INSERT INTO `customer_pricing_20170419T183023Z`.`item_price_override` (
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
        FROM `customer_pricing`.`item_price_override`
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


INSERT INTO `customer_pricing_20170419T183023Z`.`item_table_checkbox` (
        `version`, `product`, `added_product`, `checked`, `customer`, `salesperson`
    ) (
	SELECT 
            `item_table_checkbox`.`version`, 
            `item_table_checkbox`.`product`,
            `item_table_checkbox`.`row_plus_items_page_id`,
            `item_table_checkbox`.`checked`,
            `item_table_checkbox`.`customerid`,
            (
                SELECT id from `customer_pricing_20170419T183023Z`.`users` WHERE sales_attr_id = (
                    SELECT sales_attr_id FROM `customer_pricing`.`users` WHERE username = `item_table_checkbox`.`salesperson`
                )
            )
            FROM `customer_pricing`.`item_table_checkbox`
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


INSERT IGNORE INTO `customer_pricing_20170419T183023Z`.`user_product_preferences` (
    user_id, product_id, version, comment, `option`) (
    SELECT 
            (SELECT `user_id` FROM `customer_pricing_20170419T183023Z`.`user_customer` WHERE customer_id = `user_products`.`customer`), 
            `user_products`.`product`,
            `user_products`.`version`,
            `user_products`.`comment`,
            `user_products`.`option`
        FROM `customer_pricing`.`user_products`
);