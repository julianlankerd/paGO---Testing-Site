<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class PagoViewOrders extends JViewLegacy
{
	function display()
	{
		$task_method = 'task_' . JFactory::getApplication()->input->getCmd('task');

		if( method_exists( $this, $task_method ) ) {
			$this->app =& JFactory::getApplication();
			$this->$task_method();

			return;
		}

		if( JFactory::getApplication()->input->get( '_search' ) == 'true' ){
			$this->getModel()->setState( '_search', true );
			$this->getModel()->setState( 'searchField', JFactory::getApplication()->input->get( 'searchField' ) );
			$this->getModel()->setState( 'searchOper', JFactory::getApplication()->input->get( 'searchOper' ) );
			$this->getModel()->setState( 'searchString', JFactory::getApplication()->input->get( 'searchString' ) );
		}

		$items = $this->get( 'GridData');

		$order_status_options = Pago::get_instance('config')->get_order_status_options();

		if( is_array( $items ) ) {
			foreach( $items as $k => $item ) {

				if( !$item->order_status ){
					$item->order_status = 'P';
				}

				$leading_ord_id = $this->leading_zeros($item->order_id, 5);
				
				$link =
					JRoute::_(
						'index.php?option=com_pago&view=orders&task=edit&cid[]='.
						$item->order_id
					);
				$item->editlink = "<a href=\"{$link}\">{$leading_ord_id}</a>";
				
				$item->order_status = $order_status_options[ $item->order_status ];
				$item->mdate = date( 'm/d/Y ', strtotime( $item->mdate ) );
				$items[$k] = $item;
			}
		}

		$json = array(
			'page' => $this->getModel()->getState('page'),
			'total' => $this->getModel()->getState('totalpages'),
			'records' => $this->getModel()->getState('total'),
			'rows' => $items
		);

		echo( json_encode( $json ) );
	}
	
	function leading_zeros($value, $places){

		$leading = 0;
		if(is_numeric($value)){
			for($x = 1; $x <= $places; $x++){
				$ceiling = pow(10, $x);
				if($value < $ceiling){
					$zeros = $places - $x;
					for($y = 1; $y <= $zeros; $y++){
						$leading .= "0";
					}
				$x = $places + 1;
				}
			}
			$output = $leading . $value;
		}
		else{
			$output = $value;
		}
		return $output;
	}
}
