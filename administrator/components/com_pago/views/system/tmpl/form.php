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

PagoHtml::pago_top( $menu_items, false );
?>
<div class="pg-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" name="adminForm">
		
		<?php echo PagoHtml::module_top( JText::_( 'BASE_PARAMS' ) ) ?>
			<div class="pg-module-content">
				<?php echo $this->base_params ?>
			</div>
		<?php echo PagoHtml::module_bottom() ?>

		<input type="hidden" name="cid[]" value="<?php echo $this->item->state_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="id" value="<?php echo $this->item->state_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="states" />
		<input type="hidden" name="controller" value="states" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

</div><!-- end pago content -->
<?php 
PagoHtml::pago_bottom();

echo JHTML::_('behavior.keepalive');
