<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
PagoHtml::apply_layout_fixes();
PagoHtml::tooltip();
PagoHtml::uniform();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS . 'helpers' . DS . 'menu_config.php';

PagoHtml::pago_top($menu_items, 'tabs', $this->top_menu);
?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
	{
		jQuery.noConflict();
			
			if(task == 'cancel')
			{
				Joomla.submitform(task);
			}
			else
			{
				var params_zone_id= jQuery("#params_zone_id").val();
				var params_country_name= jQuery("#params_country_name").val();
				var params_country_3_code= jQuery("#params_country_3_code").val();
				var params_country_2_code= jQuery("#params_country_2_code").val();
				
				if(params_zone_id == '' || params_zone_id == '0' || params_country_name == '' || params_country_3_code == '' || params_country_2_code =='')
				{
					return false;
				}
				
				else
				{
					Joomla.submitform(task);
				}
			}

		
	}
</script>
<div class="pg-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel('tabs') ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li>
						<a href="#tabs-1" onclick="addTabPrefixInUrl(this);">
							<span class="icon"></span>
							<?php echo JText::_('PAGO_LOCATIONS_BASE_PARAMS'); ?>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="pg-tab-content">
				<div id="tabs-1">
					<div class="pg-border pg-white-bckg pg-pad-20">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_LOCATIONS_BASE_PARAMS' ), null, null, null, null, null, null, null ) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->base_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
			</div>
		</div>
	
		<?php /*echo PagoHtml::module_top( JText::_( 'CUSTOM_PARAMS' ) ) ?>
			<div class="pg-module-content">
				<?php echo $this->custom_params; ?>
			</div>
		<?php echo PagoHtml::module_bottom()*/ ?>
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->country_id; ?>" />
	    <input type="hidden" name="option" value="com_pago" />
	    <input type="hidden" name="id" value="<?php echo $this->item->country_id; ?>" />
	    <input type="hidden" name="task" value="cancel" />
	    <input type="hidden" name="view" value="locations" />
	    <input type="hidden" name="controller" value="locations" />
	    <?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div><!-- end pago content -->
<?php
PagoHtml::pago_bottom();

echo JHTML::_('behavior.keepalive');
