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

if ($type == 'Download')
{
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
}
else
{
	$allowed_extensions = '*.jpg;*.jpeg;*.gif;*.png;';
}
$dispatcher->trigger( 'uploader_allowed_extensions', array( &$allowed_extensions, $type ) );

?>
<script type="text/javascript">
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
			
			'multi':			true,
			'auto':				true,
			onUploadComplete:			function ( fileObj, data) {
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
	});
</script>

<form action="index.php?option=com_pago&amp;task=multisave" id="multisave" method="post">
<!-- File Uploader //-->
<div id = "image-upload-container">
	<div id="image_uploader">
		<div class = "pg-pad-20 clearfix">
			<div class = "upload-text-container">
				<span class = "upload-text general"><?php echo JText::_('PAGO_UPLOAD_TITLE_' . strtoupper( $type )); ?></span>
				<span class = "upload-text"><?php echo JText::_('PAGO_UPLOAD_TEXT_' . strtoupper( $type )); ?></span> 
				<span class = "upload-text"><?php echo JText::_('PAGO_UPLOAD_INSTRUCTIONS'); ?></span>
			</div>
			<div id="imageUpload"><?php echo JText::_('PAGO_UPLOAD_NOJS'); ?></div>
		</div>
	</div>
</div>

<div id="image-items"></div>

<div class = "pg-pad-20 pg-tright">
	<input type="submit" name="submit" value="<?php echo JTEXT::_('PAGO_SAVE_CHANGES'); ?>" id="submit" class = "pg-btn-medium pg-btn-light" style="display:none;" />
</div>
<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="controller" value="file" />
<input type="hidden" name="task" value="multisave" />

</form>