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
$path = trim( $this->params->get( 'media.' .$upload_type . '_url_path', 'media/pago' ), '/' );
//$config = Pago::get_instance( 'config' )->get();
//$path = trim( $config->get( 'media.'. $upload_type . '_url_path', 'media/pago' ), '/' );

$image_url = $uri . $path .'/'. $this->path_extra .'/'.
	$this->row->file_meta['sizes']['thumbnail']['file'];
?>

<table class = "upload-image-table pg-pad-20 pg-border">
	<tr>
		<td>
			<div class="upload-image">
				<img src = "<?php echo $image_url; ?>">
			</div>
		</td>
		<td class="upload-image-name"><?php echo $this->row->title; ?></td>
		<td id = "<?php echo $this->row->id; ?>" class="upload-image-remove"><a href = "javascript:void(0)" class = "pg-btn-large pg-btn-delete-icon"></a></td>
	</tr>
</table>

<input type="hidden" id="files[<?php echo $this->row->id; ?>][item_id]" name="files[<?php echo $this->row->id; ?>][item_id]" value="<?php echo $this->row->item_id; ?>" />
<table class="slidetoggle describe startclosed" summary="layout table" style="display:none;">
	<thead class="media-item-info">
		<tr>
			<!--<td class="image-thumbnail" rowspan="4"><img class="thumbnail" src="<?php echo $image_url; ?>" alt="" /></td>-->
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
								<span class="alignleft"><?php echo JText::_('Title'); ?></span>
								<span class="alignright">
									<abbr title="required" class="required">*</abbr>
								</span>
							</label>
						</th>
						<td class="field">
							<input type="text" id="files[<?php echo $this->row->id; ?>][title]" name="files[<?php echo $this->row->id; ?>][title]" value="<?php echo $this->row->title; ?>" />
						</td>
					</tr>
					<tr class="published">
						<th valign="middle" scope="row" class="label">
							<label for="files[<?php echo $this->row->id; ?>][published]">
								<span class="alignleft"><?php echo JText::_('Published'); ?></span>
								<br class="clear" />
							</label>
						</th>
						<td class="field">
							<?php echo $this->lists['published']; ?>
						</td>
					</tr>
					<tr class="access">
						<th valign="middle" scope="row" class="label">
							<label for="files[<?php echo $this->row->id; ?>][access]">
								<span class="alignleft"><?php echo JText::_('Access'); ?></span>
								<br class="clear" />
							</label>
						</th>
						<td class="field">
							<?php echo $this->lists['access']; ?>
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
					<!-- <tr class="text">
						<th valign="middle" scope="row" class="label">
							<label for="files[<?php //echo $this->row->id; ?>][text]">
								<span class="alignleft"><?php //echo JText::_('Description'); ?></span>
								<br class="clear" />
							</label>
						</th>
						<td class="field">
							<textarea type="text" id="files[<?php //echo $this->row->id; ?>][text]" name="files[<?php //echo $this->row->id; ?>][text]" rows="6" cols="32"><?php //echo $this->row->fulltext; ?></textarea>
						</td>
					</tr> -->
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
