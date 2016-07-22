<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldMedialist extends JFormField
{
	/**
	* Element name
	*
	* @access	   protected
	* @var		  string
	*/
	protected $type = 'Medialist';
	var $_data = array();

	function __construct()
	{
		Pago::load_helpers( 'imagehandler' );
	}

	function get_item_id()
	{
		static $id = 0;

		if ( !$id ) {
			$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
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

		return $this->html( $name, $value );
	}

	function getToolbar()
	{
		$item_id = $this->get_item_id();
		$bar = new JToolBar( $this->name . '-Toolbar' );

		switch ( $this->callback ) {
			case 'category':
				$title = JText::_( 'PAGO_IMAGE' );
				break;

			default:
				$title = ucfirst( $this->callback );
				break;
		}

		if($item_id){
			// echo PagoHelper::addCustomButton( JText::_( 'PAGO_ADD_IMAGE' ),
			// 	"javascript:pull_upload_form( {$item_id}, '"  .ucfirst( $this->callback ). "' , false );",
			// 	'new add-image', 'javascript:void(0);', 'toolbar', '', $bar );
			$cat_class = $this->callback == 'category' ? 'add-image-cat' : '';
			echo '<a href="javascript:void(0);" id="mediaImageUploadBTN" class="new add-image '.$cat_class.' ">Image</a>';
			echo '<div id="mediaImageUpload" style="display: none;"></div>';

			if($this->callback == 'images'){
				echo PagoHelper::addCustomButton( JText::_( 'PAGO_ADD_VIDEO' ),
				"javascript:pull_upload_form( {$item_id}, '"  .ucfirst( 'video' ). "' , false );",
				'new add-video', 'javascript:void(0);', 'toolbar', '', $bar );
			}
		}else{
			// echo PagoHelper::addCustomButton( ucfirst( $title ),
			// 	"javascript:pull_upload_form( false, '"  .ucfirst( $this->callback ). "', 'hashKeydasd' );",
			// 	'new', 'javascript:void(0);', 'toolbar', '', $bar );

			// echo PagoHelper::addCustomButton( JText::_( 'PAGO_ADD' ) . ' ' . ucfirst( $title ),
			// 	"alert('" .JText::_( 'PAGO_ELEMENTS_MEDIA_ERROR_ID' ). "');",
			// 	'new', 'javascript:void(0);', 'toolbar', '', $bar );	
		}

		//echo PagoHelper::addCustomButton( JText::_( 'PAGO_DELETE' ),
		//	"javascript:delete_files( '."  .$this->callback. "-checkboxes' );",
		//		'delete', 'javascript:void(0);', 'toolbar', '', $bar );

	}

	function html( $_name, $value )
	{
		if ( !$value ) {
			$value = '[]';
		}

		$doc = JFactory::getDocument();
		$item_id = $this->get_item_id();

		// Add Styles/Scripts
		PagoHtml::thickbox();
		PagoHtml::uniform();
		PagoHtml::tooltip();

		$data = PagoImageHandlerHelper::get_item_files( $item_id, false, array( $this->callback,'video' ) );

		ob_start();

		$file = JPATH_COMPONENT . '/helpers/video_sources.php';
		jimport('joomla.filesystem.file');
		if (JFile::exists($file))
		{
			require $file;
		}
?>
<div class="pg-tab-content pg-media pg-pad-20 pg-border">
	<div class = "media-add-container pg-pad-20 pg-mb-20 clearfix">
		<div class = "media-add-text">
			<a class = "media-add-ico" href = "javascript:void(0)"></a>
			<?php if($this->callback == 'category') { ?>
			<span><?php echo JTEXT::_('PAGO_ADD_NEW_IMAGE'); ?></span>
			<?php } else { ?>
			<span><?php echo JTEXT::_('PAGO_ADD_NEW_IMAGE_OR_VIDEO'); ?></span>
			<?php } ?>
		</div>
		<div class = "media-add-image-video">
			<?php echo $this->getToolbar(); ?>
		</div>
	</div>

	<div class = "pg-product-images">
		<table class = "pg-images-manager">
			<thead>
				<tr class="pg-sub-heading pg-multiple-headings">
					<!--<td class="pg-checkbox">
						<div class="pg-checkbox pg-first-column">
							<input type="checkbox" name="toggle" id="checkall" value="" onclick="pago_check_all(this, '.<?php echo $this->callback; ?>-checkboxes');" />
							<label for="checkall"></label>
						</div>
					</td>-->

					<td class="pg-sort">
						<a href = "javascript:void(0)"></a>
					</td>

					<td class="pg-preview">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_PREVIEW') ?>
					</td>

					<td class="pg-name">
						<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_NAME') ?>
					</td>

					<!--<td class="pg-caption">
						<?php echo JText::_('PAGO_CAPTION') ?>
					</td>-->

					<td class="pg-published">
						<?php echo JText::_('PAGO_PUBLISHED') ?>
					</td>

					<td class="pg-default">
						<?php echo JText::_('PAGO_PRIMARY'); ?>
					</td>

					<td class="pg-remove">
						<?php echo JText::_( 'PAGO_REMOVE' ); ?>
					</td>

					<!-- <td class="pg-caption">
						<div class="pg-caption">
							<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_CAPTION') ?>
						</div>
					</td> -->

					
					<!--<td class="pg-access">
						<div class="pg-access">
							<?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_ACCESS') ?>
						</div>
					</td>-->

					<!-- <td class="pg-description">
						<div class="pg-description">
							<?php //echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_DESC') ?>
						</div>
					</td> -->

					<!--<td class="pg-id">
						<div class="pg-id pg-last-column">
							<?php echo JText::_( 'PAGO_ELEMENTS_MEDIA_ID' ); ?>
						</div>
					</td>-->
				</tr>
			</thead>
			<tbody>
				<?php
				$js = '';
				if ( !empty( $data ) ) {
					for ( $i = 0; $i < count( $data ); $i++ ) {
						$totalRows = count ( $data ) - 1;
						if ( $data[$i]->type == 'store_default' ) { continue; }
						$row = clone $data[$i];

						$img = PagoImageHandlerHelper::get_image_from_object( $data[$i], 'thumbnail', false,
							 'id="img-tooltip-' .$row->id. '" class="images-grid-thumbnail"', true, false );

						$video = '';
						$img_tooltip = PagoImageHandlerHelper::get_image_from_object( $data[$i], 'medium', false );
						if($row->type == 'video'){
							$img_tooltip = $tagReplace[$row->provider];
							$img_tooltip = str_replace("{SOURCE}", $row->video_key, $img_tooltip);
							$video = 'video';
							/*$js .= 'jQuery("#img-tooltip-' .$row->id. '").tooltip( {
							content: function(){ 
							return \'' .str_replace( "'", "\'", $video_tooltip ). '\' 
							},
							position: { my: "left top", at: "right top", offset: "20px -10px", collision: "none none" }
							});';*/	
						}else{
							/*$js .= 'jQuery("#img-tooltip-' .$row->id. '").tooltip( {
							content: function(){ return \'' .str_replace( "'", "\'", $img_tooltip ). '\' },
							position: { my: "left top", at: "right top", offset: "20px -10px", collision: "none none" }
							});';	*/
						}
						?>
						<tr class="pg-table-content<?php if ( $i == 0 ) { echo ' pg-first-row'; } if ( $i == $totalRows ) { echo ' pg-last-row'; } ?>" rel="cid-<?php echo $row->id; ?>">
							<!--<td class="pg-checkbox">
								<div class="pg-first-column">
									<input type="checkbox" class="<?php echo $this->callback; ?>-checkboxes" value="<?php echo $row->id; ?>" name="cidimgs[]" id="cb<?php echo $i; ?>" onclick="pago_highlight_row(this);" />
									<label for="cb<?php echo $i; ?>"></label>
								</div>
							</td>-->

							<td class="pg-sort">
								<div class="pg-sort">
									<span class="pg-sort-handle"></span>
									<input type="hidden" name="params[images_ordering][]" value="<?php echo $row->id; ?>" />
								</div>
							</td>

							<td class="pg-preview">
								<div class = "pg-preview-small-image <?php echo $video; ?>" style = "background:url('<?php echo $img; ?>')">
									
									<?php if($row->type == 'video' && false){ ?>
										<img class="pg-play" src="components/com_pago/css/img/pg-play.png">
									<?php } ?>
								</div>
								<div class = "pg-preview-large-image">
									<?php echo $img_tooltip; ?>
								</div>
							</td>

							<td class="pg-name">
								<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][title]" value="<?php echo $row->title; ?>" />
							</td>

							<!--<td class="pg-caption">
								<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][title]" value="<?php echo $row->title; ?>" />
							</td>-->

							<td class="pg-published">
								<?php echo PagoHelper::published( $row, $i, 'publish.png',  'unpublish.png', '', ' class="publish-buttons" type="file" rel="' .$row->id. '"' ); ?>
							</td>

							<td class="pg-default">
								<a href="javascript:void(0);" class="pg-icon <?php echo $this->callback; ?>-default is-default-<?php echo $row->default; ?> id-for-delete" rel="<?php echo $row->id; ?>"></a>
							</td>

							<td class = "pg-remove">
								<a href = "javascript:void(0)"></a>
							</td>

							<!-- <td>
								<div class="pg-caption">
									<input type="text" name="<?php echo $_name ?>[<?php echo $row->id; ?>][caption]" value="<?php echo $row->caption; ?>">
								</div>
							</td> -->
													
							<!--<td class="pg-access">
								<div class="pg-access">
								<?php echo JHTML::_( 'access.assetgrouplist', $_name. '[' .$row->id. '][access]', $row->access, array( 'size' => 1, 'style' => 'max-width:130px' ) );	?>
								</div>
							</td>-->

							<!-- <td>
								<div class="pg-description">
									<a href="javascript:void(0);" onClick="img_edit_desc(<?php //echo $row->id; ?>);"><?php //echo JText::_('PAGO_EDIT'); ?></a>
								</div>
							</td> -->

							<!--<td>
								<div class="pg-id pg-last-column">
									<?php echo $row->id; ?>
								</div>
							</td>-->
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
	$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
	$allowed_extensions = '*.jpg;*.jpeg;*.gif;*.png;';
	$multi = $this->callback == 'category' ? 'false' : 'true';
	$path = JFactory::getApplication()->input->getString( 'path' );
	$uploadScript = JURI::base( true ).'/components/com_pago/helpers/access_point.php';
	$doc->addScriptDeclaration( "jQuery(document).ready(function(){
	{$js}
	// Return a helper with preserved width of cells
	
	var mia_uploadscript = '{$uploadScript}';
	variation_image = false;
	
	
	jQuery(document).on('click','#mediaImageUploadBTNV', function(e){	
		
		mia_upload(true, jQuery(this).attr('variation_id'));
		
		var trigger_ele = '#uploadifive-mediaImageUpload input:last-child';
	
		jQuery(trigger_ele).click();
	});
	
	jQuery(document).on('click','#mediaImageUploadBTN', function(e){	
		
		mia_upload(false, false);
		
		var trigger_ele = '#uploadifive-mediaImageUpload input:last-child';
		
		jQuery(trigger_ele).click();
	});
	
	function mia_upload(variation_image, variation_id)
	{
		var main_ele = '#mediaImageUpload';
		var table_ele = '.pg-images-manager tbody';
		var type = '$this->callback';
		
		if(variation_image){
			table_ele = '.pg-images-manager-v tbody';
			//type = 'variation';
		}
		
		try {
   			jQuery(main_ele).uploadifive('destroy');
		} catch (e) {}
		
		jQuery(main_ele).uploadifive({
			'uploadScript' : mia_uploadscript,
			'formData':		{
								'option':'com_pago',
								'controller':'upload',
								'task': 'upload',
								'type':type,
								'ac': '$COOKIE',
								'item_id': '$item_id;',
								'cid[]': '$cid[0]',
								'path': '$path',
								'validFileType':'$allowed_extensions',
								'variation_image': variation_image,
								'variation_id': variation_id
								},
			
			'multi':			".$multi.",
			'auto':				true,
	        'onAddQueueItem' : function(file) {
	       		queueItemID = makequeueItemID(file.queueItem[0].id);
	       		
	       		if(variation_image){
				jQuery(table_ele).append(\"<tr class='new_row_add' id=\"+queueItemID+\"><td class='pg-sort'><div class='pg-sort'><span class='pg-sort-handle'></div></div></td><td class='pg-preview pg-upload-percent'></td><td class='pg-name'><span class='pg_upload_proccess'></span></td><td rel='\"+file.queueItem[0].id+\"' class='pg-published id-for-delete '><img src='components/com_pago/css/img-new/publish.png'></td><td class='pg-remove disabled'><a href='javascript:void(0)' class='disabled'></a></td></tr>\");
	        	}else{ 
	        	jQuery(table_ele).append(\"<tr class='new_row_add' id=\"+queueItemID+\"><td class='pg-sort'><div class='pg-sort'><span class='pg-sort-handle'></div></div></td><td class='pg-preview pg-upload-percent'></td><td class='pg-name'><span class='pg_upload_proccess'></span></td><td class='pg-published'><img src='components/com_pago/css/img-new/publish.png'></td><td class='pg-default'><a href='javascript:void(0);' class='pg-icon '$this->callback'-default id-for-delete is-default-1'></a></td><td class='pg-remove disabled'><a href='javascript:void(0)' class='disabled'></a></td></tr>\");
	        	}
	       			
	       			
	       	},
	       	'onProgress'   : function(file, e) {	       	
	       		queueItemID = makequeueItemID(file.queueItem[0].id);
	            if (e.lengthComputable) {
	                var percent = Math.round((e.loaded / e.total) * 100);
	            }
	            jQuery(table_ele + ' #'+queueItemID+' .pg-upload-percent').html(percent+'%');
	            jQuery(table_ele + ' #'+queueItemID+' .pg_upload_proccess').css({ 'width': percent + '%' });
	        	
	        },
			onUploadComplete:			function ( fileObj, data) {
				if('".$this->callback."' == 'category')
				{
					jQuery(table_ele).html('');
				}
				
				queueItemID = makequeueItemID(fileObj.queueItem[0].id);
	
	            jQuery(table_ele + ' #'+queueItemID+' .pg-upload-percent').html('<img src=\"components/com_pago/css/img-new/tick.png\">');
	
				// setTimeout(function(){
					
					
				// }, 2000);
				
				jQuery(table_ele + ' #'+queueItemID).remove();
				jQuery(table_ele).append(data);
	
				bind_publish_buttons();
				return;
			},
			'onError': function(errorType,file) {
				queueItemID = makequeueItemID(file.queueItem[0].id);
				jQuery('.pg-images-manager tbody #'+queueItemID).remove();
				
				//jQuery('.uploadifive-queue-item').remove();
	            alert('Invalid file type.');
	            return;
	        }
		});
	}
	
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

	jQuery('table.pg-images-manager tbody').sortable({
		handle: 'span.pg-sort-handle',
		opacity: 0.6,
		scroll: true,
		cursor: 'move',
		axis: 'y',
		start: function(event, ui) {
			jQuery('.ui-sortable-placeholder').html('<td colspan=\"10\"><div class=\"pg-placeholder\">Stex dnel jtext</div></td>');//JText::_( 'PAGO_DROP_IMAGE_ROW_HERE' ) 
		}
	});
	jQuery(document).on('click','.{$this->callback}-default', function(){
		
		if ( jQuery(this).hasClass('is-default-1') ) {

			id = jQuery(this).attr('rel');
			el = jQuery(this);
			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: 'option=com_pago&controller=file&task=removedefault&id=' +id+ '&async=1',
				success: function( msg ) {
					if ( 1 != msg ) {
						alert( msg );
					}

					
					jQuery(el).removeClass( 'is-default-1' ).addClass( 'is-default-0' );
				}
			});
	}else{
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
	}
	})
bind_publish_buttons();
})
" );
	return $return;
	}
}
