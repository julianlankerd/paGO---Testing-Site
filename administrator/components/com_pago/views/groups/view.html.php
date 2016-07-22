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
 */
class PagoViewGroups extends JViewLegacy
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
		}

		$this->items		= $this->parse_items( $this->get( 'Items' ) );
		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	function display_form()
	{
		$cid = JFactory::getApplication()->input->get('cid',  0, 'array');
		$cid = (int)$cid[0];

		$model = JModelLegacy::getInstance( 'Groups','PagoModel' );

		$model->setState('cid', $cid );

		$item = $model->getGroup();

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		if( isset( $item->params ) ){
			$item_params = json_decode( $item->params );

			if( is_object( $item_params ) ){
				foreach( $item_params as $k=>$v){
					$item->$k = $v;
				}
			}
		}

		$bind_data = array(
			'base' => $item,
			'custom' => $item,
			'memberlist' => $item
		);

		Pago::load_helpers( 'pagoparameter' );

		$params = new PagoParameter( $bind_data,  $cmp_path . 'views/groups/elements.xml' );

		JForm::addfieldpath( array( $cmp_path . DS . 'elements' ) );

		$base = $params->render( 'base', 'base' );
		$custom = $params->render( 'custom', 'custom' );
		$member = $params->render( 'memberlist', 'memberlist' );
		$this->assignRef( 'base_params', $base );
		$this->assignRef( 'custom_params', $custom );
		$this->assignRef( 'memberlist_params', $member );
		$this->assignRef( 'item', $item );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::addNew('add');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
	}

	protected function parse_items( $items )
	{
		return $items;
	}
}
