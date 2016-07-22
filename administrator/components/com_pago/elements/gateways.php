<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldGateways extends JFormField
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        protected $type = 'gateways';

        function getInput()
        {
			$name = $this->name;
			$value = $this->value;
			$node = $this->element;
			$control_name = $this->id;

			$this->value = $value;

			$this->cid = JFactory::getApplication()->input->get('cid', array(0), 'array');
			$id = $this->cid[0];



			$doc = $document = JFactory::getDocument();

			/*$doc->addScriptDeclaration("
				jQuery(function($) {
					jQuery('div.shippers_$name').parent().attr('colspan', 2).siblings().remove();
				});
			");*/

			$order_model = JModelLegacy::getInstance( 'orders','PagoModel' );

			$cid = JFactory::getApplication()->input->get('cid',  0, 'array');

			$state = $order_model->getState();

			$state->set('order_id', (int)$cid[0] );

			$order = $order_model->getOrder();

			$user_data = $order['addresses']['billing'];

			if(!$order['items']) return false;

			$this->cart = $order['items'];

			JPluginHelper::importPlugin( 'pago_payment' );

			$payment_options = array();

			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger(
				'payment_set_options',
				array( &$payment_options, &$this->cart, &$user_data )
			);

			$field_name = $name;

			return $this->template($payment_options, $field_name, $name);
        }

		function template( $payment_options, $field_name, $name )
		{

			$chkd=false;
			$shipper_options_changed = true;

			ob_start(); ?>




           <?php
		   	$chkd = false;

		   	foreach( $payment_options as $gateway=>$options):
				if( $gateway == $this->value ){
					$chkd='checked="1"';
				}
			?>

                <input type="radio" <?php echo $chkd ?> name="<?php echo $field_name ?>" value="<?php echo $gateway ?>">
                <label><?php echo JText::_( $gateway ); ?></label>
                <?php
                $chkd=0;
            endforeach;




			return ob_get_clean();
		}
}
