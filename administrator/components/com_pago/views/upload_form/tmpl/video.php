<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

PagoHtml::loadUploadifive();

$session    = JSession::getInstance( 'none', array() );
$dispatcher = KDispatcher::getInstance();
$path       = JURI::root( true );
$item_id    = JFactory::getApplication()->input->getInt( 'item_id' );

$type       = JFactory::getApplication()->input->get( 'type' );

$allowed_extensions = '*.jpg;*.jpeg;*.gif;*.png;';

$dispatcher->trigger( 'uploader_allowed_extensions', array( &$allowed_extensions, $type ) );

$doc = JFactory::getDocument();

//$script  = 'var uploadifivePath="'. JURI::root() ."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_pago".DIRECTORY_SEPARATOR."javascript".DIRECTORY_SEPARATOR."uploadify".DIRECTORY_SEPARATOR."uploadifive.php".'";';
// $script  = 'uploadifivePath="index.php?option=com_pago&controller=attributes&task=uploadfile";';
// $script .= 'var JPATH_ROOT="'. JPATH_ROOT .'";';
// $script .= 'var JURI="'.JURI::root( true ) .'/'.'"';

// $doc->addScriptDeclaration( $script );
		

?>


<script type="text/javascript">
jQuery(window).load(function(){	
	
		jQuery(document).on('click',"input:file",function(e){
			if(jQuery("input[name=video_id]").val()==""){
				e.preventDefault();
				alert('Please enter  video ID!');
				return
			}

			if(jQuery('.video_thumb_upload').hasClass('disabled')){
				e.preventDefault();
				alert("You can upload only one video thumbnail!");
				return false;
			}
		});
	
		
});
	jQuery(document).ready(function() {

	
		jQuery("#imageUpload").uploadifive({
		
			'uploadScript' : '<?php echo JURI::base( true ); ?>/components/com_pago/helpers/access_point.php',
			'formData':		{
								'option':'com_pago',
								'controller':'upload',
								'task':'upload',
								'type':'<?php echo strtolower( $type ); ?>',
								'ac': '<?php echo $_COOKIE[$session->getName()]; ?>',
								'item_id': '<?php echo $item_id; ?>',
								'path': '<?php echo JFactory::getApplication()->input->getString( 'path' ); ?>',
								'validFileType':'<?php echo $allowed_extensions ?>',
								},
			'multi':			false,
			'auto':				true,
			onUploadComplete:			function (fileObj, data) {
				jQuery( "input:file" ).prop('disabled', false);
				jQuery("#multisave").append("<input type='hidden' value='"+jQuery("select[name=videoProvider]").val()+"' name='videoProvider' >");
			
				jQuery("select[name=videoProvider]").prop('disabled', true);
				jQuery("input[name=video_id]").prop('readonly', true);
				
				function makeid()
				{
				    var text = "";
				    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

				    for( var i=0; i < 8; i++ )
				        text += possible.charAt(Math.floor(Math.random() * possible.length));

				    return text;
				}
				queueID=makeid();
				jQuery('#submit').css( 'display', '' );
				jQuery(".uploadifive-queue-item").remove();
				jQuery('#submit').attr('added',true);
				wS = '<div id="image-item-Upload_'+queueID+'" class="image-item pg-pad-20">';
				wE = '</div>';
				jQuery('#image-items').append( wS + data + wE );

				// Clone the thumbnail as a "smallthumb" -- a tiny image to the left of the filename
				jQuery('#image-item-Upload_'+queueID+' .thumbnail').clone().attr('className', 'smallthumb toggle').prependTo('#image-item-Upload_'+queueID);

				// Also bind toggle to the links
				jQuery('#image-item-Upload_'+queueID+' a.toggle').bind('click', function(){
					jQuery(this).siblings('.slidetoggle').slideToggle(150, function(){
						var o = jQuery(this).offset();
						window.scrollTo(0, o.top-36);
					});
					jQuery(this).parent().eq(0).children('.toggle').toggle();
					jQuery(this).siblings('a.toggle').focus();
					return false;
				});
				jQuery('.video_thumb_upload').addClass('disabled');
				// Unbind before bind
				jQuery('.delete').unbind('click');

				// Bind delete button event
				jQuery('.delete').click(function(){
					del_link = jQuery(this);
					jQuery.ajax({
						url: jQuery(this).attr('href') + '&async=1&silent=0',
						success: function(data) {
							// Display response
							id = jQuery(del_link).attr('id').replace('del[', '').replace(']', '');
							jQuery('#del_attachment_'+id).html( '<p>' +data+ '</p>' );
						}
					});
					return false;
				})
			},
			'onError': function(errorType) {
				jQuery(".uploadifive-queue-item").remove();
	            alert('Invalid file type.');
	            return;
	        }
		});

		jQuery(document).on('click','.pg-btn-delete-icon ',function(e){
			jQuery("input[name=video_id]").attr("readonly", false);

		})
		jQuery(document).on('click','.video_upload #submit',function(e){
			if(jQuery("input[name=video_id]").val()==""){
				alert("Please enter video ID!");
				return false;	
			}
			if(jQuery("input[name=video_title]").val().length>20){
				alert("Video title must be no more than 20 symbols!");
				return false;	
			}
			
			if(jQuery(this).attr('added')) return;
		 	e.preventDefault();
		 	jQuery.ajax({
		 		url:'<?php echo JURI::base( true ); ?>/components/com_pago/helpers/access_point.php',
				data:		{
								'option':'com_pago',
								'controller':'upload',
								'task':'upload',
								'type':'<?php echo strtolower( $type ); ?>',
								'ac': '<?php echo $_COOKIE[$session->getName()]; ?>',
								'item_id': '<?php echo $item_id; ?>',
								'path': '<?php echo JFactory::getApplication()->input->getString( 'path' ); ?>'
								},
		 	}).done(function(result){
		 		jQuery('#image-items').append( result );
		 		jQuery('#submit').attr('added',true);
		 		jQuery('#submit').click();
		 	});
			
			
	   });
	});
	
