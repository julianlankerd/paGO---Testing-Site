<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

$configpath = JPATH_COMPONENT . '/helpers/pagoConfig.php';
require_once $configpath;
//dynamically populate tools child links
/*JPluginHelper::importPlugin( 'pago_tools' );

$dispatcher = JDispatcher::getInstance();

$tools_children = array();

foreach( $dispatcher->get( '_observers' ) as $observer ){

		$observer_type = $observer->get ('_type' );

		if( isset( $observer_type ) && $observer_type == 'pago_tools' ){
				$tools_children[ $observer->get( '_name' ) ] = array(
						'name' => $observer->get( '_text' ),
						'link' => 'index.php?option=com_pago&view=tools&plugin=' . $observer->get( '_name' ),
						'view' => 'tools'
				);
		}
}*/

//

$menu_items = array(
		'home' => array(
				'name' => JText::_('PAGO_MENU_HOME'),
				'link' => 'index.php?option=com_pago',
				'view' => ''
		),
		'items' => array(
				'name' => JText::_( 'PAGO_MENU_ITEMS' ),
				'link' => 'index.php?option=com_pago&view=items',
				'view' => 'items',
				'children' => array(
						'items' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_ITEMS' ),
								'link' => 'index.php?option=com_pago&view=items',
								'view' => 'items'
						),
						'categories' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_CATEGORIES' ),
								'link' => 'index.php?option=com_pago&view=categories',
								'view' => 'categories'
						),
						'attributes' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_ATTRIBUTES' ),
								'link' => 'index.php?option=com_pago&view=attributes',
								'view' => 'attributes'
						),
						'comments' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_COMMENTS' ),
								'link' => 'index.php?option=com_pago&view=comments',
								'view' => 'comments'
						),
						'coupons' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_COUPONS' ),
								'link' => 'index.php?option=com_pago&view=coupons',
								'view' => 'coupons'
						),
						'disconts' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_DISCOUNTS' ),
								'link' => 'index.php?option=com_pago&view=discounts',
								'view' => 'discounts'
						),
						'import' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_IMPORT' ),
								'link' => 'index.php?option=com_pago&view=import',
								'view' => 'import'
						),
						'export' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_EXPORT' ),
								'link' => 'index.php?option=com_pago&view=export',
								'view' => 'export'
						),
						'migrate' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_MIGRATION' ),
								'link' => 'index.php?option=com_pago&view=migrate',
								'view' => 'migrate'
						)
						/*,
						'files' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_FILES' ),
								'link' => 'index.php?option=com_pago&view=files',
								'view' => 'attributes'
						),
						'reviews' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_REVIEWS' ),
								'link' => 'index.php?option=com_pago&view=reviews',
								'view' => 'reviews'
						),
						'stock' => array(
								'name' => JText::_( 'PAGO_MENU_STOCK_MANAGEMENT' ),
								'link' => 'index.php?option=com_pago&view=stock',
								'view' => 'stock'
						)*/
				)
		),
		'orders' => array(
				'name' => JText::_( 'PAGO_MENU_ORDERS' ),
				'link' => 'index.php?option=com_pago&view=ordersi',
				'view' => 'ordersi',
				'children' => array(
						/*'orders' => array(
								'name' => JText::_( 'PAGO_MENU_ORDERS' ),
								'link' => 'index.php?option=com_pago&view=orders',
								'view' => 'orders'
								),*/
						/*'ordersi' => array(
								'name' => JText::_( 'PAGO_MENU_ORDERS' ),
								'link' => 'index.php?option=com_pago&view=ordersi',
								'view' => 'orders'
								),
						'shipments' => array(
								'name' => JText::_( 'PAGO_SMENU_HIPMENTS' ),
								'link' => 'index.php?option=com_pago&view=shipments',
								'view' => 'shipments'
						),
						'profiles' => array(
								'name' => JText::_( 'PAGO_MENU_RECURRING_PROFILES' ),
								'link' => 'index.php?option=com_pago&view=subscriptions',
								'view' => 'subscriptions'
						),
						'phone_order' => array(
								'name' => JText::_( 'PAGO_MENU_PHONE_ORDER' ),
								'link' => 'index.php?option=com_pago&view=order&layout=form&task=new',
								'view' => 'order'
						)*/
				)
		),
		'customers' => array(
				'name' => JText::_( 'PAGO_MENU_CUSTOMERS' ),
				'link' => 'index.php?option=com_pago&view=customers',
				'view' => 'customers',
				/*'children' => array(
						'manage_customers' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_CUSTOMERS' ),
								'link' => 'index.php?option=com_pago&view=customers',
								'view' => 'customers'
						),
						'manage_mailchimp' => array(
							'name' => JText::_( 'PAGO_MANAGE_NEWSLETTER' ),
							'link' => 'index.php?option=com_pago&view=newsletter',
							'view' => 'mailing'
						)
						'manage_groups' => array(
								'name' => JText::_( 'PAGO_MENU_MANAGE_GROUPS' ),
								'link' => 'index.php?option=com_pago&view=groups',
								'view' => 'groups'
						),
						'affiliates' => array(
								'name' => JText::_( 'PAGO_MENU_AFFILIATES' ),
								'link' => 'index.php?option=com_pago&view=affiliates',
								'view' => 'affiliates'
						),
						'customers' => array(
								'name' => JText::_( 'PAGO_MENU_ONLINE_CUSTOMERS' ),
								'link' => 'index.php?option=com_pago&view=online',
								'view' => 'online'
						)
				)*/
		),
		'payoptions' => array(
			'name' => JText::_( 'PAGO_MENU_PAYGATES' ),
			'link' => 'index.php?option=com_pago&view=payoptions',
			'view' => 'payoptions',
			'children' => array(
					'transfers' => array(
							'name' => JText::_( 'PAGO_MENU_TRANSFERS' ),
							'link' => 'index.php?option=com_pago&view=transfers',
							'view' => 'transfers'
					)
			)
		),
		'wingman' => array(
			'name' => JText::_( 'PAGO_MENU_WINGMAN' ),
			'link' => 'index.php?option=com_pago&view=wingman',
			'view' => 'wingman'
		),
		'reports' => array(
			'name' => JText::_( 'PAGO_MENU_REPORTS' ),
			'link' => 'index.php?option=com_pago&view=reports',
			 'view' => 'payoptions'
			 ),
		/*'promos' => array(
				'name' => JText::_( 'PAGO_MENU_PROMOS' ),
				'link' => 'index.php?option=com_pago&view=promos',
				'view' => 'promos',
				'children' => array(
						'discounts_sales' => array(
								'name' => JText::_( 'PAGO_MENU_DISCOUNTS_SALES' ),
								'link' => 'index.php?option=com_pago&view=promos',
								'view' => 'promos'
						),
						'coupons' => array(
								'name' => JText::_( 'PAGO_MENU_COUPONS' ),
								'link' => 'index.php?option=com_pago&view=coupons',
								'view' => 'coupons'
						)
				)
		),*/
		/*'vendors' => array(
				'name' => JText::_( 'PAGO_MENU_VENDORS' ),
				'link' => 'index.php?option=com_pago&view=vendors',
				'view' => 'vendors'
		),
		'reports' => array(
				'name' => JText::_( 'PAGO_MENU_REPORTS' ),
				'link' => 'index.php?option=com_pago&view=reports',
				'view' => 'reports'
		),*/
		'extend' => array(
				'name' => JText::_( 'PAGO_MENU_EXTEND' ),
				'link' => 'index.php?option=com_pago&view=plugins&filter_folder=pago_shippers',
				'view' => 'plugins',
				'filter_folder' => 'pago_shippers',
				// We will want to make the children dynamic
				'children' => array(
						/*'reporting_plugins' => array(
								'name' => 'Reporting Plugins',
								'link' => 'index.php?option=com_pago&view=plugins&filter_folder=pago_charts',
								'view' => 'plugins'
						),*/
						/*'payment_gateways' => array(
								'name' => JText::_( 'PAGO_PAYMENT_GATEWAYS' ),
								'link' => 'index.php?option=com_pago&view=plugins&filter_folder=pago_gateway',
								'view' => 'plugins',
								'filter_folder' => 'pago_gateway'
						),*/
						'shipping' => array(
								'name' => JText::_( 'PAGO_SHIPPING' ),
								'link' => 'index.php?option=com_pago&view=plugins&filter_folder=pago_shippers',
								'view' => 'plugins',
								'filter_folder' => 'pago_shippers'
						),
						'security' => array(
								'name' => JText::_( 'PAGO_SECURITY' ),
								'link' => 'index.php?option=com_pago&view=plugins&filter_folder=pago_security',
								'view' => 'plugins',
								'filter_folder' => 'pago_security'
						),
				)
		),
		'config' => array(
				'name' => JText::_('PAGO_MENU_CONFIG'),
				'link' => 'index.php?option=com_pago&view=config',
				'view' => 'config',
				'children' => array(
						'configuration' => array(
								'name' => JText::_('PAGO_MENU_MAIN_CONFIGURATION'),
								'link' => 'index.php?option=com_pago&view=config',
								'view' => 'config'
						),
						'mSystem' => array(
								'name' => JText::_('PAGO_MENU_MANAGE_SYSTEM'),
								'link' => 'index.php?option=com_pago&view=system',
								'view' => 'system'
						),
						'mCustomShipping' => array(
								'name' => JText::_('PAGO_MENU_MANAGE_CUSTOM_SHIPPING'),
								'link' => 'index.php?option=com_pago&view=shippingrules',
								'view' => 'shippingrules'
						),
						'mtaxrules' => array(
								'name' => JText::_('PAGO_MENU_MANAGE_TAX_RULES'),
								'link' => 'index.php?option=com_pago&view=tax_class',
								'view' => 'tax_class'
						),
						'mlocations' => array(
								'name' => JText::_('PAGO_MENU_MANAGE_LOCATIONS'),
								'link' => 'index.php?option=com_pago&view=locations',
								'view' => 'locations'
						),
						'mMailTemplates' => array(
								'name' => JText::_('PAGO_MENU_MANAGE_MAIL_TEMPLATES'),
								'link' => 'index.php?option=com_pago&view=emails',
								'view' => 'emails'

 						),
						'mViewTemplates' => array(
								'name' => JText::_('PAGO_MENU_MANAGE_VIEW_TEMPLATES'),
								'link' => 'index.php?option=com_pago&view=templates',
								'view' => 'templates'

 						)
				)
		),/*
		'tools' => array(
				'name' => JText::_( 'PAGO_MENU_TOOLS' ),
				'link' => 'index.php?option=com_pago&view=tools',
				'view' => 'tools'
		),

		'support' => array(
				'name' => JText::_( 'PAGO_MENU_SUPPORT' ),
				'link' => 'index.php?option=com_pago&view=support',
				'view' => 'support'
		),

		'updates' => array(
				'name' => JText::_( 'PAGO_MENU_UPDATES' ),
				'link' => 'https://headwayapp.co/pago-commerce-changelog',
				'view' => 'updates'
		),
		*/
);

JPluginHelper::importPlugin( 'pago_tools' );

$dispatcher = KDispatcher::getInstance();
$dispatcher->trigger( 'admin_menu', array( &$menu_items ) );
