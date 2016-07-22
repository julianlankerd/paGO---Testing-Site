<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>

<?php echo $this->invoice_view; ?>


<form style="" method="post" action="<?php echo JRoute::_( 'index.php?view=checkout' ) ?>">
	<input type="hidden" name="step" value="payment" />
	<?php $chkd='checked="1"';foreach( $this->payment_options as $gateway=>$options): ?>
        <label><?php echo JText::_( $gateway ); ?></label>
        <input type="radio" <?php echo $chkd ?> name="payment_option" value="<?php echo $gateway ?>">
    <?php $chkd=0;endforeach ?><br />
    <input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_CHECKOUT_FINALISE' ) ?>" />
</form>

<div>
    <form style="" method="post" action="<?php echo JRoute::_( 'index.php?view=checkout' ) ?>">
        <input type="hidden" name="step" value="shipping" />
        <input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_BACK_SHIPPING' ) ?>" />
    </form>
    </div>
    <div style="width:200px;margin:auto">
    <form method="post" action="<?php echo JRoute::_( 'index.php?view=category&cid=' . $this->referer_cid ) ?>">
        <input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_CANCEL_CONTINUE_SHOPPING' ) ?>" />
    </form>
</div>
