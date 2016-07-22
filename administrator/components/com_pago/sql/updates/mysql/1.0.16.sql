CREATE TABLE IF NOT EXISTS `#__pago_discount_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_rule_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `#__pago_discount_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_rule_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `#__pago_discount_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_use_per_user` int(11) NOT NULL,
  `discount_type` int(11) NOT NULL,
  `discount_amount` float NOT NULL,
  `discount_event` int(11) NOT NULL,
  `discount_filter` int(11) NOT NULL,
  `discount_filter_value` float NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;