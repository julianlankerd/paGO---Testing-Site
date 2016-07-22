<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<script type = "text/javascript">
	jQuery(window).load(function(){
		

		if(jQuery('#params_countryparamscountry :selected').length!==1 ){
			jQuery('#params_stateparamsstate').prop('disabled', true);
		};
		if(jQuery('#params_countryparamscountry :selected').length>1){
				jQuery('#params_stateparamsstate').prop('disabled', true);
				
		}
		jQuery(document).on('change','#params_countryparamscountry',function(){
			if(jQuery('#params_countryparamscountry :selected').length!==1){
				jQuery('#params_stateparamsstate').prop('disabled', true);
				
			}else{
				jQuery('#params_stateparamsstate').prop('disabled', false);
			}
		});
		
		jQuery('#params_countryparamscountry').chosen();
		jQuery('#params_stateparamsstate').chosen();
		jQuery('#params_categoryparamscategory').chosen();
		jQuery('#params_itemsparamsitems').chosen();


		var stateCode = [];

		jQuery("#params_stateparamsstate").find('option:selected').each(function(){
			stateCode.push(jQuery(this).val());	
		});
		
		var countryCode = '';
		jQuery("#params_countryparamscountry").find('option:selected').each(function(){
			countryCode += jQuery(this).val()+',';	
		});

		countryCode = countryCode.substring(0, countryCode.length-1);

		jQuery.ajax({
        	type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=config&task=loadState&countryCode=' +countryCode+ '',
			dataType : 'json',
            success : function(data) {
        		jQuery('.pg-stateslist select').html("");
        		jQuery('.pg-stateslist select').trigger('chosen:updated');

            	if(data){
            		jQuery('.pg-stateslist select').append(data);	
					jQuery('.pg-stateslist select').trigger('chosen:updated'); 
					
					
					jQuery("#params_stateparamsstate").find('option').each(function(){
					
		 					
		 					if((stateCode.indexOf( jQuery(this).val()))!==-1 ){
		 					
		 						jQuery(this).attr("selected",true);	
		 						
		 						jQuery('.pg-stateslist select').trigger('chosen:updated');
		 				}
		 
					});
					
				}
            }
        });
   
       
	});
</script>

