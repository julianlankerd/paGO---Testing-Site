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

class PagoViewTransfers extends JViewLegacy
{
	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		require_once( JPATH_SITE.'/components/com_pago/controllers'.DS.'api'.'.php' );
		
		switch ($this->_layout)
		{
			case 'get':
				return $this->display_get();
		}
		
		$params = Pago::get_instance('params')->params;
		
		$recipient_id = $params->get('payoptions.test_recipient_id');
		
		if($livemode = $params->get('payoptions.livemode')){
			$recipient_id = $params->get('payoptions.live_recipient_id');
		}
		
		$payload = (object)[
			'account_id'=> $recipient_id,
			'livemode' => $livemode
		];
		
		$api = new PagoControllerApi;
		
		$transfers = $api->call('GET', 'transfers', $payload, false);
		
		$top_menu = [];
		
		$this->assignRef( 'top_menu',  $top_menu );
		$this->assignRef( 'items',  $transfers->data );
		
		parent::display($tpl);
	}

	private function display_get()
	{
		$input = JFactory::getApplication()->input;

		$params = Pago::get_instance('params')->params;
		
		$recipient_id = $params->get('payoptions.test_recipient_id');
		
		if($livemode = $params->get('payoptions.livemode')){
			$recipient_id = $params->get('payoptions.live_recipient_id');
		}
		
		$payload = (object)[
			'id'=> $input->get('id'),
			'account_id'=> $recipient_id,
			'livemode' => $livemode
		];
		
		$api = new PagoControllerApi;
		
		$transfer = $api->call('GET', 'transfers', $payload, false);
		
		$top_menu = [];
		
		$this->assignRef( 'top_menu',  $top_menu );
		$this->assignRef( 'item',  $transfer->transfer );
		$this->assignRef( 'items',  $transfer->transactions->data );
		
		return parent::display('default');
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
		JToolBarHelper::addNew('new');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');

	}
}
