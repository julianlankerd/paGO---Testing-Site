<?php

/**

 * @package paGO Commerce

 * @author 'corePHP', LLC

 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce

 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html

**/

defined('_JEXEC') or die();

class plgPago_ExportOrder_Export extends JPlugin
{

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/* Gets called when a export button is clicked

	 *

	 * @param int $order_id The id of the new order

	 */

	public function on_order_export( $context, $order_id )
	{
	
	}
}
