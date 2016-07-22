<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
/**
 * Pago  Component Controller
 */
class PagoController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$document->setGenerator( 'paGO Commerce: http://www.pagocommerce.com' );
		$document->addScriptDeclaration('var $PRODUCT_NOT_EXIST = "'.JTEXT::_("PAGO_PRODUCT_NOT_EXIST").'"');
		//check if format is set, if so we don't need to do the tmpl
		//processing stuff
		
		$newCurrencyId = JFactory::getApplication()->input->getInt( 'currencyChanger');
		if($newCurrencyId){
			Pago::get_instance( 'cookie' )->set( 'current_currency', $newCurrencyId );	
		}

		$version = new JVersion();

		if($version->RELEASE >= 3){
			JHtml::_('jquery.framework');
		}

		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/models' );

       	$config = Pago::get_instance('config')->get('global');
		$shop_online = $config->get('general.online');
		
		if(!$shop_online)
		{
			JFactory::getApplication()->input->set( 'view', 'offline' );
		}
		else
		{
			if ( !$view = JFactory::getApplication()->input->get( 'view' ) ) 
			{
				JFactory::getApplication()->input->set( 'view', 'frontpage' );
			}
		}

		parent::display($cachable, $urlparams);
	}

	public function getMessage()
	{
		return $this->message;
	}
}
?>
