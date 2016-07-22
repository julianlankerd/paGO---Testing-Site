<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerCoupons extends PagoController
{
	private $_view = 'coupons';

	public function __construct( $default = array() )
	{
		parent::__construct( $default );
	}

	public function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}

	function publish()
	{
		$model = JModelLegacy::getInstance('Coupons','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'array' ) );
		$model->publish();

		$msg 	= JText::_('PAGO_COUPON_PUBLISHED');
		$link 	= 'index.php?option=com_pago&view=coupons';

		$this->setRedirect($link, $msg);
	}

	function unpublish()
	{
		$model = JModelLegacy::getInstance('Coupons','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'array' ) );
		$model->unpublish();

		$msg 	= JText::_('PAGO_COUPON_UNPUBLISHED');
		$link 	= 'index.php?option=com_pago&view=coupons';

		$this->setRedirect($link, $msg);
	}

	/**
	* display the edit form
	* @return void
	*/
	function edit()
	{
		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	/**
	* display the edit form
	* @return void
	*/
	function add()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		JFactory::getApplication()->input->set( 'layout', 'form' );
		parent::display();
	}

	function save()
	{
		$model = JModelLegacy::getInstance('Coupon','PagoModel');

		$return = $model->store();
		
		if(isset($return['status'])){
			$msg = $return['message'];
		}else{
			$msg = JText::_( 'PAGO_COUPON_ERROR_SAVE' );	
		}

		$link = 'index.php?option=com_pago&view=coupons';
		$this->setRedirect($link, $msg);
	}

	function apply()
	{
		$model = JModelLegacy::getInstance('Coupon','PagoModel');
		$return = $model->store();


		if(isset($return['status'])){
			$msg = $return['message'];
			if(isset($return['id'])){
				$id = $return['id'];
			}else{
				$id = JFactory::getApplication()->input->get('id',  0, 'int');
			}
		}else{
			$msg = JText::_( 'PAGO_COUPON_ERROR_SAVE' );	
			$id = JFactory::getApplication()->input->get('id',  0, 'int');
		}

		
		$link = 'index.php?option=com_pago&view=coupons&task=edit&cid[]=' . $id;

		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = JModelLegacy::getInstance('Coupons','PagoModel');

		$model->setState('cid', JFactory::getApplication()->input->get('cid', 0, 'array' ) );
		$model->remove();

		$msg 	= JText::_('PAGO_COUPON_REMOVED');
		$link 	= 'index.php?option=com_pago&view=coupons';
		$this->setRedirect($link, $msg);
	}

	function getrulefields()
	{
		$rule = JFactory::getApplication()->input->get( 'rule' );

		$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
		$rule_html = $model->get_rule_fields( $rule );
		if ( $rule_html === null ) {
			return false;
		}

		ob_start();
		echo $rule_html->get_html_fields();
		ob_flush();
		return;
	}
	function assign_item()
	{
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$word = JFactory::getApplication()->input->get('q', null, 'string');
		$table = JTable::getInstance( 'Items', 'Table' );
		$items = $table->search_item($word);
		echo json_encode($items);
        exit();
	}
	function show_assign_items(){

		$couponId = JFactory::getApplication()->input->get( 'couponId' );

		$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
		$html = $model->assign_item_html($couponId);

		echo json_encode($html);
        exit();
	}
	function show_assign_category(){
		$couponId = JFactory::getApplication()->input->get( 'couponId' );

		$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
		$html = $model->assign_category_html($couponId);
		
		echo json_encode($html);
        exit();	
	}
	function show_assign_groups(){
		$couponId = JFactory::getApplication()->input->get( 'couponId' );

		$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
		$html = $model->assign_groups_html($couponId);
		
		echo json_encode($html);
        exit();	
	}
	function show_assign_users(){

		$couponId = JFactory::getApplication()->input->get('couponId' );

		$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
		$html = $model->assign_user_html($couponId);

		echo json_encode($html);
        exit();
	}
	function assign_user()
	{
		$model = JModelLegacy::getInstance( 'Coupon', 'PagoModel' );
		$word = JFactory::getApplication()->input->get('q', null, 'string');
		$users = $model->search_user($word);
		echo json_encode($users);
        exit();
	}
}
