ALTER TABLE `#__pago_items` ADD `ordering` INT NOT NULL DEFAULT '1' ;
ALTER TABLE `#__pago_items` CHANGE `price` `price` VARCHAR(100) NOT NULL;