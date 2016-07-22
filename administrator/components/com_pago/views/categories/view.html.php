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

/**
 * View class for a list of Categories.
 *
 * @package		paGO Commerce
 * @subpackage	com_pago
 * @since		1.6
 */
class PagoViewCategories extends JViewLegacy
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
			case 'tree': $this->display_tree(); parent::display( $tpl ); return;
			case 'copy': $this->display_copy_cat(); parent::display( $tpl ); return;
		}

		// Initialise variables.
		$this->secondary_categories = $this->get( 'Secondary_categories' );
		$this->categories = $this->get( 'Category_list' );

		$this->items		= $this->parse_items( $this->get( 'Items' ) );
		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );
		$this->ordering		= array();
		foreach ($this->items as $item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//$this->addToolbar();

		////////// Our tool bar
		$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_PUBLISHED'), 'class' => 'publish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_UNPUBLISH'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'copy', 'text' => JTEXT::_('PAGO_COPY'), 'class' => 'copy pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'new', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');
		//$top_menu[] = array('task' => 'popup-options', 'text' => JTEXT::_('PAGO_OPTIONS'), 'class' => 'popup-options pg-btn-medium pg-btn-dark');
		//$top_menu[] = array('task' => 'help', 'text' => JTEXT::_('PAGO_HELP'), 'class' => 'help pg-btn-medium pg-btn-dark');
		
		$this->assignRef( 'top_menu',  $top_menu );

		parent::display($tpl);
	}
	
	function display_copy_cat($tpl = null)
	{
		$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance('categories', 'PagoModel');
		Pago::load_helpers('pagoparameter');
		$this->state = $this->get('State');
		$this->pagination	= $this->get('Pagination');
		$model->setState('cid', $cid);
		$created_ids = $model->copy_cat();
		$params = new PagoParameter( false, $cmp_path . 'views/categories/metadata.xml');
		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		// Initialise variables.
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->assign('items', $this->parse_items($this->get( 'Items')));
		
		$this->ordering		= array();
		foreach ($this->items as $item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		////////// Our tool bar
		$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_PUBLISHED'), 'class' => 'publish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_UNPUBLISH'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'copy', 'text' => JTEXT::_('PAGO_COPY'), 'class' => 'copy pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'new', 'text' => JTEXT::_('PAGO_NEW'), 'class' => 'new pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');
		//$top_menu[] = array('task' => 'popup-options', 'text' => JTEXT::_('PAGO_OPTIONS'), 'class' => 'popup-options pg-btn-medium pg-btn-dark');
		//$top_menu[] = array('task' => 'help', 'text' => JTEXT::_('PAGO_HELP'), 'class' => 'help pg-btn-medium pg-btn-dark');
		
		$this->assignRef( 'top_menu',  $top_menu );
	}

	function display_form()
	{
		//toolbar

		// $title = JText::_( 'PAGO_ITEMS_MANAGER' );
		// $desc = JText::_( 'PAGO_ITEMS_MANAGER_DESC' ) . $item->name;
		// $text = JFactory::getApplication()->input->get('cid',array(0),'array') ? JText::_( 'PAGO_EDIT' ) : JText::_( 'PAGO_NEW' );
		// $title .= ': <small><small>[ ' . $text.' ]</small></small>';

		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green');
		$top_menu[] = array('task' => 'save2new', 'text' => JTEXT::_('PAGO_SAVE_AND_NEW'), 'class' => 'save2new pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'cancel pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );

		/*JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::save2new();
		JToolBarHelper::cancel();*/

		Pago::load_helpers( 'pagoparameter' );
		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		$model = JModelLegacy::getInstance( 'category', 'PagoModel' );
		$item = $model->getData();
		$item->name = html_entity_decode($item->name);
		//$ini = Pago::get_instance( 'config' )->ini_encode( $item );

		$bind_data = array(
			'params' => $item
		);

		$params = new PagoParameter( $bind_data,  $cmp_path . 'views/categories/metadata.xml' );


		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );
		
		if( !$base_params = $params->render( 'params', 'base', JText::_( 'PAGO_PARAMETERS' ), 'base-parameters', '', false, true, true  ) ){
			$base_params = 'No Base Parameters found';
		}

		if( !$description_params = $params->render( 'params', 'description', JText::_( 'PAGO_CATEGORY_DESCRIPTION' ), 'description-parameters', '', false, true, true  ) ){
			$description_params = 'No Base Parameters found';
		}

		if ( !$meta_params = $params->render( 'params', 'meta', '', 'meta-parameters', '', false, false, false ) ) {
			$meta_params = false;
		}

		if ( !$images_params = $params->render( 'params', 'images', JText::_( 'PAGO_ITEMS_TITLE_IMAGES_PARAMETERS1' ), 'image-parameters', '', false ) )
			$images_params = false;

		if ( !$category_settings = $params->render( 'params', 'category_settings', false, 'category-parameters', '', false, false, false) ) {
			$category_settings = false;
		}

		if ( !$category_settings_image_settings = $params->render( 'params', 'category_settings_image_settings', false, 'category-image-parameters', '', false, false, false) ) {
			$category_settings_image_settings = false;
		}

		if ( !$category_settings_product_image_settings = $params->render( 'params', 'category_settings_product_image_settings', false, 'category-product-image-parameters', '', false, false, false) ) {
			$category_settings_product_image_settings = false;
		}

		if ( !$product_settings = $params->render( 'params', 'product_settings', false, 'product-parameters', '', false, false, false) ) {
			$product_settings = false;
		}

		if ( !$product_settings_sharings = $params->render( 'params', 'product_settings_sharings', JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_SHARING' ), 'product-sharing-parameters', '', false, false, false) ) {
			$product_settings_sharings = false;
		}

		if ( !$product_grid = $params->render( 'params', 'product_grid', false, 'product-grid-parameters', '', false, false, false) ) {
			$product_grid = false;
		}

		if ( !$product_view_settings = $params->render( 'params', 'product_view_settings', false ) ) {
			$product_view_settings = false;
		}

		if ( !$product_view_settings_sharings = $params->render( 'params', 'product_view_settings_sharings', JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_VIEW_SHARING' )) ) {
			$product_view_settings_sharings = false;
		}

		$this->assignRef( 'base_params', $base_params );
		$this->assignRef( 'description_params', $description_params );
		$this->assignRef( 'meta_params', $meta_params );
		$this->assignRef( 'images_params', $images_params );
		$this->assignRef( 'category_settings', $category_settings );
		$this->assignRef( 'category_settings_image_settings', $category_settings_image_settings );
		$this->assignRef( 'category_settings_product_image_settings', $category_settings_product_image_settings );
		$this->assignRef( 'product_settings', $product_settings );
		$this->assignRef( 'product_settings_sharings', $product_settings_sharings);
		$this->assignRef( 'product_view_settings', $product_view_settings);
		$this->assignRef( 'product_grid', $product_grid );
		$this->assignRef( 'product_view_settings_sharings', $product_view_settings_sharings);
		$this->assignRef( 'item', $item );
	}

	function display_tree()
	{
		$cat_ul	= Pago::get_instance( 'template' )->get_category_menu_tree( 1, 999999 );

		JToolBarHelper::title( JText::_( 'Categories Manager' ), 'generic.png' );

		$this->assignRef( 'cat_ul', $cat_ul );
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
		/*
		JToolBarHelper::custom(
				JText::_('Tree'), 'tree.png', 'tree.png', JText::_('Tree'), false, false
			);*/
		JToolBarHelper::custom(
				'copy', 'copy.png', 'copy.png', JText::_('PAGO_COPY'), true, false
			);
		
		JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_pago');
		JToolBarHelper::divider();

		JToolBarHelper::help('JHELP_COMPONENTS_BANNERS_BANNERS');
	}

	protected function parse_items( $items )
	{
		//return $items;

		$ordering = array();
		foreach( $items as $item ) {
			$ordering[$item->parent_id][] = $item->id;
		}

		if( is_array( $items ) ) {
			foreach( $items as $item ) {
				// root node
				if ( $item->parent_id == 0 ) {
					$item->editlink = $item->name;
					$item->order = false;
					continue;
				}
				$orderkey = array_search( $item->id, $ordering[$item->parent_id] );

				$link = JRoute::_(
					'index.php?option=com_pago&view=categories&id='. $item->id
				);

				if ( isset( $ordering[$item->parent_id][$orderkey - 1 ] ) ) {
					$item->order =
						"<a href=\"{$link}&task=moveup\"><img src=\"components/com_pago/css/images/uparrow.png\" /></a>";
				} else {
					$item->order =
						"<span style=\"float:left;display:block;width:16px;\">&nbsp;</span>";
				}

				if ( isset ( $ordering[$item->parent_id][$orderkey + 1 ] ) ) {
					$item->order .=
						"<a href=\"{$link}&task=movedown\"><img src=\"components/com_pago/css/images/downarrow.png\" /></a>";
				} else {
					$item->order .= "";
				}

				$indent = str_repeat('<span class="gi">|&mdash;</span>', $item->level - 1 );

				$link = JRoute::_(
					'index.php?option=com_pago&view=categories&task=edit&cid[]='. $item->id
				);

				$item->editlink = "<a href=\"{$link}\">{$indent}{$item->name}</a>";

				$link = JRoute::_(
					'index.php?option=com_pago&view=categories&id='. $item->id
				);

				if( $item->published ){
					$item->ispublishedlink =
						"<a href=\"{$link}&task=unpublish\"><img src=\"templates/bluestork/images/admin/tick.png\" /></a>";
				} else {
					$item->ispublishedlink =
						"<a href=\"{$link}&task=publish\"><img src=\"templates/bluestork/images/admin/publish_x.png\" /></a>";
				}
				if( $item->featured ){
					$item->isfeaturedlink =
						"<a href=\"{$link}&task=unfeature\"><img src=\"templates/bluestork/images/admin/tick.png\" /></a>";
				} else {
					$item->isfeaturedlink =
						"<a href=\"{$link}&task=feature\"><img src=\"templates/bluestork/images/admin/publish_x.png\" /></a>";
				}
			}
		}

		return $items;
	}
}
