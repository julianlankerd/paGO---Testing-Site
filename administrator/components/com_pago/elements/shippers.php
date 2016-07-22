<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

/**
 * Renders a multiple item select element
 * using SQL result and explicitly specified params
 *
 * sqlmultilistx
 * sqlmultiListX
 */

class JFormFieldShippers extends JFormField
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        protected $type = 'shippers';

        function getInput()
        {
			$name = $this->name;
			$value = $this->value;
			$node = $this->element;
			$control_name = $this->id;

			$this->value = false;

			$this->cid = JFactory::getApplication()->input->get('cid', array(0), 'array' );
			$id = $this->cid[0];

			if( $id && !is_array( $value ) ){

				JError::raiseNotice( 100, JText::_( 'PAGO PLEASE SELECT A SHIPPER' ) );
			} elseif( is_array( $value ) ) {

				$this->value = implode( '|', $value );
			} else {
				JError::raiseNotice( 100, JText::_( 'PAGO YOU WILL FIRST NEED TO APPLY / SAVE BEFORE BEING ABLE TO SELECT A SHIPPER' ) );
			}

			$doc = $document = JFactory::getDocument();

			/*$doc->addScriptDeclaration("
				jQuery(function($) {
					jQuery('div.shippers_$name').parent().attr('colspan', 2).siblings().remove();
				});
			");*/

			/*$order_model = JModelLegacy::getInstance( 'orders','PagoModel' );

			$cid = JRequest::getVar('cid',  0, '', 'array');

			$state = $order_model->getState();

			$state->set('order_id', (int)$cid[0] );

			$order = $order_model->getOrder();*/

			$cid = JFactory::getApplication()->input->get('cid',  0, 'array');

			$order = Pago::get_instance( 'orders' )->get( (int)$cid[0] );

			$user_data = $order['addresses']['shipping'];

			if(!$order['items']) return false;

			//$this->cart = $order['items'];

			JPluginHelper::importPlugin( 'pago_shippers' );

			$shipping_options = array();

			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger(
				'set_options',
				array(
					&$shipping_options,
					&$order,
					&$user_data
				)
			);

			$field_name = $name;

			return $this->template($shipping_options, $field_name, $name);
        }

		function template( $shipping_options, $field_name, $name ){


			$chkd=false;
			$shipper_options_changed = true;

			ob_start(); ?>




   <?php echo'<div style="padding:10px;">' .
   JText::_( 'If you change items or shipping zip / country you will need to save and then re select shipping options' )
   . '</div>' ?>


            <div class="shippers_<?php echo $name ?> shippers">
			<?php foreach( $shipping_options as $carrier=>$options): ?>

            <fieldset>
                <legend><?php echo $carrier ?></legend>
                <?php foreach( $options as $k=>$option):
					$ship_method_id = $carrier . '|' . $option['code'] .'|' . $option['name'].'|' . $option['value'];
					if( $ship_method_id == $this->value ){
						$chkd='checked="1"';
						$shipper_options_changed = false;
					}
				?>
                    <input type="radio" <?php echo $chkd ?> name="<?php echo $field_name ?>" value="<?php echo $carrier . '|' . $option['code'] .'|' . $option['name'].'|' . $option['value'] ?>">
                        <?php echo $option['name'] ?> ($<?php echo number_format( $option['value'], 2, '.', false ) ?>)<br />
                <?php $chkd=0;endforeach ?>
            </fieldset>

            <?php endforeach; ?>
			</div>
			<?php

			if( $shipper_options_changed ){
				JError::raiseNotice( 100, JText::_( 'PAGO SHIPPING OPTIONS RECALCULATED PLEASE RE SELECT AND SAVE AGAIN - ORIGINAL: ' . $this->value ) );
			}

			return ob_get_clean();
		}
}
