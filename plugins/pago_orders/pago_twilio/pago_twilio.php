<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die();
require_once __DIR__ . '/vendor/autoload.php';

class plgPago_OrdersPago_twilio extends JPlugin
{
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin('pago_orders', 'pago_twilio');
		$this->_params = new JRegistry($this->_plugin->params);

		// Hooks
		KDispatcher::add_filter('on_new_order', array($this, 'on_new_order'));
		KDispatcher::add_filter('on_new_order_subscription', array($this, 'on_new_order_subscription'));
	}
	/* Gets called whan a new one-off order is placed
	 *
	 * @param int $order_id The id of the new order
	 */
	public function on_new_order( $order_id )
	{
		$do = $this->_params->get('twilio_send_on');
		if($do == 'order' || $do == 'all')
		{
			$template = $this->_params->get('twilio_order_message');
			return $this->sendMessage($order_id, $template);
		}
	}

	/* Gets called when a new subscription order is placed
	 *
	 * @param int $order_id The id of the new order
	 */
	public function on_new_order_subscription( $order_id )
	{
		$do = $this->_params->get('twilio_send_on');
		if($do == 'subscription' || $do == 'all')
		{
			$template = $this->_params->get('twilio_subscription_message');
			return $this->sendMessage($order_id, $template);
		}
	}
	
	private function sendMessage($order_id, $message_template)
	{
		//$order = Pago::get_instance('orders')->get($order_id);
		
		$url = $this->makeUrl($order_id);
		
		$message = str_replace(array('%id', '%url'), array($order_id, $url), $message_template);
		
		$from = $this->validatePhone($this->_params->get('twilio_from'));
		$to = $this->validatePhone($this->_params->get('twilio_to'));
		
		if($to && $from)
		{
			try
			{
				$client = new Services_Twilio($this->_params->get('twilio_account_sid'), $this->_params->get('twilio_auth_token'));

				$client->account->messages->sendMessage(
					$from,
					$to,
					$message
				);

				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
		}
	}
	
	public function makeUrl($order_id)
	{
		return JRoute::_(JUri::root() . 'administrator/index.php?option=com_pago&controller=ordersi&task=edit&view=ordersi&cid%5B%5D='.$order_id, false);
	}
	
	public function validatePhone($phone)
	{
		$matches = array();
		
		preg_match('(\+[0-9]*)', $phone, $matches);
		
		if($matches[0])
		{
			return $matches[0];
		}
		
		return false;
	}
}
