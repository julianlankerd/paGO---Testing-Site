<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
$dispatcher = KDispatcher::getInstance();
//$params = $this->params;
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs', $this->top_menu );

$doc = JFactory::getDocument();
$doc->addScriptDeclaration( "
	// item currency
	function delete_currency_row() {
		if (!confirm('Are you sure?'))
			return;
	
		jQuery('#pg-configuration-currencies tbody td.pg-checkbox input:checked').each(function(el) {

			currencyId = jQuery(this).closest('tr').attr('id');
			if (jQuery('#' + currencyId + ' td.pg-default div.pg-default .currency-default').hasClass('is-default-0')){
				currencyId = currencyId.replace('currency_','');
				jQuery.ajax({
		        	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=currencies&task=delete&id=' +currencyId,
		            success : function(data) {
		            	data = jQuery.trim(parseInt(data));
		            	jQuery('#currency_'+data).remove();
		            }
		        });
			}
		});
	}
	function show_currency_add(){
		clear_currency_add();
		jQuery('.add_currency_control').css('display','none');
	

		var currentCurrencies = [];
		jQuery('.pg-configuration-currencies tbody tr .pg-currency-code input').each(function(){
			currentCurrencies.push(jQuery(this).val());
		});

		var currentCurrenciesJson = JSON.stringify(currentCurrencies);

		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=currencies&task=getCurrencies&dataType=json&currentCurrenciesJson='+currentCurrenciesJson,
			success: function( res ) {
				
				var obj = jQuery.parseJSON(res);
				jQuery('#new-currency-code').children().remove();
				jQuery.each(obj, function (k, val) {
					jQuery('#new-currency-code').append(jQuery('<option>', { 
				        value: k,
				        text : k 
				    }));
				});
	jQuery('select').trigger('chosen:updated');
			}
		});
		jQuery('tr.new-currency-con').css('display','table-row');
	}
	function clear_currency_add(){
		jQuery('.add_currency_control').css('display','block');
		jQuery('tr.new-currency-con').css('display','none');
		jQuery('#new-currency-name').val('');
		jQuery('#new-currency-code').val('');
		jQuery('#new-currency-symbol').val('');
	}
	function currency_add() {
		currencyName   = jQuery('#new-currency-name').val();
		currencyCode   = jQuery('#new-currency-code').val();
		currencySymbol = jQuery('#new-currency-symbol').val();

		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=currencies&task=add&dataType=json&currencyName=' +currencyName+ '&currencyCode=' +currencyCode+ '&currencySymbol=' +currencySymbol,
			success: function( res ) {
				var obj = jQuery.parseJSON(res);
				if(obj.error == 1){
					alert(obj.message);
				}
				else{
					newId = obj.id;
					html = '';
					html += '<tr id=\'currency_'+ newId +'\' class=\'no-margin\'>';
						html += '<td class=\'pg-checkbox\'>';
							html += '<input type=\'checkbox\' id=\'currency_checkbox['+newId+']\' />';
							html += '<label for=\'currency_checkbox['+newId+']\'></label>';
						html += '</td>';
						html += '<td class=\'pg-currency-name\'><input type=\'text\' name=\'currencies['+ newId +'][name]\' value=\''+ currencyName +'\' /></td>';
						html += '<td class=\'pg-currency-code\'><input type=\'text\' name=\'currencies['+ newId +'][code]\' value=\''+ currencyCode +'\' maxlength=\'3\' /></td>';
						html += '<td class=\'pg-currency-symbol\'><input type=\'text\' name=\'currencies['+ newId +'][symbol]\' value=\''+ currencySymbol +'\' maxlength=\'10\' /></td>';
						html += '<td class=\'pg-default\'>';
							html += '<div class=\'pg-default\'>';
								html += '<a href=\'javascript:void(0);\' class=\'pg-icon currency-default is-default-0\' rel=\''+ newId +'\'></a>';
							html += '</div>';
						html += '</td>';
						html += '<td class=\'pg-published\'>';
							html += '<div class=\'pg-published\'>';
								html += '<a href=\'javascript:void(0);\' class=\'publish-buttons\' type=\'currency\' rel=\''+ newId +'\' title=\'Publish item\'>';
									html += '<img src=\'components/com_pago/css/img-new/publish_x.png\' border=\'0\' alt=\'Unpublished\' class=\'item-publish\'>';
								html += '</a>';
							html += '</div>';
						html += '</td>';
					html += '</tr>';

					jQuery('#pg-configuration-currencies tbody').append(html);
					//jQuery('#pg-configuration-currencies tbody tr:last').find('td.pg-checkbox input').uniform();
					
					close_currency_add();
					
				}
			}
		});
	}
	
	function close_currency_add() 
	{
		jQuery('#new-currency-modal').modal('hide');
	};

	jQuery('.currency-default').live('click',function(){

		if ( jQuery(this).hasClass('is-default-1') ) {
			alert('" .JText::_( 'CURRENCY_ALREADY_DEFAULT' ). "');
			return false;
		}

		id = jQuery(this).attr('rel');
		el = jQuery(this);

		if ( jQuery('#currency_' + id + ' td.pg-published div.pg-published .publish-buttons img').hasClass('item-publish') ) {
			alert('" .JText::_( 'CANT_DEFAULT_UNPUBLISHED_CURRENCY' ). "');
			return false;
		}

		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=currencies&task=default&id=' +id,
			success: function( msg ) {
				if ( 1 != msg ) {
					alert( msg );
					return false;
				}

				jQuery('.currency-default').each(function(){
					if ( jQuery(this).hasClass( 'is-default-1' ) ) {
						jQuery(this).removeClass( 'is-default-1' ).addClass( 'is-default-0' );
					}
				});

				jQuery(el).removeClass( 'is-default-0' ).addClass( 'is-default-1' );

				//current default currency
				jQuery('#pg-configuration-currencies').find( '.pg-disabled-checkbox' ).remove();
				jQuery('<span class=\'pg-disabled-checkbox\'></span>').insertBefore('#currency_' + id + ' .pg-checkbox .checker');

			}
		});
	})

	// item types
	function delete_item_types_row() {
		if (!confirm('Are you sure?'))
			return;
			
		jQuery('#pg-configuration-item-types td.pg-checkbox input:checked').each(function(el) {

			itemTypeId = jQuery(this).closest('tr').attr('id');
			//if (jQuery('#' + itemTypeId + ' td.pg-default div.pg-default .item-types-default').hasClass('is-default-0')){
				itemTypeId = itemTypeId.replace('item_types_','');
				jQuery.ajax({
		        	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=item_types&task=delete&id=' +itemTypeId,
		            success : function(data) {
						data = parseInt(jQuery.trim(data));
		            	jQuery('#item_types_'+data).remove();
		            }
		        });
			//}
		});
	}

	function show_item_types_add(){
		clear_currency_add();
		jQuery('tr.new-item-types-con').css('display','table-row');
	}
	function clear_item_types_add(){
		jQuery('tr.new-item-types-con').css('display','none');
		jQuery('#new-item-types-name').val('');
	}
	function item_types_add() {
		itemTypeName   = jQuery('#new-item-types-name').val();
		itemTypePhysical = jQuery('#new-item-types-physical').val();

		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=item_types&task=add&dataType=json&itemTypeName='+itemTypeName+'&itemTypePhysical='+itemTypePhysical,
			success: function( res ) {
				var obj = jQuery.parseJSON(res);
				if(obj.error == 1){
					alert(obj.message);
				}
				else{
					newId = obj.id;
					html = '';
					html += '<tr id=\'item_types_'+ newId +'\' class=\'no-margin\'>';
						html += '<td class=\'pg-checkbox\'>';
							html += '<input type=\'checkbox\' id=\'item-type_checkbox['+newId+']\' />';
							html += '<label for=\'item-type_checkbox['+newId+']\'></label>';
						html += '</td>';
						html += '<td class=\'pg-item-types-name\'><input type=\'text\' name=\'itemTypes['+ newId +'][name]\' value=\''+ itemTypeName +'\' /></td>';
						html += '<td class=\'pg-default\'>';
							html += '<div class=\'pg-default\'>';
								html += '<a href=\'javascript:void(0);\' class=\'pg-icon item-types-default is-default-0\' rel=\''+ newId +'\'></a>';
							html += '</div>';
						html += '</td>';
						html += '<td class=\'pg-published\'>';
							html += '<div class=\'pg-published\'>';
								html += '<a href=\'javascript:void(0);\' class=\'publish-buttons\' type=\'item_types\' rel=\''+ newId +'\' title=\'Publish item\'>';
									html +=	'<img src=\'components/com_pago/css/img-new/publish_x.png\' border=\'0\' alt=\'Unpublished\' class=\'item-publish\'>';
								html += '</a>';
							html += '</div>';
						html += '</td>';
					html += '</tr>';

					jQuery('#pg-configuration-item-types tbody').append(html);
					//jQuery('#pg-configuration-item-types tbody tr:last').find('td.pg-checkbox input').uniform();
					
					close_item_types_add();
				}
			}
		});
	}
	
	function close_item_types_add() 
	{
		jQuery('#new-item-type-modal').modal('hide');
	};

	// jQuery('.item-types-default').live('click',function(){

	// 	if ( jQuery(this).hasClass('is-default-1') ) {
	// 		alert('" .JText::_( 'ITEM_TYPES_ALREADY_DEFAULT' ). "');
	// 		return false;
	// 	}

	// 	id = jQuery(this).attr('rel');
	// 	el = jQuery(this);

	// 	if ( jQuery('#item_types_' + id + ' td.pg-published div.pg-published .publish-buttons img').hasClass('item-publish') ) {
	// 		alert('" .JText::_( 'CANT_DEFAULT_UNPUBLISHED_ITEM_TYPES' ). "');
	// 		return false;
	// 	}

	// 	jQuery.ajax({
	// 		type: 'POST',
	// 		url: 'index.php',
	// 		data: 'option=com_pago&controller=item_types&task=default&id=' +id,
	// 		success: function( msg ) {
	// 			if ( 1 != msg ) {
	// 				alert( msg );
	// 				return false;
	// 			}

	// 			jQuery('.item-types-default').each(function(){
	// 				if ( jQuery(this).hasClass( 'is-default-1' ) ) {
	// 					jQuery(this).removeClass( 'is-default-1' ).addClass( 'is-default-0' );
	// 				}
	// 			});

	// 			jQuery(el).removeClass( 'is-default-0' ).addClass( 'is-default-1' );

	// 			//current default item types
	// 			jQuery('#pg-configuration-item-types').find( '.pg-disabled-checkbox' ).remove();
	// 			jQuery('<span class=\'pg-disabled-checkbox\'></span>').insertBefore('#item_types_' + id + ' .pg-checkbox .checker');

	// 		}
	// 	});
	// })

	// item size unit
	function delete_size_unit_row() {
		if (!confirm('Are you sure?'))
			return;
	
		jQuery('#pg-configuration-size_unit td.pg-checkbox input:checked').each(function(el) {

			sizeUnitId = jQuery(this).closest('tr').attr('id');
			if (jQuery('#' + sizeUnitId + ' td.pg-default div.pg-default .size_unit-default').hasClass('is-default-0')){
				sizeUnitId = sizeUnitId.replace('size_unit_','');
				jQuery.ajax({
		        	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=units&task=delete&id=' +sizeUnitId,
		            success : function(data) {
						data = parseInt(jQuery.trim(data));
		            	jQuery('#size_unit_'+data).remove();
		            }
		        });
			}
		});
	}
	function show_size_unit_add()
	{
		clear_size_unit_add();
		jQuery('tr.new-size_unit-con').css('display','table-row');
	}
	function clear_size_unit_add(){
		jQuery('tr.new-size_unit-con').css('display','none');
		jQuery('#new-size_unit-name').val('');
		jQuery('#new-size_unit-code').val('');
	}
	function size_unit_add() {
		unitName   = jQuery('#new-size_unit-name').val();
		unitCode   = jQuery('#new-size_unit-code').val();
		unitType   = 'size';
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=units&task=add&dataType=json&unitName=' +unitName+ '&unitCode=' +unitCode+ '&unitType=' +unitType,
			success: function( res ) {
				var obj = jQuery.parseJSON(res);
				if(obj.error == 1){
					alert(obj.message);
				}
				else{
					newId = obj.id;
					html = '';
					html += '<tr id=\'size_unit_'+ newId +'\' class=\'no-margin\'>';
						html += '<td class=\'pg-checkbox\'>';
							html += '<input type=\'checkbox\' id=\'size_checkbox['+newId+']\' />';
							html += '<label for=\'size_checkbox['+newId+']\'></label>';
						html += '</td>';
						html += '<td class=\'pg-size_unit-name\'><input type=\'text\' name=\'size_unit['+ newId +'][name]\' value=\''+ unitName +'\' /></td>';
						html += '<td class=\'pg-size_unit-code\'><input type=\'text\' name=\'size_unit['+ newId +'][code]\' value=\''+ unitCode +'\' maxlength=\'3\' /></td>';
						html += '<td class=\'pg-default\'>';
							html += '<div class=\'pg-default\'>';
								html += '<a href=\'javascript:void(0);\' class=\'pg-icon size_unit-default is-default-0\' rel=\''+ newId +'\'></a>';
							html += '</div>';
						html += '</td>';
						html += '<td class=\'pg-published\'>';
							html += '<div class=\'pg-published\'>';
								html += '<a href=\'javascript:void(0);\' class=\'publish-buttons\' type=\'size_unit\' rel=\''+ newId +'\' title=\'Publish item\'>';
									html +=	'<img src=\'components/com_pago/css/img-new/publish_x.png\' border=\'0\' alt=\'Unpublished\' class=\'item-publish\'>';
								html += '</a>';
							html += '</div>';
						html += '</td>';
					html += '</tr>';

					jQuery('#pg-configuration-size_unit tbody').append(html);
					//jQuery('#pg-configuration-size_unit tbody tr:last').find('td.pg-checkbox input').uniform();
					
					close_size_unit_add();
					
				}
			}
		});
	}
	
	function close_size_unit_add() 
	{
		jQuery('#new-size-modal').modal('hide');
	};
	
	jQuery('.size_unit-default').live('click',function(){

		if ( jQuery(this).hasClass('is-default-1') ) {
			alert('" . JText::_('SIZE_UNIT_ALREADY_DEFAULT') . "');
			return false;
		}

		id = jQuery(this).attr('rel');
		el = jQuery(this);

		if ( jQuery('#size_unit_' + id + ' td.pg-published div.pg-published .publish-buttons img').hasClass('item-publish') ) {
			alert('" . JText::_('CANT_DEFAULT_UNPUBLISHED_SIZE_UNIT') . "');
			return false;
		}

		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=units&task=default&id=' +id+ '&type=size',
			success: function( msg ) {
				if ( 1 != msg ) {
					alert( msg );
					return false;
				}

				jQuery('.size_unit-default').each(function(){
					if ( jQuery(this).hasClass( 'is-default-1' ) ) {
						jQuery(this).removeClass( 'is-default-1' ).addClass( 'is-default-0' );
					}
				});

				jQuery(el).removeClass( 'is-default-0' ).addClass( 'is-default-1' );

				//size_unit default currency
				jQuery('#pg-configuration-size_unit').find( '.pg-disabled-checkbox' ).remove();
				jQuery('<span class=\'pg-disabled-checkbox\'></span>').insertBefore('#size_unit_' + id + ' .pg-checkbox .checker');

			}
		});
	})
	// item weight unit
	function delete_weight_unit_row() {
		if (!confirm('Are you sure?'))
			return;
			
		jQuery('#pg-configuration-weight_unit td.pg-checkbox input:checked').each(function(el) {

			weightUnitId = jQuery(this).closest('tr').attr('id');
			if (jQuery('#' + weightUnitId + ' td.pg-default div.pg-default .weight_unit-default').hasClass('is-default-0')){
				weightUnitId = weightUnitId.replace('weight_unit_','');
				jQuery.ajax({
		        	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=units&task=delete&id=' +weightUnitId,
		            success : function(data) {
		            	data = jQuery.trim(parseInt(data));
		            	jQuery('#weight_unit_'+data).remove();
		            }
		        });
			}
		});
	}
	function show_weight_unit_add()
	{
		clear_weight_unit_add();
		jQuery('tr.new-weight_unit-con').css('display','table-row');
	}
	function clear_weight_unit_add(){
		jQuery('tr.new-weight_unit-con').css('display','none');
		jQuery('#new-weight_unit-name').val('');
		jQuery('#new-weight_unit-code').val('');
	}
	function weight_unit_add() {
		unitName   = jQuery('#new-weight_unit-name').val();
		unitCode   = jQuery('#new-weight_unit-code').val();
		unitType   = 'weight';
		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=units&task=add&dataType=json&unitName=' +unitName+ '&unitCode=' +unitCode+ '&unitType=' +unitType,
			success: function( res ) {
				var obj = jQuery.parseJSON(res);
				if(obj.error == 1){
					alert(obj.message);
				}
				else{
					newId = obj.id;
					html = '';
					html += '<tr id=\'weight_unit_'+ newId +'\' class=\'no-margin\'>';
						html += '<td class=\'pg-checkbox\'>';
							html += '<input type=\'checkbox\' id=\'weight_checkbox['+newId+']\' />';
							html += '<label for=\'weight_checkbox['+newId+']\'></label>';
						html += '</td>';
						html += '<td class=\'pg-weight_unit-name\'><input type=\'text\' name=\'weight_unit['+ newId +'][name]\' value=\''+ unitName +'\' /></td>';
						html += '<td class=\'pg-weight_unit-code\'><input type=\'text\' name=\'weight_unit['+ newId +'][code]\' value=\''+ unitCode +'\' maxlength=\'3\' /></td>';
						html += '<td class=\'pg-default\'>';
							html += '<div class=\'pg-default\'>';
								html += '<a href=\'javascript:void(0);\' class=\'pg-icon weight_unit-default is-default-0\' rel=\''+ newId +'\'></a>';
							html += '</div>';
						html += '</td>';
						html += '<td class=\'pg-published\'>';
							html += '<div class=\'pg-published\'>';
								html += '<a href=\'javascript:void(0);\' class=\'publish-buttons\' type=\'size_unit\' rel=\''+ newId +'\' title=\'Publish item\'>';
									html +=	'<img src=\'components/com_pago/css/img-new/publish_x.png\' border=\'0\' alt=\'Unpublished\' class=\'item-publish\'>';
								html += '</a>';
							html += '</div>';
						html += '</td>';
					html += '</tr>';

					jQuery('#pg-configuration-weight_unit tbody').append(html);
					// jQuery('#pg-configuration-weight_unit tbody tr:last').find('td.pg-checkbox input').uniform();
					
					close_weight_unit_add();
					
				}
			}
		});
	}
	
	function close_weight_unit_add() 
	{
		jQuery('#new-weight-modal').modal('hide');
	};
	
	jQuery('.weight_unit-default').live('click',function(){

		if ( jQuery(this).hasClass('is-default-1') ) {
			alert('" . JText::_('WEIGHT_UNIT_ALREADY_DEFAULT') . "');
			return false;
		}

		id = jQuery(this).attr('rel');
		el = jQuery(this);

		if ( jQuery('#weight_unit_' + id + ' td.pg-published div.pg-published .publish-buttons img').hasClass('item-publish') ) {
			alert('" . JText::_('CANT_DEFAULT_UNPUBLISHED_WEIGHT_UNIT') . "');
			return false;
		}

		jQuery.ajax({
			type: 'POST',
			url: 'index.php',
			data: 'option=com_pago&controller=units&task=default&id=' +id+ '&type=weight',
			success: function( msg ) {
				if ( 1 != msg ) {
					alert( msg );
					return false;
				}

				jQuery('.weight_unit-default').each(function(){
					if ( jQuery(this).hasClass( 'is-default-1' ) ) {
						jQuery(this).removeClass( 'is-default-1' ).addClass( 'is-default-0' );
					}
				});

				jQuery(el).removeClass( 'is-default-0' ).addClass( 'is-default-1' );

				//weight_unit default currency
				jQuery('#pg-configuration-weight_unit').find( '.pg-disabled-checkbox' ).remove();
				jQuery('<span class=\'pg-disabled-checkbox\'></span>').insertBefore('#weight_unit_' + id + ' .pg-checkbox .checker');

			}
		});
	})
	// global
	jQuery('.publish-buttons').live('click', function(){
			id = jQuery(this).attr('rel');
			type = jQuery(this).attr('type');
			img = jQuery(this).children().first();
			css = jQuery(img).attr('class');

			if ( 'item-unpublish' == css ) {
				task = 'unpublish';
			} else { // Is unpublished
				task = 'publish';
			}
			el = jQuery(this);

			if(type == 'currency'){
				if (task == 'unpublish' && (jQuery('#currency_' + id + ' td.pg-default div.pg-default .currency-default').hasClass('is-default-1'))){
					alert('" .JText::_( 'COM_PAGO_CANT_SET_DEAFULT_TO_UNPUBLISH' ). "');
					return false;
				}else{
					jQuery.ajax({
						type: 'POST',
						url: 'index.php',
						data: 'option=com_pago&controller=currencies&task=' +task+ '&id=' +id,
						success: function( content ) {
							if ( content ) {
								jQuery(el).replaceWith( content );
								jQuery('.publish-buttons').unbind('click');
							}
						}
					});
				}
			}
			if(type == 'item_types'){
				if (task == 'unpublish' && (jQuery('#item_types_' + id + ' td.pg-default div.pg-default .item-types-default').hasClass('is-default-1'))){
					alert('" .JText::_( 'CANT_UNPUBLISHED_DEFAULT_ITEM_TYPES' ). "');
					return false;
				}else{
					jQuery.ajax({
						type: 'POST',
						url: 'index.php',
						data: 'option=com_pago&controller=item_types&task=' +task+ '&id=' +id,
						success: function( content ) {
							if ( content ) {
								jQuery(el).replaceWith( content );
								jQuery('.publish-buttons').unbind('click');
							}
						}
					});
				}
			}
			if(type == 'weight_unit'){
				if (task == 'unpublish' && (jQuery('#weight_unit_' + id + ' td.pg-default div.pg-default .weight_unit-default').hasClass('is-default-1'))){
					alert('" . JText::_('CANT_UNPUBLISHED_DEFAULT_WEIGHT_UNIT') . "');
					return false;
				}else{
					jQuery.ajax({
						type: 'POST',
						url: 'index.php',
						data: 'option=com_pago&controller=units&task=' +task+ '&id=' +id+'&type=weight',
						success: function( content ) {
							if ( content ) {
								jQuery(el).replaceWith( content );
								jQuery('.publish-buttons').unbind('click');
							}
						}
					});
				}
			}
			if(type == 'size_unit'){
				if (task == 'unpublish' && (jQuery('#size_unit_' + id + ' td.pg-default div.pg-default .size_unit-default').hasClass('is-default-1'))){
					alert('" . JText::_('CANT_UNPUBLISHED_DEFAULT_SIZE_UNIT') . "');
					return false;
				}else{
					jQuery.ajax({
						type: 'POST',
						url: 'index.php',
						data: 'option=com_pago&controller=units&task=' +task+ '&id=' +id+ '&type=size',
						success: function( content ) {
							if ( content ) {
								jQuery(el).replaceWith( content );
								jQuery('.publish-buttons').unbind('click');
							}
						}
					});
				}
			}
		})

	jQuery(document).ready(function(){
		Joomla.submitbutton = function(task){
			// remove required
		    jQuery('#pg-configuration-currencies tbody').find( '.currency-required' ).removeClass('currency-required');
		    jQuery('#pg-configuration-item-types tbody').find( '.item-types-required' ).removeClass('item-types-required');

			var error = 0;

			//validate currencies
			jQuery('#pg-configuration-currencies tbody tr').each(function(el) {
				currencyId = jQuery(this).closest('tr').attr('id');
				name = jQuery.trim(jQuery('#' + currencyId + ' td.pg-currency-name input').val());
				if (name == ''){
					error = 1;
					jQuery('#' + currencyId + ' td.pg-currency-name input').addClass('currency-required');
				}
				code = jQuery.trim(jQuery('#' + currencyId + ' td.pg-currency-code input').val());
				if (code == ''){
					error = 1;
					jQuery('#' + currencyId + ' td.pg-currency-code input').addClass('currency-required');
				}
			});

			//validate item types
			jQuery('#pg-configuration-item-types tbody tr').each(function(el) {
				itemTypeId = jQuery(this).closest('tr').attr('id');
				name = jQuery.trim(jQuery('#' + itemTypeId + ' td.pg-item-types-name input').val());
				if (name == ''){
					error = 1;
					jQuery('#' + itemTypeId + ' td.pg-item-types-name input').addClass('item-types-required');
				}
			});

			//validate weight units
			jQuery('#pg-configuration-weight_unit tbody tr').each(function(el) {
				weightUnitId = jQuery(this).closest('tr').attr('id');
				name = jQuery.trim(jQuery('#' + weightUnitId + ' td.pg-weight_unit-name input').val());
				if (name == ''){
					error = 1;
					jQuery('#' + weightUnitId + ' td.pg-weight_unit-name input').addClass('weight_unit-required');
				}
				code = jQuery.trim(jQuery('#' + weightUnitId + ' td.pg-weight_unit-code input').val());
				if (code == ''){
					error = 1;
					jQuery('#' + weightUnitId + ' td.pg-weight_unit-code input').addClass('weight_unit-required');
				}
			});

			//validate size units
			jQuery('#pg-configuration-size_unit tbody tr').each(function(el) {
				sizeUnitId = jQuery(this).closest('tr').attr('id');
				name = jQuery.trim(jQuery('#' + sizeUnitId + ' td.pg-size_unit-name input').val());
				if (name == ''){
					error = 1;
					jQuery('#' + sizeUnitId + ' td.pg-size_unit-name input').addClass('size_unit-required');
				}
				code = jQuery.trim(jQuery('#' + sizeUnitId + ' td.pg-size_unit-code input').val());
				if (code == ''){
					error = 1;
					jQuery('#' + sizeUnitId + ' td.pg-size_unit-code input').addClass('size_unit-required');
				}
			});

			if(error == 0){
				Joomla.submitform(task);
			}
			return false;
		};
	})
	" );

