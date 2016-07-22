<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery( 'jqueryui' );
PagoHtml::apply_layout_fixes();
PagoHtml::uniform();
PagoHtml::tooltip();
if(isset($this->item->unlimited)){
	$unlimited = $this->item->unlimited;
}else{
	$unlimited = 0;	
}
?>

<script type="text/javascript">
jQuery(document).ready(function(){
		var unlimitedCpn = <?php echo $unlimited ?>;
		if(unlimitedCpn == 1)
		{
			jQuery("#params_quantity").prop("readonly",true);
			jQuery('#params_quantity').css('opacity', 0.4);
		}
		else
		{
			jQuery("#params_quantity").removeAttr("readonly") ;
			jQuery('#params_quantity').css('opacity', 1);
		}
});
</script>

<?php

JHTML::_('behavior.tooltip');
include( JPATH_ADMINISTRATOR . '/components/com_pago/helpers/menu_config.php' );
PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );
?>
<div class="pg-content">
	<div class = "pg-pad-20 pg-white-bckg pg-border">
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<div class = "pg-row">
				<?php echo PagoHtml::module_top( JText::_( 'PAGO_COUPONS_BASE' ), null, null, null, 'pg-col-6 pg-mb-20', '', '', false  ) ?>
				<?php echo $this->base_params ?>
				<?php echo PagoHtml::module_bottom() ?>

				<div class = "pg-col-6">
					<div class = "pg-row">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_COUPON_ASSIGN' ), null, null, null, 'pg-col-12 pg-mb-20', '', '', false    ) ?>
							<?php echo $this->assign_params ?>
						<?php echo PagoHtml::module_bottom() ?>

						<?php echo PagoHtml::module_top( JText::_( 'PAGO_COUPONS_RULES' ), null, null, null, 'pg-col-12 pg-mb-20', '', '', false   ) ?>
							<?php echo $this->rule_params ?>
						<?php echo PagoHtml::module_bottom() ?>

						<?php echo PagoHtml::module_top( JText::_( 'PAGO_COUPONS_EVENTS' ), null, null, null, 'pg-col-12', '', '', false   ) ?>
							<?php echo $this->event_params ?>
						<?php echo PagoHtml::module_bottom() ?>	
					</div>
				</div>

			    <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
			    <input type="hidden" name="option" value="com_pago" />
			    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
			    <input type="hidden" name="task" value="cancel" />
			    <input type="hidden" name="view" value="coupons" />
			    <?php echo JHTML::_( 'form.token' ); ?>
		    </div>
		</form>
	</div>
</div>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	jQuery.noConflict();
	if(task == 'cancel'){
		Joomla.submitform(task);
		return;
	}

	var params_name= jQuery("#params_name").val();
	var params_code= jQuery("#params_code").val();

	if(params_name == '')
	{
		jQuery('#params_name').css('border','solid 1px #FF0000');
		return false;
	}
	if(params_code == '')
	{
		jQuery('#params_code').css('border','solid 1px #FF0000');
		return false;
	}
	if ((params_name!='' && params_code!=''))
	{
		jQuery('#selectedTab').val(jQuery('.ui-state-active a').attr('href'));
		Joomla.submitform(task);
	}
	else
	{
		jQuery('#error').css('display','block');
		return false;
	}
}
</script>

<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();
