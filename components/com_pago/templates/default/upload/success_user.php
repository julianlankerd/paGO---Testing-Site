<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>

<?php
$uri = str_replace( array( 'components/com_pago/helpers/', 'administrator/' ), '', JURI::root() );

$upload_type = JFactory::getApplication()->input->getWord( 'type' );
$path = trim( $this->params->get( $upload_type . '_url_path', 'media/pago' ), '/' );

$image_url = $uri . $path .'/'. JFilterOutput::stringURLSafe( $this->category->name ) .'/'. $this->row->file_meta['sizes']['thumbnail']['file'];
?>

<a class="toggle describe-toggle-on" href="#"><?php echo JText::_('ADD_CAPTION'); ?></a>
<a class="toggle describe-toggle-off" href="#" style="display:none;"><?php echo JText::_('Hide'); ?></a>
<div class="filename new"><?php echo $this->row->title; ?></div>
<input type="hidden" id="files[<?php echo $this->row->id; ?>][product]" name="files[<?php echo $this->row->id; ?>][product]" value="<?php echo $this->row->product; ?>" />
<table class="slidetoggle describe startclosed" summary="layout table" style="display:none;">
	<thead class="media-item-info">
		<tr>
			<td class="image-thumbnail" rowspan="4"><img class="thumbnail" src="<?php echo $image_url; ?>" alt="" /></td>
			<td><?php echo $this->row->file_name; ?></td>
		</tr>
		<tr>
			<td><?php echo $this->row->mime_type; ?></td>
		</tr>
		<tr>
			<td><?php echo $this->row->created_time; ?></td>
		</tr>
		<tr>
			<td></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td width="425" colspan="2">
				<table class="image-data">
					<tr class="title">
						<th valign="middle" scope="row" class="label">
							<label for="files[<?php echo $this->row->id; ?>][title]">
								<span class="alignleft"><?php echo JText::_('PAGO_TITLE'); ?></span>
								<span class="alignright">
									<abbr title="required" class="required">*</abbr>
								</span>
							</label>
						</th>
						<td class="field">
							<input type="text" id="files[<?php echo $this->row->id; ?>][title]" name="files[<?php echo $this->row->id; ?>][title]" value="<?php echo $this->row->title; ?>" />
						</td>
					</tr>
					<tr class="caption">
						<th valign="middle" scope="row" class="label">
							<label for="files[<?php echo $this->row->id; ?>][caption]">
								<span class="alignleft"><?php echo JText::_('Caption'); ?></span>
								<br class="clear" />
							</label>
						</th>
						<td class="field">
							<input type="text" id="files[<?php echo $this->row->id; ?>][caption]" name="files[<?php echo $this->row->id; ?>][caption]" value="<?php echo $this->row->caption; ?>" />
							<p class="help"><?php echo JText::_('Also used as alternate text for the image'); ?></p>
						</td>
					</tr>
					<tr class="text">
						<th valign="middle" scope="row" class="label">
							<label for="files[<?php echo $this->row->id; ?>][text]">
								<span class="alignleft"><?php echo JText::_('Description'); ?></span>
								<br class="clear" />
							</label>
						</th>
						<td class="field">
							<textarea type="text" id="files[<?php echo $this->row->id; ?>][text]" name="files[<?php echo $this->row->id; ?>][text]" rows="6" cols="32"><?php echo $this->row->fulltext; ?></textarea>
						</td>
					</tr>
					<tr class="submit">
						<td></td>
						<td class="savesend">
							<a href="#" class="del-link" onclick="document.getElementById('del_attachment_<?php echo $this->row->id; ?>').style.display='block';return false;"><?php echo JText::_('Delete'); ?></a>
							<div id="del_attachment_<?php echo $this->row->id; ?>" class="del-attachment" style="display:none;">
								<?php echo JText::_('You are about to delete'); ?> <strong><?php echo $this->row->file_name; ?></strong>. 
								<a href="index.php?option=com_pago&amp;controller=files&amp;task=remove&amp;cid[]=<?php echo $this->row->id; ?>" id="del[<?php echo $this->row->id; ?>]" class="delete"><?php echo JText::_('Continue'); ?></a>
								<a href="#" class="del-link" onclick="this.parentNode.style.display='none';return false;"><?php echo JText::_('Cancel'); ?></a>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="files[<?php echo $this->row->id; ?>][access]" value="0" />
<input type="hidden" name="files[<?php echo $this->row->id; ?>][published]" value="1" />