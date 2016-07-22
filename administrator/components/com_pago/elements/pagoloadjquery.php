<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
/* ------------------------------------------------------------------------
 * Bang2Joom Alfheim Image Gallery PRO  for Joomla 2.5 & 3.0
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2012 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */

defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');
class JFormFieldpagoloadjquery extends JFormField {

    protected $type = 'pagoloadjquery';

    protected function getInput() {	
		$doc = JFactory::$document;
		
		$version = new JVersion();
		if($version->RELEASE <= 3){
			$config = Pago::get_instance('config')->get();
			$pago_theme   = $config->get( 'template.pago_theme', 'default' );
			$doc->addScript( JURI::root( true ) . '/components/com_pago/templates/'.$pago_theme.'/js/jquery.js' );
		}

		return null;
    }

}

?>