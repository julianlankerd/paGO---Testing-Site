<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/kview.php';
/**
 * HTML View class for the Pago  component
 */
class PagoViewAccount extends PagoView
{
	function display( $tpl = null )
	{
		// Initialize some variables
		$document	= JFactory::getDocument();
		$app 		= JFactory::getApplication();
		$pathway    = $app->getPathWay();
		$user       = JFactory::getUser();
		JLoader::register( 'NavigationHelper', JPATH_COMPONENT .'/helpers/navigation.php');
		$nav = new NavigationHelper();

		// Set view from template switcher
		$config = Pago::get_instance( 'config' )->get();

        $layout = $config->get( 'account.account_custom_layout' );
       
        if(!$layout) $layout = 'default'; 
        
        if($layout != "")
        {
            $this->set_theme($layout);
        }
		$this->set( 'account' );
		// Get Customer information
		$user_model  = JModelLegacy::getInstance( 'Customers', 'PagoModel' );

		
		//Dirty hack to change days for order-history
		if( !$days = JFactory::getApplication()->input->get('days') ) {
			if( JFactory::getApplication()->input->get('layout') == 'order_history' || JFactory::getApplication()->input->get('layout') == 'order-history'){
				$days = 90;
				parent::setLayout('order_history');				
			}
			else
				$days = 15;
		}

		$user_info   = $user_model->get_user();
		$orders      = $user_model->get_recent_orders_status($days, JFactory::getApplication()->input->get('startdate'), JFactory::getApplication()->input->get('enddate'));
		$addresses   = $user_model->get_user_addresses();
		$orders_item = $user_model->get_recently_purchased_products();


		// Redirect to login page with redirect back to Account Page
		if( $user->guest && JFactory::getApplication()->input->get( 'layout' ) != 'register' ) {
			//JError::raiseNotice(false, JText::_('PAGO_MUST_REGISTER_TO_ACCESS') );
			parent::setLayout('guest');
			$return = 'index.php?option=com_pago&view=account';
			$this->assignRef('return', $return);
		}

		foreach( $addresses as $address ) {
			$type = $address->address_type;
			$user_addresses[$type][] = $address;
		}

		// Add Styles/Scripts
		PagoHtml::behaviour_jquery();
		// PagoHtml::add_css( JURI::root( true ) . '/components/com_pago/javascript/uploadify/uploadifive.css' );
		// PagoHtml::add_js( JURI::root( true ). '/components/com_pago/javascript/uploadify/jquery.uploadifive.js' );
		PagoHtml::loadUploadifive();
		
		// Lets check to see if user has user data - if not lets have them fill it in
		//if( !$user_info && !$user->guest ) {
		//	if( JRequest::getVar( 'layout' ) != 'register' ) {
		//		$app->redirect( 'index.php?option=com_pago&view=account&layout=register' );
		//	}
		//}

		$this->assign( 'option', 'com_pago' );
		$this->assign( 'view',   'category' );

		$startdate = JFactory::getApplication()->input->get('startdate');
		$enddate = JFactory::getApplication()->input->get('enddate');
		$this->assignRef( 'startdate',   $startdate );
		$this->assignRef( 'enddate',     $enddate );

		$this->assignRef( 'user',            $user );
		$this->assignRef( 'user_info',       $user_info );
		$this->assignRef( 'orders',          $orders );
		$this->assignRef( 'document',        $document );
		$this->assignRef( 'pathway',         $pathway );
		$this->assignRef( 'addresses',       $user_addresses );
		$this->assignRef( 'orders_item',     $orders_item );
		$this->assignRef( 'nav', $nav );

		// Order Receipt
		$orders_model = JModelLegacy::getInstance( 'Orders', 'PagoModel' );
		$order       = $orders_model->getOrder(true);
		if(JFactory::getApplication()->input->get( 'layout' ) == 'order_receipt' && !$order){
			parent::setLayout('order_history');		
		}
		$this->assignRef( 'order',       $order );

		/* Affiliate specific
		$affiliate           = JModel::getInstance( 'Affiliates', 'PagoModel' );

		$affiliate_data      = $affiliate->get_affiliate_data();
		$affiliate_sales     = $affiliate->get_affiliate_sales();
		$affiliate_signups   = $affiliate->get_affiliate_signup_count();
		$affiliate_referrals = $affiliate->get_affiliate_referral_list();

		$this->assignRef( 'affiliate_data',      $affiliate_data );
		$this->assignRef( 'affiliate_sales',     $affiliate_sales );
		$this->assignRef( 'affiliate_signups',   $affiliate_signups );
		$this->assignRef( 'affiliate_referrals', $affiliate_referrals );*/

		parent::display($tpl);
	}
}
?>
