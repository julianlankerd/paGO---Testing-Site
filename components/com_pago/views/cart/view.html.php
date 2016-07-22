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
class PagoViewCart extends PagoView
{
	function display( $tpl = null )
	{
		$document = JFactory::getDocument();
		$app      = JFactory::getApplication();
		$pathway  = $app->getPathWay();
		$itemid = JFactory::getApplication()->input->getInt('itemid');

		Pago::load_helpers( array( 'imagehandler','attributes' ) );

		// Set view from template switcher
		$config = Pago::get_instance( 'config' )->get();

        $layout = $config->get( 'cart.cart_custom_layout', 'default' );
        
        if(!$layout) $layout = 'default';
        
        if($layout != "")
        {
            $this->set_theme($layout);
        }
		$this->set( 'cart' , $layout );
		JFactory::getSession()->clear( 'order_id', 'pago_cart' );
		JFactory::getSession()->clear( 'payment_option', 'pago_cart' );
		JFactory::getSession()->clear( 'carrier', 'pago_cart' );
		JFactory::getSession()->clear( 'cart_finalized', 'pago_cart' );
		JFactory::getSession()->clear( 'vendors', 'pago_cart' );

		$document->setTitle( JText::_( 'PAGO_VIEWCART' ) );

		$pathway->addItem( JText::_( 'PAGO_VIEWCART' ) , false);

		$this->session = JFactory::getSession();
		$referer_cid = $this->session->get('referer_cid', array(), 'pago_cart');

		$cart = Pago::get_instance( 'cart' )->get();

		if(isset($cart['items'])){
			$cart['items'] = PagoAttributesHelper::get_attribute_for_cart($cart['items']);
		}

		if(empty($cart)){
			JError::raiseNotice(false, JText::_('PAGO_CART_IS_EMPTY') );
		}

		$dispatcher = KDispatcher::getInstance();
		JPluginHelper::importPlugin( 'pago_gateway' );
		$express_payment_options = array();
		$dispatcher->trigger(
			'express_payment_set_options',
			array( &$express_payment_options )
		);
		JLoader::register('NavigationHelper', JPATH_COMPONENT . '/helpers/navigation.php');
		$nav = new NavigationHelper;
		$this->assignRef('nav', $nav);
		$this->assignRef( 'express_payment_options', $express_payment_options );
		$this->assignRef( 'referer_cid', $referer_cid );
		$this->assignRef( 'itemid', $itemid );
		$this->assignRef( 'cart', $cart );
		$this->assignRef( 'itemsAttributes', $itemsAttributes );
	
		parent::display($tpl);
	}
}
?>
