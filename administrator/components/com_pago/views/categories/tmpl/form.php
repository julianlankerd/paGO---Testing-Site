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

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();

include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );
PagoHtml::loadUploadifive();
?>
<div class="pg-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li class="first pg-information"><a href="#tabs-1"><?php echo JText::_( 'PAGO_CATEGORIES_TITLE_INFORMATION' ) ?></a></li>
					<li class="pg-category-view"><a href="#tabs-2"><?php echo JText::_( 'PAGO_CATEGORIES_TITLE_CATEGORY_VIEW' ) ?></a></li>
					<li class="pg-product-view"><a href="#tabs-3"><?php echo JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_VIEW' ); ?></a></li>
					<?php
					// Needs to be a filter, otherwise the $counter gets lost
					//$counter = 3;
					//$dispatcher->trigger( 'backend_category_tab_name', array( &$counter ) );
					?>
				</ul>
				<div class="clear"></div>
			</div>

			<div class = "pg-pad-20 pg-white-bckg pg-border">
				<div id="tabs-1">
					<?php if ( isset( $this->item->id ) && 0 < $this->item->id ) : ?>
						<!-- Start of pago module content -->
						<div class = "pg-container-header">
							<?php echo JText::_('PAGO_CATEGORIES_CATEGORY_INFORMATION'); ?>
						</div>
						<div class="pg-pad-20 pg-mb-20 pg-white-bckg pg-border">
							<table class = "pg-table">
								<thead>
									<tr>
										<td class = "pg-id"> <?php echo JText::_( 'PAGO_ID' ); ?> </td>
										<td class = "pg-published"> <?php echo JText::_( 'PAGO_PUBLISHED' ); ?> </td>
										<td> <?php echo JText::_( 'PAGO_CREATED' ) ?> </td>
										<td> <?php echo JText::_( 'PAGO_MODIFIED' ) ?> </td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class = "pg-id"> <?php echo $this->item->id; ?> </td>
										<td class = "pg-published"> <?php echo ( $this->item->published ? '<span class="pg-published">' . JText::_( 'PAGO_PUBLISHED' ) . '</span>' : '<span class="pg-unpublished">' . JText::_( 'PAGO_UNPUBLISHED' ) . '</span>' ) ?> </td>
										<td> <?php echo date( 'l jS \of F Y h:i:s A', strtotime( $this->item->created_time )) ?> </td>
										<?php if($this->item->modified_time == "0000-00-00 00:00:00"){?>
										<td> <?php echo date( 'l jS \of F Y h:i:s A', strtotime( $this->item->created_time ) ) ?> </td>
										<?php } else { ?>
										<td> <?php echo date( 'l jS \of F Y h:i:s A', strtotime( $this->item->modified_time ) ) ?> </td>
										<?php } ?>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- End of pago module content -->
					<?php endif; ?>

					<div class = "pg-row pg-mb-20">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_CATEGORIES_CATEGORY_INFORMATION' ), null, null, null, 'pg-col-6', '', '', false ) ?>
							<div class = "pg-pad-20 pg-border">
								<?php echo $this->base_params ?>
								<div class = "pg-mb-20"></div>
								<?php echo $this->description_params ?>
							</div>
						<?php echo PagoHtml::module_bottom() ?>
						
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_META_INFORMATION' ), null, null, null, 'pg-col-6', '', '', false ) ?>
							<div class = "pg-pad-20 pg-border">
								<?php echo $this->meta_params ?>
							</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
					<?php echo PagoHtml::module_top( JText::_( 'PAGO_MEDIA' ), null, null, null, '', '', '', false ) ?>
						<?php echo $this->images_params ?>
					<?php echo PagoHtml::module_bottom() ?>
				</div>

				<div id="tabs-2">
					<!-- start -->
					<div class="clear"></div>
					<div class = "pg-row">
						<div class = "pg-col-6">
							<div class = "pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_CATEGORIES_TITLE_CATEGORY_SETTINGS' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-mb-20 pg-border">
										<div class = "pg-row">
											<div class = "pg-col-6">
												<?php echo $this->category_settings ?>	
												<?php echo $this->category_settings_image_settings ?>	
											</div>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>

								<?php echo PagoHtml::module_top( JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_GRID_CATEGORY_VIEW' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-border">
										<div class = "pg-row">
											<div class = "pg-col-12">
												<?php echo $this->product_grid ?>
												<?php echo $this->category_settings_product_image_settings ?>
											</div>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
						<div class = "pg-col-6">
							<div class = "pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_SETTINGS_CATEGORY_VIEW' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-border">
										<?php echo $this->product_settings ?>
										<div class = "category-view-sharing-title pg-mb-20">
											<?php echo JTEXT::_('PAGO_CATEGORIES_TITLE_PRODUCT_SHARING')?>
										</div>
										<?php echo $this->product_settings_sharings ?>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
					</div>
				<!-- end -->	
				</div><!-- end tab-2 -->
				
				<!-- tab3 start -->
				<div id="tabs-3">
					<div class="clear"></div>
					<div class="pg-row">
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_CATEGORIES_TITLE_PRODUCT_SETTINGS' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-mb-20 pg-border">
										<div class = "pg-row">
											<div class = "pg-col-12">
												<?php //print_r($this);die; ?>
												<?php echo $this->product_view_settings ?>
												<?php echo $this->product_view_settings_sharings ?>	
											</div>
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
					</div>
				</div>
				<!-- tab3 end -->
			</div>

			<?php
			// Needs to be a filter, otherwise the $counter gets lost
			// $counter = 3;
			// $dispatcher->trigger( 'backend_category_tab_data', array( &$counter, $this->item ) );
			?>
		</div>

		<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="controller" value="categories" />
		<input type="hidden" name="view" value="categories" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

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
			jQuery('#system-message-container').html("<dl id='system-message'><dt class='error'>Error</dt><dd class='error message'><ul><li><?php echo JText::_( 'PAGO_CATEGORY_ERROR_MUST_CONTAIN_NAME' ); ?></li></ul></dd></dl>");
			jQuery('#params_name').css('border','solid 1px #FF0000');
			return false;
		}
		if(params_name.length > 255){

			jQuery('#params_name').css('border','solid 1px #FF0000');
			alert("<?php echo JText::_( 'PAGO_CATEGORY_ERROR_NAME_MAX_SIZE' ); ?>");
			return false;
			
		}
		if (params_name!='')
		{
			Joomla.submitform(task);
		}
		else
		{
			jQuery('#system-message-container').html("");
			jQuery('#error').css('display','block');
			return false;
		}
	}
	</script>	
</div><!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();
