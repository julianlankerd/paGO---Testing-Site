<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::apply_layout_fixes();

$text = JFactory::getApplication()->input->get('cid', array(0), 'array') ? JText::_( 'Edit' ) : JText::_( 'New' );

JToolBarHelper::save();
JToolBarHelper::apply();

if ( JFactory::getApplication()->input->get('cid', array(0), 'array') )  {
	JToolBarHelper::cancel();
} else {
	JToolBarHelper::cancel( 'cancel', 'Close' );
}

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items, false );
?>
<div class="pg-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" name="adminForm">

		<?php echo PagoHtml::module_top( JText::_( 'PAGO_DETAILS' ) ) ?>
		<?php echo $this->base_params ?>
		<?php echo $this->memberlist_params ?>
		<?php echo PagoHtml::module_bottom() ?>

		<input type="hidden" name="cid[]" value="<?php echo $this->item->group_id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="group_id" value="<?php echo $this->item->group_id; ?>" />
		<input type="hidden" name="task" value="cancel" />
		<input type="hidden" name="view" value="groups" />
		<input type="hidden" name="controller" value="groups" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

</div><!-- end pago content -->
<?php
PagoHtml::pago_bottom();
echo JHTML::_('behavior.keepalive');
