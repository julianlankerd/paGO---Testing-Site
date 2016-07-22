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

JHTML::_('behavior.tooltip');
include( JPATH_ADMINISTRATOR . '/components/com_pago/helpers/menu_config.php' );
PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );
?>
<div class="pg-content">
	<div class = "pg-pad-20 pg-white-bckg pg-border">
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<div class = "pg-row">
				<?php echo PagoHtml::module_top( JText::_( 'PAGO_DISCOUNT_BASE' ), null, null, null, 'pg-mb-6 pg-mb-20', '', '', false  ) ?>
				<?php echo $this->base_params ?>
				<?php echo PagoHtml::module_bottom() ?>
                
						<br />
		
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_DISCOUNT_RULES' ), null, null, null, 'pg-mb-20', '', '', false   ) ?>
							<?php echo $this->rule_params ?>
						<?php echo PagoHtml::module_bottom() ?>
						
						<br />
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_DISCOUNT_EVENTS' ), null, null, null, 'pg-mb-20', '', '', false   ) ?>
							<?php echo $this->event_params ?>
						<?php echo PagoHtml::module_bottom() ?>	
            

			    <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
			    <input type="hidden" name="option" value="com_pago" />
			    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
			    <input type="hidden" name="task" value="cancel" />
			    <input type="hidden" name="view" value="discounts" />
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

	var params_rule_name= jQuery("#params_rule_name").val();

	if(params_rule_name == '')
	{
		jQuery('#params_rule_name').css('border','solid 1px #FF0000');
		return false;
	}
	
	if ((params_rule_name!='' ))
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
