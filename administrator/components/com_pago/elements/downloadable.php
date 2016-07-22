<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldDownloadable extends JFormField
{
	/**
	* Element name
	*
	* @access	   protected
	* @var		  string
	*/
	protected $type = 'Downloadable';
	var $_data = array();

	function __construct()
	{
		Pago::load_helpers( 'imagehandler' );
	}

	function get_item_id()
	{
		static $id = 0;

		if ( !$id )
		{
			$array = JFactory::getApplication()->input->get('cid',  0, 'array');
			$id = (int) $array[0];
		}

		return $id;
	}

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$attr = $node->attributes();

		$this->name = $attr['name'];
		$this->callback = $attr['callback'];

		return $this->html($name, $value);
	}

	function getToolbar()
	{
		$item_id = $this->get_item_id();
		$bar = new JToolBar($this->name . '-Toolbar');

		switch ($this->callback)
		{
			case 'category':
				$title = JText::_('PAGO_IMAGE');
				break;

			default:
				$title = ucfirst($this->callback);
				break;
		}

		if ($item_id)
		{
			// echo PagoHelper::addCustomButton(JText::_('PAGO_ADD') . ' ' . ucfirst($title),
			// 	"javascript:pull_upload_form({$item_id}, '"  . ucfirst($this->callback) . "');",
			// 	'pg-btn-small pg-btn-dark pg-btn-add', 'javascript:void(0);', 'toolbar', '', $bar);

			echo PagoHelper::addCustomButton(JText::_('PAGO_ADD') . ' ' . ucfirst($title),"",
				'pg-btn-small pg-btn-dark pg-btn-add mediaFileUploadBTN', 'javascript:void(0);', 'toolbar', '', $bar);

			echo '<div id="mediaFileUpload" style="display: none;"></div>';
			//echo '<a href="javascript:void(0);" id="mediaFileUploadBTN" class="pg-btn-small pg-btn-dark pg-btn-add>'.JText::_('PAGO_ADD') .' '. ucfirst($title).'</a>';
		}
		else
		{
			// echo PagoHelper::addCustomButton(JText::_('PAGO_ADD') . ' ' . ucfirst($title),
			// 	"alert('" . JText::_('PAGO_ELEMENTS_MEDIA_ERROR_ID') . "');",
			// 	'pg-btn-small pg-btn-dark pg-btn-add', 'javascript:void(0);', 'toolbar', '', $bar);
		}

		//echo PagoHelper::addCustomButton(JText::_( 'PAGO_DELETE'),
			//"javascript:delete_files('."  .$this->callback. "-checkboxes');",
				//'pg-btn-small pg-btn-dark pg-btn-delete', 'javascript:void(0);', 'toolbar', '', $bar);

	}

	function html( $_name, $value )
	{
		if ( !$value )
		{
			$value = '[]';
		}

		$doc = JFactory::getDocument();
		$item_id = $this->get_item_id();

		// Add Styles/Scripts
		PagoHtml::thickbox();
		PagoHtml::uniform();
		PagoHtml::tooltip();

		$data = PagoImageHandlerHelper::get_item_files($item_id, false, array( $this->callback ));
		
	

		ob_start();
?>
<div class="pg-table-wrap">
	<div class = "pg-container-header">
		<?php echo JText::_( 'PAGO_ITEMS_TITLE_DONWLOAD_PARAMETERS' ); ?>
		<div class="pg-right pg-container-header-buttons">
			<?php echo $this->getToolbar() ?>
		</div>
	</div>
	<div class = "pg-white-bckg pg-border pg-pad-20">
		<table class="pg-table pg-download-manager pg-repeated-rows">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings">
					<!--<td class="pg-checkbox" >
						<input type="checkbox" id="checkall" name="toggle" value="" onclick="pago_check_all(this, '.<?php echo $this->callback; ?>-checkboxes');" />
						<label for="checkall"></label>
					</td>-->

<!-- 					<td class="pg-id">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_ID'); ?>
					</td>
 -->
					<td class="pg-sort">
						<a href = "javascript:void(0)"></a>
					</td>

					<td class="pg-preview">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_PREVIEW') ?>
					</td>

					<td class="pg-name">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_NAME') ?>
					</td>

					<!--<td class="pg-caption" width="20%">
						<div class="pg-caption">
							<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_CAPTION') ?>
						</div>
					</td>

					<td class="pg-description">
						<div class="pg-description">
							<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_DESC') ?>
						</div>
					</td>-->

					<td class="pg-access">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_ACCESS') ?>
					</td>

					<td class="pg-published">
						<?php echo JText::_('PAGO_PUBLISHED') ?>
					</td>

					<td class="pg-remove">
						<?php echo JText::_( 'PAGO_REMOVE' ); ?>
					</td>
				</tr>
			</thead>
			<tbody>

				<?php
				$js = '';

				if ( !empty( $data ) )
				{
					for ($i = 0; $i < count($data); $i++)
					{
						$totalRows = count($data) - 1;
						

						if ( $data[$i]->type == 'store_default' )
						{
							continue;
						}

						$row = clone $data[$i];
						$extension = explode('.', $row->file_name);
						$extension = $extension[count($extension)-1];

						$link = JURI::ROOT().'media/pago/items/'.$row->primary_category.'/'.$row->file_name;

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
						<tr class="pg-table-content<?php if ( $i == 0 ) { echo ' pg-first-row'; } if ( $i == $totalRows ) { echo ' pg-last-row'; } ?>" rel="cid-<?php echo $row->id; ?>" filetype="<?php echo $extension; ?>">
							<!--<td class = "pg-checkbox">
								<input type="checkbox" class="<?php echo $this->callback; ?>-checkboxes" value="<?php echo $row->id; ?>" name="cidimgs[]" id="cb<?php echo $i; ?>" onclick="pago_highlight_row(this);" />
								<label for="cb<?php echo $i; ?>"></label>
							</td>-->

<!-- 							<td class="pg-id">
								<?php echo $row->id; ?>
							</td> -->

							<td class="pg-sort">
								<div class="pg-sort">
									<span class="pg-sort-handle"></span>
									<input type="hidden" name="params[downloads_ordering][]" value="<?php echo $row->id; ?>" />
								</div>
							</td>

							<td class = "pg-preview">
	  							<a href="<?php echo $link; ?>" class = "pg-preview-small-image" >
	  								<?php echo  $icon; ?>
  								</a>
	   						</td>

							<td class="pg-name">
								<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][title]" value="<?php echo $row->title; ?>" />
							</td>

							<!--<td>
								<div class="pg-caption">
									<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][caption]" value="<?php echo $row->caption; ?>">
								</div>
							</td>

							<td>
								<div class="pg-description">
									<a href="javascript:void(0);" onClick="img_edit_desc(<?php echo $row->id; ?>);"><?php echo JText::_('PAGO_EDIT'); ?></a>
								</div>
							</td>-->

							<td class="pg-access show-overflow">
								<?php 
								
									$opp_data = array(
									    array(
									        'value' => 1,
									        'text' => JText::_('PAGO_ELEMENTS_DOWNLOADABLE_ONLY_PURCHASE'),
									        //'attr' => array('data-price'=>'5'),
									    ),
									    array(
									        'value' => 2,
									        'text' => JText::_('PAGO_ELEMENTS_DOWNLOADABLE_FREELY_AVAILABLE'),
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
									    'list.select'=> $row->access, // value of the SELECTED field
									);
									
									echo JHtmlSelect::genericlist($opp_data, $_name. '[' .$row->id. '][access]', $options);
								
								?>
								<?php //echo JHTML::_( 'access.assetgrouplist', $_name. '[' .$row->id. '][access]', $row->access, array( 'size' => 1 ) );	?>
							</td>

							<td class="pg-published">
								<?php echo PagoHelper::published( $row, $i, 'publish.png',  'unpublish.png', '', ' class="publish-buttons" type="file" rel="' .$row->id. '"' ); ?>
							</td>

							<td class = "pg-remove">
								<a href = "javascript:void(0)"></a>
							</td>
						</tr>
					<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php
$return = ob_get_clean();
$session    = JSession::getInstance( 'none', array() );
$COOKIE = $_COOKIE[$session->getName()];
$path = JFactory::getApplication()->input->getString( 'path' );
$uploadScript = JURI::base( true ).'/components/com_pago/helpers/access_point.php';

$params = Pago::get_instance('config')->get();
$media_param = $params->get('media');
$ext = $media_param->allowed_download_extension;
$ext = explode(",", $ext);
$allowed_extensions = '';


if (count($ext) > 0)
{
	for ($j = 0; $j < count($ext); $j++)
	{
		$allowed_extensions .= '*.' . $ext[$j] . ';';
	}
}

$accessHtml = JHTML::_( 'access.assetgrouplist', 'test', 0, array( 'size' => 1 ) );
	
	$doc->addScriptDeclaration( "

	jQuery(document).ready(function(){
	{$js}
	// Return a helper with preserved width of cells

	jQuery(document).on('click','.mediaFileUploadBTN', function(){
		jQuery('#uploadifive-mediaFileUpload input:last-child').click();
	});

	jQuery('#mediaFileUpload').uploadifive({
		'uploadScript' : '$uploadScript',
		'formData':		{
							'option':'com_pago',
							'controller':'upload',
							'task':'upload',
							'type':'$this->callback',
							'ac': '$COOKIE',
							'item_id': '$item_id;',
							'path': '$path',
							'validFileType':'$allowed_extensions',
							},
		
		'multi':			true,
		'auto':				true,
        'onAddQueueItem' : function(file) {
       		queueItemID = makequeueItemID(file.queueItem[0].id);
			jQuery('.pg-download-manager tbody').append(\"<tr class='new_row_add' id=\"+queueItemID+\"><td class='pg-sort'><div class='pg-sort'><span class='pg-sort-handle'></div></div></td><td class='pg-preview pg-upload-percent'></td><td class='pg-name'><span class='pg_upload_proccess'></span></td><td class='pg-access'><select disabled ><option select='select' value='0'>Public</option></select></td><td class='pg-published'><img src='components/com_pago/css/img-new/publish.png'></td><td class='pg-remove disabled'><a href='javascript:void(0)'></a></td></tr>\");
        	jQuery('.pg-download-manager tbody select').chosen({'disable_search': true, 'disable_search_threshold': 6, 'width': 'auto' });
        },
       	'onProgress'   : function(file, e) {	       	
       		queueItemID = makequeueItemID(file.queueItem[0].id);
            if (e.lengthComputable) {
                var percent = Math.round((e.loaded / e.total) * 100);
            }
            jQuery('.pg-download-manager tbody #'+queueItemID+' .pg-upload-percent').html(percent+'%');
            jQuery('.pg-download-manager tbody #'+queueItemID+' .pg_upload_proccess').css({ 'width': percent + '%' });
        	
        },
		onUploadComplete:			function ( fileObj, data) {
			queueItemID = makequeueItemID(fileObj.queueItem[0].id);

            jQuery('.pg-download-manager tbody #'+queueItemID+' .pg-upload-percent').html('<img src=\"components/com_pago/css/img-new/tick.png\">');


			jQuery('.pg-download-manager tbody #'+queueItemID).remove();
			jQuery('.pg-download-manager tbody').append(data);
        	jQuery('.pg-download-manager tbody select').chosen({'disable_search': true, 'disable_search_threshold': 6, 'width': 'auto' });

			bind_publish_buttons();
			return;
		},
		'onError': function(errorType,file) {
			queueItemID = makequeueItemID(file.queueItem[0].id);
			jQuery('.pg-download-manager tbody #'+queueItemID).remove();
			
			//jQuery('.uploadifive-queue-item').remove();
            alert('Invalid file type.');
            return;
        }
	});
	
	function makequeueItemID(str)
	{
		str = str.replace('uploadifive-mediaImageUpload-file-','');
		str = 'queue_'+str;
		
	    return str;
	}

	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	jQuery('table.pg-download-manager tbody').sortable({
		handle: 'span.pg-sort-handle',
		opacity: 0.6,
		scroll: true,
		cursor: 'move',
		axis: 'y',
		start: function(event, ui) {
			jQuery('.ui-sortable-placeholder').html('<td colspan=\"10\"><div class=\"pg-placeholder\">" . JText::_( 'PAGO_DROP_IMAGE_ROW_HERE' ) . "</div></td>');
		}
	});
jQuery('.{$this->callback}-default').click(function(){
	if ( jQuery(this).hasClass('is-default-1') ) {
		alert('" .JText::_( 'IMAGE_ALREADY_DEFAULT' ). "');
		return;
	}

	id = jQuery(this).attr('rel');
	el = jQuery(this);
	jQuery.ajax({
		type: 'POST',
		url: 'index.php',
		data: 'option=com_pago&controller=file&task=default&id=' +id+ '&async=1',
		success: function( msg ) {
			if ( 1 != msg ) {
				alert( msg );
			}

			jQuery('.{$this->callback}-default').each(function(){
				if ( jQuery(this).hasClass( 'is-default-1' ) ) {
					jQuery(this).removeClass( 'is-default-1' ).addClass( 'is-default-0' );
				}
			});

			jQuery(el).removeClass( 'is-default-0' ).addClass( 'is-default-1' );
		}
	});
})
bind_publish_buttons();
})
" );
		return $return;
	}
}
