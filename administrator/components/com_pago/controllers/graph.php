<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

class PagoControllerGraph extends PagoController
{
	/**
	* Custom Constructor
	*/
	private $_view = 'graph';

	function __construct( $default = array() )
	{
		parent::__construct( $default );
	}

	function display( $cacheable = false, $urlparams = false )
	{
		parent::display( $cacheable = false, $urlparams = false );
	}
	
	public function generateRevenue()
	{
		$revenue_start_date = JFactory::getApplication()->input->get('revenue_start_date');
		$revenue_end_date = JFactory::getApplication()->input->get('revenue_end_date');
		$this->setRedirect('index.php?option=com_pago&view=graph&type=revenue&generated=1&startdate=' . $revenue_start_date . '&enddate=' . $revenue_end_date  );
	}
	
	public function generateAvgOrder()
	{
		$order_start_date = JFactory::getApplication()->input->get('order_start_date');
		$order_end_date = JFactory::getApplication()->input->get('order_end_date');
		$this->setRedirect('index.php?option=com_pago&view=graph&type=avgord&avgOrder=1&startdate=' . $order_start_date . '&enddate=' . $order_end_date  );
	}
	
	public function exportdata()
	{
		$export = JFactory::getApplication()->input->get('export');
		$model = $this->getModel('graph');
		$total = 0;
		$subtotal = 0;
		$enddt = '';
		$startdt ='';
		
		if($export == 'revenue' || $export == 'avgorder')
		{
			$total = JFactory::getApplication()->input->get('rtotal');
			$subtotal = JFactory::getApplication()->input->get('rsubtl');
			$startdt = JFactory::getApplication()->input->get('startdt');
			$enddt = JFactory::getApplication()->input->get('enddt');
		}
		
		$model->exportData($export, $total, $subtotal, $startdt, $enddt);
	}
	
	
}