<?php
/**
 * @version		$Id: view.html.php 21907 2011-07-20 16:23:13Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PagoViewShippingrules extends JViewLegacy
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */

	public function display( $tpl = null ){		
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/css/perfect-scrollbar.css' );
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/css/chosen.css' );
		PagoHtml::add_js(  JURI::root( true ) . '/administrator/components/com_pago/javascript/perfect-scrollbar.js' );
		PagoHtml::add_js(  JURI::root( true ) . '/administrator/components/com_pago/javascript/chosen.jquery.js' );
		PagoHtml::add_js(  JURI::root( true ) . '/administrator/components/com_pago/javascript/com_pago.js' );

		switch ($this->_layout){
			case 'form':
				$this->display_form();
				parent::display($tpl);

				return;
		}

		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// $this->addToolbar();
		$this->items = $this->parse_items($this->get('Items'));
		
		$top_menu = array(
			array(
				'task' => 'publish',
				'text' => JText::_('PAGO_PUBLISH'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'unpublish',
				'text' => JText::_('PAGO_UNPUBLISH'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'new',
				'text' => JText::_('PAGO_NEW'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark pg-btn-green'
			),
			array(
				'task' => 'edit',
				'text' => JText::_('PAGO_EDIT'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'delete',
				'text' => JText::_('PAGO_DELETE'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			)
		);
		
		$this->assignRef('top_menu', $top_menu);
		
		parent::display($tpl);
	}
	
	function display_form(){
		$cid = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance('Shippingrule', 'PagoModel');

		Pago::load_helpers('pagoparameter');

		$model->setId($cid[0]);

		$item = $model->getData();

		// Toolbar

		$title = JText::_('PAGO_CUSTOM_SHIPPING_RULES');
		$desc = JText::_('PAGO_CUSTOM_SHIPPING_RULES_DESC') . $item->rule_name;
		$text = JFactory::getApplication()->input->get('cid', array(0), 'array') ? JText::_('PAGO_EDIT') : JText::_('PAGO_NEW');
		$title .= ': <small><small>[ ' . $text . ' ]</small></small>';

		// JToolBarHelper::title($title, 'generic.png');
		// JToolBarHelper::save();
		// JToolBarHelper::apply();
		// JToolBarHelper::save2new();
		// JToolBarHelper::save2copy();
		
		// if (JFactory::getApplication()->input->get('cid', array(0), 'array')){
		// 	JToolBarHelper::cancel();
		// }
		// else{
		// 	JToolBarHelper::cancel('cancel', JText::_('PAGO_CANCEL'));
		// }

		$item = (array) $item;

		foreach ((array) $item as $k => $v){
			if (!strstr($k, '*')){
				$item['basic'][ $k ] = $v;
			}
		}

		$item_bind = array(
			'params' => (array) $item['basic'],
			'base' => (array) $item['basic'],
			'pricing' => (array) $item['basic'],
			'dimensions' => (array) $item['basic'],
			'meta' => (array) $item['basic'],
			'images' => (array) $item['basic'],
			'files' => (array) $item['basic'],
			'attributes' => (array) $item['basic']
		);

		$item = (object) $item;

		// PagoParameter Overrides JParameter render to put pago class names in html
		$params = new PagoParameter($item_bind,  $cmp_path . 'views/shippingrules/metadata.xml');

		JForm::addfieldpath(array(
			$cmp_path . DS . 'elements'
			)
		);

		if ( !$base_params = $params->render('params', 'base', '')){
			$base_params = JText::_('PAGO_SHIPPING_RULE_ERROR_GENERAL_PARAMETERS');
		}

		if ( !$pricing_params = $params->render('params', 'pricing', '')){
			$pricing_params = JText::_('PAGO_SHIPPING_RULE_ERROR_PRICING_PARAMETERS');
		}

		if ( !$category_params = $params->render('params', 'category', '')){
			$category_params = false;
		}

		if ( !$order_params = $params->render('params', 'order', '')){
			$order_params = false;
		}

		if ( !$weight_params = $params->render('params', 'weight', '')){
			$weight_params = false;
		}

		if ( !$address_params = $params->render('params', 'address', '')){
			$address_params = false;
		}

		$currenciesModel = JModelLegacy::getInstance('Currencies', 'PagoModel');
		$defaultCurrency = $currenciesModel->getDefault();

		if($defaultCurrency->symbol != ''){
			$this->defaultCurrency = $defaultCurrency->symbol;
		}
		else{
			$this->defaultCurrency = $defaultCurrency->code;
		}

		$this->assignRef('base_params',      			$base_params);
		$this->assignRef('pricing_params',   			$pricing_params);
		$this->assignRef('category_params',  			$category_params);
		$this->assignRef('order_params',  			    $order_params);
		$this->assignRef('weight_params',  			    $weight_params);
		$this->assignRef('address_params',  			$address_params);
		$this->assignRef('item',             			$item);
		
		$top_menu = array(
			array(
				'task' => 'save',
				'text' => JText::_('PAGO_SAVE_AND_CLOSE'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'apply',
				'text' => JText::_('PAGO_SAVE'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark pg-btn-green'
			),
			array(
				'task' => 'save2new',
				'text' => JText::_('PAGO_SAVE_AND_NEW'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			),
			array(
				'task' => 'cancel',
				'text' => JText::_('PAGO_CANCEL'),
				'class' => 'pg-btn pg-btn-medium pg-btn-dark'
			)
		);
		
		$this->assignRef('top_menu', $top_menu);
		
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar(){
		$user	= JFactory::getUser();

		JToolBarHelper::publish('publish');
		JToolBarHelper::unpublish('unpublish');
		//JToolBarHelper::custom('copy', 'copy.png', 'copy.png', JText::_('PAGO_COPY'), true, false);
		JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
	}

	protected function parse_items( $items ){
		return $items;
	}	
}