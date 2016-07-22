<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/kview.php';

class PagoViewCheckout extends PagoView
{
	public function display( $tpl = null )
	{
		JPluginHelper::importPlugin( 'pago_products' );
		$dispatcher = JDispatcher::getInstance();
		$results    = $dispatcher->trigger('onProductPrice');
		
		// Using Single Page Checkout as default - process is the thankyou page
		//so we don't override this
		if(JFactory::getApplication()->input->get('task') != 'complete')
			return $this->singlepage( $tpl );
		
		Pago::load_helpers( 'imagehandler' );

		$this->set( 'checkout' );
		$document = JFactory::getDocument();
		$cart = Pago::get_instance('cart')->get();
		$user = JFactory::getUser();
		JLoader::register('NavigationHelper', JPATH_COMPONENT . '/helpers/navigation.php');
		$nav = new NavigationHelper;
		$terms = JFactory::getApplication()->input->get('terms');

		$config = Pago::get_instance( 'config' )->get();
		$layout = $config->get( 'checkout.checkout_custom_layout' );
		if($layout != "")
		{
			$this->set_theme($layout);
		}
		$storeName = $config->get( 'general.pago_store_name' );

		$this->assignRef( 'storeName', $storeName );
		$this->assignRef( 'terms', $terms ); 
		$this->assignRef('nav', $nav);
		$this->assignRef( 'document', $document );
		$this->assignRef( 'cart', $cart );
		parent::display( $tpl );
	}
	
	public function singlepage( $tpl = null )
	{
		$template   = Pago::get_instance( 'template' );
		$pago_theme = $this->config->get( 'checkout.checkout_custom_layout', 'default' );
		
		$paths = $template->find_paths( $pago_theme, 'checkout' );
		
		define('JS_PATH',  $this->baseurl . '/components/com_pago/javascript/' );
		define('CSS_PATH', $this->baseurl . '/components/com_pago/css/' );
		
		setcookie( 'apiUrl',       JURI::base( true ) . '/index.php?option=com_pago&view=api&format=json' );
		setcookie( 'componentUrl', JURI::base( true ) . '/index.php?option=com_pago' );
		setcookie( 'cartUrl',      JRoute::_( JURI::base( true ) . '/index.php?option=com_pago&view=cart' ) );
		setcookie( 'passUrl',      JRoute::_( JURI::base( true ) . '/index.php?option=com_users&view=reset' ) );
		setcookie( 'sucsUrl',      JRoute::_( JURI::base( true ) . '/index.php?option=com_pago&view=checkout&task=complete&clear=1' ) );
		setcookie( 'tmplUrl',      $paths[6] . 'checkout/' );
		
		$this->_layout = 'singlepage';
		
		$document = JFactory::getDocument();
		
		$document->addStyleSheet($paths[6] . 'css/bootstrap.min.css');
		$document->addStyleSheet($paths[6] . 'css/pago.singlepage.css');
		
		// Others
		$document->addScript($this->baseurl . '/media/jui/js/jquery.min.js');
		$document->addScript($this->baseurl . '/media/jui/js/bootstrap.min.js');
		
		// Angular source
		$document->addScript(JS_PATH . 'angular.min.js');
		$document->addScript(JS_PATH . 'angular-messages.min.js');
		$document->addScript(JS_PATH . 'angular-cookies.min.js');
		
		// ngForm directive improved
		$document->addScript(JS_PATH . 'angular-ngformfixes.js');
		
		// Modules
		$document->addScript(JS_PATH . 'pago.singlepage.app.js');
		$document->addScript(JS_PATH . 'pago.singlepage.cart.js');
		$document->addScript(JS_PATH . 'pago.singlepage.users.js');
		$document->addScript(JS_PATH . 'pago.singlepage.addresses.js');
		$document->addScript(JS_PATH . 'pago.singlepage.shipping.js');
		$document->addScript(JS_PATH . 'pago.singlepage.paygate.js');
		
		// Services / Providers
		$document->addScript(JS_PATH . 'pago.singlepage.api.js');
		$document->addScript(JS_PATH . 'pago.singlepage.config.js');
		
		// Directives
		$document->addScript(JS_PATH . 'pago.singlepage.utils.js');

		parent::display($tpl);
	}
	
	public function spc( $tpl = null )
	{
		$this->_layout = 'spc';
		
		define('JS_PATH', $this->baseurl . DS . 'components' . DS . 'com_pago' . DS . 'javascript' . DS);
		define('CSS_PATH', $this->baseurl . DS . 'components' . DS . 'com_pago' . DS . 'css' . DS);
		
		$document = JFactory::getDocument();
		
		// Angular Source and Modules
		$document->addScript(JS_PATH . 'angular.min.js');
		$document->addScript(JS_PATH . 'angular-resource.min.js');
		
		// App
		$document->addScript(JS_PATH . 'spc/pago.spc.js');
		
		// API Interface
		$document->addScript(JS_PATH . 'spc/providers/pago.api.js');
		
		// Resources 
		$document->addScript(JS_PATH . 'spc/resources/pago.users.js');
		
		parent::display($tpl);
	}
}