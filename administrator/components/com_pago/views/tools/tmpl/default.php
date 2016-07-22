<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

PagoHtml::apply_layout_fixes();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items );
$select_filters='';
				//echo PagoHtml::module_top( JText::_( 'PAGO_QUICK_OVERVIEW' ), null, null, $select_filters );
?>

<div class="pg-content">
	<div id="cpanel" style="padding:20px">

		<?php
		echo PagoHtml::module_top( JText::_( 'PAGO_TOOLS' ), null, null, null, $select_filters );
		?>

		<tr class="pg-table-content">
			<td>
				<?php
				if (!$this->buttons){
					echo JTEXT::_('PAGO_NO_TOOLS_ACTIVATED');echo '</td>';
				}
				foreach ($this->buttons as $button):
					echo $this->button($button);
				endforeach;

				echo PagoHtml::module_bottom();
				?>
			</td>
		</tr>
	</div>
</div><!-- end pago content -->

<?php echo JHTML::_('behavior.keepalive');

PagoHtml::pago_bottom();