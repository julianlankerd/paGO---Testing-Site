<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php
$dispatcher = KDispatcher::getInstance();
$path = PagoHelper::get_files_base_path();


$dispatcher->trigger( 'file_uploader_get_vars',
	array( &$this->row->id, &$this->row->file_meta['file_path'], 'files', 'abs_path' ) );

$abs_path = $path .DS. trim( $this->row->file_meta['file_path'], DS )
	.DS. $this->row->file_name;

$file_type = PagoImageHelper::ext2type( strtolower( pathInfo( $abs_path, PATHINFO_EXTENSION ) ) );

$extension = explode('.', $this->row->file_name);
$extension = $extension[count($extension)-1];


$primary_category = PagoHelper::getItemPrimaryCat($this->row->item_id);

$uri = str_replace( array( 'components/com_pago/helpers/', 'administrator/' ), '', JURI::root() );

$link = $uri.'media/pago/items/'.$primary_category.'/'.$this->row->file_name;

switch($extension){
	case 'zip':
		$icon = '<i class = "fa fa-file-archive-o"></i>';
		break;
	case 'gzip':
		$icon = '<i class = "fa fa-file-archive-o"></i>';
		break;
	case 'rar':
		$icon = '<i class = "fa fa-file-archive-o"></i>';
		break;
	case 'jpg':
		$icon = '<i class = "fa fa-file-image-o"></i>';
		break;
	case 'jpeg':
		$icon = '<i class = "fa fa-file-image-o"></i>';
		break;
	case 'png':
		$icon = '<i class = "fa fa-file-image-o"></i>';
		break;
	case 'pdf':
		$icon = '<i class = "fa fa-file-pdf-o"></i>';
		break;
	case 'xls':
		$icon = '<i class = "fa fa-file-excel-o"></i>';
		break;
	case 'xlsx':
		$icon = '<i class = "fa fa-file-excel-o"></i>';
		break;
	case 'doc':
		$icon = '<i class = "fa fa-file-word-o"></i>';
		break;
	case 'docx':
		$icon = '<i class = "fa fa-file-word-o"></i>';
		break;
	case 'mp3':
		$icon = '<i class = "fa fa-music"></i>';
		break;
	case 'wav':
		$icon = '<i class = "fa fa-music"></i>';
		break;
	case 'midi':
		$icon = '<i class = "fa fa-music"></i>';
		break;
	case 'avi':
		$icon = '<i class = "fa fa-film"></i>';
		break;
	case 'mp4':
		$icon = '<i class = "fa fa-film"></i>';
		break;
	case 'flv':
		$icon = '<i class = "fa fa-film"></i>';
		break;
	case 'wmv':
		$icon = '<i class = "fa fa-film"></i>';
		break;
	case '3gp':
		$icon = '<i class = "fa fa-film"></i>';
		break;
	case 'txt':
		$icon = '<i class = "fa fa-file-text-o"></i>';
		break;
	default:
		$icon = '<i class = "fa fa-question"></i>';
		break;
}

?>
<tr class="pg-table-content" rel="cid-<?php echo $this->row->id; ?>">
	<td class="pg-sort">
		<div class="pg-sort">
			<span class="pg-sort-handle"></span>
			<input type="hidden" name="params[downloads_ordering][]" value="<?php echo $this->row->id; ?>" />
		</div>
	</td>
	<td class = "pg-preview">
			<a href="<?php echo $link; ?>" class = "pg-preview-small-image" >
				<?php echo  $icon; ?>
			</a>
		</td>

	<td class="pg-name">
		<input type="text" name="params[downloadable][<?php echo $this->row->id; ?>][title]" value="<?php echo $this->row->title; ?>" />
	</td>
	
	
	
	<td class="pg-access">
		
		<?php 
								
			$opp_data = array(
			    array(
			        'value' => 1,
			        'text' => 'Only on Purchase',
			        //'attr' => array('data-price'=>'5'),
			    ),
			    array(
			        'value' => 2,
			        'text' => 'Freely Availble',
			        //'attr' => array('data-price'=>'3'),
			    ),
			);
			
			$options = array(
			    //'id' => $_name. '[' .$row->id. '][access1]', // HTML id for select field
			    'list.attr' => array( // additional HTML attributes for select field
			        'class'=>'field-apples',
			    ),
			    'list.translate'=>false, // true to translate
			    'option.key'=>'value', // key name for value in data array
			    'option.text'=>'text', // key name for text in data array
			    'option.attr'=>'attr', // key name for attr in data array
			    'list.select'=> 1, // value of the SELECTED field
			);
			
			echo JHtmlSelect::genericlist($opp_data, 'params[downloadable][' .$this->row->id. '][access]', $options);
		
		?>
								
		<?php //echo JHTML::_( 'access.assetgrouplist', 'params[downloadable][' .$this->row->id. '][access]', $this->row->access, array( 'size' => 1 ) );	?>
	</td>

	<td class="pg-published">
		<?php echo PagoHelper::published( $this->row, '', 'publish.png',  'unpublish.png', '', ' class="publish-buttons" type="file" rel="' .$this->row->id. '"' ); ?>
	</td>

	<td class = "pg-remove">
		<a href = "javascript:void(0)"></a>
	</td>
</tr>	