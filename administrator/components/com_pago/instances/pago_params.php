<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');

/**
 * Set and Get Component Parameters
 *
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */ 
class pago_params
{
	public 
		$cmp_name = 'com_pago',
		$set_error = false;
	
	function __construct() {
		$this->params = JComponentHelper::getParams($this->cmp_name);
   	}
	
	/**
	 * Get component params
	 *
	 * @params $name string
	 * @return JRegistry Object
	 */
	public function get($name, $default=false)
	{
		return $this->params->get($name, $default);
	}
	
	/**
	 * Set component params
	 *
	 * @params $name string
	 * @params $value mixed
	 * @return bool
	 */
	public function set($name, $value)
	{
		$this->params->set($name, $value);
		$componentid = JComponentHelper::getComponent($this->cmp_name)->id;
		$table = JTable::getInstance('extension');
		
		$table->load($componentid);
		$table->bind(array('params' => $this->params->toString()));
		
		if(!$table->check()) $this->set_error = $table->getError();
		if(!$table->store()) $this->set_error = $table->getError();
		if($this->set_error) return false;
		
		return true;
	}
}