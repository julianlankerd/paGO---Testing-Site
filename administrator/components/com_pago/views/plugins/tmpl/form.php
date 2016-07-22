<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery( 'jqueryui' );
PagoHtml::apply_layout_fixes();
PagoHtml::uniform();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

PagoHtml::pago_top( $menu_items, 'tabs',$this->top_menu );

$doc = JFactory::getDocument();

$doc->addScript(JURI::root(true) . '/plugins/pago_gateway/pago/script.js');

$doc->addScriptDeclaration( "
	jQuery(document).ready(function(){
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			async: true,
			data: ({
				option: 'com_pago',
				controller: 'plugins',
				task : 'checkinExtension',
				extension_id: '+<?php echo $this->extensionId?>+',
			}),
			success: function( response ) {
				if(response){
					result = JSON.parse(response);
					if(result.status == 'success'){
						jQuery.ajax({
							type: 'GET',
							url: 'index.php',
							async: false,
							data: ({
								option: 'com_plugins',
								task: 'plugin.edit',
								extension_id: '+<?php echo $this->extensionId?>+',
								tmpl: 'component',
							}),
							success: function( response ) {
								if(response){
									fr = jQuery(jQuery.parseHTML(response)).filter('form');
						
									jQuery('.pg_plugin_container').append(fr);
									jQuery('.pg_plugin_container select').chosen({'disable_search': true,  'disable_search_threshold': 6});
									jQuery('.pg_plugin_container .pane-sliders .panel:nth-child(2)').addClass('open');
									
									jQuery('.hasTooltip').each( function() {
										var $this = jQuery(this);
										title = $this.attr('title');
										alt = $this.attr('alt');
										
										$this.attr('title', $this.attr('title').replace('<strong>', ''));
										$this.attr('title', $this.attr('title').replace('</strong>', ''));
										$this.attr('title', $this.attr('title').replace('<br />', ' :: '));
										//$this.attr('title',jQuery(title).text()).attr('alt',jQuery(alt).text());
									});
									
									//jQuery('#jform_params_version-lbl').parents('.control-group').remove();
									//jQuery('[class*=\" showon_1\"]').remove();
									
									var elements = {},
			linkedoptions = function(element, target, checkType) {
				var v = element.val(), id = element.attr('id');
				if(checkType && !element.is(':checked'))
					return;
				jQuery('[rel=\"showon_'+target+'\"]').each(function(){
					var i = jQuery(this);
					if (i.hasClass('showon_' + v))
						i.slideDown();
					else
						i.slideUp();
				});
			};
		jQuery('[rel^=\"showon_\"]').each(function(){
			var el = jQuery(this), target = el.attr('rel').replace('showon_', ''), targetEl = jQuery('[name=\"' + target+'\"]');
			if (!elements[target]) {
				var targetType = targetEl.attr('type'), checkType = (targetType == 'checkbox' || targetType == 'radio');
				targetEl.bind('change', function(){
					linkedoptions( jQuery(this), target, checkType);
				}).bind('click', function(){
					linkedoptions( jQuery(this), target, checkType );
				}).each(function(){
					linkedoptions( jQuery(this), target, checkType );
				});
				elements[target] = true;
			}
		});
		
		
								}
							}
						});
					}
				}
			}
		});
	})	
");
?>
<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	jQuery.noConflict();
	if(task == 'cancel'){
		jQuery('form#style-form input[name="task"]').val('plugin.cancel');
		jQuery.ajax({
           type: "POST",
           url: jQuery('form#style-form').attr('action'),
           async: false,
           data: jQuery('form#style-form').serialize(),
           success: function(data)
           {
           		window.location.replace("index.php?option=com_pago&view=plugins");
           }
         });
		return;
	}
	if(task == 'apply'){
		jQuery('#system-message-container').html('');
		jQuery('form#style-form input[name="task"]').val('plugin.apply');
		jQuery.ajax({
           type: "POST",
           url: jQuery('form#style-form').attr('action'),
           async: false,
           data: jQuery('form#style-form').serialize(),
           success: function(data)
           {
           		message = jQuery(jQuery.parseHTML(data)).find('#system-message-container').html();
     //       		if(message){
     //       			jQuery('#system-message-container').html(message);
     //       			setTimeout(function() {
					//       jQuery('#system-message-container').html('');
					// }, 2000);
     //       		}
		      	if(message){
		 	 		window.location.replace("index.php?option=com_pago&view=plugins&status=1&task=edit&cid[]="+<?php echo $this->extensionId?>);
	           		}else{
	           			window.location.replace("index.php?option=com_pago&view=plugins&task=edit&cid[]="+<?php echo $this->extensionId?>);
		       }
           }
         });
		return;
	}
	if(task == 'save'){
		jQuery('form#style-form input[name="task"]').val('plugin.save');
		jQuery.ajax({
           type: "POST",
           url: jQuery('form#style-form').attr('action'),
           async: false,
           data: jQuery('form#style-form').serialize(),
           success: function(data)
           {
	 	 		window.location.replace("index.php?option=com_pago&view=plugins&status=1");
           }
         });
		return;
	}
}
</script>

<div class="pg_plugin_container">
		
</div>	
<?php
PagoHtml::pago_bottom();
