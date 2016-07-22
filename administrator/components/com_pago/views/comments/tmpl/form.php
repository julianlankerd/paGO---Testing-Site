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

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( "
	
" );
?>
<div class="pg-content">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<input type='hidden' name="selectedTab" id='selectedTab' />
		<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
		<div id="tabs">

			<div class="pg-tabs">
				<ul>
					<li class="first pg-information"><a onClick="addTabPrefixInUrl(this);" href="#tabs-1"><span class="icon"></span><?php echo JText::_( 'PAGO_ITEM_TAB_INFORMATION' ); ?></a></li>
				</ul>
				<div class="clear"></div>
			</div>

			<div class="tabs-content pg-pad-20 pg-white-bckg pg-border ">
				<div id="tabs-1">
					<div class="clear"></div>
					<div class="pg-row">
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_COMMENTS_INFORMATION_TITLE' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class="pg-pad-20 pg-mb-20 pg-border">
										<div class="pg-row">
											<div class="pg-col-4">
												<span class="field-heading">
													<label><?php echo JText::_( 'PAGO_ID' ) ?></label>
												</span>
												<input type="text" name="" id="" value="<?php echo $this->comment->id ?>" readonly>
											</div>
											<?php if($this->comment->author_id == 0){ ?>
											<div class="pg-col-4 cl-l">
												<span class="field-heading">
													<label><?php echo JText::_( 'PAGO_COMMENTS_AUTHOR_NAME' ) ?></label>
												</span>
												<span> <?php echo $this->comment->author_name;?> </span>
											</div>
											<div class="pg-col-4">	
												<span class="field-heading">
													<label><?php echo JText::_( 'PAGO_COMMENTS_AUTHOR_EMAIL' ) ?></label>
												</span>
												<span> <?php echo $this->comment->author_email;?> </span>
											</div>
											<div class="pg-col-4">
												<span class="field-heading">
													<label><?php echo JText::_( 'PAGO_COMMENTS_AUTHOR_WEB_SITE' ) ?></label>
												</span>
												<span> <?php echo $this->comment->author_web_site;?> </span>
											</div>
											<?php }else{ ?>
											<div class="pg-col-4">
												<span class="field-heading">
													<label><?php echo JText::_( 'PAGO_COMMENTS_AUTHOR_ID' ) ?></label>
												</span>
												<input type="text" name="" id="" readonly value="<?php echo $this->comment->author_id;?> ">
											</div>
											<?php } ?>
											<?php if($this->comment->parent_id != 0){ ?>
											<div class="pg-col-4">
												<span class="field-heading">
													<label><?php echo JText::_( 'PAGO_COMMENTS_PARENT_ID' ) ?></label>
												</span>
												<span> <a href="<?php echo JRoute::_('index.php?option=com_pago&controller=comments&task=edit&view=comments&cid[]='.(int) $this->comment->parent_id); ?>"><?php echo $this->comment->id;?></a> </span>
											</div>
											<?php } ?>
											<!--
											<div class="pg-col-12">
												<span class="field-heading">
													<label><?php //echo JText::_( 'PAGO_RATING' ) ?></label>
												</span>
												<?php //echo '<span class="pg-published">' . $this->item->rating . '</span>' ?>
											</div> 
											-->
										</div>
									</div>
								<?php echo PagoHtml::module_bottom() ?>
							</div>
						</div>
						<div class="pg-col-12">
							<div class="pg-row">
								<?php echo PagoHtml::module_top( JText::_( 'PAGO_COMMENTS_EDIT_TITLE' ), null, null, null, 'pg-col-12', '', '', false ) ?>
									<div class = "pg-pad-20 pg-mb-20 pg-border">
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
			</div>
		</div>

		<input type="hidden" name="cid[]" value="<?php echo $this->comment->id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->comment->id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="comments" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

</div><!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');
PagoHtml::pago_bottom();