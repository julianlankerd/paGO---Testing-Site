<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldRecipientid extends JFormField
{
   protected $type = 'Recipientid';

   protected function getInput()
	{
		$livefix = false;
		
		if($this->form->getName() == 'paramspayoptions_live'){
			$params = $this->form->getData()->get('payoptions_live');
			$livefix = '_live';
		} else {
			$params = $this->form->getData()->get('payoptions');
		}
		
		$html = false;
		$html .= '<strong style="color:red" id="params_instantpay'.$livefix.'_recipient_id_text">'.@$params->recipient_id.'</strong>';
		$html .= "<br><br>";
		
		return $html;
    }
}