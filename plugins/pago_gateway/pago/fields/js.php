<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldJS extends JFormField
{
   protected $type = 'JS';

   protected function getInput()
	{
		$doc = JFactory::getDocument();
      $doc->addScript(JURI::root(true) . '/plugins/pago_gateway/pago/script.js?'.time());

      $params = $this->form->getData()->get('payoptions');

		$validate_text = JText::_('PLG_PAGO_GATEWAY_PAGO_BUTTON_VALIDATE');
		$delete_text = JText::_('PLG_PAGO_GATEWAY_PAGO_BUTTON_DELETE');

		$html = '<button id="pago_gateway_button" class="pg-btn-large pg-btn-light pg-btn-green" onclick="paGOapi.recipients.post();return false">'.$validate_text.'</button>';

      $hidden = 'display:none';

      if(isset($params->recipient_id) && $params->recipient_id)
         $hidden = '';

      $html .= '&nbsp;<button style="'.$hidden.'" id="pago_gateway_button_delete" class="pg-btn-large pg-btn-light pg-btn-red" onclick="paGOapi.recipients.delete();return false">'.$delete_text.'</button>';

      $html .= '<span style="display:none" id="pago_payoptions_saving">&nbsp;&nbsp;Saving&nbsp;&nbsp;<span class="spin">&nbsp;</span></span><br /><br /><pre style="display:none" class="pg-response"></pre>';

      $html .= '
  By using service you agree to the <strong><a target="_blank" href="https://www.corephp.com/joomla-products/pago-commerce/pago-quickpay-terms-of-service">paGO QuickPay Terms of Service</a></strong>.
';
      return $html;
    }
}