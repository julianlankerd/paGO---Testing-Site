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

/**
 */
class PagoViewSystem extends JViewLegacy
{
	protected $items;

	/**
	* Display the view
	*/

	public function display( $tpl = null )
	{

		// JToolBarHelper::apply();

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Get Currencies
		$currenciesModel = JModelLegacy::getInstance('currencies', 'PagoModel');
		$this->currencies = $currenciesModel->getItems();

		// Get Item Types
		$itemTypesModel = JModelLegacy::getInstance('item_types', 'PagoModel');
		$this->itemTypes = $itemTypesModel->getItems();

		// Get Weight Units
		$unitsModel = JModelLegacy::getInstance('units', 'PagoModel');
		$this->weightUnits = $unitsModel->getItems();

		// Get Size Units
		$this->sizeUnits = $unitsModel->getSizeUnits();
		
		//pago api
		Pago::load_helpers( 'pagoparameter' );
		
		$params = Pago::get_instance('params')->params;
		
		$bind_data = [
			'params' => $params
		];
		
		$this->params = new PagoParameter( $bind_data,  dirname( __FILE__ ) . '/params.xml' );
		
		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'pg-btn-medium pg-btn-green pg-btn-dark');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );

		parent::display($tpl);
	}
}
