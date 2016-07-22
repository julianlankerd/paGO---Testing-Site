<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>

<?php echo $this->invoice_view; ?>

 <label><?php echo JText::_( 'PAGO_CHECKOUT_MESSAGE_TO_VENDOR' ); ?></label>
<form style="margin:auto" method="post" action="<?php echo JRoute::_( 'index.php?view=checkout' ) ?>">
	<input type="hidden" name="step" value="complete" />
        <textarea rows="10" cols="50"></textarea>
   <br />
    <div>
    	<input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_CHECKOUT_COMPLETE' ) ?>" />
    </div>
</form>


<br />
<div>
    <div style="float:left">
    <form style="" method="post" action="<?php echo JRoute::_( 'index.php?view=checkout' ) ?>">
	<input type="hidden" name="step" value="payment" />
    <input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_BACK_PAYMENT' ) ?>" />
</form>
    </div>
    <div style="width:200px;margin:auto">
   <form style="" method="post" action="<?php echo JRoute::_( 'index.php?view=category&cid=' . $this->referer_cid ) ?>">
    <input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_CANCEL_CONTINUE_SHOPPING' ) ?>" />
</form>
    </div>
</div>
