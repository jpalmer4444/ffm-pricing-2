use `pricing_skeleton`;

DROP TABLE IF EXISTS `item_table_checkbox`;
DROP TABLE IF EXISTS `row_plus_items_page`;
DROP TABLE IF EXISTS `item_price_override`;
DROP TABLE IF EXISTS `pricing_override_report`;
DROP TABLE IF EXISTS `user_products`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `user_role`;
drop table if exists `role_permission`;
drop table if exists `permissions`;
drop table if exists `roles`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` INTEGER DEFAULT 1,
  `email` varchar(128) NOT NULL,
  `full_name` varchar(512) NOT NULL,
  `password` varchar(256) NOT NULL,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `pwd_reset_token` varchar(32) DEFAULT NULL,
  `pwd_reset_token_creation_date` datetime DEFAULT NULL,
  `salespersonname` varchar(100) NOT NULL,
  `phone1` varchar(100) NOT NULL,
  `sales_attr_id` integer NOT NULL,
  `last_login` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_idx` (`email`)
);

CREATE INDEX index_users_salespersonname
ON users (salespersonname);

CREATE INDEX index_users_sales_attr_id
ON users (sales_attr_id);

CREATE INDEX cmp_index_users_salespersonname_sales_attr_id
ON users (salespersonname, sales_attr_id);

CREATE INDEX cmp_index_users_username_salespersonname_sales_attr_id
ON users (email, salespersonname, sales_attr_id);

INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(1, 'Jason Palmer', 1, NOW(), '$2y$10$BaoRbZVUPtpZlhRJxd2dYeXEGf71LshO2AFWs6xlfYqKb6v5DgTjC', 'Jason Palmer', 183, 'jpalmer@meadedigital.com', '630-999-0139');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(2, 'Foo Bar', 1, NOW(), '$2y$10$BaoRbZVUPtpZlhRJxd2dYeXEGf71LshO2AFWs6xlfYqKb6v5DgTjC', 'Foo Bar X', 247, 'foobar@fultonfishmarket.com', '802-233-9957');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(3, 'David Tanzer', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Cyndi Metallo', 183, 'dtanzer@fultonfishmarket.com', '802-233-9957');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1)
VALUES(4, 'Jeff Downs', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Cyndi Metallo', 183, 'jdowns@fultonfishmarket.com', '802-238-1452');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(5, 'Cyndi Metallo', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Cyndi Metallo', 183, 'cmetallo@fultonfishmarket.com', '847-809-6512');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(6, 'Mike Spindler', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Cyndi Metallo', 183, 'mspindler@fultonfishmarket.com', '847-809-6512');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(7, 'Bill Zakrinski', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Bill Zakrinski', 206, 'bzak@fultonfishmarket.com', '347-680-2772');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(8, 'Iris Derfler', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Iris Derfler', 181, 'iderfler@fultonfishmarket.com', '847-606-2555');
INSERT INTO users (id, full_name, status, date_created, password, salespersonname, sales_attr_id, email, phone1) 
VALUES(9, 'Jody Meade', 1, NOW(), '$2y$11$dNgq1cOKM4hEhuML8rwZD.XY195yLIz.i0.cnn92/EtnY2vl1PGrO', 'Jody Meade', 180, 'jody@fultonfishmarket.com', '570-335-6484');

CREATE TABLE `pricing_2`.`user_sessions` (
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`session_id`,`user_agent`),
  KEY `IDX_1DE7C6A3A76ED395` (`session_id`),
  KEY `IDX_1DE7C6A3D60322AC` (`user_agent`),
  CONSTRAINT `FK_2DE8C6A2A76DD395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B63E2EC75E237E06` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `roles` (`id`, `name`) 
VALUES(1, 'admin');

INSERT INTO `roles` (`id`, `name`) 
VALUES(2, 'user');

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`),
  CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 1);# admin jpalmer
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 3);# admin dtanzer
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 4);# admin jdowns
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 6);# admin mspindler
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(1, 5);# admin cmetallo
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 7);# user bzakrinsky
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 2);# user foobarx
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 8);# user iderfler
INSERT INTO `user_role` (`role_id`, `user_id`) VALUES(2, 9);# user jmeade

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2DEDCC6F5E237E06` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

# IndexController
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(10, 'index/about', 'About Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(11, 'index/index', 'Index Action');
INSERT INTO `permissions` (`id`, `name`, `title`) VALUES(12, 'index/settings', 'Settings Action');


CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `IDX_6F7DF886D60322AC` (`role_id`),
  KEY `IDX_6F7DF886FED90CCA` (`permission_id`),
  CONSTRAINT `FK_6F7DF886D60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6F7DF886FED90CCA` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 10); # adminindex/about (About Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 11); # admin index/index (Index Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(1, 12); # admin index/settings (Settings Action)

# User Permissions
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 9); # sales user/usersTable (Ajax Users Table Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 10); # sales index/about (About Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 11); # sales index/index (Index Action)
INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES(2, 12); # sales index/settings (Settings Action)