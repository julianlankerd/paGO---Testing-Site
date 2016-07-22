<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_backend_layout_adaptor
{
	var
	$_config,
	$_ini;

	function run()
	{

		if(JFactory::getApplication()->input->get('tmpl') || JFactory::getApplication()->input->get('format')) return false;

		PagoHtml::apply_layout_fixes();

		$menu = array();

		include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

		ob_start();

		PagoHtml::pago_top( $menu_items );
		PagoHtml::pago_bottom();

		$wrapper = ob_get_clean();

		$document   = JFactory::getDocument();
		$buffer = $document->getBuffer('component') . $wrapper;

		$document->setBuffer($buffer, 'component');

		return;

		/*

		$wrapper = str_replace('&amp;', '&', $wrapper);
		$wrapper = str_replace('&', '&amp;', $wrapper);

		$wrapper_dom = qp( $wrapper );


		$document   = JFactory::getDocument();

		//$buffer = str_replace('<div class="padding">');

			$buffer = $document->getBuffer('component');

			$buffer_dom = qp( $buffer );
		$wrapper_dom->find('div[class=pg-main]')->append($buffer_dom->find('body')->innerHtml());

		$buffer = $wrapper_dom->top()->find('body')->html();

		$buffer = str_replace(array('<![CDATA[', ']]>'), '', $buffer);
	//<![CDATA[

		$document->setBuffer($buffer, 'component');
		*/
	}
}