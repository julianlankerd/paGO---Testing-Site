<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

$doc =& JFactory::getDocument();

jimport('joomla.html.pane');

$editor = &JFactory::getEditor();
$pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));

JHTML::_('behavior.tooltip');
?>

<form action="index.php" method="post" name="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top" width="50%">
				<br />
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'ITEM_BASE_PARAMS' ); ?></legend>
					<?php echo $this->base_params ?>
				</fieldset>
				<br />
			</td>
			<td valign="top" width="350%" style="padding: 0px 0 0 5px">
				<br />
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'ITEM_INFORMATION' ); ?></legend>
					<table width="100%" style="border: 0px dashed silver; padding: 5px; margin-bottom: 10px;">
						<tbody>
							<tr>
								<td><strong>Item ID:</strong></td>
								<td> <?php echo $this->item->id ?> </td>
							</tr>
							<tr>
								<td><strong>State</strong></td>
								<td><?php echo ($this->item->published ? '<span style="color:green">Published</span>' : '<span style="color:red">Unpublished</span>' )?></td>
							</tr>
							<tr>
                                <td><strong>Created</strong></td>
                                <td><?php echo date( 'l jS \of F Y h:i:s A', strtotime( $this->item->created )) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Modified</strong></td>
                                <td><?php echo date( 'l jS \of F Y h:i:s A', strtotime($this->item->modified )) ?></td>
                            </tr>
                        </tbody>
                    </table>
				</fieldset>
            </td>
        </tr>
		<tr>
			<td>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SALES_CONDITIONS' ); ?></legend>
					<?php echo $this->condition_params; ?>
				</fieldset>
			</td>
			<td>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SALES_PRODUCTS' ); ?></legend>
					<?php echo $this->product_params; ?>
				</fieldset>
			</td>
		</tr>
    </table>
    <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="option" value="com_pago" />
    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="task" value="cancel" />
    <input type="hidden" name="view" value="sales" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php echo JHTML::_('behavior.keepalive');
