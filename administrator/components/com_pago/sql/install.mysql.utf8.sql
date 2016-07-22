-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 05, 2014 at 04:59 PM
-- Server version: 5.5.28
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pago_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_affiliates`
--

CREATE TABLE IF NOT EXISTS `#__pago_affiliates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `visitors` int(11) NOT NULL,
  `paytype` text NOT NULL,
  `payamount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `withdrawn` decimal(10,2) NOT NULL DEFAULT '0.00',
  KEY `affiliate_id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_affiliates_account`
--

CREATE TABLE IF NOT EXISTS `#__pago_affiliates_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `paid` date NOT NULL DEFAULT '0000-00-00',
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_affiliates_withdrawals`
--

CREATE TABLE IF NOT EXISTS `#__pago_affiliates_withdrawals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_id` (`affiliate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_attr`
--

CREATE TABLE IF NOT EXISTS `#__pago_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT NULL,
  `searchable` tinyint(1) NOT NULL,
  `global` tinyint(1) NOT NULL,
  `showfront` tinyint(1) NOT NULL,
  `display_type` tinyint(1) NOT NULL,
  `compare` tinyint(1) NOT NULL,
  `expiry_date` int(11) NOT NULL,
  `attr_enable` tinyint(4) NOT NULL DEFAULT '1',
  `for_item` int(11) NOT NULL DEFAULT '0',
  `required` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_attr_assign`
--

