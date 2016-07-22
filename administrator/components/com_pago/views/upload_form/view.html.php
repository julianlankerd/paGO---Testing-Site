<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.view' );
class PagoViewUpload_Form extends JViewLegacy
{
	function display( $tpl = null )
	{
		$mainframe = JFactory::getApplication();

		$type = JFactory::getApplication()->input->get( 'type' );
		if($type == 'Video'){
			
			$model =  JModelLegacy::getInstance( 'file', 'PagoModel' );
			$providers = $model->getVideoProviders();
			if (count($providers))
	        {

	            foreach ($providers as $provider)
	            {
	                $providersOptions[] = JHTML::_('select.option', $provider, ucfirst($provider));
	            }

	        }
	        
	        $providersList = JHTML::_('select.genericlist', $providersOptions, 'videoProvider', '', 'value', 'text', '');
	        
	        $this->assignRef('providersList', $providersList);
			$this->_layout = "video";
		}

		// Add Styles/Scripts
		PagoHtml::behaviour_jquery();
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/css/pago.css' );
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/css/chosen.css' );
		//var_dump(JURI::root( true ) . '/administrator/components/com_pago/css/styles.css');
		// PagoHtml::add_css( JURI::root( true ) . '/components/com_pago/css/uploadify.css' );
		PagoHtml::add_js( JURI::root( true ) . '/components/com_pago/javascript/swfobject.js', true );
		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/chosen.jquery.js', true );
		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/com_pago.js', true );
		// PagoHtml::add_js( JURI::root( true )
			// . '/components/com_pago/javascript/jquery.uploadify.js' );
		PagoHtml::loadUploadifive();

		
		
		parent::display( $tpl );
	}
}
?>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" type="text/css">
