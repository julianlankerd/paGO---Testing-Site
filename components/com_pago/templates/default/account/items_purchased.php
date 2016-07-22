<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
	
	$this->document->setTitle( 'Products Purchased' );
	
	// Load sidemenu template (account/account_menu.php)
	if ( $menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $menu; 
?>