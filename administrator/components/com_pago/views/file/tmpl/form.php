<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('behavior.tooltip'); ?>
<?php
$this->row->metadata = json_decode( $this->row->metadata, true );
$files_meta = json_decode( $this->params->get( 'files_meta', '[]' ), true );
?>
<script type="text/javascript">
<!--
function submitbutton( pressbutton )
{
	var form = document.adminForm;

	if ( pressbutton == 'cancel' ) {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if ( form.title.value == "" ) {
		alert( "Image must have a Title" );
		return false;
	}

	submitform( pressbutton );
}
//-->
</script>
<form action="index.php" method="post" name="adminForm">
	<div class="col width-55">
		<fieldset class="adminform">
			<legend><?php echo JText::_('Details')?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key">
							<label for="title"><?php echo JText::_('Title')?>:</label>
						</td>
						<td colspan="2">
							<input type="text" title="<?php JText::_('Title of your information'); ?>" maxlength="50" size="50" value="<?php echo $this->row->title; ?>" id="title" name="title" class="text_area"/>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="key">
							<label for="alias"><?php echo JText::_('Alias')?>:</label>
						</td>
						<td colspan="2">
							<input type="text" title="<?php echo JText::_( 'ALIASTIP' ); ?>" maxlength="255" size="50" value="<?php echo $this->row->alias; ?>" id="alias" name="alias" class="text_area"/>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="key">
							<label for="caption"><?php echo JText::_('Caption')?>:</label>
						</td>
						<td colspan="2">
							<input type="text" title="<?php echo JText::_('Caption'); ?>" maxlength="255" size="50" value="<?php echo $this->row->caption; ?>" id="caption" name="caption" />
						</td>
					</tr>
					<?php if ( 'files' != $this->row->type ) { ?>
					<tr>
						<td class="key"><?php echo JText::_('Published')?>:</td>
						<td colspan="2">
							<?php echo $this->lists['published']; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="ordering"><?php echo JText::_( 'Ordering' ); ?>:</label>
						</td>
						<td colspan="2">
							<?php echo $this->lists['ordering']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" nowrap="nowrap" class="key">
							<label for="access"><?php echo JText::_('Access Level')?>:</label>
						</td>
						<td>
							<?php echo $this->lists['access']; ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('Description'); ?></legend>
			<table class="admintable">
				<tr>
					<td>
						<?php
						// parameters : areaname, content, width, height, cols, rows
						echo $this->editor->display( 'filetext',  $this->row->fulltext , '100%', '400', '45', '20' ) ;
						?>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<?php
	$db = JFactory::getDBO();
	$create_date 	= null;
	$nullDate 		= $db->getNullDate();

	// used to hide "Reset Hits" when hits = 0
	if ( !$this->row->hits ) {
		$visibility = 'style="display: none; visibility: hidden;"';
	} else {
		$visibility = '';
	}

	?>
	<table width="320" style="border: 1px dashed silver; padding: 5px; margin: 10px 0 10px 5px;display:inline-block;">
		<?php if ( $this->row->id ) { ?>
		<tr>
			<td><strong><?php echo JText::_( 'FIle ID' ); ?>:</strong></td>
			<td>
				<?php echo $this->row->id; ?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td><strong><?php echo JText::_( 'File name' ); ?>:</strong></td>
			<td>
				<?php echo $this->row->file_name; ?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_( 'Mime Type' ); ?>:</strong></td>
			<td>
				<?php echo $this->row->mime_type;?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_( 'Hits' ); ?>:</strong></td>
			<td>
				<?php echo $this->row->hits;?>
				<span <?php echo $visibility; ?>>
					<input name="reset_hits" type="button" class="button" value="<?php echo JText::_( 'Reset' ); ?>" onclick="submitbutton('resethits');" />
				</span>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_( 'Created' ); ?>:</strong></td>
			<td>
				<?php
				if ( $this->row->created_time == $nullDate ) {
					echo JText::_( 'New document' );
				} else {
					echo JHTML::_('date',  $this->row->created_time,  JText::_('DATE_FORMAT_LC2') );
				}
				?>
			</td>
		</tr>
		<tr>
			<td><strong><?php echo JText::_( 'Modified' ); ?>:</strong></td>
			<td>
				<?php
				if ( $this->row->modified_time == $nullDate ) {
					echo JText::_( 'Not modified' );
				} else {
					echo JHTML::_('date',  $this->row->modified_time, JText::_('DATE_FORMAT_LC2'));
				}
				?>
			</td>
		</tr>
	</table>

	<table width="320" style="border: 1px solid silver; padding: 5px; margin: 10px 0 10px 5px;display:inline-block;">
		<thead>
			<tr>
				<th>Custom metadata</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $files_meta as $meta => $default_val ) {
				$value = $default_val;
				$name = strtolower( preg_replace( '/[^\w]/', '', $meta ) );

				if ( is_array( $this->row->metadata ) && isset( $this->row->metadata[$name] ) ) {
					$value = $this->row->metadata[$name];
				}
			?>
			<tr>
				<td><label for="<?php echo $name; ?>"><strong><?php echo $meta; ?>:</strong></label></td>
				<td>
					<input type="text" name="metadata[<?php echo $name; ?>]" value="<?php echo $value; ?>" id="<?php echo $name; ?>" />
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_pago" />
	<input type="hidden" name="view" value="file" />
	<input type="hidden" name="controller" value="file" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="layout" value="form" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="submit" value="Save" />

</form>