?>

<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
<div class="pg-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li><a href="#tabs-1"><?php echo JText::_('PAGO_CURRENCY_TAB') ?></a></li>
					<!--<li><a href="#tabs-2"><?php echo JText::_('PAGO_CURRENCY_ITEM_TYPES') ?></a></li>-->
					<li><a href="#tabs-3"><?php echo JText::_('PAGO_WEIGHT_UNITS') ?></a></li>
					<li><a href="#tabs-4"><?php echo JText::_('PAGO_SIZE_UNITS') ?></a></li>
					<li><a href="#tabs-5"><?php echo JText::_('PAGO_API') ?></a></li>
				</ul>
				<div class="clear"></div>
			</div>
			
			<div class="tabs-content pg-pad-20 pg-white-bckg pg-border">

				<div id="tabs-1">
					<div class="pg-tab-content">
						<div class="modal modal-sm fade" id="new-currency-modal">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_NEW_CURRENCY' ) . '<button class="pg-btn-modal-close" data-dismiss="modal" aria-label="Close"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false ); ?>
							<div class="pg-pad-20 pg-border">
								<div class="pg-row no-margin">
									<div class="pg-col-4">
										<span class="field-heading">
											<label for="new-currency-name"><?php echo JText::_( 'PAGO_CURRENCY_NAME' ); ?></label>
										</span>
										<input type="text" id="new-currency-name" name="new-currency-name" value="" />
									</div>
									<div class="pg-col-4">
										<span class="field-heading">
											<label for="new-currency-code"><?php echo JText::_( 'PAGO_CURRENCY_CODE' ); ?></label>
										</span>
										<select id="new-currency-code" name="new-currency-code" maxlength="3" value="" />
										</select>
									</div>
									<div class="pg-col-4">
										<span class="field-heading">
											<label for="new-currency-symbol"><?php echo JText::_( 'PAGO_CURRENCY_SYMBOL' ); ?></label>
										</span>
										<input type="text" id="new-currency-symbol" name="new-currency-symbol" maxlength="10" value="" />
									</div>
								</div>
							</div>
							<div class="pg-pad-20 text-center">
								<div class="clear"></div>
								<button type="button" onclick="return currency_add();" class="pg-btn pg-btn-small pg-btn-light pg-btn-green"><?php echo JText::_('PAGO_ADD'); ?></button>
								<div class="clear"></div>
							</div>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
						
						<div class="pg-title-button-wrap pg-sub-title-button-wrap pg-mb-20">
							<button type="button" onclick="return jQuery('#new-currency-modal').modal() && show_currency_add();" class="pg-title-button pg-button-add pg-sub-title-button pg-btn pg-btn-large pg-btn-green pg-btn-light pg-btn-add" rel="">
								<?php echo JText::_('PAGO_ADD'); ?>
							</button>
							<button type="button" onclick="return delete_currency_row();" class="pg-title-button pg-button-delete pg-sub-title-button pg-btn pg-btn-large pg-btn-red pg-btn-light pg-btn-delete" rel="">
								<?php echo JText::_('PAGO_DELETE'); ?>
							</button>
						</div>
						<div class="clear"></div>
						
						<div class="pg-table-wrap">
							<table class="pg-table" id="pg-configuration-currencies">
								<thead>
									<thead>
										<tr class="pg-sub-heading">
											<td class="pg-checkbox">
												<input type="checkbox" id="checkbox_all" />
												<label for="checkbox_all"></label>
											</td>
											<td class="pg-currency-name">
												<?php echo JText::_( 'PAGO_CURRENCY_NAME' ); ?>
											</td>
											<td class="pg-currency-code">
												<?php echo JText::_( 'PAGO_CURRENCY_CODE' ); ?>
											</td>
											<td class="pg-currency-symbol">
												<?php echo JText::_( 'PAGO_CURRENCY_SYMBOL' ); ?>
											</td>
											<td class="pg-default">
												<?php echo  JText::_( 'PAGO_DEFAULT' ); ?>
											</td>
											<td class="pg-published">
												<?php echo  JText::_( 'PAGO_PUBLISH' ); ?>
											</td>
										</tr>
									</thead>
								</thead>
								<tbody>
									<?php foreach ( $this->currencies as $item ) : ?>
									
									<tr id="currency_<?php echo $item->id ?>" class="no-margin">
										<td class="pg-checkbox">
											<?php if ( !$item->default == 1 ) : ?>
											
											<input type="checkbox" id="currency_checkbox<?php echo $item->id ?>" />
											<label for="currency_checkbox<?php echo $item->id ?>"></label>
											
											<?php else: ?>
											
											<input type="checkbox" id="currency_checkbox<?php echo $item->id ?>" />
											<label class="pg-disabled-checkbox" for="currency_checkbox<?php echo $item->id ?>"></label>
											
											<?php endif; ?>
										</td>
										<td class="pg-currency-name">
											<input type="text" name="<?php echo "currencies[{$item->id}][name]"; ?>" value="<?php echo $item->name; ?>" />
										</td>
										<td class="pg-currency-code">
											<input disabled="disabled" type="text" name="<?php echo "currencies[{$item->id}][code]"; ?>" value="<?php echo $item->code; ?>" maxlength="3" />
										</td>
										<td class="pg-currency-symbol">
											<input type="text" name="<?php echo "currencies[{$item->id}][symbol]"; ?>" value="<?php echo $item->symbol; ?>" maxlength="10" />
										</td>
										<td class="pg-default">
											<div class="pg-default">
												<a href="javascript:void(0);" class="pg-icon currency-default is-default-<?php echo $item->default; ?>" rel="<?php echo $item->id; ?>"></a>
											</div>
										</td>
										<td class="pg-published">
											<div class="pg-published">
												<?php echo PagoHelper::published( $item, false, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="currency" rel="' .$item->id. '"' ); ?>
											</div>
										</td>
									</tr>
									
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<!--
				<div id="tabs-2">
					<div class="pg-tab-content">
						<div class="modal modal-sm fade" id="new-item-type-modal">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_NEW_ITEM_TYPE' ) . '<button class="pg-btn-modal-close" data-dismiss="modal" aria-label="Close"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false ); ?>
							<div class="pg-pad-20 pg-border">
								<div class="pg-row no-margin">
									<div class="pg-col-12">
										<span class="field-heading">
											<label for="new-item-types-name"><?php echo JText::_( 'PAGO_ITEM_TYPES_NAME' ); ?></label>
										</span>
										<input type="text" id="new-item-types-name" name="new-item-types-name" value="" />
									</div>
								</div>
								
								<div class="filter-primary_category pg-right pg-mr-20 pg-filter-primary-category">
									<label for="new-item-types-physical">Is Physical ?</label>
                					<select name="filter_primary_category" class="inputbox" id="new-item-types-physical">
                   						<option value="0">No</option>
                   						<option value="1">Yes</option>
                					</select>
            					</div> 
							</div>
							<div class="pg-pad-20 text-center">
								<div class="clear"></div>
								<button type="button" onclick="return item_types_add();" class="pg-btn pg-btn-small pg-btn-light pg-btn-green"><?php echo JText::_('PAGO_ADD'); ?></button>
								<div class="clear"></div>
							</div>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
						
						<div class="pg-title-button-wrap pg-sub-title-button-wrap pg-mb-20">
							<button type="button" onclick="return jQuery('#new-item-type-modal').modal() && clear_item_types_add();" class="pg-title-button pg-button-add pg-sub-title-button pg-btn pg-btn-large pg-btn-green pg-btn-light pg-btn-add" rel="">
								<?php echo JText::_('PAGO_ADD'); ?>
							</button>
							<button type="button" onclick="return delete_item_types_row();" class="pg-title-button pg-button-delete pg-sub-title-button pg-btn pg-btn-large pg-btn-red pg-btn-light pg-btn-delete" rel="">
								<?php echo JText::_('PAGO_DELETE'); ?>
							</button>
						</div>
						<div class="clear"></div>
						
						<div class="pg-table-wrap">
							<table class="pg-table" id="pg-configuration-item-types">
								<thead>
									<tr class="pg-sub-heading">
										<td class="pg-checkbox">
											<input type="checkbox" id="checkbox_all" />
											<label for="checkbox_all"></label>
										</td>
										<td class="pg-item-types-name">
											<?php echo JText::_( 'PAGO_ITEM_TYPES_NAME' ); ?>
										</td>
										<td class="pg-default">
											<?php echo  JText::_( 'PAGO_DEFAULT' ); ?>
										</td>
										<td class="pg-published">
											<?php echo  JText::_( 'PAGO_PUBLISH' ); ?>
										</td>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $this->itemTypes as $item ) : ?>
									
									<tr id="item_types_<?php echo $item->id ?>" class="no-margin">
										<td class="pg-checkbox">
											<?php if ( !$item->default == 1 ) : ?>
											
											<input type="checkbox" id="item-type_checkbox<?php echo $item->id ?>" />
											<label for="item-type_checkbox<?php echo $item->id ?>"></label>
											
											<?php else: ?>
											
											<input type="checkbox" id="item-type_checkbox<?php echo $item->id ?>" />
											<label class="pg-disabled-checkbox" for="item-type_checkbox<?php echo $item->id ?>"></label>
											
											<?php endif; ?>
										</td>
										<td class="pg-item-types-name">
											<input type="text" name="<?php echo "itemTypes[{$item->id}][name]"; ?>" value="<?php echo $item->name; ?>" />
										</td>
										<td class="pg-default">
											<div class="pg-default">
												<a href="javascript:void(0);" class="pg-icon currency-default is-default-<?php echo $item->default; ?>" rel="<?php echo $item->id; ?>"></a>
											</div>
										</td>
										<td class="pg-published">
											<div class="pg-published">
												<?php echo PagoHelper::published( $item, false, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="item_types" rel="' .$item->id. '"' ); ?>
											</div>
										</td>
									</tr>
									
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				-->
				<div id="tabs-3">
					<div class="pg-tab-content">
						<div class="modal modal-sm fade" id="new-weight-modal">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_NEW_WEIGHT' ) . '<button class="pg-btn-modal-close" data-dismiss="modal" aria-label="Close"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false ); ?>
							<div class="pg-pad-20 pg-border">
								<div class="pg-row no-margin">
									<div class="pg-col-6">
										<span class="field-heading">
											<label for="new-weight_unit-name"><?php echo JText::_( 'PAGO_WEIGHT_UNIT_NAME' ); ?></label>
										</span>
										<input type="text" id="new-weight_unit-name" name="new-weight_unit-name" value="" />
									</div>
									<div class="pg-col-6">
										<span class="field-heading">
											<label for="new-weight_unit-code"><?php echo JText::_( 'PAGO_WEIGHT_UNIT_CODE' ); ?></label>
										</span>
										<input type="text" id="new-weight_unit-code" name="new-weight_unit-code" maxlength="12" value="" />
									</div>
								</div>
							</div>
							<div class="pg-pad-20 text-center">
								<div class="clear"></div>
								<button type="button" onclick="return weight_unit_add();" class="pg-btn pg-btn-small pg-btn-light pg-btn-green"><?php echo JText::_('PAGO_ADD'); ?></button>
								<div class="clear"></div>
							</div>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
						
						<div class="pg-title-button-wrap pg-sub-title-button-wrap pg-mb-20">
							<button type="button" onclick="return jQuery('#new-weight-modal').modal() && clear_weight_unit_add();" class="pg-title-button pg-button-add pg-sub-title-button pg-btn pg-btn-large pg-btn-green pg-btn-light pg-btn-add" rel="">
								<?php echo JText::_('PAGO_ADD'); ?>
							</button>
							<button type="button" onclick="return delete_weight_unit_row();" class="pg-title-button pg-button-delete pg-sub-title-button pg-btn pg-btn-large pg-btn-red pg-btn-light pg-btn-delete" rel="">
								<?php echo JText::_('PAGO_DELETE'); ?>
							</button>
						</div>
						<div class="clear"></div>
						
						<div class="pg-table-wrap">
							<table class="pg-table" id="pg-configuration-weight_unit">
								<thead>
									<tr class="pg-sub-heading">
										<td class="pg-checkbox">
											<input type="checkbox" id="checkbox_all" />
											<label for="checkbox_all"></label>
										</td>
										<td class="pg-weight_unit-name">
											<?php echo JText::_('PAGO_WEIGHT_UNIT_NAME'); ?>
										</td>
										<td class="pg-weight_unit-code">
											<?php echo JText::_('PAGO_WEIGHT_UNIT_CODE'); ?>
										</td>
										<td class="pg-default">
											<?php echo  JText::_('PAGO_DEFAULT'); ?>
										</td>
										<td class="pg-published">
											<?php echo  JText::_('PAGO_PUBLISH'); ?>
										</td>
									</tr>
								</thead>
								<tbody>
									
									<?php foreach ( $this->weightUnits as $item ): ?>
									
									<tr id="weight_unit_<?php echo $item->id ?>" class="no-margin">
										<td class="pg-checkbox">
											<?php if ( !$item->default == 1 ) : ?>
											
											<input type="checkbox" id="weight_checkbox<?php echo $item->id ?>" />
											<label for="weight_checkbox<?php echo $item->id ?>"></label>
											
											<?php else: ?>
											
											<input type="checkbox" id="weight_checkbox<?php echo $item->id ?>" />
											<label class="pg-disabled-checkbox" for="weight_checkbox<?php echo $item->id ?>"></label>
											
											<?php endif; ?>
										</td>
										<td class="pg-weight_unit-name">
											<input type="text" name="<?php echo "weight_unit[{$item->id}][name]"; ?>" value="<?php echo $item->name; ?>" />
										</td>
										<td class="pg-weight_unit-code">
											<input type="text" name="<?php echo "weight_unit[{$item->id}][code]"; ?>" value="<?php echo $item->code; ?>" maxlength="12" />
										</td>
										<td class="pg-default">
											<div class="pg-default">
												<a href="javascript:void(0);" class="pg-icon weight_unit-default is-default-<?php echo $item->default; ?>" rel="<?php echo $item->id; ?>"></a>
											</div>
										</td>
										<td class="pg-published">
											<div class="pg-published">
												<?php echo PagoHelper::published($item, false, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="weight_unit" rel="' . $item->id . '"'); ?>
											</div>
										</td>
									</tr>
									
									<?php endforeach; ?>
								
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				<div id="tabs-4">
					<div class="pg-tab-content">
						<div class="modal modal-sm fade" id="new-size-modal">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_NEW_SIZE_UNIT' ) . '<button class="pg-btn-modal-close" data-dismiss="modal" aria-label="Close"><span class="fa fa-close"></span></button>', '', null, null, null, '', '', '', false ); ?>
							<div class="pg-pad-20 pg-border">
								<div class="pg-row no-margin">
									<div class="pg-col-6">
										<span class="field-heading">
											<label for="new-size_unit-name"><?php echo JText::_( 'PAGO_SIZE_UNIT_NAME' ); ?></label>
										</span>
										<input type="text" id="new-size_unit-name" name="new-size_unit-name" value="" />
									</div>
									<div class="pg-col-6">
										<span class="field-heading">
											<label for="new-size_unit-code"><?php echo JText::_( 'PAGO_SIZE_UNIT_CODE' ); ?></label>
										</span>
										<input type="text" id="new-size_unit-code" name="new-size_unit-code" value="" />
									</div>
								</div>
							</div>
							<div class="pg-pad-20 text-center">
								<div class="clear"></div>
								<button type="button" onclick="return size_unit_add();" class="pg-btn pg-btn-small pg-btn-light pg-btn-green"><?php echo JText::_('PAGO_ADD'); ?></button>
								<div class="clear"></div>
							</div>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
						
						<div class="pg-title-button-wrap pg-sub-title-button-wrap pg-mb-20">
							<button type="button" onclick="return jQuery('#new-size-modal').modal() && clear_size_unit_add();" class="pg-title-button pg-button-add pg-sub-title-button pg-btn pg-btn-large pg-btn-green pg-btn-light pg-btn-add" rel="">
								<?php echo JText::_('PAGO_ADD'); ?>
							</button>
							<button type="button" onclick="return delete_size_unit_row();" class="pg-title-button pg-button-delete pg-sub-title-button pg-btn pg-btn-large pg-btn-red pg-btn-light pg-btn-delete" rel="">
								<?php echo JText::_('PAGO_DELETE'); ?>
							</button>
						</div>
						<div class="clear"></div>
						
						<div class="pg-table-wrap">
							<table class="pg-table" id="pg-configuration-size_unit">
								<thead>
									<tr class="pg-sub-heading">
										<td class="pg-checkbox">
											<input type="checkbox" id="checkbox_all" />
											<label for="checkbox_all"></label>
										</td>
										<td class="pg-size_unit-name">
											<?php echo JText::_( 'PAGO_SIZE_UNIT_NAME' ); ?>
										</td>
										<td class="pg-size_unit-code">
											<?php echo JText::_( 'PAGO_SIZE_UNIT_CODE' ); ?>
										</td>
										<td class="pg-default">
											<?php echo  JText::_( 'PAGO_DEFAULT' ); ?>
										</td>
										<td class="pg-published">
											<?php echo  JText::_( 'PAGO_PUBLISH' ); ?>
										</td>
									</tr>
								</thead>
								<tbody>
									
									<?php foreach ( $this->sizeUnits as $item ) : ?>
									
									<tr id="size_unit_<?php echo $item->id ?>" class="no-margin">
										<td class="pg-checkbox">
											<?php if ( !$item->default == 1 ) : ?>
											
											<input type="checkbox" id="size_checkbox<?php echo $item->id ?>" />
											<label for="size_checkbox<?php echo $item->id ?>"></label>
											
											<?php else: ?>
											
											<input type="checkbox" id="checkbox<?php echo $item->id ?>" />
											<label class="pg-disabled-checkbox" for="checkbox<?php echo $item->id ?>"></label>
											
											<?php endif; ?>
										</td>
										<td class="pg-size_unit-name">
											<input type="text" name="<?php echo "size_unit[{$item->id}][name]"; ?>" value="<?php echo $item->name; ?>" />
										</td>
										<td class="pg-size_unit-code">
											<input type="text" name="<?php echo "size_unit[{$item->id}][code]"; ?>" value="<?php echo $item->code; ?>" maxlength="12" />
										</td>
										<td class="pg-default">
											<div class="pg-default">
												<a href="javascript:void(0);" class="pg-icon size_unit-default is-default-<?php echo $item->default; ?>" rel="<?php echo $item->id; ?>"></a>
											</div>
										</td>
										<td class="pg-published">
											<div class="pg-published">
												<?php echo PagoHelper::published( $item, false, 'tick.png',  'publish_x.png', '', ' class="publish-buttons" type="size_unit" rel="' .$item->id. '"' ); ?>
											</div>
										</td>
									</tr>
									
									<?php endforeach; ?>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				<div id="tabs-5">
					<div class="pg-tab-content">
						<div style="width:400px">
							<?php echo $this->params->render_config( 'params', 'pago_api', JText::_( 'PAGO_API_CONFIGURATION' ), 'general-configuration pg-pad-20 pg-border','no' ); ?>
						</div>
					</div>
				</div>
				
				
			</div>

		</div>

		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="view" value="system" />

	</form>
</div>
<?php echo JHTML::_('behavior.keepalive');

PagoHtml::pago_bottom();
?>