<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldCouponrules extends JFormField
{
	protected $type = 'Couponrules';
	var $_data = array();

	protected function getInput()
	{

		return $this->html();
	}

	/**
	 * Retrive and return a list of available rules
	 *
	 */
	protected function getRules()
	{
		$db = JFactory::getDBO();

		$db->setQuery( 'SELECT * FROM #__pago_crules' );
		return $db->loadAssocList();
	}

	protected function html()
	{
		$model = JModelLegacy::getInstance( 'coupon', 'PagoModel' );

		PagoHtml::add_js( JURI::root( true ) .
			'/administrator/components/com_pago/javascript/com_pago_coupon_rule.js');

		if ( $this->value ) {
	?>
		<div id="pg-rule-container">
<?php foreach( $this->value as $rule ) {
	echo $model->get_rule_fields( $rule['name'], $rule )->get_html_fields();
}?>
		</div>
		<script type="text/javascript">jQuery.couponrule();</script>
	<?php
		} else {
?>
		<div id="pg-rule-container">
<?php foreach( $this->getRules() as $rule ) {
	echo $model->get_rule_fields( $rule['class'] )->get_html_fields();
}?>
		</div>
		<?php
		}
	}
}
