<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PagoViewItems extends JViewLegacy
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
 
	public function display( $tpl = null )
	{
		switch( $this->_layout ){
			case 'form': $this->display_form(); parent::display( $tpl ); return;
			case 'copy': $this->display_copy(); parent::display( $tpl ); return;
		}

		// Initialise variables.
		$this->secondary_categories = $this->get( 'Secondary_categories' );
		$this->categories = $this->get( 'Category_list' );

		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}


		////////// Our tool bar
		$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_PUBLISHED'), 'class' => 'publish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_UNPUBLISH'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'copy', 'text' => JTEXT::_('PAGO_COPY'), 'class' => 'copy pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'new', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'MassMove', 'text' => JTEXT::_('PAGO_MASS_MOVE'), 'class' => 'massmove pg-btn-medium pg-btn-dark');
		
		$this->assignRef( 'top_menu',  $top_menu );
		
		////////  Our tool bar end

		$this->items		= $this->parse_items( $this->get( 'Items' ) );
		$this->ordering		= array();
		
		foreach ($this->items as $item)
		{
			$this->ordering[] = $item->id;
		}
		parent::display($tpl);
	}

	function display_form(){		
		$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );

		
		Pago::load_helpers( 'pagoparameter' );

		$model->setId( $cid[0] );

		$item = $model->getData();

		//toolbar

		$title = JText::_( 'PAGO_ITEMS_MANAGER' );
		$desc = JText::_( 'PAGO_ITEMS_MANAGER_DESC' ) . $item->name;
		$text = JFactory::getApplication()->input->get('cid',array(0),'array') ? JText::_( 'PAGO_EDIT' ) : JText::_( 'PAGO_NEW' );
		$title .= ': <small><small>[ ' . $text.' ]</small></small>';
		
		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green');
		$top_menu[] = array('task' => 'save2new', 'text' => JTEXT::_('PAGO_SAVE_AND_NEW'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'cancel pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );
		// JToolBarHelper::title( $title, 'generic.png' );
		// JToolBarHelper::save();
		// JToolBarHelper::apply();

		// if ( JFactory::getApplication()->input->get('cid',array(0),'array') )  {
		// 	JToolBarHelper::cancel();
		// } else {
		// 	JToolBarHelper::cancel( 'cancel', JText::_( 'PAGO_CANCEL' ) );
		// }

		$item = (array)$item;
		$item['name'] = html_entity_decode($item['name']);

		foreach( (array)$item as $k=>$v ){
			if( !strstr($k, '*') ){
				$item['basic'][ $k ] = $v;
			}
		}

		$item_bind=array(
			'params' => (array)$item['basic'],
			'base' => (array)$item['basic'],
			'pricing' => (array)$item['basic'],
			'dimensions' => (array)$item['basic'],
			'meta' => (array)$item['basic'],
			'images' => (array)$item['basic'],
			'files' => (array)$item['basic'],
			'badges' => (array)$item['basic'],
			'attributes' => (array)$item['basic']

		);

		$item = (object)$item;

		// PagoParameter Overrides JParameter render to put pago class names in html
		$params = new PagoParameter( $item_bind,  $cmp_path . 'views/items/metadata.xml' );

		JForm::addfieldpath( array(
			$cmp_path . DS . 'elements'
			)
		);

		if ( !$base_params = $params->render( 'params', 'base', JText::_( 'PAGO_ITEMS_TITLE_GENERAL_PARAMETERS' ), 'details' , '', false ))
			$base_params = JText::_( 'PAGO_ITEMS_ERROR_GENERAL_PARAMETERS' );
			
		if ( !$subscription_params_price = $params->render( 'params', 'subscription_price', JText::_( 'PAGO_ITEMS_TITLE_SUBSCRIPTION_PARAMETERS' ), '' , '', false ))
			$subscription_params_price = JText::_( 'PAGO_ITEMS_ERROR_GENERAL_PARAMETERS' );
		
		if ( !$subscription_params_interval = $params->render( 'params', 'subscription_interval', JText::_( 'PAGO_ITEMS_TITLE_SUBSCRIPTION_PARAMETERS' ), '' , '', false ))
			$subscription_params_interval = JText::_( 'PAGO_ITEMS_ERROR_GENERAL_PARAMETERS' );
		
		if ( !$subscription_params_trial = $params->render( 'params', 'subscription_trial', JText::_( 'PAGO_ITEMS_TITLE_SUBSCRIPTION_PARAMETERS' ), '' , '', false ))
			$subscription_params_trial = JText::_( 'PAGO_ITEMS_ERROR_GENERAL_PARAMETERS' );

		if ( !$badges_params = $params->render( 'params', 'badges', 'Badges', 'badges', '', false ) )
			$badges_params = 'PAGO_ITEMS_ERROR_BADGES_PARAMETERS';

		if ( !$tax_params = $params->render( 'params', 'tax', "Tax", 'tax-parameters', '', false ) )
			$tax_params = JText::_( 'PAGO_ITEMS_ERROR_TAX_PARAMETERS' );

		if ( !$short_desc_params = $params->render( 'params', 'short-description', "Short description", 'short-description', '', false ) )
			$short_desc_params = JText::_( 'PAGO_ITEMS_ERROR_SHORT_DESCRIPTION_PARAMETERS' );

		if ( !$long_desc_params = $params->render( 'params', 'long-description', "Long description", 'long-description', '', false ) )
			$long_desc_params = JText::_( 'PAGO_ITEMS_ERROR_LONG_DESCRIPTION_PARAMETERS' );

		if ( !$discount_params = $params->render( 'params', 'discounts', JText::_( 'PAGO_ITEMS_TITLE_DISCOUNT_PARAMETERS' ), 'discounts-parameters no-margin', '', false, false ) )
			$discount_params = JText::_( 'PAGO_ITEMS_ERROR_DISCOUNT_PARAMETERS' );

		if ( !$shipping_params = $params->render( 'params', 'shipping', JText::_( 'PAGO_ITEMS_TITLE_SHIPPING_PARAMETERS' ), 'shipping-parameters', '', false, true, true ) )
			$shipping_params = JText::_( 'PAGO_ITEMS_ERROR_SHIPPING_PARAMETERS' );

		if ( !$downloadable_params = $params->render( 'params', 'downloadable', '', 'downloadable-parameters', '', false,  false, false ) )
			$downloadable_params = false;

		if ( !$dimension_params = $params->render( 'params', 'dimensions', JText::_( 'PAGO_ITEMS_TITLE_DIMENSION_PARAMETERS' ), 'dimension-parameters', '', false, true, true ) )
			$dimension_params = false;

		if ( !$meta_params = $params->render( 'params', 'meta', JText::_( 'PAGO_ITEMS_TITLE_META_PARAMETERS' ), 'meta-parameters', '', false,  true, false) )
			$meta_params = false;

		if ( !$images_params = $params->render( 'params', 'images', JText::_( 'PAGO_ITEMS_TITLE_IMAGES_PARAMETERS1' ), 'image-parameters', '', false ) )
			$images_params = false;

		if ( !$files_params = $params->render( 'params', 'files', JText::_( 'PAGO_ITEMS_TITLE_FILES_PARAMETERS' ) ) )
			$files_params = false;

		if ( !$attribute_params = $params->render( 'params', 'custom_attribute_params', '', 'custom-attr-parameters' ) )
			$attribute_params = false;

		if ( !$related_item_params = $params->render( 'params', 'related_item', JText::_( 'PAGO_ITEM_RELATED_TITLE' ), 'related-products-params', '', false, true, true  ) )
			$related_item_params = false;

		if ( !$category_params = $params->render( 'params', 'category', JText::_( 'PAGO_ITEMS_TITLE_CATEGORY_PARAMETERS' ), 'category', '', false, true, false, false ) )
			$category_params = false;
		
		if ( !$related_category_params = $params->render( 'params', 'related_category', JText::_( 'PAGO_CATEGORY_RELATED_TITLE' ), 'related-category-params', '', false, true, true  ) )
			$related_category_params = false;

		if ( !$view_settings = $params->render( 'params', 'view_settings', false ) ) {
			$view_settings = false;
		}

		if ( !$view_settings_sharings = $params->render( 'params', 'view_settings_sharings', JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_VIEW_SHARING' )) ) {
			$view_settings_sharings = false;
		}
		// TODO : remove currecny commented code before go live
		/*$currenciesModel = JModel::getInstance( 'Currencies', 'PagoModel' );
		$defaultCurrency = $currenciesModel->getDefault();
		if($defaultCurrency->symbol != ''){
			$this->defaultCurrency = $defaultCurrency->symbol;
		}else{
			$this->defaultCurrency = $defaultCurrency->code;
		}*/
		
		$this->assignRef( 'related_category_params',      $related_category_params );
		$this->assignRef( 'base_params',      			  $base_params );
		$this->assignRef( 'subscription_params_price',    $subscription_params_price );
		$this->assignRef( 'subscription_params_interval', $subscription_params_interval );
		$this->assignRef( 'subscription_params_trial',    $subscription_params_trial );
		$this->assignRef( 'badges_params',      		  $badges_params );
		$this->assignRef( 'short_desc_params',      	  $short_desc_params );
		$this->assignRef( 'long_desc_params',      		  $long_desc_params );
		$this->assignRef( 'tax_params',   				  $tax_params );
		$this->assignRef( 'discount_params',   			  $discount_params );
		$this->assignRef( 'shipping_params',   			  $shipping_params );
		$this->assignRef( 'dimension_params', 			  $dimension_params );
		$this->assignRef( 'meta_params',      			  $meta_params );
		$this->assignRef( 'files_params',     			  $files_params );
		$this->assignRef( 'images_params',    			  $images_params );
		$this->assignRef( 'attribute_params', 			  $attribute_params );
		$this->assignRef( 'related_item_params',		  $related_item_params );
		$this->assignRef( 'category_params',  			  $category_params );
		$this->assignRef( 'downloadable_params',      	  $downloadable_params );
		$this->assignRef( 'view_settings',      		  $view_settings );
		$this->assignRef( 'view_settings_sharings',       $view_settings_sharings );
		$this->assignRef( 'item',             			  $item );
	}

	function display_copy($tpl = null){
		$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance( 'Item', 'PagoModel' );


		Pago::load_helpers( 'pagoparameter' );

		$this->state = $this->get( 'State' );
		$this->pagination	= $this->get( 'Pagination' );

		$model->setState( 'cid', $cid );
		$created_ids = $model->copy();

		$params = new PagoParameter( false, $cmp_path . 'views/item/metadata.xml');
		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		// Initialise variables.
		$this->secondary_categories = $this->get( 'Secondary_categories' );
		$this->categories = $this->get( 'Category_list' );

		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );

		$this->secondary_categories = $this->get( 'Secondary_categories' );

		////////// Our tool bar
		$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_PUBLISHED'), 'class' => 'publish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_UNPUBLISH'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'copy', 'text' => JTEXT::_('PAGO_COPY'), 'class' => 'copy pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'new', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'massmove', 'text' => JTEXT::_('PAGO_MASS_MOVE'), 'class' => 'massmove pg-btn-medium pg-btn-dark');
		
		$this->assignRef( 'top_menu',  $top_menu );
		$this->assign( 'items', $this->parse_items( $this->get( 'Items' )) );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{

		$user	= JFactory::getUser();

		JToolBarHelper::publish('publish');
		JToolBarHelper::unpublish('unpublish');
		JToolBarHelper::custom(
				'copy', 'copy.png', 'copy.png', JText::_('PAGO_COPY'), true, false
			);
		JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
		PagoHtml::behaviour_jquery();
		PagoHtml::behaviour_jqueryui();
		PagoHtml::add_css( JURI::root(true) . '/components/com_pago/css/thickbox.css');
		PagoHtml::add_js( JURI::root(true)
			. '/components/com_pago/javascript/jquery.thickbox-3.1.js');
		JToolBarHelper::custom('MassMove', 'copy.png', 'copy.png', JText::_('PAGO_MASS_MOVE'), true, false);
		//JToolBarHelper::divider();
		//JToolBarHelper::preferences('com_pago');
		//JToolBarHelper::divider();
	}

	protected function parse_items( $items ){
		//return $items;

		if( is_array( $items ) ) {
			foreach( $items as $k => $item ) {

				if ( array_key_exists( $item->id, $this->secondary_categories ) ) {
					$item->secondary_categories = $this->secondary_categories[$item->id]['names'];
				}
			}
		}

		return $items;
	}
}
