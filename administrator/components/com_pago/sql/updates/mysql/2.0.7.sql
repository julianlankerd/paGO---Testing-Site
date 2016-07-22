ALTER TABLE  `#__pago_items` ADD  `subscr_installments` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `sub_recur` ,
ADD  `subscr_enddate` VARCHAR( 20 ) NOT NULL AFTER  `subscr_installments` ;