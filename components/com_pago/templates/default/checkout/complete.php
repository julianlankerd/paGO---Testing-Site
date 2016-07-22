<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php $this->load_header() ?>
<div id="pg-checkout" class="pg-step3">
	<!-- <h1 class="pg-title">Checkout</h1> -->
    <?php $this->step = 4; ?>
	<?php //if ( $progress = PagoHelper::load_template( 'checkout', 'checkout_progress' ) ) require $progress; ?>
	<div id="pg-checkout-order-receipt">
		<?php
		$result = Pago::get_instance('orders')->replaceOrderDetailsInformations($this->order, $this->receipt_tmpl->pgtemplate_body);
		echo  $result;?>
	</div>
</div>
<?php $this->load_footer(); ?>