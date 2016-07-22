<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

$session = JSession::getInstance( 'none', array() );
$item = JFactory::getApplication()->input->getInt( 'item' );
$type    = JFactory::getApplication()->input->get( 'type' );
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#imageUpload").uploadify({
			'uploader':			JPATH_ROOT + '/components/com_pago/javascript/uploadify/uploadify.swf',
			'cancelImg':		JPATH_ROOT + '/components/com_pago/javascript/uploadify/cancel.png',
			'script': 			JPATH_COMPONENT + 'helpers/access_point.php',
			'scriptData':		{
								'option':'com_pago',
								'controller':'upload',
								'task':'upload',
								'type':'<?php echo strtolower( $type ); ?>',
								'ac': '<?php echo $_COOKIE[$session->getName()]; ?>',
								'product': '<?php echo $item; ?>'
								},
			'fileDataName':		'upload',
			'fileDesc':			'Upload <?php echo $type; ?>',
			'fileExt':			'*.jpg;*.jpeg;*.gif;*.png',
			'sizeLimit':		'<?php echo PagoHelper::max_upload_size(); ?>',
			'simUploadLimit':	'1',
			'multi':			true,
			'auto':				true,
			onComplete:			function (evt, queueID, fileObj, response, data) {
				jQuery('#submit').css( 'display', '' );

				wS = '<div id="image-item-Upload_'+queueID+'" class="image-item">';
				wE = '</div>';
				jQuery('#image-items').append( wS + response + wE );

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
			}
		});
	});
</script>

<form action="index.php?option=com_pago&amp;task=multisave" id="multisave" method="post">
<!-- File Uploader //-->
<div id="image_uploader">
	<div>
		<h2><?php echo JText::_('PAGO_UPLOAD_TITLE_' . strtoupper( $type )); ?></h2>
		<span id="imageUploadtxt"><?php echo JText::_('PAGO_UPLOAD_TEXT_' . strtoupper( $type )); ?></span> <div id="imageUpload"><?php echo JText::_('PAGO_UPLOAD_NOJS'); ?></div>
		<em><?php echo JText::_('PAGO_UPLOAD_INSTRUCTIONS'); ?></em>
	</div>
</div>

<div id="image-items"></div>

<input type="submit" name="submit" value="Save changes" id="submit" style="display:none;" />
<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="controller" value="files" />
<input type="hidden" name="task" value="multisave" />

</form>