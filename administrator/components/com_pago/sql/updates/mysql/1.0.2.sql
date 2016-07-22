ALTER TABLE `#__pago_items` ADD `qty_limit` INT NOT NULL DEFAULT '1' AFTER `currency`;
ALTER TABLE `#__pago_items` CHANGE `qty` `qty` INT NOT NULL DEFAULT '0';
ALTER TABLE `#__pago_product_varation` ADD `qty_limit` INT NOT NULL DEFAULT '1' AFTER `sku`;
ALTER TABLE `#__pago_product_varation` CHANGE `qty` `qty` INT(11) NOT NULL DEFAULT '0';