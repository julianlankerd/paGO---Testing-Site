<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }
jimport( 'joomla.application.component.controller' );
jimport( 'joomla.database.table' );
JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/tables' );

class PagoControllerContact_info extends PagoController
{
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	function __construct()
	{
		parent::__construct();
	}

	// Send Product Inquiry Mail
	public function sendcontactmail()
	{
		ob_clean();
		$post = JFactory::getApplication()->input->getArray($_POST);
		$model = $this->getModel('contact_info');
		
		if(isset($post['holy_wtf_batman'])){
			$link = 'index.php?option=com_pago&view=cart';
			$msg = JText::_('PAGO_MAIL_SEND_SUCCESSFULLY');
			return $this->setRedirect( $link, $msg );
		}

		
		if ($model->send_mail($post))
		{
			echo $msg = JText::_('PAGO_MAIL_SEND_SUCCESSFULLY');
			exit;
		}
		else
		{
			echo $msg = JText::_('PAGO_ERROR_IN_MAIL_SEND');
			exit;
		}
	}

	public function sendPyamenErrorEmail()
	{
		ob_clean();
		
	
		
		$view = JFactory::getApplication()->input->get('view');
		$model = $this->getModel('contact_info');
		$post = JFactory::getApplication()->input->get();
		
		if ($model->sendPyamenErrorEmail($post))
		{
			echo $msg = JText::_('PAGO_MAIL_SEND_SUCCESSFULLY');
			exit;
		}
		else
		{
			echo $msg = JText::_('PAGO_ERROR_IN_MAIL_SEND');
			exit;
		}
	}

	public function get_contact_form()
	{
		ob_clean();
		$contact = PagoHelper::load_template( 'common', 'tmpl_contact_form' );
		$return = array();

		ob_start();
			require $contact;
			$return['formHtml'] = ob_get_contents();
		ob_end_clean();
		$return['status'] = "success";
		ob_clean();
		echo json_encode($return);
		exit();
	}

	public function get_payment_form()
	{
		ob_clean();
		$contact = PagoHelper::load_template( 'common', 'tmpl_contact_form' );
		$return = array();

		$this->payment_error = true;

		ob_start();
			require $contact;
			$return['formHtml'] = ob_get_contents();
		ob_end_clean();
		$return['status'] = "success";
		ob_clean();
		echo json_encode($return);
		exit();
	}
}
?>
