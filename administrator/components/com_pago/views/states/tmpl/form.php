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

PagoHtml::pago_top( $menu_items, false, $this->top_menu );
$country_id = $this->item->country_id;
if(!$country_id)
{
	$country_id = $this->country_id;
}
?>

<script>
	Joomla.submitbutton = function (pressbutton) 
	{
		submitbutton(pressbutton);
	}
	
	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
	
		if (pressbutton) 
		{
			if (pressbutton == 'publish' || pressbutton == 'unpublish' 
				|| pressbutton == 'remove' || pressbutton == 'copy' 
				|| pressbutton == 'edit')
			{
				if (form.boxchecked.value == 0)
				{
					alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_ITEMS');?>');
					return false;
				}
				else
				{
					form.task.value = pressbutton;
					form.submit();
				}
			}
			else
			{	
				form.task.value = pressbutton;
				
				// form.onsubmit();
				
				form.submit();
				
				return false;
			}
		}
	}
</script>
<div class="pg-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" name="adminForm">
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
				<div class="pg-border pg-white-bckg pg-pad-20">
					<div id="tabs-1">
						<?php echo PagoHtml::module_top( JText::_( 'PAGO_LOCATIONS_BASE_PARAMS' ), null, null, null, null, null, null, null ) ?>
						<div class="pg-border pg-white-bckg pg-pad-20">
							<?php echo $this->base_params ?>
						</div>
						<?php echo PagoHtml::module_bottom() ?>
					</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="cid[]" value="<?php echo $this->item->state_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->state_id; ?>" />
		<input type="hidden" name="country_id" value="<?php echo $country_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="states" />
		<input type="hidden" name="controller" value="states" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

</div><!-- end pago content -->
<?php 
PagoHtml::pago_bottom();

echo JHTML::_('behavior.keepalive');
