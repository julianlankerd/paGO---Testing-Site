<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>

<?php 
JHTML::_('behavior.mootools'); 
$user =& JFactory::getUser();
?>

<?php if ( $user->guest ): ?>

<div>
    <div>
    	<h3><?php echo JText::_( 'PAGO_CHECKOUT_LOGIN' ) ?></h3>
		<?php echo pago::get_instance('template')->get_module_position( 'pago_checkout_login' ) ?>
    </div>
    <div>
		<h3><?php echo JText::_( 'PAGO_CHECKOUT_REGISTER' ) ?></h3>
		<?php echo pago::get_instance('template')->get_module_position( 'pago_checkout_register' ) ?>
    </div>
</div>

<?php else: ?>

<div>
    <h3><?php echo JText::_( 'PAGO_CHECKOUT_INVOICE' ) ?></h3>
    <?php echo $this->invoice_view ?>
</div>

<?php endif ?>
