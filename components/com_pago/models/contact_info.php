<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.model' );

class PagoModelContact_info extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();
	}

	function send_mail()
	{
		$from_email = JFactory::getApplication()->input->get('from_email');
		$from_name = JFactory::getApplication()->input->get('from_name');
		$subject = JFactory::getApplication()->input->get('subject');
		$your_message = JFactory::getApplication()->input->get('your_message');
		$id = JFactory::getApplication()->input->get('id');
		
		$prd_name = 'N/A';
		
		if($id){
			$prd_name = $this->getProductName($id);
		} 
		
		$html = "<div><span style='font-weight:bold'>Product Name :</span>&nbsp;<span>" . $prd_name . "</span></div>";
		$html .= "<div><span style='font-weight:bold'>Message :</span>&nbsp;<span>" . $your_message . "</span></div>";
		$store_cfg 	= Pago::get_instance('config')->get();
		$to = array( $store_cfg->get('general.store_email'), $store_cfg->get('general.pago_store_name') );
		$from = array( $from_email, $from_name);
		$this->send_email($subject, $html, $to[0], $from, true);

		return true;

	}

	function sendPyamenErrorEmail($data)
	{
		$from_email = $data['from_email'];
		$from_name = $data['from_name'];
		$subject = $data['subject'];
		$your_message = $data['your_message'];
		$config  = JFactory::getConfig();
		$jmailfrom =  $config->get('mailfrom');
		$html = "<div><span style='font-weight:bold'>Name :</span>&nbsp;<span>" . $from_name . "</span></div>";
		$html .= "<div><span style='font-weight:bold'>Message :</span>&nbsp;<span>" . $your_message . "</span></div>";
		$store_cfg 	= Pago::get_instance('config')->get();
		$to = array( $store_cfg->get('general.store_email'), $store_cfg->get('general.pago_store_name') );

		if(!isset($to[0]))
		{
			$to[0] = $jmailfrom;
		}

		$from = array( $from_email, $from_name);
		$this->send_email($subject, $html, $to[0], $from, true);
		return true;



	}

	function getProductName($id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT name from #__pago_items WHERE id=" . $id;
		$db->setQuery($query);

		return $db->loadResult();
	}

	function send_email( $subject, $body, $to, $from, $html=false )
	{
		// Invoke JMail Class
		$mailer = JFactory::getMailer();

		// Set sender array so that my name will show up neatly in your inbox
		$mailer->setSender($from);

		// Add a recipient -- this can be a single address (string) or an array of addresses
		$mailer->addRecipient($to);
		$mailer->setSubject($subject);
		$mailer->setBody($body);

		// If you would like to send as HTML, include this line; otherwise, leave it out
		if ($html)

		{
			$mailer->isHTML();
		}

		// Send once you have set all of your options
		$mailer->send();

	}
}
?>
