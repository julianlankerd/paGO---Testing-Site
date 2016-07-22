ALTER TABLE `#__pago_attr_opts` DROP `preselected`;
ALTER TABLE `#__pago_attr` DROP `required`;
ALTER TABLE `#__pago_product_varation` ADD `preselected` TINYINT NOT NULL DEFAULT '0' ;