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
PagoHtml::uniform();
PagoHtml::tooltip();


JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );
if ($this->attribute->attr_enable == 1){
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration( "
		jQuery(window).load(function(){
				jQuery('#params_type').css('display','none');
				jQuery('#uniform-params_type').live('click',function(e){
					alert('You can not change attribute type');
					return false;
				})
			});
		");
}			

?>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
		<!-- tabs start -->
		 <div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li><a href="#tabs-1" onclick="addTabPrefixInUrl(this);"><?php echo JText::_( 'PAGO_ATTRIBUTE_PROPERTIES' ) ?></a></li>
					<li><a href="#tabs-2" onclick="addTabPrefixInUrl(this);"><?php echo JText::_( 'PAGO_ATTRIBUTE_VALUES' ) ?></a></li>
					<li><a href="#tabs-3" onclick="addTabPrefixInUrl(this);"><?php echo JText::_( 'PAGO_ATTRIBUTE_DISPLAY_PROPERTIES' ) ?></a></li>
					<li><a href="#tabs-4" onclick="addTabPrefixInUrl(this);"><?php echo JText::_( 'PAGO_ATTRIBUTE_ASSIGNMENTS' ) ?></a></li>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="pg-tab-content tabs-content pg-pad-20 pg-white-bckg pg-border ">
				<div id="tabs-1">
					<div class="clear"></div>
					<div class="pg-row">
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_ATTRIBUTE_PROPERTIES' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-border">
										<div class = "pg-row">
											<div class = "pg-col-12">
												<?php echo $this->base_params ?>
											</div>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
					</div>
				</div>
				<div id="tabs-2">
					<div class="clear"></div>
					<div class="pg-row">
						<div class="pg-col-12 pg-mb-20 text-right">
							<button type="button" onclick="return jQuery.attributeOpts('addField');" class="pg-btn-large pg-btn-light pg-btn-green pg-btn-add" data-toggle="modal" data-target="#addAttribute">
								<?php echo JText::_('PAGO_CUSTOM_ATTRIBUTES_ADD_ATTRIBUTE_VALUE'); ?>
							</button>
						</div>
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_ATTRIBUTE_VALUES' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class="pg-pad-20 pg-border">
										<div class="pg-table-wrapper">
											<table class="pg-table pg-repeated-rows pg-attributes-opt_values-manager" id="pg-attribute-options">
												<thead>
													<tr>
														<?php
															switch ($this->attribute->type):
																// Color
																case 0:
														?>
														
														<td><?php echo JText::_("PAGO_ATTRIBUTE_TYPE_NAME_{$this->attribute->type}"); ?></td>
														<td><?php echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_COLOR_ON_PALETTE'); ?></td>
														
														<?php
																break;
																
																// Size
																case 1:
														?>
														
														<td><?php echo JText::_('PAGO_SIZE_NAME'); ?></td>
														<td><?php echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE'); ?></td>
														<td><?php echo JText::_('PAGO_ATTRIBUTE_OPT_VALUES_SIZE_TYPE'); ?></td>
														
														<?php
																break;
																
																// Material / custom
																default:
														?>
														
														<td><?php echo JText::_("PAGO_ATTRIBUTE_TYPE_NAME_{$this->attribute->type}"); ?></td>
														
														<?php
																break;
															endswitch;
														?>
														<td class="pg-sort" style="width:10%">
															<?php echo JText::_('JGRID_HEADING_ORDERING');  ?>
														</td>
														<td class="pg-edit"><?php echo JText::_('PAGO_EDIT'); ?></td>
														<td class="pg-remove"><?php echo JText::_('PAGO_DELETE'); ?></td>
													</tr>
												</thead>
												<tbody>
													<?php echo $this->values ?>
												</tbody>
											</table>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
					</div>
					<div id="addAttribute" class="modal modal-sm fade new_attr_opt">
						<?php 
							$close = '<button class="pg-btn-modal-close" data-dismiss="modal"><span class="fa fa-close"></span></button>';
						
							echo PagoHtml::module_top( JText::_( 'PAGO_ATTRIBUTE_VALUES' ) . $close, null, null, null, null, null, null, false );
						?>	
						<div class="pg-border pg-pad-20" id="add-attribute-fields">
						</div>
						<div class="pg-pad-20 text-center">
							<button type="button" onclick="return jQuery.attributeOpts('save');" class="pg-btn-small pg-btn-light pg-btn-green">
								<?php echo JText::_('PAGO_SAVE'); ?>
							</button>	
						</div>
						<?php echo PagoHtml::module_bottom(); ?>	
					</div>
				</div>
				<div id="tabs-3">
					<div class="clear"></div>
					<div class="pg-row">
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_ATTRIBUTE_DISPLAY_PROPERTIES' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-border">
										<div class = "pg-row">
											<div class = "pg-col-12">
												<?php echo $this->display_options ?>
											</div>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
					</div>
				</div>
				<div id="tabs-4">
					<div class="clear"></div>
					<div class="pg-row">
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_ATTRIBUTE_ASSIGNMENTS' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-border">
										<div class = "pg-row">
											<div class = "pg-col-12">
												<?php echo $this->assignments ?>
											</div>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<!-- tabs end -->
		<?php //echo PagoHtml::module_top( JText::_( 'ATTRIBUTE_INFORMATION' ) ) ?>
		
		<?php //echo $this->options_params ?>

		<?php //echo PagoHtml::module_bottom() ?>

		<input type="hidden" name="cid[]" value="<?php echo $this->attribute->id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->attribute->id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="attributes" />
		<input type="hidden" name="controller" value="attributes" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
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

	if(params_name == '')
	{
		jQuery('#params_name').css('border','solid 1px #FF0000');
		return false;
	}
	if (params_name!='')
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

	jQuery(document).ready(function(){
		jQuery('table.pg-attributes-opt_values-manager tbody').sortable({
			handle: 'span.pg-sort-handle',
			opacity: 0.6,
			scroll: true,
			cursor: 'move',
			axis: 'y',
			start: function(event, ui) {
				jQuery('.ui-sortable-placeholder').html('<td colspan=\"10\"><div class=\"pg-placeholder\">Drop item row here.</div></td>');
			},
			stop: function(event, ui ) {
				var idArray = [];
					jQuery('.ui-sortable tr').each(function () {
					var relValue = jQuery(this).attr('rel');
					idArray.push(relValue);
				});

				jQuery.ajax({
					type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=attributes&task=attributeOptionsOrdering&id=' +idArray+ '&async=1',
				});
			}
		});
		
	})

</script>
<?php echo JHTML::_('behavior.keepalive');
