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

//$uri = str_replace( array( 'components/com_pago/helpers/', 'administrator/' ), '', JURI::root() );

//$upload_type = JFactory::getApplication()->input->getWord( 'type' );
//$path = trim( $this->params->get( 'media.' .$upload_type . '_url_path', 'media/pago' ), '/' );
//$config = Pago::get_instance( 'config' )->get();
//$path = trim( $config->get( 'media.'. $upload_type . '_url_path', 'media/pago' ), '/' );

// $image_url = $uri . $path .'/'. $this->path_extra .'/'.
// 	$this->row->file_meta['sizes']['thumbnail']['file'];


// $img = PagoImageHandlerHelper::get_image_from_object( $this->row, 'thumbnail', false,'id="img-tooltip-' .$this->row->id. '" class="images-grid-thumbnail"' );
// $img_tooltip = PagoImageHandlerHelper::get_image_from_object( $this->row, 'medium', false );
// var_dump($img);
// exit();

$thumbnail_file = @$this->row->file_meta['sizes']['thumbnail']['file'];
$img = $uri . $path .'/'. $this->path_extra .'/'. $thumbnail_file;
$medium_file = @$this->row->file_meta['sizes']['medium']['file'];
$img_tooltip = $uri . $path .'/'. $this->path_extra .'/'. $medium_file;
	
?>
<tr class="pg-table-content" rel="cid-<?php echo $this->row->id; ?>">
	<td class="pg-sort">
		<div class="pg-sort">
			<span class="pg-sort-handle"></span>
			<input type="hidden" name="params[images_ordering][]" value="<?php echo $this->row->id; ?>">
		</div>
	</td>

	<td class="pg-preview">
		<div class = "pg-preview-small-image" style = "background:url('<?php echo $img; ?>')">
			
		</div>
		<div class = "pg-preview-large-image">
			<img src = "<?php echo $img_tooltip; ?>">
		</div>
	</td>

	<td class="pg-name">
		<input type="text" name="params[images][<?php echo $this->row->id; ?>][title]" value="<?php echo $this->row->title; ?>">
	</td>

	<td class="pg-published">
		<?php echo PagoHelper::published( $this->row, '', 'publish.png',  'unpublish.png', '', ' class="publish-buttons" type="file" rel="' .$this->row->id. '"' ); ?>
	</td>

	<td class="pg-default">
		<a href="javascript:void(0);" class="pg-icon images-default id-for-delete is-default-<?php echo $this->row->default; ?>" rel="<?php echo $this->row->id; ?>"></a>
	</td>

	<td class = "pg-remove">
		<a href = "javascript:void(0)"></a>
	</td>

</tr>