CREATE TABLE IF NOT EXISTS `#__pago_attr_assign` (
  `attribut_id` int(11) NOT NULL,
  `assign_type` tinyint(4) NOT NULL,
  `assign_items` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_attr_categories`
--

CREATE TABLE IF NOT EXISTS `#__pago_attr_categories` (
  `category_id` int(11) NOT NULL,
  `attribut_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_attr_opts`
--

CREATE TABLE IF NOT EXISTS `#__pago_attr_opts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attr_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `show_qty` tinyint(1) NOT NULL DEFAULT '0',
  `price_sign` tinyint(1) NOT NULL,
  `price_sum` varchar(50) NOT NULL DEFAULT '',
  `price_type` tinyint(1) NOT NULL,
  `in_stock` tinytext NOT NULL,
  `color` varchar(50) NOT NULL,
  `size` varchar(10) NOT NULL,
  `size_type` varchar(10) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL,
  `sku` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `expiry_date` int(11) NOT NULL,
  `opt_enable` tinyint(1) NOT NULL DEFAULT '1',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `for_item` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `#__pago_categoriesi`
--

CREATE TABLE IF NOT EXISTS `#__pago_categoriesi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `item_count` int(10) unsigned NOT NULL DEFAULT '0',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `access` tinyint(4) NOT NULL,
  `category_custom_layout` varchar(255) NOT NULL,
  `item_custom_layout` varchar(255) NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  `expiry_date` int(10) NOT NULL,
  `truncate_desc` int(11) NOT NULL DEFAULT '100',
   `category_view_display_items` tinyint(1) NOT NULL DEFAULT '0',
  `category_settings_category_title` tinyint(1) NOT NULL DEFAULT '1',
  `category_settings_product_counter` tinyint(1) NOT NULL DEFAULT '0',
  `category_settings_category_description` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_product_title` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_product_image` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_link_to_product` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_featured_badge` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_quantity_in_stock` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_short_desc` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_short_desc_limit` int(11) NOT NULL DEFAULT '50',
  `product_settings_desc` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_desc_limit` int(11) NOT NULL DEFAULT '100',
  `product_settings_sku` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_price` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_discounted_price` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_attribute` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_media` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_downloads` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_rating` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_category` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_read_more` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_add_to_cart` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_add_to_cart_qty` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_fb` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_tw` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_pinterest` tinyint(1) NOT NULL DEFAULT '0',
  `product_settings_google_plus` tinyint(1) NOT NULL DEFAULT '0',
  `product_grid_extra_small` tinyint(4) NOT NULL DEFAULT '1',
  `product_grid_small` tinyint(4) NOT NULL DEFAULT '2',
  `product_grid_medium` tinyint(4) NOT NULL DEFAULT '2',
  `product_grid_large` tinyint(4) NOT NULL DEFAULT '2',
  `product_view_settings_product_title` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_product_image` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_featured_badge` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_quantity_in_stock` tinyint(1) NOT NULL DEFAULT '0',
  `product_view_settings_short_desc` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_short_desc_limit` int(11) NOT NULL DEFAULT '50',
  `product_view_settings_desc` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_desc_limit` int(11) NOT NULL DEFAULT '100',
  `product_view_settings_sku` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_price` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_discounted_price` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_attribute` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_media` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_downloads` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_rating` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_category` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_add_to_cart` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_add_to_cart_qty` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_product_review` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_related_products` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_fb` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_tw` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_pinterest` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_google_plus` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_related_num_of_products` int(11) NOT NULL DEFAULT '5',
  `product_view_settings_related_title` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_related_category` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_related_image` tinyint(1) NOT NULL DEFAULT '1',
  `product_view_settings_related_short_text` tinyint(1) NOT NULL DEFAULT '1',
  `category_settings_image_settings` text NOT NULL,
  `category_settings_product_image_settings` text NOT NULL,
  `product_view_settings_image_settings` text NOT NULL,
  `category_settings_category_image` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_link_on_product_image` tinyint(1) NOT NULL DEFAULT '1',
  `product_settings_product_per_page` tinyint(4) NOT NULL DEFAULT '6',
  `product_settings_product_title_limit` tinytext NOT NULL,
  `product_view_settings_product_title_limit` tinytext NOT NULL,
  `product_view_settings_product_image_zoom` tinyint(4) NOT NULL,
	`inherit_parameters_from` INT(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_lft` (`lft`),
  KEY `idx_rgt` (`rgt`),
  KEY `idx_name` (`name`),
  KEY `idx_alias` (`alias`),
  KEY `idx_published` (`published`),
  KEY `idx_featured` (`featured`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__pago_categoriesi`
--
INSERT IGNORE INTO `#__pago_categoriesi` (`id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `name`, `alias`, `description`, `item_count`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `published`, `featured`, `access`, `category_custom_layout`, `item_custom_layout`, `visibility`, `expiry_date`, `truncate_desc`, `category_settings_category_title`, `category_settings_product_counter`, `category_settings_category_description`, `product_settings_product_title`, `product_settings_product_image`, `product_settings_link_to_product`, `product_settings_featured_badge`, `product_settings_quantity_in_stock`, `product_settings_short_desc`, `product_settings_short_desc_limit`, `product_settings_desc`, `product_settings_desc_limit`, `product_settings_sku`, `product_settings_price`, `product_settings_discounted_price`, `product_settings_attribute`, `product_settings_media`, `product_settings_downloads`, `product_settings_rating`, `product_settings_category`, `product_settings_read_more`, `product_settings_add_to_cart`, `product_settings_add_to_cart_qty`, `product_settings_fb`, `product_settings_tw`, `product_settings_pinterest`, `product_settings_google_plus`, `product_grid_extra_small`, `product_grid_small`, `product_grid_medium`, `product_grid_large`, `product_view_settings_product_title`, `product_view_settings_product_image`, `product_view_settings_featured_badge`, `product_view_settings_quantity_in_stock`, `product_view_settings_short_desc`, `product_view_settings_short_desc_limit`, `product_view_settings_desc`, `product_view_settings_desc_limit`, `product_view_settings_sku`, `product_view_settings_price`, `product_view_settings_discounted_price`, `product_view_settings_attribute`, `product_view_settings_media`, `product_view_settings_downloads`, `product_view_settings_rating`, `product_view_settings_category`, `product_view_settings_add_to_cart`, `product_view_settings_add_to_cart_qty`, `product_view_settings_product_review`, `product_view_settings_related_products`, `product_view_settings_fb`, `product_view_settings_tw`, `product_view_settings_pinterest`, `product_view_settings_google_plus`, `product_view_settings_related_num_of_products`, `product_view_settings_related_title`, `product_view_settings_related_category`, `product_view_settings_related_image`, `product_view_settings_related_short_text`, `category_settings_image_settings`, `category_settings_product_image_settings`, `product_view_settings_image_settings`, `category_settings_category_image`, `product_settings_link_on_product_image`, `product_settings_product_per_page`, `product_settings_product_title_limit`, `product_view_settings_product_title_limit`, `product_view_settings_product_image_zoom`) VALUES
(1, 0, 0, 23, 0, 'root', 'Parent category', 'root', 'Parent category', 6, 0, '0000-00-00 00:00:00', 492, '2014-06-13 17:45:15', 1, 0, 0, '', '', 1, 0, 100, 1, 0, 1, 1, 1, 1, 1, 1, 1, 50, 1, 100, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 4, 4, 4, 4, 1, 1, 1, 1, 1, 50, 1, 100, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"30","border_bottom":"1","margin_bottom":"0"}', '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"0","border_bottom":"1","margin_bottom":"0"}', '{"padding_left":"0","border_left":"1","margin_left":"0","margin_top":"0","border_top":"1","padding_top":"0","image_size":"3","padding_bottom":"0","padding_right":"0","border_right":"1","margin_right":"0","border_bottom":"1","margin_bottom":"20"}', 1, 1, 6, '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_categories_items`
--

CREATE TABLE IF NOT EXISTS `#__pago_categories_items` (
  `category_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  UNIQUE KEY `category_Id` (`category_id`,`item_id`),
  KEY `item_id` (`item_id`),
  KEY `category_id_2` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_config`
--

CREATE TABLE IF NOT EXISTS `#__pago_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `modified` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `#__pago_config`
--

INSERT IGNORE INTO `#__pago_config` (`id`, `modified`, `modified_by`, `name`, `params`) VALUES
(1, '2013-07-10 07:49:40', 0, 'dbversion', '1000'),
(2, '0000-00-00 00:00:00', 0, '', '1000');

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_cookie`
--

CREATE TABLE IF NOT EXISTS `#__pago_cookie` (
  `id` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `lastseen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` longtext NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_country`
--

CREATE TABLE IF NOT EXISTS `#__pago_country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL DEFAULT '1',
  `country_name` varchar(64) DEFAULT NULL,
  `country_3_code` char(3) DEFAULT NULL,
  `country_2_code` char(2) DEFAULT NULL,
  `publish` tinyint(4) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`country_id`),
  KEY `idx_country_name` (`country_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Country records' AUTO_INCREMENT=246 ;

--
-- Dumping data for table `#__pago_country`
--

INSERT IGNORE INTO `#__pago_country` (`country_id`, `zone_id`, `country_name`, `country_3_code`, `country_2_code`, `publish`, `params`) VALUES
(1, 1, 'Afghanistan', 'AFG', 'AF', 1, ''),
(2, 1, 'Albania', 'ALB', 'AL', 1, ''),
(3, 1, 'Algeria', 'DZA', 'DZ', 1, ''),
(4, 1, 'American Samoa', 'ASM', 'AS', 1, ''),
(5, 1, 'Andorra', 'AND', 'AD', 1, ''),
(6, 1, 'Angola', 'AGO', 'AO', 1, ''),
(7, 1, 'Anguilla', 'AIA', 'AI', 1, ''),
(8, 1, 'Antarctica', 'ATA', 'AQ', 1, ''),
(9, 1, 'Antigua and Barbuda', 'ATG', 'AG', 1, ''),
(10, 1, 'Argentina', 'ARG', 'AR', 1, ''),
(11, 1, 'Armenia', 'ARM', 'AM', 1, ''),
(12, 1, 'Aruba', 'ABW', 'AW', 1, ''),
(13, 1, 'Australia', 'AUS', 'AU', 1, ''),
(14, 1, 'Austria', 'AUT', 'AT', 1, ''),
(15, 1, 'Azerbaijan', 'AZE', 'AZ', 1, ''),
(16, 1, 'Bahamas', 'BHS', 'BS', 1, ''),
(17, 1, 'Bahrain', 'BHR', 'BH', 1, ''),
(18, 1, 'Bangladesh', 'BGD', 'BD', 1, ''),
(19, 1, 'Barbados', 'BRB', 'BB', 1, ''),
(20, 1, 'Belarus', 'BLR', 'BY', 1, ''),
(21, 1, 'Belgium', 'BEL', 'BE', 1, ''),
(22, 1, 'Belize', 'BLZ', 'BZ', 1, ''),
(23, 1, 'Benin', 'BEN', 'BJ', 1, ''),
(24, 1, 'Bermuda', 'BMU', 'BM', 1, ''),
(25, 1, 'Bhutan', 'BTN', 'BT', 1, ''),
(26, 1, 'Bolivia', 'BOL', 'BO', 1, ''),
(27, 1, 'Bosnia and Herzegowina', 'BIH', 'BA', 1, ''),
(28, 1, 'Botswana', 'BWA', 'BW', 1, ''),
(29, 1, 'Bouvet Island', 'BVT', 'BV', 1, ''),
(30, 1, 'Brazil', 'BRA', 'BR', 1, ''),
(31, 1, 'British Indian Ocean Territory', 'IOT', 'IO', 1, ''),
(32, 1, 'Brunei Darussalam', 'BRN', 'BN', 1, ''),
(33, 1, 'Bulgaria', 'BGR', 'BG', 1, ''),
(34, 1, 'Burkina Faso', 'BFA', 'BF', 1, ''),
(35, 1, 'Burundi', 'BDI', 'BI', 1, ''),
(36, 1, 'Cambodia', 'KHM', 'KH', 1, ''),
(37, 1, 'Cameroon', 'CMR', 'CM', 1, ''),
(38, 1, 'Canada', 'CAN', 'CA', 1, ''),
(39, 1, 'Cape Verde', 'CPV', 'CV', 1, ''),
(40, 1, 'Cayman Islands', 'CYM', 'KY', 1, ''),
(41, 1, 'Central African Republic', 'CAF', 'CF', 1, ''),
(42, 1, 'Chad', 'TCD', 'TD', 1, ''),
(43, 1, 'Chile', 'CHL', 'CL', 1, ''),
(44, 1, 'China', 'CHN', 'CN', 1, ''),
(45, 1, 'Christmas Island', 'CXR', 'CX', 1, ''),
(46, 1, 'Cocos (Keeling) Islands', 'CCK', 'CC', 1, ''),
(47, 1, 'Colombia', 'COL', 'CO', 1, ''),
(48, 1, 'Comoros', 'COM', 'KM', 1, ''),
(49, 1, 'Congo', 'COG', 'CG', 1, ''),
(50, 1, 'Cook Islands', 'COK', 'CK', 1, ''),
(51, 1, 'Costa Rica', 'CRI', 'CR', 1, ''),
(52, 1, 'Cote D''Ivoire', 'CIV', 'CI', 1, ''),
(53, 1, 'Croatia', 'HRV', 'HR', 1, ''),
(54, 1, 'Cuba', 'CUB', 'CU', 1, ''),
(55, 1, 'Cyprus', 'CYP', 'CY', 1, ''),
(56, 1, 'Czech Republic', 'CZE', 'CZ', 1, ''),
(57, 1, 'Denmark', 'DNK', 'DK', 1, ''),
(58, 1, 'Djibouti', 'DJI', 'DJ', 1, ''),
(59, 1, 'Dominica', 'DMA', 'DM', 1, ''),
(60, 1, 'Dominican Republic', 'DOM', 'DO', 1, ''),
(61, 1, 'East Timor', 'TMP', 'TP', 1, ''),
(62, 1, 'Ecuador', 'ECU', 'EC', 1, ''),
(63, 1, 'Egypt', 'EGY', 'EG', 1, ''),
(64, 1, 'El Salvador', 'SLV', 'SV', 1, ''),
(65, 1, 'Equatorial Guinea', 'GNQ', 'GQ', 1, ''),
(66, 1, 'Eritrea', 'ERI', 'ER', 1, ''),
(67, 1, 'Estonia', 'EST', 'EE', 1, ''),
(68, 1, 'Ethiopia', 'ETH', 'ET', 1, ''),
(69, 1, 'Falkland Islands (Malvinas)', 'FLK', 'FK', 1, ''),
(70, 1, 'Faroe Islands', 'FRO', 'FO', 1, ''),
(71, 1, 'Fiji', 'FJI', 'FJ', 1, ''),
(72, 1, 'Finland', 'FIN', 'FI', 1, ''),
(73, 1, 'France', 'FRA', 'FR', 1, ''),
(74, 1, 'France, Metropolitan', 'FXX', 'FX', 1, ''),
(75, 1, 'French Guiana', 'GUF', 'GF', 1, ''),
(76, 1, 'French Polynesia', 'PYF', 'PF', 1, ''),
(77, 1, 'French Southern Territories', 'ATF', 'TF', 1, ''),
(78, 1, 'Gabon', 'GAB', 'GA', 1, ''),
(79, 1, 'Gambia', 'GMB', 'GM', 1, ''),
(80, 1, 'Georgia', 'GEO', 'GE', 1, ''),
(81, 1, 'Germany', 'DEU', 'DE', 1, ''),
(82, 1, 'Ghana', 'GHA', 'GH', 1, ''),
(83, 1, 'Gibraltar', 'GIB', 'GI', 1, ''),
(84, 1, 'Greece', 'GRC', 'GR', 1, ''),
(85, 1, 'Greenland', 'GRL', 'GL', 1, ''),
(86, 1, 'Grenada', 'GRD', 'GD', 1, ''),
(87, 1, 'Guadeloupe', 'GLP', 'GP', 1, ''),
(88, 1, 'Guam', 'GUM', 'GU', 1, ''),
(89, 1, 'Guatemala', 'GTM', 'GT', 1, ''),
(90, 1, 'Guinea', 'GIN', 'GN', 1, ''),
(91, 1, 'Guinea-bissau', 'GNB', 'GW', 1, ''),
(92, 1, 'Guyana', 'GUY', 'GY', 1, ''),
(93, 1, 'Haiti', 'HTI', 'HT', 1, ''),
(94, 1, 'Heard and Mc Donald Islands', 'HMD', 'HM', 1, ''),
(95, 1, 'Honduras', 'HND', 'HN', 1, ''),
(96, 1, 'Hong Kong', 'HKG', 'HK', 1, ''),
(97, 1, 'Hungary', 'HUN', 'HU', 1, ''),
(98, 1, 'Iceland', 'ISL', 'IS', 1, ''),
(99, 1, 'India', 'IND', 'IN', 1, ''),
(100, 1, 'Indonesia', 'IDN', 'ID', 1, ''),
(101, 1, 'Iran (Islamic Republic of)', 'IRN', 'IR', 1, ''),
(102, 1, 'Iraq', 'IRQ', 'IQ', 1, ''),
(103, 1, 'Ireland', 'IRL', 'IE', 1, ''),
(104, 1, 'Israel', 'ISR', 'IL', 1, ''),
(105, 1, 'Italy', 'ITA', 'IT', 1, ''),
(106, 1, 'Jamaica', 'JAM', 'JM', 1, ''),
(107, 1, 'Japan', 'JPN', 'JP', 1, ''),
(108, 1, 'Jordan', 'JOR', 'JO', 1, ''),
(109, 1, 'Kazakhstan', 'KAZ', 'KZ', 1, ''),
(110, 1, 'Kenya', 'KEN', 'KE', 1, ''),
(112, 1, 'Korea, Democratic People''s Republic of', 'PRK', 'KP', 1, ''),
(113, 1, 'Korea, Republic of', 'KOR', 'KR', 1, ''),
(114, 1, 'Kuwait', 'KWT', 'KW', 1, ''),
(117, 1, 'Latvia', 'LVA', 'LV', 1, ''),
(118, 1, 'Lebanon', 'LBN', 'LB', 1, ''),
(120, 1, 'Liberia', 'LBR', 'LR', 1, ''),
(121, 1, 'Libyan Arab Jamahiriya', 'LBY', 'LY', 1, ''),
(122, 1, 'Liechtenstein', 'LIE', 'LI', 1, ''),
(123, 1, 'Lithuania', 'LTU', 'LT', 1, ''),
(124, 1, 'Luxembourg', 'LUX', 'LU', 1, ''),
(125, 1, 'Macau', 'MAC', 'MO', 1, ''),
(126, 1, 'Macedonia, The Former Yugoslav Republic of', 'MKD', 'MK', 1, ''),
(127, 1, 'Madagascar', 'MDG', 'MG', 1, ''),
(128, 1, 'Malawi', 'MWI', 'MW', 1, ''),
(129, 1, 'Malaysia', 'MYS', 'MY', 1, ''),
(130, 1, 'Maldives', 'MDV', 'MV', 1, ''),
(131, 1, 'Mali', 'MLI', 'ML', 1, ''),
(132, 1, 'Malta', 'MLT', 'MT', 1, ''),
(133, 1, 'Marshall Islands', 'MHL', 'MH', 1, ''),
(134, 1, 'Martinique', 'MTQ', 'MQ', 1, ''),
(135, 1, 'Mauritania', 'MRT', 'MR', 1, ''),
(136, 1, 'Mauritius', 'MUS', 'MU', 1, ''),
(137, 1, 'Mayotte', 'MYT', 'YT', 1, ''),
(138, 1, 'Mexico', 'MEX', 'MX', 1, ''),
(139, 1, 'Micronesia, Federated States of', 'FSM', 'FM', 1, ''),
(140, 1, 'Moldova, Republic of', 'MDA', 'MD', 1, ''),
(141, 1, 'Monaco', 'MCO', 'MC', 1, ''),
(142, 1, 'Mongolia', 'MNG', 'MN', 1, ''),
(143, 1, 'Montserrat', 'MSR', 'MS', 1, ''),
(144, 1, 'Morocco', 'MAR', 'MA', 1, ''),
(145, 1, 'Mozambique', 'MOZ', 'MZ', 1, ''),
(146, 1, 'Myanmar', 'MMR', 'MM', 1, ''),
(147, 1, 'Namibia', 'NAM', 'NA', 1, ''),
(148, 1, 'Nauru', 'NRU', 'NR', 1, ''),
(149, 1, 'Nepal', 'NPL', 'NP', 1, ''),
(150, 1, 'Netherlands', 'NLD', 'NL', 1, ''),
(151, 1, 'Netherlands Antilles', 'ANT', 'AN', 1, ''),
(152, 1, 'New Caledonia', 'NCL', 'NC', 1, ''),
(153, 1, 'New Zealand', 'NZL', 'NZ', 1, ''),
(154, 1, 'Nicaragua', 'NIC', 'NI', 1, ''),
(155, 1, 'Niger', 'NER', 'NE', 1, ''),
(156, 1, 'Nigeria', 'NGA', 'NG', 1, ''),
(157, 1, 'Niue', 'NIU', 'NU', 1, ''),
(158, 1, 'Norfolk Island', 'NFK', 'NF', 1, ''),
(159, 1, 'Northern Mariana Islands', 'MNP', 'MP', 1, ''),
(160, 1, 'Norway', 'NOR', 'NO', 1, ''),
(161, 1, 'Oman', 'OMN', 'OM', 1, ''),
(162, 1, 'Pakistan', 'PAK', 'PK', 1, ''),
(163, 1, 'Palau', 'PLW', 'PW', 1, ''),
(164, 1, 'Panama', 'PAN', 'PA', 1, ''),
(165, 1, 'Papua New Guinea', 'PNG', 'PG', 1, ''),
(166, 1, 'Paraguay', 'PRY', 'PY', 1, ''),
(167, 1, 'Peru', 'PER', 'PE', 1, ''),
(168, 1, 'Philippines', 'PHL', 'PH', 1, ''),
(169, 1, 'Pitcairn', 'PCN', 'PN', 1, ''),
(170, 1, 'Poland', 'POL', 'PL', 1, ''),
(171, 1, 'Portugal', 'PRT', 'PT', 1, ''),
(172, 1, 'Puerto Rico', 'PRI', 'PR', 1, ''),
(173, 1, 'Qatar', 'QAT', 'QA', 1, ''),
(174, 1, 'Reunion', 'REU', 'RE', 1, ''),
(175, 1, 'Romania', 'ROM', 'RO', 1, ''),
(176, 1, 'Russian Federation', 'RUS', 'RU', 1, ''),
(177, 1, 'Rwanda', 'RWA', 'RW', 1, ''),
(178, 1, 'Saint Kitts and Nevis', 'KNA', 'KN', 1, ''),
(179, 1, 'Saint Lucia', 'LCA', 'LC', 1, ''),
(180, 1, 'Saint Vincent and the Grenadines', 'VCT', 'VC', 1, ''),
(181, 1, 'Samoa', 'WSM', 'WS', 1, ''),
(182, 1, 'San Marino', 'SMR', 'SM', 1, ''),
(183, 1, 'Sao Tome and Principe', 'STP', 'ST', 1, ''),
(184, 1, 'Saudi Arabia', 'SAU', 'SA', 1, ''),
(185, 1, 'Senegal', 'SEN', 'SN', 1, ''),
(186, 1, 'Seychelles', 'SYC', 'SC', 1, ''),
(187, 1, 'Sierra Leone', 'SLE', 'SL', 1, ''),
(188, 1, 'Singapore', 'SGP', 'SG', 1, ''),
(189, 1, 'Slovakia (Slovak Republic)', 'SVK', 'SK', 1, ''),
(190, 1, 'Slovenia', 'SVN', 'SI', 1, ''),
(191, 1, 'Solomon Islands', 'SLB', 'SB', 1, ''),
(192, 1, 'Somalia', 'SOM', 'SO', 1, ''),
(193, 1, 'South Africa', 'ZAF', 'ZA', 1, ''),
(194, 1, 'South Georgia and the South Sandwich Islands', 'SGS', 'GS', 1, ''),
(195, 1, 'Spain', 'ESP', 'ES', 1, ''),
(196, 1, 'Sri Lanka', 'LKA', 'LK', 1, ''),
(197, 1, 'St. Helena', 'SHN', 'SH', 1, ''),
(198, 1, 'St. Pierre and Miquelon', 'SPM', 'PM', 1, ''),
(199, 1, 'Sudan', 'SDN', 'SD', 1, ''),
(200, 1, 'Suriname', 'SUR', 'SR', 1, ''),
(201, 1, 'Svalbard and Jan Mayen Islands', 'SJM', 'SJ', 1, ''),
(202, 1, 'Swaziland', 'SWZ', 'SZ', 1, ''),
(203, 1, 'Sweden', 'SWE', 'SE', 1, ''),
(204, 1, 'Switzerland', 'CHE', 'CH', 1, ''),
(205, 1, 'Syrian Arab Republic', 'SYR', 'SY', 1, ''),
(206, 1, 'Taiwan', 'TWN', 'TW', 1, ''),
(207, 1, 'Tajikistan', 'TJK', 'TJ', 1, ''),
(208, 1, 'Tanzania, United Republic of', 'TZA', 'TZ', 1, ''),
(209, 1, 'Thailand', 'THA', 'TH', 1, ''),
(210, 1, 'Togo', 'TGO', 'TG', 1, ''),
(211, 1, 'Tokelau', 'TKL', 'TK', 1, ''),
(212, 1, 'Tonga', 'TON', 'TO', 1, ''),
(213, 1, 'Trinidad and Tobago', 'TTO', 'TT', 1, ''),
(214, 1, 'Tunisia', 'TUN', 'TN', 1, ''),
(215, 1, 'Turkey', 'TUR', 'TR', 1, ''),
(216, 1, 'Turkmenistan', 'TKM', 'TM', 1, ''),
(217, 1, 'Turks and Caicos Islands', 'TCA', 'TC', 1, ''),
(218, 1, 'Tuvalu', 'TUV', 'TV', 1, ''),
(219, 1, 'Uganda', 'UGA', 'UG', 1, ''),
(220, 1, 'Ukraine', 'UKR', 'UA', 1, ''),
(221, 1, 'United Arab Emirates', 'ARE', 'AE', 1, ''),
(222, 1, 'United Kingdom', 'GBR', 'GB', 1, ''),
(223, 1, 'United States', 'USA', 'US', 1, ''),
(224, 1, 'United States Minor Outlying Islands', 'UMI', 'UM', 1, ''),
(225, 1, 'Uruguay', 'URY', 'UY', 1, ''),
(226, 1, 'Uzbekistan', 'UZB', 'UZ', 1, ''),
(227, 1, 'Vanuatu', 'VUT', 'VU', 1, ''),
(228, 1, 'Vatican City State (Holy See)', 'VAT', 'VA', 1, ''),
(229, 1, 'Venezuela', 'VEN', 'VE', 1, ''),
(230, 1, 'Viet Nam', 'VNM', 'VN', 1, ''),
(231, 1, 'Virgin Islands (British)', 'VGB', 'VG', 1, ''),
(232, 1, 'Virgin Islands (U.S.)', 'VIR', 'VI', 1, ''),
(233, 1, 'Wallis and Futuna Islands', 'WLF', 'WF', 1, ''),
(234, 1, 'Western Sahara', 'ESH', 'EH', 1, ''),
(235, 1, 'Yemen', 'YEM', 'YE', 1, ''),
(236, 1, 'Serbia', 'SRB', 'RS', 1, ''),
(237, 1, 'The Democratic Republic of Congo', 'DRC', 'DC', 1, ''),
(238, 1, 'Zambia', 'ZMB', 'ZM', 1, ''),
(239, 1, 'Zimbabwe', 'ZWE', 'ZW', 1, ''),
(240, 1, 'East Timor', 'XET', 'XE', 1, ''),
(241, 1, 'Jersey', 'XJE', 'XJ', 1, ''),
(242, 1, 'St. Barthelemy', 'XSB', 'XB', 1, ''),
(243, 1, 'St. Eustatius', 'XSE', 'XU', 1, ''),
(244, 1, 'Canary Islands', 'XCA', 'XC', 1, ''),
(245, 1, 'Montenegro', 'MNE', 'ME', 1, ''),
(246, 1, 'Curaçao', 'CUR', 'CW', 1, ''),
(247, 1, 'St. Maarten', 'SXM', 'SX', 1, ''),
(248, 1, 'Bonaire', 'BON', 'BQ', 1, ''),
(249, 1, 'St. Eustatius', 'EUX', 'BQ', 1, ''),
(250, 1, 'Saba', 'SAB', 'BQ', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_country_state`
--

CREATE TABLE IF NOT EXISTS `#__pago_country_state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL DEFAULT '1',
  `state_name` varchar(64) DEFAULT NULL,
  `state_3_code` char(3) DEFAULT NULL,
  `state_2_code` char(2) DEFAULT NULL,
  `publish` tinyint(4) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`state_id`),
  UNIQUE KEY `state_3_code` (`country_id`,`state_3_code`),
  UNIQUE KEY `state_2_code` (`country_id`,`state_2_code`),
  KEY `idx_country_id` (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='States that are assigned to a country' AUTO_INCREMENT=449 ;

--
-- Dumping data for table `#__pago_country_state`
--

INSERT IGNORE INTO `#__pago_country_state` (`state_id`, `country_id`, `state_name`, `state_3_code`, `state_2_code`, `publish`, `params`) VALUES
(1, 223, 'Alabama', 'ALA', 'AL', 1, ''),
(2, 223, 'Alaska', 'ALK', 'AK', 1, ''),
(3, 223, 'Arizona', 'ARZ', 'AZ', 1, ''),
(4, 223, 'Arkansas', 'ARK', 'AR', 1, ''),
(5, 223, 'California', 'CAL', 'CA', 1, ''),
(6, 223, 'Colorado', 'COL', 'CO', 1, ''),
(7, 223, 'Connecticut', 'CCT', 'CT', 1, ''),
(8, 223, 'Delaware', 'DEL', 'DE', 1, ''),
(9, 223, 'District Of Columbia', 'DOC', 'DC', 1, ''),
(10, 223, 'Florida', 'FLO', 'FL', 1, ''),
(11, 223, 'Georgia', 'GEA', 'GA', 1, ''),
(12, 223, 'Hawaii', 'HWI', 'HI', 1, ''),
(13, 223, 'Idaho', 'IDA', 'ID', 1, ''),
(14, 223, 'Illinois', 'ILL', 'IL', 1, ''),
(15, 223, 'Indiana', 'IND', 'IN', 1, ''),
(16, 223, 'Iowa', 'IOA', 'IA', 1, ''),
(17, 223, 'Kansas', 'KAS', 'KS', 1, ''),
(18, 223, 'Kentucky', 'KTY', 'KY', 1, ''),
(19, 223, 'Louisiana', 'LOA', 'LA', 1, ''),
(20, 223, 'Maine', 'MAI', 'ME', 1, ''),
(21, 223, 'Maryland', 'MLD', 'MD', 1, ''),
(22, 223, 'Massachusetts', 'MSA', 'MA', 1, ''),
(23, 223, 'Michigan', 'MIC', 'MI', 1, ''),
(24, 223, 'Minnesota', 'MIN', 'MN', 1, ''),
(25, 223, 'Mississippi', 'MIS', 'MS', 1, ''),
(26, 223, 'Missouri', 'MIO', 'MO', 1, ''),
(27, 223, 'Montana', 'MOT', 'MT', 1, ''),
(28, 223, 'Nebraska', 'NEB', 'NE', 1, ''),
(29, 223, 'Nevada', 'NEV', 'NV', 1, ''),
(30, 223, 'New Hampshire', 'NEH', 'NH', 1, ''),
(31, 223, 'New Jersey', 'NEJ', 'NJ', 1, ''),
(32, 223, 'New Mexico', 'NEM', 'NM', 1, ''),
(33, 223, 'New York', 'NEY', 'NY', 1, ''),
(34, 223, 'North Carolina', 'NOC', 'NC', 1, ''),
(35, 223, 'North Dakota', 'NOD', 'ND', 1, ''),
(36, 223, 'Ohio', 'OHI', 'OH', 1, ''),
(37, 223, 'Oklahoma', 'OKL', 'OK', 1, ''),
(38, 223, 'Oregon', 'ORN', 'OR', 1, ''),
(39, 223, 'Pennsylvania', 'PEA', 'PA', 1, ''),
(40, 223, 'Rhode Island', 'RHI', 'RI', 1, ''),
(41, 223, 'South Carolina', 'SOC', 'SC', 1, ''),
(42, 223, 'South Dakota', 'SOD', 'SD', 1, ''),
(43, 223, 'Tennessee', 'TEN', 'TN', 1, ''),
(44, 223, 'Texas', 'TXS', 'TX', 1, ''),
(45, 223, 'Utah', 'UTA', 'UT', 1, ''),
(46, 223, 'Vermont', 'VMT', 'VT', 1, ''),
(47, 223, 'Virginia', 'VIA', 'VA', 1, ''),
(48, 223, 'Washington', 'WAS', 'WA', 1, ''),
(49, 223, 'West Virginia', 'WEV', 'WV', 1, ''),
(50, 223, 'Wisconsin', 'WIS', 'WI', 1, ''),
(51, 223, 'Wyoming', 'WYO', 'WY', 1, ''),
(52, 38, 'Alberta', 'ALB', 'AB', 1, ''),
(53, 38, 'British Columbia', 'BRC', 'BC', 1, ''),
(54, 38, 'Manitoba', 'MAB', 'MB', 1, ''),
(55, 38, 'New Brunswick', 'NEB', 'NB', 1, ''),
(56, 38, 'Newfoundland and Labrador', 'NFL', 'NL', 1, ''),
(57, 38, 'Northwest Territories', 'NWT', 'NT', 1, ''),
(58, 38, 'Nova Scotia', 'NOS', 'NS', 1, ''),
(59, 38, 'Nunavut', 'NUT', 'NU', 1, ''),
(60, 38, 'Ontario', 'ONT', 'ON', 1, ''),
(61, 38, 'Prince Edward Island', 'PEI', 'PE', 1, ''),
(62, 38, 'Quebec', 'QEC', 'QC', 1, ''),
(63, 38, 'Saskatchewan', 'SAK', 'SK', 1, ''),
(64, 38, 'Yukon', 'YUT', 'YT', 1, ''),
(65, 222, 'England', 'ENG', 'EN', 1, ''),
(66, 222, 'Northern Ireland', 'NOI', 'NI', 1, ''),
(67, 222, 'Scotland', 'SCO', 'SD', 1, ''),
(68, 222, 'Wales', 'WLS', 'WS', 1, ''),
(69, 13, 'Australian Capital Territory', 'ACT', 'AC', 1, ''),
(70, 13, 'New South Wales', 'NSW', 'NS', 1, ''),
(71, 13, 'Northern Territory', 'NOT', 'NT', 1, ''),
(72, 13, 'Queensland', 'QLD', 'QL', 1, ''),
(73, 13, 'South Australia', 'SOA', 'SA', 1, ''),
(74, 13, 'Tasmania', 'TAS', 'TS', 1, ''),
(75, 13, 'Victoria', 'VIC', 'VI', 1, ''),
(76, 13, 'Western Australia', 'WEA', 'WA', 1, ''),
(77, 138, 'Aguascalientes', 'AGS', 'AG', 1, ''),
(78, 138, 'Baja California Norte', 'BCN', 'BN', 1, ''),
(79, 138, 'Baja California Sur', 'BCS', 'BS', 1, ''),
(80, 138, 'Campeche', 'CAM', 'CA', 1, ''),
(81, 138, 'Chiapas', 'CHI', 'CS', 1, ''),
(82, 138, 'Chihuahua', 'CHA', 'CH', 1, ''),
(83, 138, 'Coahuila', 'COA', 'CO', 1, ''),
(84, 138, 'Colima', 'COL', 'CM', 1, ''),
(85, 138, 'Distrito Federal', 'DFM', 'DF', 1, ''),
(86, 138, 'Durango', 'DGO', 'DO', 1, ''),
(87, 138, 'Guanajuato', 'GTO', 'GO', 1, ''),
(88, 138, 'Guerrero', 'GRO', 'GU', 1, ''),
(89, 138, 'Hidalgo', 'HGO', 'HI', 1, ''),
(90, 138, 'Jalisco', 'JAL', 'JA', 1, ''),
(91, 138, 'México (Estado de)', 'EDM', 'EM', 1, ''),
(92, 138, 'Michoacán', 'MCN', 'MI', 1, ''),
(93, 138, 'Morelos', 'MOR', 'MO', 1, ''),
(94, 138, 'Nayarit', 'NAY', 'NY', 1, ''),
(95, 138, 'Nuevo León', 'NUL', 'NL', 1, ''),
(96, 138, 'Oaxaca', 'OAX', 'OA', 1, ''),
(97, 138, 'Puebla', 'PUE', 'PU', 1, ''),
(98, 138, 'Querétaro', 'QRO', 'QU', 1, ''),
(99, 138, 'Quintana Roo', 'QUR', 'QR', 1, ''),
(100, 138, 'San Luis Potosí', 'SLP', 'SP', 1, ''),
(101, 138, 'Sinaloa', 'SIN', 'SI', 1, ''),
(102, 138, 'Sonora', 'SON', 'SO', 1, ''),
(103, 138, 'Tabasco', 'TAB', 'TA', 1, ''),
(104, 138, 'Tamaulipas', 'TAM', 'TM', 1, ''),
(105, 138, 'Tlaxcala', 'TLX', 'TX', 1, ''),
(106, 138, 'Veracruz', 'VER', 'VZ', 1, ''),
(107, 138, 'Yucatán', 'YUC', 'YU', 1, ''),
(108, 138, 'Zacatecas', 'ZAC', 'ZA', 1, ''),
(109, 30, 'Acre', 'ACR', 'AC', 1, ''),
(110, 30, 'Alagoas', 'ALG', 'AL', 1, ''),
(111, 30, 'Amapá', 'AMP', 'AP', 1, ''),
(112, 30, 'Amazonas', 'AMZ', 'AM', 1, ''),
(113, 30, 'Bahía', 'BAH', 'BA', 1, ''),
(114, 30, 'Ceará', 'CEA', 'CE', 1, ''),
(115, 30, 'Distrito Federal', 'DFB', 'DF', 1, ''),
(116, 30, 'Espirito Santo', 'ESS', 'ES', 1, ''),
(117, 30, 'Goiás', 'GOI', 'GO', 1, ''),
(118, 30, 'Maranhão', 'MAR', 'MA', 1, ''),
(119, 30, 'Mato Grosso', 'MAT', 'MT', 1, ''),
(120, 30, 'Mato Grosso do Sul', 'MGS', 'MS', 1, ''),
(121, 30, 'Minas Geraís', 'MIG', 'MG', 1, ''),
(122, 30, 'Paraná', 'PAR', 'PR', 1, ''),
(123, 30, 'Paraíba', 'PRB', 'PB', 1, ''),
(124, 30, 'Pará', 'PAB', 'PA', 1, ''),
(125, 30, 'Pernambuco', 'PER', 'PE', 1, ''),
(126, 30, 'Piauí', 'PIA', 'PI', 1, ''),
(127, 30, 'Rio Grande do Norte', 'RGN', 'RN', 1, ''),
(128, 30, 'Rio Grande do Sul', 'RGS', 'RS', 1, ''),
(129, 30, 'Rio de Janeiro', 'RDJ', 'RJ', 1, ''),
(130, 30, 'Rondônia', 'RON', 'RO', 1, ''),
(131, 30, 'Roraima', 'ROR', 'RR', 1, ''),
(132, 30, 'Santa Catarina', 'SAC', 'SC', 1, ''),
(133, 30, 'Sergipe', 'SER', 'SE', 1, ''),
(134, 30, 'São Paulo', 'SAP', 'SP', 1, ''),
(135, 30, 'Tocantins', 'TOC', 'TO', 1, ''),
(136, 44, 'Anhui', 'ANH', '34', 1, ''),
(137, 44, 'Beijing', 'BEI', '11', 1, ''),
(138, 44, 'Chongqing', 'CHO', '50', 1, ''),
(139, 44, 'Fujian', 'FUJ', '35', 1, ''),
(140, 44, 'Gansu', 'GAN', '62', 1, ''),
(141, 44, 'Guangdong', 'GUA', '44', 1, ''),
(142, 44, 'Guangxi Zhuang', 'GUZ', '45', 1, ''),
(143, 44, 'Guizhou', 'GUI', '52', 1, ''),
(144, 44, 'Hainan', 'HAI', '46', 1, ''),
(145, 44, 'Hebei', 'HEB', '13', 1, ''),
(146, 44, 'Heilongjiang', 'HEI', '23', 1, ''),
(147, 44, 'Henan', 'HEN', '41', 1, ''),
(148, 44, 'Hubei', 'HUB', '42', 1, ''),
(149, 44, 'Hunan', 'HUN', '43', 1, ''),
(150, 44, 'Jiangsu', 'JIA', '32', 1, ''),
(151, 44, 'Jiangxi', 'JIX', '36', 1, ''),
(152, 44, 'Jilin', 'JIL', '22', 1, ''),
(153, 44, 'Liaoning', 'LIA', '21', 1, ''),
(154, 44, 'Nei Mongol', 'NML', '15', 1, ''),
(155, 44, 'Ningxia Hui', 'NIH', '64', 1, ''),
(156, 44, 'Qinghai', 'QIN', '63', 1, ''),
(157, 44, 'Shandong', 'SNG', '37', 1, ''),
(158, 44, 'Shanghai', 'SHH', '31', 1, ''),
(159, 44, 'Shaanxi', 'SHX', '61', 1, ''),
(160, 44, 'Sichuan', 'SIC', '51', 1, ''),
(161, 44, 'Tianjin', 'TIA', '12', 1, ''),
(162, 44, 'Xinjiang Uygur', 'XIU', '65', 1, ''),
(163, 44, 'Xizang', 'XIZ', '54', 1, ''),
(164, 44, 'Yunnan', 'YUN', '53', 1, ''),
(165, 44, 'Zhejiang', 'ZHE', '33', 1, ''),
(166, 104, 'Israel', 'ISL', 'IL', 1, ''),
(167, 104, 'Gaza Strip', 'GZS', 'GZ', 1, ''),
(168, 104, 'West Bank', 'WBK', 'WB', 1, ''),
(169, 151, 'St. Maarten', 'STM', 'SM', 1, ''),
(170, 151, 'Bonaire', 'BNR', 'BN', 1, ''),
(171, 151, 'Curacao', 'CUR', 'CR', 1, ''),
(172, 175, 'Alba', 'ABA', 'AB', 1, ''),
(173, 175, 'Arad', 'ARD', 'AR', 1, ''),
(174, 175, 'Arges', 'ARG', 'AG', 1, ''),
(175, 175, 'Bacau', 'BAC', 'BC', 1, ''),
(176, 175, 'Bihor', 'BIH', 'BH', 1, ''),
(177, 175, 'Bistrita-Nasaud', 'BIS', 'BN', 1, ''),
(178, 175, 'Botosani', 'BOT', 'BT', 1, ''),
(179, 175, 'Braila', 'BRL', 'BR', 1, ''),
(180, 175, 'Brasov', 'BRA', 'BV', 1, ''),
(181, 175, 'Bucuresti', 'BUC', 'B', 1, ''),
(182, 175, 'Buzau', 'BUZ', 'BZ', 1, ''),
(183, 175, 'Calarasi', 'CAL', 'CL', 1, ''),
(184, 175, 'Caras Severin', 'CRS', 'CS', 1, ''),
(185, 175, 'Cluj', 'CLJ', 'CJ', 1, ''),
(186, 175, 'Constanta', 'CST', 'CT', 1, ''),
(187, 175, 'Covasna', 'COV', 'CV', 1, ''),
(188, 175, 'Dambovita', 'DAM', 'DB', 1, ''),
(189, 175, 'Dolj', 'DLJ', 'DJ', 1, ''),
(190, 175, 'Galati', 'GAL', 'GL', 1, ''),
(191, 175, 'Giurgiu', 'GIU', 'GR', 1, ''),
(192, 175, 'Gorj', 'GOR', 'GJ', 1, ''),
(193, 175, 'Hargita', 'HRG', 'HR', 1, ''),
(194, 175, 'Hunedoara', 'HUN', 'HD', 1, ''),
(195, 175, 'Ialomita', 'IAL', 'IL', 1, ''),
(196, 175, 'Iasi', 'IAS', 'IS', 1, ''),
(197, 175, 'Ilfov', 'ILF', 'IF', 1, ''),
(198, 175, 'Maramures', 'MAR', 'MM', 1, ''),
(199, 175, 'Mehedinti', 'MEH', 'MH', 1, ''),
(200, 175, 'Mures', 'MUR', 'MS', 1, ''),
(201, 175, 'Neamt', 'NEM', 'NT', 1, ''),
(202, 175, 'Olt', 'OLT', 'OT', 1, ''),
(203, 175, 'Prahova', 'PRA', 'PH', 1, ''),
(204, 175, 'Salaj', 'SAL', 'SJ', 1, ''),
(205, 175, 'Satu Mare', 'SAT', 'SM', 1, ''),
(206, 175, 'Sibiu', 'SIB', 'SB', 1, ''),
(207, 175, 'Suceava', 'SUC', 'SV', 1, ''),
(208, 175, 'Teleorman', 'TEL', 'TR', 1, ''),
(209, 175, 'Timis', 'TIM', 'TM', 1, ''),
(210, 175, 'Tulcea', 'TUL', 'TL', 1, ''),
(211, 175, 'Valcea', 'VAL', 'VL', 1, ''),
(212, 175, 'Vaslui', 'VAS', 'VS', 1, ''),
(213, 175, 'Vrancea', 'VRA', 'VN', 1, ''),
(214, 105, 'Agrigento', 'AGR', 'AG', 1, ''),
(215, 105, 'Alessandria', 'ALE', 'AL', 1, ''),
(216, 105, 'Ancona', 'ANC', 'AN', 1, ''),
(217, 105, 'Aosta', 'AOS', 'AO', 1, ''),
(218, 105, 'Arezzo', 'ARE', 'AR', 1, ''),
(219, 105, 'Ascoli Piceno', 'API', 'AP', 1, ''),
(220, 105, 'Asti', 'AST', 'AT', 1, ''),
(221, 105, 'Avellino', 'AVE', 'AV', 1, ''),
(222, 105, 'Bari', 'BAR', 'BA', 1, ''),
(223, 105, 'Belluno', 'BEL', 'BL', 1, ''),
(224, 105, 'Benevento', 'BEN', 'BN', 1, ''),
(225, 105, 'Bergamo', 'BEG', 'BG', 1, ''),
(226, 105, 'Biella', 'BIE', 'BI', 1, ''),
(227, 105, 'Bologna', 'BOL', 'BO', 1, ''),
(228, 105, 'Bolzano', 'BOZ', 'BZ', 1, ''),
(229, 105, 'Brescia', 'BRE', 'BS', 1, ''),
(230, 105, 'Brindisi', 'BRI', 'BR', 1, ''),
(231, 105, 'Cagliari', 'CAG', 'CA', 1, ''),
(232, 105, 'Caltanissetta', 'CAL', 'CL', 1, ''),
(233, 105, 'Campobasso', 'CBO', 'CB', 1, ''),
(234, 105, 'Carbonia-Iglesias', 'CAR', 'CI', 1, ''),
(235, 105, 'Caserta', 'CAS', 'CE', 1, ''),
(236, 105, 'Catania', 'CAT', 'CT', 1, ''),
(237, 105, 'Catanzaro', 'CTZ', 'CZ', 1, ''),
(238, 105, 'Chieti', 'CHI', 'CH', 1, ''),
(239, 105, 'Como', 'COM', 'CO', 1, ''),
(240, 105, 'Cosenza', 'COS', 'CS', 1, ''),
(241, 105, 'Cremona', 'CRE', 'CR', 1, ''),
(242, 105, 'Crotone', 'CRO', 'KR', 1, ''),
(243, 105, 'Cuneo', 'CUN', 'CN', 1, ''),
(244, 105, 'Enna', 'ENN', 'EN', 1, ''),
(245, 105, 'Ferrara', 'FER', 'FE', 1, ''),
(246, 105, 'Firenze', 'FIR', 'FI', 1, ''),
(247, 105, 'Foggia', 'FOG', 'FG', 1, ''),
(248, 105, 'Forli-Cesena', 'FOC', 'FC', 1, ''),
(249, 105, 'Frosinone', 'FRO', 'FR', 1, ''),
(250, 105, 'Genova', 'GEN', 'GE', 1, ''),
(251, 105, 'Gorizia', 'GOR', 'GO', 1, ''),
(252, 105, 'Grosseto', 'GRO', 'GR', 1, ''),
(253, 105, 'Imperia', 'IMP', 'IM', 1, ''),
(254, 105, 'Isernia', 'ISE', 'IS', 1, ''),
(255, 105, 'L''Aquila', 'AQU', 'AQ', 1, ''),
(256, 105, 'La Spezia', 'LAS', 'SP', 1, ''),
(257, 105, 'Latina', 'LAT', 'LT', 1, ''),
(258, 105, 'Lecce', 'LEC', 'LE', 1, ''),
(259, 105, 'Lecco', 'LCC', 'LC', 1, ''),
(260, 105, 'Livorno', 'LIV', 'LI', 1, ''),
(261, 105, 'Lodi', 'LOD', 'LO', 1, ''),
(262, 105, 'Lucca', 'LUC', 'LU', 1, ''),
(263, 105, 'Macerata', 'MAC', 'MC', 1, ''),
(264, 105, 'Mantova', 'MAN', 'MN', 1, ''),
(265, 105, 'Massa-Carrara', 'MAS', 'MS', 1, ''),
(266, 105, 'Matera', 'MAA', 'MT', 1, ''),
(267, 105, 'Medio Campidano', 'MED', 'VS', 1, ''),
(268, 105, 'Messina', 'MES', 'ME', 1, ''),
(269, 105, 'Milano', 'MIL', 'MI', 1, ''),
(270, 105, 'Modena', 'MOD', 'MO', 1, ''),
(271, 105, 'Napoli', 'NAP', 'NA', 1, ''),
(272, 105, 'Novara', 'NOV', 'NO', 1, ''),
(273, 105, 'Nuoro', 'NUR', 'NU', 1, ''),
(274, 105, 'Ogliastra', 'OGL', 'OG', 1, ''),
(275, 105, 'Olbia-Tempio', 'OLB', 'OT', 1, ''),
(276, 105, 'Oristano', 'ORI', 'OR', 1, ''),
(277, 105, 'Padova', 'PDA', 'PD', 1, ''),
(278, 105, 'Palermo', 'PAL', 'PA', 1, ''),
(279, 105, 'Parma', 'PAA', 'PR', 1, ''),
(280, 105, 'Pavia', 'PAV', 'PV', 1, ''),
(281, 105, 'Perugia', 'PER', 'PG', 1, ''),
(282, 105, 'Pesaro e Urbino', 'PES', 'PU', 1, ''),
(283, 105, 'Pescara', 'PSC', 'PE', 1, ''),
(284, 105, 'Piacenza', 'PIA', 'PC', 1, ''),
(285, 105, 'Pisa', 'PIS', 'PI', 1, ''),
(286, 105, 'Pistoia', 'PIT', 'PT', 1, ''),
(287, 105, 'Pordenone', 'POR', 'PN', 1, ''),
(288, 105, 'Potenza', 'PTZ', 'PZ', 1, ''),
(289, 105, 'Prato', 'PRA', 'PO', 1, ''),
(290, 105, 'Ragusa', 'RAG', 'RG', 1, ''),
(291, 105, 'Ravenna', 'RAV', 'RA', 1, ''),
(292, 105, 'Reggio Calabria', 'REG', 'RC', 1, ''),
(293, 105, 'Reggio Emilia', 'REE', 'RE', 1, ''),
(294, 105, 'Rieti', 'RIE', 'RI', 1, ''),
(295, 105, 'Rimini', 'RIM', 'RN', 1, ''),
(296, 105, 'Roma', 'ROM', 'RM', 1, ''),
(297, 105, 'Rovigo', 'ROV', 'RO', 1, ''),
(298, 105, 'Salerno', 'SAL', 'SA', 1, ''),
(299, 105, 'Sassari', 'SAS', 'SS', 1, ''),
(300, 105, 'Savona', 'SAV', 'SV', 1, ''),
(301, 105, 'Siena', 'SIE', 'SI', 1, ''),
(302, 105, 'Siracusa', 'SIR', 'SR', 1, ''),
(303, 105, 'Sondrio', 'SOO', 'SO', 1, ''),
(304, 105, 'Taranto', 'TAR', 'TA', 1, ''),
(305, 105, 'Teramo', 'TER', 'TE', 1, ''),
(306, 105, 'Terni', 'TRN', 'TR', 1, ''),
(307, 105, 'Torino', 'TOR', 'TO', 1, ''),
(308, 105, 'Trapani', 'TRA', 'TP', 1, ''),
(309, 105, 'Trento', 'TRE', 'TN', 1, ''),
(310, 105, 'Treviso', 'TRV', 'TV', 1, ''),
(311, 105, 'Trieste', 'TRI', 'TS', 1, ''),
(312, 105, 'Udine', 'UDI', 'UD', 1, ''),
(313, 105, 'Varese', 'VAR', 'VA', 1, ''),
(314, 105, 'Venezia', 'VEN', 'VE', 1, ''),
(315, 105, 'Verbano Cusio Ossola', 'VCO', 'VB', 1, ''),
(316, 105, 'Vercelli', 'VER', 'VC', 1, ''),
(317, 105, 'Verona', 'VRN', 'VR', 1, ''),
(318, 105, 'Vibo Valenzia', 'VIV', 'VV', 1, ''),
(319, 105, 'Vicenza', 'VII', 'VI', 1, ''),
(320, 105, 'Viterbo', 'VIT', 'VT', 1, ''),
(321, 195, 'A Coruña', 'ACO', '15', 1, ''),
(322, 195, 'Alava', 'ALA', '01', 1, ''),
(323, 195, 'Albacete', 'ALB', '02', 1, ''),
(324, 195, 'Alicante', 'ALI', '03', 1, ''),
(325, 195, 'Almeria', 'ALM', '04', 1, ''),
(326, 195, 'Asturias', 'AST', '33', 1, ''),
(327, 195, 'Avila', 'AVI', '05', 1, ''),
(328, 195, 'Badajoz', 'BAD', '06', 1, ''),
(329, 195, 'Baleares', 'BAL', '07', 1, ''),
(330, 195, 'Barcelona', 'BAR', '08', 1, ''),
(331, 195, 'Burgos', 'BUR', '09', 1, ''),
(332, 195, 'Caceres', 'CAC', '10', 1, ''),
(333, 195, 'Cadiz', 'CAD', '11', 1, ''),
(334, 195, 'Cantabria', 'CAN', '39', 1, ''),
(335, 195, 'Castellon', 'CAS', '12', 1, ''),
(336, 195, 'Ceuta', 'CEU', '51', 1, ''),
(337, 195, 'Ciudad Real', 'CIU', '13', 1, ''),
(338, 195, 'Cordoba', 'COR', '14', 1, ''),
(339, 195, 'Cuenca', 'CUE', '16', 1, ''),
(340, 195, 'Girona', 'GIR', '17', 1, ''),
(341, 195, 'Granada', 'GRA', '18', 1, ''),
(342, 195, 'Guadalajara', 'GUA', '19', 1, ''),
(343, 195, 'Guipuzcoa', 'GUI', '20', 1, ''),
(344, 195, 'Huelva', 'HUL', '21', 1, ''),
(345, 195, 'Huesca', 'HUS', '22', 1, ''),
(346, 195, 'Jaen', 'JAE', '23', 1, ''),
(347, 195, 'La Rioja', 'LRI', '26', 1, ''),
(348, 195, 'Las Palmas', 'LPA', '35', 1, ''),
(349, 195, 'Leon', 'LEO', '24', 1, ''),
(350, 195, 'Lleida', 'LLE', '25', 1, ''),
(351, 195, 'Lugo', 'LUG', '27', 1, ''),
(352, 195, 'Madrid', 'MAD', '28', 1, ''),
(353, 195, 'Malaga', 'MAL', '29', 1, ''),
(354, 195, 'Melilla', 'MEL', '52', 1, ''),
(355, 195, 'Murcia', 'MUR', '30', 1, ''),
(356, 195, 'Navarra', 'NAV', '31', 1, ''),
(357, 195, 'Ourense', 'OUR', '32', 1, ''),
(358, 195, 'Palencia', 'PAL', '34', 1, ''),
(359, 195, 'Pontevedra', 'PON', '36', 1, ''),
(360, 195, 'Salamanca', 'SAL', '37', 1, ''),
(361, 195, 'Santa Cruz de Tenerife', 'SCT', '38', 1, ''),
(362, 195, 'Segovia', 'SEG', '40', 1, ''),
(363, 195, 'Sevilla', 'SEV', '41', 1, ''),
(364, 195, 'Soria', 'SOR', '42', 1, ''),
(365, 195, 'Tarragona', 'TAR', '43', 1, ''),
(366, 195, 'Teruel', 'TER', '44', 1, ''),
(367, 195, 'Toledo', 'TOL', '45', 1, ''),
(368, 195, 'Valencia', 'VAL', '46', 1, ''),
(369, 195, 'Valladolid', 'VLL', '47', 1, ''),
(370, 195, 'Vizcaya', 'VIZ', '48', 1, ''),
(371, 195, 'Zamora', 'ZAM', '49', 1, ''),
(372, 195, 'Zaragoza', 'ZAR', '50', 1, ''),
(373, 11, 'Aragatsotn', 'ARG', 'AG', 1, ''),
(374, 11, 'Ararat', 'ARR', 'AR', 1, ''),
(375, 11, 'Armavir', 'ARM', 'AV', 1, ''),
(376, 11, 'Gegharkunik', 'GEG', 'GR', 1, ''),
(377, 11, 'Kotayk', 'KOT', 'KT', 1, ''),
(378, 11, 'Lori', 'LOR', 'LO', 1, ''),
(379, 11, 'Shirak', 'SHI', 'SH', 1, ''),
(380, 11, 'Syunik', 'SYU', 'SU', 1, ''),
(381, 11, 'Tavush', 'TAV', 'TV', 1, ''),
(382, 11, 'Vayots-Dzor', 'VAD', 'VD', 1, ''),
(383, 11, 'Yerevan', 'YER', 'ER', 1, ''),
(384, 99, 'Andaman & Nicobar Islands', 'ANI', 'AI', 1, ''),
(385, 99, 'Andhra Pradesh', 'AND', 'AN', 1, ''),
(386, 99, 'Arunachal Pradesh', 'ARU', 'AR', 1, ''),
(387, 99, 'Assam', 'ASS', 'AS', 1, ''),
(388, 99, 'Bihar', 'BIH', 'BI', 1, ''),
(389, 99, 'Chandigarh', 'CHA', 'CA', 1, ''),
(390, 99, 'Chhatisgarh', 'CHH', 'CH', 1, ''),
(391, 99, 'Dadra & Nagar Haveli', 'DAD', 'DD', 1, ''),
(392, 99, 'Daman & Diu', 'DAM', 'DA', 1, ''),
(393, 99, 'Delhi', 'DEL', 'DE', 1, ''),
(394, 99, 'Goa', 'GOA', 'GO', 1, ''),
(395, 99, 'Gujarat', 'GUJ', 'GU', 1, ''),
(396, 99, 'Haryana', 'HAR', 'HA', 1, ''),
(397, 99, 'Himachal Pradesh', 'HIM', 'HI', 1, ''),
(398, 99, 'Jammu & Kashmir', 'JAM', 'JA', 1, ''),
(399, 99, 'Jharkhand', 'JHA', 'JH', 1, ''),
(400, 99, 'Karnataka', 'KAR', 'KA', 1, ''),
(401, 99, 'Kerala', 'KER', 'KE', 1, ''),
(402, 99, 'Lakshadweep', 'LAK', 'LA', 1, ''),
(403, 99, 'Madhya Pradesh', 'MAD', 'MD', 1, ''),
(404, 99, 'Maharashtra', 'MAH', 'MH', 1, ''),
(405, 99, 'Manipur', 'MAN', 'MN', 1, ''),
(406, 99, 'Meghalaya', 'MEG', 'ME', 1, ''),
(407, 99, 'Mizoram', 'MIZ', 'MI', 1, ''),
(408, 99, 'Nagaland', 'NAG', 'NA', 1, ''),
(409, 99, 'Orissa', 'ORI', 'OR', 1, ''),
(410, 99, 'Pondicherry', 'PON', 'PO', 1, ''),
(411, 99, 'Punjab', 'PUN', 'PU', 1, ''),
(412, 99, 'Rajasthan', 'RAJ', 'RA', 1, ''),
(413, 99, 'Sikkim', 'SIK', 'SI', 1, ''),
(414, 99, 'Tamil Nadu', 'TAM', 'TA', 1, ''),
(415, 99, 'Tripura', 'TRI', 'TR', 1, ''),
(416, 99, 'Uttaranchal', 'UAR', 'UA', 1, ''),
(417, 99, 'Uttar Pradesh', 'UTT', 'UT', 1, ''),
(418, 99, 'West Bengal', 'WES', 'WE', 1, ''),
(419, 101, 'Ahmadi va Kohkiluyeh', 'BOK', 'BO', 1, ''),
(420, 101, 'Ardabil', 'ARD', 'AR', 1, ''),
(421, 101, 'Azarbayjan-e Gharbi', 'AZG', 'AG', 1, ''),
(422, 101, 'Azarbayjan-e Sharqi', 'AZS', 'AS', 1, ''),
(423, 101, 'Bushehr', 'BUS', 'BU', 1, ''),
(424, 101, 'Chaharmahal va Bakhtiari', 'CMB', 'CM', 1, ''),
(425, 101, 'Esfahan', 'ESF', 'ES', 1, ''),
(426, 101, 'Fars', 'FAR', 'FA', 1, ''),
(427, 101, 'Gilan', 'GIL', 'GI', 1, ''),
(428, 101, 'Gorgan', 'GOR', 'GO', 1, ''),
(429, 101, 'Hamadan', 'HAM', 'HA', 1, ''),
(430, 101, 'Hormozgan', 'HOR', 'HO', 1, ''),
(431, 101, 'Ilam', 'ILA', 'IL', 1, ''),
(432, 101, 'Kerman', 'KER', 'KE', 1, ''),
(433, 101, 'Kermanshah', 'BAK', 'BA', 1, ''),
(434, 101, 'Khorasan-e Junoubi', 'KHJ', 'KJ', 1, ''),
(435, 101, 'Khorasan-e Razavi', 'KHR', 'KR', 1, ''),
(436, 101, 'Khorasan-e Shomali', 'KHS', 'KS', 1, ''),
(437, 101, 'Khuzestan', 'KHU', 'KH', 1, ''),
(438, 101, 'Kordestan', 'KOR', 'KO', 1, ''),
(439, 101, 'Lorestan', 'LOR', 'LO', 1, ''),
(440, 101, 'Markazi', 'MAR', 'MR', 1, ''),
(441, 101, 'Mazandaran', 'MAZ', 'MZ', 1, ''),
(442, 101, 'Qazvin', 'QAS', 'QA', 1, ''),
(443, 101, 'Qom', 'QOM', 'QO', 1, ''),
(444, 101, 'Semnan', 'SEM', 'SE', 1, ''),
(445, 101, 'Sistan va Baluchestan', 'SBA', 'SB', 1, ''),
(446, 101, 'Tehran', 'TEH', 'TE', 1, ''),
(447, 101, 'Yazd', 'YAZ', 'YA', 1, ''),
(448, 101, 'Zanjan', 'ZAN', 'ZA', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_coupon`
--

CREATE TABLE IF NOT EXISTS `#__pago_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `published` int(1) DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `unlimited` INT(1) NOT NULL,
  `per_user` int(11) NOT NULL,
  `used` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `#__pago_coupon_assign`
--

CREATE TABLE IF NOT EXISTS `#__pago_coupon_assign` (
  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `assign_items` varchar(500) NOT NULL,
  `assign_users` varchar(500) NOT NULL,
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_coupon_categories`
--

CREATE TABLE IF NOT EXISTS `#__pago_coupon_categories` (
  `category_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_coupon_events`
--

CREATE TABLE IF NOT EXISTS `#__pago_coupon_events` (
  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `available_type` tinyint(4) NOT NULL,
  `available_condition` tinyint(4) NOT NULL,
  `filter_sum` varchar(50) NOT NULL,
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `#__pago_coupon_groups`
--

CREATE TABLE IF NOT EXISTS `#__pago_coupon_groups` (
  `group_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_coupon_rules`
--

CREATE TABLE IF NOT EXISTS `#__pago_coupon_rules` (
  `coupon_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `params` text NOT NULL,
  `discount` decimal(10,2) unsigned DEFAULT NULL,
  `is_percent` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coupon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_crules`
--

CREATE TABLE IF NOT EXISTS `#__pago_crules` (
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__pago_crules`
--

INSERT IGNORE INTO `#__pago_crules` (`name`, `class`) VALUES
('Flat Discount', 'flat_discount');

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_currency`
--

CREATE TABLE IF NOT EXISTS `#__pago_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `code` char(3) DEFAULT NULL,
  `symbol` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Used to store currencies' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__pago_currency`
--

INSERT IGNORE INTO `#__pago_currency` (`id`, `name`, `code`, `symbol`, `published`, `default`) VALUES
(1, 'Dollar', 'USD', '$', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_custom_shipping_rules`
--

CREATE TABLE IF NOT EXISTS `#__pago_custom_shipping_rules` (
  `rule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(255) NOT NULL,
  `order_total_start` decimal(10,2) unsigned DEFAULT NULL,
  `order_total_end` decimal(10,2) unsigned DEFAULT NULL,
  `weight_start` decimal(10,2) unsigned DEFAULT NULL,
  `weight_end` decimal(10,2) unsigned DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `state` text NOT NULL,
  `zipcode` text NOT NULL,
  `category` text NOT NULL,
  `items` text NOT NULL,
  `shipping_price` decimal(10,2) unsigned DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_discount`
--

CREATE TABLE IF NOT EXISTS `#__pago_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `quantity_min` int(11) NOT NULL,
  `quantity_max` int(11) NOT NULL,
  `user_type` varchar(10) NOT NULL,
  `disc_type` varchar(10) DEFAULT NULL,
  `amount` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_files`
--

CREATE TABLE IF NOT EXISTS `#__pago_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) NOT NULL DEFAULT '0',
  `default` tinyint(3) NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `caption` text NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `type` varchar(24) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_meta` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  `provider` varchar(50) NOT NULL,
  `video_key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product` (`item_id`),
  KEY `access` (`access`),
  KEY `published` (`published`),
  KEY `type` (`type`),
  KEY `default` (`default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__pago_groups`
--

CREATE TABLE IF NOT EXISTS `#__pago_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `params` longtext NOT NULL,
  `isdefault` tinyint(4) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_groups_users`
--

CREATE TABLE IF NOT EXISTS `#__pago_groups_users` (
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `group_id` (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_items`
--
CREATE TABLE IF NOT EXISTS `#__pago_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(500) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL,
  `primary_category` int(11) NOT NULL,
  `price` varchar(100) NOT NULL,
  `price_type` varchar(20) NOT NULL,
  `subscr_start_num` int(11) NOT NULL,
  `subscr_start_type` varchar(10) NOT NULL,
  `subscr_init_price` varchar(100) NOT NULL,
  `subscr_price` varchar(100) NOT NULL,
  `subscr_shipping` varchar(100) NOT NULL,
  `sub_recur` varchar(20) NOT NULL,
  `description` mediumtext NOT NULL,
  `sku` varchar(100) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '0',
  `qty_limit` int(11) NOT NULL DEFAULT '1',
  `tax_exempt` tinyint(4) NOT NULL,
  `content` longtext NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `height` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `width` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `length` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `weight` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `related_items` varchar(500) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `shipping_methods` text NOT NULL,
  `free_shipping` tinyint(1) NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  `expiry_date` int(10) NOT NULL,
  `pgtax_class_id` tinyint(1) NOT NULL,
  `item_custom_layout` varchar(255) NOT NULL,
  `availibility_date` date NOT NULL,
  `availibility_options` int(11) NOT NULL,
  `apply_discount` tinyint(1) NOT NULL,
  `disc_start_date` date NOT NULL,
  `disc_end_date` date NOT NULL,
  `discount_amount` varchar(100) NOT NULL,
  `discount_type` tinyint(1) NOT NULL,
  `related_category` text NOT NULL,
  `show_new` tinyint(4) NOT NULL,
  `until_new_date` date NOT NULL,
  `rating` varchar(50) NOT NULL DEFAULT '0',
  `view_settings_product_title` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_product_image` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_featured_badge` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_quantity_in_stock` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_short_desc` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_short_desc_limit` int(11) NOT NULL DEFAULT '50',
  `view_settings_desc` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_desc_limit` int(11) NOT NULL DEFAULT '100',
  `view_settings_sku` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_price` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_discounted_price` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_attribute` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_media` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_downloads` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_rating` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_category` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_add_to_cart` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_add_to_cart_qty` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_product_review` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_related_products` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_related_num_of_products` int(11) NOT NULL DEFAULT '5',
  `view_settings_related_title` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_related_category` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_related_image` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_related_short_text` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_image_settings_show` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_image_settings` text NOT NULL,
  `view_settings_fb` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_tw` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_pinterest` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_google_plus` tinyint(1) NOT NULL DEFAULT '2',
  `item_custom_layout_inherit` tinyint(1) NOT NULL DEFAULT '2',
  `view_settings_title_limit_inherit` tinyint(4) NOT NULL DEFAULT '0',
  `view_settings_title_limit` tinytext NOT NULL,
  `view_settings_product_image_zoom` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `item_ordering` int(11) NOT NULL DEFAULT '1',
  `featured_start_date` date NOT NULL,
  `featured_end_date` date NOT NULL,
  `jump_to_checkout` tinyint(1) NOT NULL,
  `subscr_installments` INT( 11 ) NOT NULL DEFAULT  '0',
  `subscr_enddate` VARCHAR( 20 ) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `visibility` (`visibility`),
  KEY `type` (`type`(333)),
  KEY `primary_category` (`primary_category`),
  KEY `id` (`id`),
  KEY `sku` (`sku`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `#__pago_items_attr`
--

CREATE TABLE IF NOT EXISTS `#__pago_items_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `attr_id` int(11) NOT NULL,
  `attr_opt_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_attr_opts` (`item_id`,`attr_id`,`attr_opt_id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_items_attr_opt_rel`
--

CREATE TABLE IF NOT EXISTS `#__pago_items_attr_opt_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_items_attr_rel`
--

CREATE TABLE IF NOT EXISTS `#__pago_items_attr_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `attr_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_items_data`
--

CREATE TABLE IF NOT EXISTS `#__pago_items_data` (
  `item_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `height` decimal(10,4) NOT NULL,
  `width` decimal(10,4) NOT NULL,
  `length` decimal(10,4) NOT NULL,
  `weight` decimal(10,4) NOT NULL,
  UNIQUE KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_item_types`
--

CREATE TABLE IF NOT EXISTS `#__pago_item_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `#__pago_item_types`
--

INSERT IGNORE INTO `#__pago_item_types` (`id`, `name`, `published`, `default`) VALUES
(1, 'test type', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_mail_templates`
--

CREATE TABLE IF NOT EXISTS `#__pago_mail_templates` (
  `pgemail_id` int(11) NOT NULL AUTO_INCREMENT,
  `pgemail_name` varchar(255) NOT NULL,
  `pgemail_type` varchar(255) NOT NULL,
  `pgemail_body` text NOT NULL,
  `pgemail_enable` tinyint(4) NOT NULL,
  `template_for` varchar(255) NOT NULL,
  PRIMARY KEY (`pgemail_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__pago_mail_templates`
--

INSERT IGNORE INTO `#__pago_mail_templates` (`pgemail_id`, `pgemail_name`, `pgemail_type`, `pgemail_body`, `pgemail_enable`, `template_for`) VALUES

(1, 'Invoice Mail', 'email_invoice', '<div style="font-family: arial; font-size: 20px; margin-bottom: 20px;">INVOICE DETAILS</div>\n<div style="font-family: arial; background: #494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight: bold;">Order Information</div>\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\n<tbody>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\n</tr>\n</tbody>\n</table>\n<div style="font-family: arial; background: #494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight: bold;">Customer Information</div>\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\n<tbody>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="50%">\n<div><strong>{order_billing_add_lbl}</strong></div>\n<div>{billingaddress}</div>\n</td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="50%">\n<div><strong>{order_mailing_add_lbl}</strong></div>\n<div>{mailingaddress}</div>\n</td>\n</tr>\n</tbody>\n</table>\n<div style="font-family: arial; background: #494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight: bold;">Order Items</div>\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\n<tbody>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\n</tr>\n</tbody>\n</table>\n<div><!-- {item_loop_start} -->\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\n<tbody>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\n</tr>\n</tbody>\n</table>\n<!-- {item_loop_end} --></div>\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\n<tbody>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"> </td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"> </td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\n</tr>\n<tr>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"> </td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\n</tr>\n</tbody>\n</table>', 1, 'site'),
(2, 'Invoice Mail', 'email_invoice', '<div style="font-family: arial; font-size: 20px; margin-bottom: 20px;">INVOICE DETAILS</div>\r\n<div style="font-family: arial; background: #494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight: bold;">Order Information</div>\r\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\r\n<tbody>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family: arial; background: #494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight: bold;">Customer Information</div>\r\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\r\n<tbody>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family: arial; background: #494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight: bold;">Order Items</div>\r\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\r\n<tbody>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\r\n<tbody>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table style="border: 1px solid #a3a19c; border-collapse: collapse; margin-bottom: 20px;" border="0" width="100%" cellspacing="0">\r\n<tbody>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border: 1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, 'admin'),
(3, 'Fraud Order Notification', 'fraud_order_email', '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">Fraud Order Warning</div>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%">{orderid_lbl}:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%">{order_status_lbl}:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%">{order_cdate_lbl}:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%">{ordertotal_lbl}:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{fraud_data}</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, 'admin');

INSERT IGNORE INTO `#__pago_mail_templates` (`pgemail_id`, `pgemail_name`, `pgemail_type`, `pgemail_body`, `pgemail_enable`, `template_for`) VALUES
(4, 'Order status update', 'email_update_order_status', '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">Order Status Info</div>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td>{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_detail_link_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_detail_link}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{tracking_number_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{tracking_number}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Items</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{order_item_ship_method_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_item_ship_method}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{tracking_number_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{tracking_number}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{ordertotal}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Payment Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{payment_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_payment_msg_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Mailing Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_shipmethod_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">{order_customernote_lbl}</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, 'site');

INSERT IGNORE INTO `#__pago_mail_templates` (`pgemail_id`, `pgemail_name`, `pgemail_type`, `pgemail_body`, `pgemail_enable`, `template_for`) VALUES
(6, 'Your most recent invoice payment failed', 'email_invoice_failed', '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">INVOICE PAYMENT FAILED</div>\r\n<p>Unfortunately your most recent invoice payment was declined. \r\n    This could be due to a change in your card number or your card expiring, cancelation of your credit card, \r\n    or the bank not recognizing the payment and taking action to prevent it.</p>\r\n<p>Over the next week other attempts will be made to draw down payment. You can use this time to resolve any balance issues.</p>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Items</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{order_item_ship_method_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_item_ship_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" > </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Payment Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{payment_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_payment_msg_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Mailing Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_shipmethod_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">{order_customernote_lbl}</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, 'site'),
(7, 'Your most recent invoice payment failed', 'email_invoice_failed', '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">INVOICE PAYMENT FAILED</div>\r\n<p>Unfortunately your most recent invoice payment was declined. \r\n    This could be due to a change in your card number or your card expiring, cancelation of your credit card, \r\n    or the bank not recognizing the payment and taking action to prevent it.</p>\r\n<p>Over the next week other attempts will be made to draw down payment. You can use this time to resolve any balance issues.</p>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Items</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{order_item_ship_method_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_item_ship_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" > </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Payment Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{payment_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_payment_msg_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Mailing Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_shipmethod_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">{order_customernote_lbl}</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_menu`
--

CREATE TABLE IF NOT EXISTS `#__pago_menu` (
  `cat_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `published` int(1) DEFAULT '0',
  PRIMARY KEY (`cat_id`,`menu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_meta_data`
--

CREATE TABLE IF NOT EXISTS `#__pago_meta_data` (
  `id` int(11) NOT NULL,
  `type` enum('item','category') NOT NULL,
  `html_title` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `robots` varchar(100) NOT NULL,
  `keywords` varchar(1024) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_orders`
--

CREATE TABLE IF NOT EXISTS `#__pago_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_email` varchar(255) DEFAULT NULL,
  `vendor_id` int(11) NOT NULL DEFAULT '0',
  `order_number` varchar(32) DEFAULT NULL,
  `user_info_id` varchar(32) DEFAULT NULL,
  `payment_gateway` varchar(100) NOT NULL,
  `order_total` decimal(15,5) NOT NULL DEFAULT '0.00000',
  `order_subtotal` decimal(15,5) DEFAULT NULL,
  `order_refundtotal` decimal(15,5) NOT NULL,
  `order_tax` decimal(10,2) DEFAULT NULL,
  `order_tax_details` text NOT NULL,
  `order_shipping` decimal(10,2) DEFAULT NULL,
  `order_shipping_tax` decimal(10,2) DEFAULT NULL,
  `coupon_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `coupon_code` varchar(32) DEFAULT NULL,
  `order_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `order_currency` varchar(16) DEFAULT NULL,
  `order_status` char(4) DEFAULT NULL,
  `cdate` datetime NOT NULL,
  `mdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ship_method_id` varchar(255) DEFAULT NULL,
  `customer_note` text NOT NULL,
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `ipn_dump` longtext NOT NULL,
  `payment_message` varchar(255) NOT NULL,
  `tracking_number` varchar(255) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_user_email` (`user_email`),
  KEY `idx_orders_vendor_id` (`vendor_id`),
  KEY `idx_orders_order_number` (`order_number`),
  KEY `idx_orders_user_info_id` (`user_info_id`),
  KEY `idx_orders_ship_method_id` (`ship_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Used to store all orders' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_orders_addresses`
--

CREATE TABLE IF NOT EXISTS `#__pago_orders_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `address_type` char(2) DEFAULT NULL,
  `address_type_name` varchar(32) DEFAULT NULL,
  `company` varchar(64) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `first_name` varchar(32) DEFAULT NULL,
  `middle_name` varchar(32) DEFAULT NULL,
  `phone_1` varchar(32) DEFAULT NULL,
  `phone_2` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `address_1` varchar(64) NOT NULL DEFAULT '',
  `address_2` varchar(64) DEFAULT NULL,
  `city` varchar(32) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` varchar(32) NOT NULL DEFAULT 'US',
  `zip` varchar(32) NOT NULL DEFAULT '',
  `user_email` varchar(255) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `mdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `perms` varchar(40) NOT NULL DEFAULT 'shopper',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Customer Information, BT = BillTo and ST = ShipTo' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_orders_items`
--

CREATE TABLE IF NOT EXISTS `#__pago_orders_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,5) NOT NULL,
  `price_type` varchar(20) NOT NULL,
  `sub_recur` varchar(20) NOT NULL,
  `sub_status` varchar(20) NOT NULL,
  `sub_payment_data` longtext NOT NULL,
  `attributes` text,
  `varation_id` int(11) NOT NULL,
  `order_item_shipping` decimal(10,2) unsigned DEFAULT NULL,
  `order_item_ship_method_id` varchar(255) NOT NULL,
  `order_item_tax` decimal(10,2) unsigned DEFAULT NULL,
  `order_item_shipping_tax` decimal(10,2) unsigned DEFAULT NULL,
  `order_item_status` CHAR(4) NOT NULL,
  `tracking_number` VARCHAR(255) NOT NULL,
  UNIQUE KEY `order_id` (`order_id`,`item_id`),
  PRIMARY KEY (`order_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_orders_log`
--

CREATE TABLE IF NOT EXISTS `#__pago_orders_log` (
  `order_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  PRIMARY KEY (`order_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_orders_sub_payments`
--

CREATE TABLE IF NOT EXISTS `#__pago_orders_sub_payments` (
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `txn_id` varchar(100) NOT NULL,
  `sdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment` decimal(10,5) NOT NULL,
  `status` tinytext NOT NULL,
  `payment_data` longtext NOT NULL,
  `isfraud` tinyint(4) NOT NULL,
  `fraud_message` text NOT NULL,
  `card_number` int(11) NOT NULL,
  `payment_capture_status` ENUM( 'Authorized', 'Captured' ) NULL DEFAULT NULL,
  UNIQUE KEY `order_item_id` (`order_id`,`item_id`,`txn_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_params`
--

CREATE TABLE IF NOT EXISTS `#__pago_params` (
  `name` varchar(100) NOT NULL,
  `value` longtext NOT NULL,
  `serialized` int(1) NOT NULL DEFAULT '0',
  `namespace` varchar(100) NOT NULL DEFAULT 'global',
  `group` varchar(100) NOT NULL,
  PRIMARY KEY (`name`,`namespace`,`group`),
  KEY `namespace` (`namespace`),
  KEY `group_xref` (`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__pago_params`
--

INSERT IGNORE INTO `#__pago_params` (`name`, `value`, `serialized`, `namespace`, `group`) VALUES
('ajax_checkout', '0', 0, 'default', 'checkout'),
('force_checkout_register', '0', 0, 'default', 'checkout'),
('title', '', 0, 'default', 'general'),
('company', '', 0, 'default', 'general'),
('fax', '', 0, 'default', 'general'),
('phone_2', '', 0, 'default', 'general'),
('phone_1', '', 0, 'default', 'general'),
('zip', '', 0, 'default', 'general'),
('state', '', 0, 'default', 'general'),
('country', 'AF', 0, 'default', 'general'),
('city', '', 0, 'default', 'general'),
('address_2', '', 0, 'default', 'general'),
('address_1', '', 0, 'default', 'general'),
('last_name', '', 0, 'default', 'general'),
('middle_name', '', 0, 'default', 'general'),
('first_name', '', 0, 'default', 'general'),
('description', '', 0, 'default', 'general'),
('online', '1', 0, 'default', 'general'),
('store_email', '', 0, 'default', 'general'),
('pago_store_slogan', 'Commerce Evolved', 0, 'default', 'general'),
('pago_store_name', 'paGO Commerce', 0, 'default', 'general'),
('files_meta', 'a:1:{s:1:"N";s:1:"o";}', 1, 'global', 'media'),
('files_file_path', 'media/pago/files', 0, 'global', 'media'),
('category_file_path', 'media/pago/', 0, 'global', 'media'),
('category_url_path', 'media/pago/', 0, 'global', 'media'),
('allowed_download_extension', 'zip,doc,docx,rar,gzip', 0, 'global', 'media'),
('image_sizes', 'a:4:{s:9:"thumbnail";a:3:{s:5:"width";s:3:"150";s:6:"height";s:3:"150";s:4:"crop";s:1:"1";}s:6:"medium";a:3:{s:5:"width";s:3:"250";s:6:"height";s:3:"250";s:4:"crop";s:1:"1";}s:5:"large";a:3:{s:5:"width";s:3:"706";s:6:"height";s:3:"598";s:4:"crop";s:1:"0";}s:5:"small";a:3:{s:5:"width";s:3:"100";s:6:"height";s:3:"100";s:4:"crop";s:1:"0";}}', 1, 'global', 'media'),
('user_url_path', 'media/pago/', 0, 'global', 'media'),
('user_file_path', 'media/pago/', 0, 'global', 'media'),
('images_default_access', '0', 0, 'global', 'media'),
('allow_user_uploaded', '1', 0, 'global', 'media'),
('images_auto_publish', '1', 0, 'global', 'media'),
('images_url_path', 'media/pago/', 0, 'global', 'media'),
('images_file_path', 'media/pago/', 0, 'global', 'media'),
('img_thumb_amount_item', '4', 0, 'global', 'media'),
('pago_theme', 'default', 0, 'global', 'template'),
('order_complete', '', 0, 'global', 'checkout'),
('terms_services', '', 0, 'global', 'checkout'),
('calculate_tax_address', 'shipping', 0, 'global', 'checkout'),
('default_tax_address', '0', 0, 'global', 'checkout'),
('display_price_with_tax', '0', 0, 'global', 'checkout'),
('default_tax_class', '1', 0, 'global', 'checkout'),
('shipping_type', '0', 0, 'global', 'checkout'),
('skip_shipping', '0', 0, 'global', 'checkout'),
('ajax_checkout', '0', 0, 'global', 'checkout'),
('force_checkout_register', '0', 0, 'global', 'checkout'),
('title', '', 0, 'global', 'general'),
('company', '', 0, 'global', 'general'),
('fax', '', 0, 'global', 'general'),
('phone_2', '', 0, 'global', 'general'),
('phone_1', '', 0, 'global', 'general'),
('zip', '', 0, 'global', 'general'),
('state', '', 0, 'global', 'general'),
('country', 'AF', 0, 'global', 'general'),
('city', '', 0, 'global', 'general'),
('address_2', '', 0, 'global', 'general'),
('address_1', '', 0, 'global', 'general'),
('store_email', '', 0, 'global', 'general'),
('online', '1', 0, 'global', 'general'),
('description', 'paGO Commerce-Commerce Evolved', 0, 'global', 'general'),
('first_name', '', 0, 'global', 'general'),
('middle_name', '', 0, 'global', 'general'),
('last_name', '', 0, 'global', 'general'),
('pago_store_slogan', 'Commerce Evolved', 0, 'global', 'general'),
('pago_store_name', 'paGO Commerce', 0, 'global', 'general'),
('skip_shipping', '0', 0, 'default', 'checkout'),
('shipping_type', '0', 0, 'default', 'checkout'),
('default_tax_class', '1', 0, 'default', 'checkout'),
('display_price_with_tax', '0', 0, 'default', 'checkout'),
('default_tax_address', '0', 0, 'default', 'checkout'),
('calculate_tax_address', 'shipping', 0, 'default', 'checkout'),
('terms_services', '', 0, 'default', 'checkout'),
('order_complete', '', 0, 'default', 'checkout'),
('pago_theme', 'default', 0, 'default', 'template'),
('img_thumb_amount_item', '4', 0, 'default', 'media'),
('images_file_path', 'media/pago/', 0, 'default', 'media'),
('images_url_path', 'media/pago/', 0, 'default', 'media'),
('images_auto_publish', '1', 0, 'default', 'media'),
('allow_user_uploaded', '1', 0, 'default', 'media'),
('images_default_access', '0', 0, 'default', 'media'),
('user_file_path', 'media/pago/', 0, 'default', 'media'),
('user_url_path', 'media/pago/', 0, 'default', 'media'),
('category_file_path', 'media/pago/', 0, 'default', 'media'),
('category_url_path', 'media/pago/', 0, 'default', 'media'),
('allowed_download_extension', 'zip,doc,docx,rar,gzip', 0, 'default', 'media'),
('image_sizes', 'a:7:{i:1;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}i:2;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}i:3;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}i:4;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}i:5;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}i:6;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}i:7;a:3:{s:5:"width";N;s:6:"height";N;s:4:"crop";N;}}', 1, 'default', 'media'),
('files_file_path', 'media/pago/files', 0, 'default', 'media'),
('files_meta', 'a:1:{i:1;a:2:{i:0;s:1:"N";i:1;s:1:"o";}}', 1, 'default', 'media'),
('pago_cart_itemid', '', 0, 'default', 'checkout'),
('pago_cart_itemid', '', 0, 'global', 'checkout'),
('keep_drop_db_tables', '1', 0, 'global', 'general'),
('show_store_title', '0', 0, 'global', 'general'),
('currency_symbol_display', '1', 0, 'global', 'general'),
('price_seperator', '.', 0, 'global', 'general'),
('pago_theme_style', '0', 0, 'global', 'template'),
('pago_fonts', '0', 0, 'global', 'general'),
('cart_image_size', '1', 0, 'global', 'cart'),
('mini_cart_image_size', '1', 0, 'global', 'cart'),
('show_comments', '1', 0, 'global', 'comments'),
('comment_moderation', '1', 0, 'global', 'comments'),
('comment_guest_submition', '0', 0, 'global', 'comments'),
('comment_replay', '1', 0, 'global', 'comments'),
('comment_replay_notification', '1', 0, 'global', 'comments'),
('product_settings_product_title', '1', 0, 'global', 'search_product_settings'),
('product_settings_product_title_limit', '', 0, 'global', 'search_product_settings'),
('product_settings_link_to_product', '1', 0, 'global', 'search_product_settings'),
('product_settings_product_image', '1', 0, 'global', 'search_product_settings'),
('product_settings_product_per_page', '6', 0, 'global', 'search_product_settings'),
('product_settings_link_on_product_image', '1', 0, 'global', 'search_product_settings'),
('product_settings_featured_badge', '1', 0, 'global', 'search_product_settings'),
('product_settings_quantity_in_stock', '0', 0, 'global', 'search_product_settings'),
('product_settings_short_desc', '1', 0, 'global', 'search_product_settings'),
('product_settings_short_desc_limit', '', 0, 'global', 'search_product_settings'),
('product_settings_desc', '0', 0, 'global', 'search_product_settings'),
('pago_fonts', '0', 0, 'global', 'template'),
('product_settings_desc_limit', '', 0, 'global', 'search_product_settings'),
('product_settings_sku', '1', 0, 'global', 'search_product_settings'),
('product_settings_price', '1', 0, 'global', 'search_product_settings'),
('product_settings_discounted_price', '1', 0, 'global', 'search_product_settings'),
('product_settings_attribute', '0', 0, 'global', 'search_product_settings'),
('product_settings_media', '1', 0, 'global', 'search_product_settings'),
('product_settings_downloads', '0', 0, 'global', 'search_product_settings'),
('product_settings_rating', '0', 0, 'global', 'search_product_settings'),
('product_settings_category', '0', 0, 'global', 'search_product_settings'),
('product_settings_read_more', '1', 0, 'global', 'search_product_settings'),
('product_settings_add_to_cart', '1', 0, 'global', 'search_product_settings'),
('product_settings_add_to_cart_qty', '1', 0, 'global', 'search_product_settings'),
('product_settings_fb', '0', 0, 'global', 'search_product_social_settings'),
('product_settings_tw', '0', 0, 'global', 'search_product_social_settings'),
('product_settings_pinterest', '0', 0, 'global', 'search_product_social_settings'),
('product_settings_google_plus', '0', 0, 'global', 'search_product_social_settings'),
('product_grid_large', '2', 0, 'global', 'search_product_grid_settings'),
('product_grid_medium', '2', 0, 'global', 'search_product_grid_settings'),
('product_grid_small', '2', 0, 'global', 'search_product_grid_settings'),
('product_grid_extra_small', '1', 0, 'global', 'search_product_grid_settings'),
('category_settings_product_image_settings', '{"padding_left":"0","border_left":"0","margin_left":"0","margin_top":"0","border_top":"0","padding_top":"0","image_size":"2","padding_bottom":"0","padding_right":"0","border_right":"0","margin_right":"0","border_bottom":"0","margin_bottom":"0"}', 0, 'global', 'search_product_grid_settings'),
('pago_config_load_bootstrap', '1', 0, 'global', 'template'),
('pago_config_load_use_font_awesome', '1', 0, 'global', 'template');
-- --------------------------------------------------------

--
-- Table structure for table `#__pago_product_varation`
--

CREATE TABLE IF NOT EXISTS `#__pago_product_varation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `price_type` tinyint(1) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `qty_limit` int(11) NOT NULL DEFAULT '1',
  `qty` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `expiry_date` int(11) NOT NULL,
  `var_enable` tinyint(1) NOT NULL,
  `preselected` tinyint(4) NOT NULL DEFAULT '0',
  `default` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_product_varation_rel`
--

CREATE TABLE IF NOT EXISTS `#__pago_product_varation_rel` (
  `varation_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `attr_id` int(11) NOT NULL,
  `opt_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `#__pago_sales`
--

CREATE TABLE IF NOT EXISTS `#__pago_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `sale_start` datetime NOT NULL,
  `sale_end` datetime NOT NULL,
  `condition` varchar(255) DEFAULT NULL,
  `categories` varchar(255) DEFAULT NULL,
  `price_min` decimal(10,2) unsigned DEFAULT NULL,
  `price_max` decimal(10,2) unsigned DEFAULT NULL,
  `quantity_min` int(11) unsigned NOT NULL,
  `quantity_max` int(11) unsigned NOT NULL,
  `amount` decimal(10,2) unsigned DEFAULT NULL,
  `value` varchar(10) NOT NULL,
  `published` int(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `vendor` int(11) DEFAULT NULL,
  `manufacturer` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_taxrules`
--

CREATE TABLE IF NOT EXISTS `#__pago_taxrules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `applytoaddress` varchar(20) NOT NULL DEFAULT 'shipping',
  `group_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `item_type` int(11) NOT NULL,
  `zip` varchar(100) NOT NULL,
  `tax` decimal(10,0) NOT NULL,
  `shipping_exempt` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_tax_class`
--

CREATE TABLE IF NOT EXISTS `#__pago_tax_class` (
  `pgtax_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `pgtax_class_name` varchar(255) NOT NULL,
  `pgtax_class_enable` int(11) NOT NULL,
  PRIMARY KEY (`pgtax_class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__pago_tax_class`
--

INSERT IGNORE INTO `#__pago_tax_class` (`pgtax_class_id`, `pgtax_class_name`, `pgtax_class_enable`) VALUES
(1, 'Demo Tax Class', 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_tax_rates`
--

CREATE TABLE IF NOT EXISTS `#__pago_tax_rates` (
  `pgtax_id` int(11) NOT NULL AUTO_INCREMENT,
  `pgtax_rate_name` varchar(255) NOT NULL,
  `pgtax_class_id` int(11) NOT NULL,
  `pgtax_country` varchar(255) NOT NULL,
  `pgtax_state` varchar(255) NOT NULL,
  `pgzip_from` varchar(255) NOT NULL,
  `pgzip_to` varchar(255) NOT NULL,
  `pgtax_rate` float(10,2) NOT NULL,
  `pgtax_apply_on_shipping` tinyint(4) NOT NULL,
  `pgtax_enable` tinyint(1) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`pgtax_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__pago_tax_rates`
--

INSERT IGNORE INTO `#__pago_tax_rates` (`pgtax_id`, `pgtax_rate_name`, `pgtax_class_id`, `pgtax_country`, `pgtax_state`, `pgzip_from`, `pgzip_to`, `pgtax_rate`, `pgtax_apply_on_shipping`, `pgtax_enable`, `priority`) VALUES
(1, 'Demo Tax rate', 1, 'US', '', '', '', 0.00, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_units`
--

CREATE TABLE IF NOT EXISTS `#__pago_units` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` char(12) NOT NULL,
  `type` varchar(255) NOT NULL,
  `default` tinyint(4) NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `#__pago_units`
--

INSERT IGNORE INTO `#__pago_units` (`id`, `name`, `code`, `type`, `default`, `published`) VALUES
(1, 'Pound', 'pound', 'weight', 1, 1),
(2, 'Kilo Gram', 'kg', 'weight', 0, 0),
(3, 'LBS', 'lbs', 'weight', 0, 0),
(4, 'Gram', 'gram', 'weight', 0, 0),
(5, 'Inch', 'in', 'size', 1, 1),
(6, 'Meter', 'm', 'size', 0, 0),
(7, 'Millimetre', 'mm', 'size', 0, 0),
(8, 'Centimetre', 'cm', 'size', 0, 0),
(9, 'Feet', 'feet', 'size', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_userfield`
--

CREATE TABLE IF NOT EXISTS `#__pago_userfield` (
  `fieldid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `maxlength` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `required` tinyint(4) DEFAULT '0',
  `ordering` int(11) DEFAULT NULL,
  `cols` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `default` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `registration` tinyint(1) NOT NULL DEFAULT '0',
  `shipping` tinyint(1) NOT NULL DEFAULT '0',
  `account` tinyint(1) NOT NULL DEFAULT '1',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  `calculated` tinyint(1) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  `vendor_id` int(11) DEFAULT NULL,
  `params` mediumtext,
  PRIMARY KEY (`fieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Holds the fields for the user information' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_userfield_values`
--

CREATE TABLE IF NOT EXISTS `#__pago_userfield_values` (
  `fieldvalueid` int(11) NOT NULL AUTO_INCREMENT,
  `fieldid` int(11) NOT NULL DEFAULT '0',
  `fieldtitle` varchar(255) NOT NULL DEFAULT '',
  `fieldvalue` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `sys` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fieldvalueid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Holds the different values for dropdown and radio lists' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_user_info`
--

CREATE TABLE IF NOT EXISTS `#__pago_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `address_type` char(2) DEFAULT NULL,
  `address_type_name` varchar(32) DEFAULT NULL,
  `company` varchar(64) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `last_name` varchar(32) DEFAULT NULL,
  `first_name` varchar(32) DEFAULT NULL,
  `middle_name` varchar(32) DEFAULT NULL,
  `phone_1` varchar(32) DEFAULT NULL,
  `phone_2` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `address_1` varchar(64) NOT NULL DEFAULT '',
  `address_2` varchar(64) DEFAULT NULL,
  `city` varchar(32) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `country` varchar(32) NOT NULL DEFAULT 'US',
  `zip` varchar(32) NOT NULL DEFAULT '',
  `user_email` varchar(255) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `mdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `perms` varchar(40) NOT NULL DEFAULT 'shopper',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `address_type` (`address_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Customer Information, BT = BillTo and ST = ShipTo' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_view_templates`
--

CREATE TABLE IF NOT EXISTS `#__pago_view_templates` (
  `pgtemplate_id` int(11) NOT NULL AUTO_INCREMENT,
  `pgtemplate_name` varchar(255) NOT NULL,
  `pgtemplate_type` varchar(255) NOT NULL,
  `pgtemplate_parent_section` varchar(255) NOT NULL,
  `pgtemplate_enable` tinyint(4) NOT NULL,
  `pgtemplate_body` text NOT NULL,
  PRIMARY KEY (`pgtemplate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__pago_view_templates`
--

INSERT IGNORE INTO `#__pago_view_templates` (`pgtemplate_id`, `pgtemplate_name`, `pgtemplate_type`, `pgtemplate_parent_section`, `pgtemplate_enable`, `pgtemplate_body`) VALUES

(1, 'Order Receipt', 'order_receipt', 'order', 1, '<p style="padding-bottom:15px;border-bottom:1px solid #a3a19c;font-weight:bold;margin-top:50px;">{order_receipt_lbl}</p>\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-collapse: separate ; background-color: #ffffff"><thead>\r\n<tr>\r\n<th colspan="2" style="color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{order_information_lbl}\r\n</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{orderid_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_status_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_tax_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_ship_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_disc_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_coupon_disc_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{ordertotal_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n<div class="table-responsive"  style="margin-top:50px;">\r\n<table class="table" style="border-collapse: separate ;max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-spacing: 0px; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style="line-height:46px;font-size: 20px;color:white;background-color: #494646;border-bottom: 0;">{customer_information_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td>\r\n<table class="table" style="border-collapse: separate;">\r\n<tbody>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{order_billing_add_lbl}</td>\r\n</tr>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{billingaddress}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td style="border-left: 1px solid #a3a19c;">\r\n<table class="table" style="border-collapse: separate ;border:none;">\r\n<tbody>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{order_mailing_add_lbl}</td>\r\n</tr>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{mailingaddress}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="border-collapse: separate ;max-width: 90%;margin:0 auto;border: 1px solid #a3a19c; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style=" color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{order_items_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;  width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;border-left:none;">{item_name_lbl}</td>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;  width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;">{item_quantity_lbl}</td>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c; width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;">{item_sku_lbl}</td>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;  width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;">{item_price_lbl}</td>\r\n</tr>{items_det}\r\n<tr>\r\n<td style="border-left:0px;padding: 0px;">\r\n<table class="table" style="margin-left:50%;width:50%; border-collapse: separate ;margin-bottom: 0px;">\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;">{order_subtotal_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;border-left: 1px solid #a3a19c;">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;">{order_ship_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;border-left: 1px solid #a3a19c;">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;">{ordertotal_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;border-left: 1px solid #a3a19c;">{ordertotal}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-collapse: separate ; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style="color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{mailing_information_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_shipmethod_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Table structure for table `#__pago_currency_cource`
--

CREATE TABLE IF NOT EXISTS `#__pago_currency_cource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `cource` varchar(1500) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__pago_comments`
--

CREATE TABLE IF NOT EXISTS `#__pago_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `author_name` varchar(500) NOT NULL,
  `author_email` varchar(500) NOT NULL,
  `author_web_site` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__pago_item_rating`
--

CREATE TABLE IF NOT EXISTS `#__pago_item_rating` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__pago_discount_categories`
--

CREATE TABLE IF NOT EXISTS `#__pago_discount_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_rule_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__pago_discount_items`
--

CREATE TABLE IF NOT EXISTS `#__pago_discount_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_rule_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `#__pago_discount_rules`
--

CREATE TABLE IF NOT EXISTS `#__pago_discount_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` text NOT NULL,
  `discount_message` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_use_per_user` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `discount_type` int(11) NOT NULL,
  `discount_amount` float NOT NULL,
  `discount_event` int(11) NOT NULL,
  `discount_filter` int(11) NOT NULL,
  `discount_filter_value` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `#__pago_product_varation_rel` ADD INDEX ( `item_id` );
ALTER TABLE `#__pago_product_varation_rel` ADD INDEX ( `attr_id` );
ALTER TABLE `#__pago_product_varation_rel` ADD INDEX ( `opt_id` );
ALTER TABLE `#__pago_product_varation` ADD INDEX ( `item_id` );

--
-- Table structure for table `#__pago_search_keywords`
--

CREATE TABLE IF NOT EXISTS `#__pago_search_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pgkeyword` varchar(255) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
