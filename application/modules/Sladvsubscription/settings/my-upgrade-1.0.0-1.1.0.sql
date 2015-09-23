ALTER TABLE  `engine4_authorization_levels` ADD  `order` INT NOT NULL;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sladvsubscription_admin_main_order', 'sladvsubscription', 'Order Levels', '', '{"route":"admin_default","module":"sladvsubscription","controller":"settings","action":"order"}', 'sladvsubscription_admin_main', '', 2);