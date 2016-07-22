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

class PagoViewGraph extends JViewLegacy
{
	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		Pago::load_helpers( 'pagoparameter' );
		$type = JFactory::getApplication()->input->get('type');
		$generated = JFactory::getApplication()->input->getInt('generated');
		$avgOrder = JFactory::getApplication()->input->getInt('avgOrder');
		$model = $this->getModel('graph');
		
		if(isset($generated))
		{
			$revenue_start_date = JFactory::getApplication()->input->get('startdate');
			$revenue_end_date = JFactory::getApplication()->input->get('enddate');
			$revenueDetails = $model->getRevenueDetails($revenue_start_date, $revenue_end_date);
			$this->assign('revenueDetails', $revenueDetails);
		}
		
		if(isset($avgOrder))
		{
			$order_start_date = JFactory::getApplication()->input->get('startdate');
			$order_end_date = JFactory::getApplication()->input->get('enddate');
			$orderDetails = $model->getOrderAverages($order_start_date, $order_end_date);
			$this->assign('orderDetails', $orderDetails);
		}
		
		if($type == 'search')
		{
			$keywords = $model->getKeywords();
			$this->assign('keywords', $keywords);
			$this->state		= $this->get('State');
		}
		
		if($type == 'cart')
		{
			$abandonedCart = $model->getAbandonedCart();
			$this->assign('abandonedCart', $abandonedCart);
			$this->state		= $this->get('State');
		}
		
		if($type == 'purchase')
		{
			$purchasedItems = $model->getPurchasedItems();
			$unpurchasedItems = $model->getUnpurchasedItems($purchasedItems);
			$this->assign('purchasedItems', $purchasedItems);
			$this->assign('unpurchasedItems', $unpurchasedItems);
		}
		
		$this->assign('type', $type);
		$this->assign('generated', $generated);
		$this->assign('avgOrder', $avgOrder);

		parent::display($tpl);
	}
	
	
	
}