</script>
<form action="index.php?option=com_pago&amp;task=saveVideo" id="multisave" class="video_upload" method="post">
<!-- File Uploader //-->
<div id = "video-upload-container">
	<div id="image_uploader">
		<div class = "pg-pad-20 clearfix">
			<span class = "upload-text general pg-mb-20"><?php echo JText::_('PAGO_UPLOAD_TITLE_' . strtoupper( $type )); ?></span>
			<div class = "pg-row">
				<div class = "pg-col-7">
					<div class = "pg-row">
						<div class = "pg-col-12">
							<div class = "pg-mb-10">
								<label><?php echo JTEXT::_('PAGO_VIDEO_SELECT_PROVIDER'); ?></label>
							</div>
							<?php echo $this->providersList; ?>
						</div>
						<div class = "pg-col-12">
							<div class = "pg-mb-10">
								<label><?php echo JTEXT::_('PAGO_VIDEO_VIDEO_ID'); ?></label>
							</div>
							<input type="text" name="video_id"/>
						</div>
						<div class = "pg-col-12">
							<div class = "pg-mb-10">
								<label><?php echo JTEXT::_('PAGO_VIDEO_TITLE'); ?></label>
							</div>
							<input type="text" name="video_title"/>
						</div>
					</div>
				</div>

				<div class = "pg-col-5">
					<div class='video_thumb_upload'>
						<span id="imageUploadtxt"><?php echo JText::_('PAGO_VIDEO_THUMB'); ?></span> 
						<div id="imageUpload"><?php echo JText::_('PAGO_UPLOAD_NOJS'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="image-items"></div>

<div class = "pg-pad-20 pg-tright">
	<input type="submit" name="submit" value="<?php echo JTEXT::_('PAGO_SAVE_CHANGES'); ?>" id="submit" class = "pg-btn-medium pg-btn-light" />
</div>
<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="controller" value="file" />
<input type="hidden" name="task" value="saveVideo" />
<input type="hidden" name="type" value="<?php echo $type ?>" />
<input type="hidden" name="item_id" value="<?php echo $item_id ?>" />
</form>