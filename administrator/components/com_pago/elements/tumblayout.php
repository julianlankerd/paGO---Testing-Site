<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
/* ------------------------------------------------------------------------
 * Bang2Joom Alfheim Image Gallery PRO  for Joomla 2.5 & 3.0
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2012 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */

defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');
class JFormFieldtumblayout extends JFormField {

    protected $type = 'tumblayout';

    protected function getInput() {
		$name = $this->name;
		$name = str_replace("[","",$name);
		$name = str_replace("]","",$name);
        $doc = JFactory::$document;
		$style='
		#'.$name.'{width:320px;height:320px;position:relative;border:1px solid #ccc;}
		#'.$name.' .sizes{width:110px;height:110px;padding:0 5px;position:absolute;left:50%;top:50%;margin-top:-55px;margin-left:-55px;border:1px solid #ccc;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;z-index:5;background:#fff;}
		#'.$name.' .sizes:hover{background:#ddd;}
		#'.$name.' .sizes .selector{margin-top:45px;}
		#'.$name.' .padding{width:180px;height:180px;position:absolute;left:50%;top:50%;margin-top:-90px;margin-left:-90px;border:1px solid #ccc;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;z-index:4;background:#f7f7f7;}
		#'.$name.' .padding:hover{background:#ddd;}
		#'.$name.' .padding #'.$name.'_padding_left{width:26px;height:26px;position:absolute;left:4px;top:50%;margin-top:-12px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .padding #'.$name.'_padding_right{width:26px;height:26px;position:absolute;right:4px;top:50%;margin-top:-12px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .padding #'.$name.'_padding_top{width:26px;height:26px;position:absolute;top:4px;left:50%;margin-left:-16px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .padding #'.$name.'_padding_bottom{width:26px;height:26px;position:absolute;bottom:-16px;left:50%;margin-left:-16px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .border{width:250px;height:250px;position:absolute;left:50%;top:50%;margin-top:-125px;margin-left:-125px;border:1px dashed #ccc;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;z-index:3;background:#fff;}
		#'.$name.' .border:hover{background:#ddd;}
		#'.$name.' .border #'.$name.'_border_left{width:26px;height:26px;position:absolute;left:4px;top:50%;margin-top:-12px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .border #'.$name.'_border_right{width:26px;height:26px;position:absolute;right:4px;top:50%;margin-top:-12px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .border #'.$name.'_border_top{width:26px;height:26px;position:absolute;top:4px;left:50%;margin-left:-16px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .border #'.$name.'_border_bottom{width:26px;height:26px;position:absolute;bottom:-16px;left:50%;margin-left:-16px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .margin{	width:320px;height:320px;position:absolute;left:50%;top:50%;margin-top:-160px;margin-left:-160px;border:1px solid #ccc;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;z-index:2;background:#f7f7f7;}
		#'.$name.' .margin:hover{background:#ddd;}
		#'.$name.' .margin #'.$name.'_margin_left{width:26px;height:26px;position:absolute;left:4px;top:50%;margin-top:-12px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .margin #'.$name.'_margin_right{width:26px;height:26px;position:absolute;right:4px;top:50%;margin-top:-12px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .margin #'.$name.'_margin_top{width:26px;height:26px;position:absolute;top:4px;left:50%;margin-left:-16px; padding: 5px !important;text-indent: 0;text-align: center;}
		#'.$name.' .margin #'.$name.'_margin_bottom{width:26px;height:26px;position:absolute;bottom:-16px;left:50%;margin-left:-16px; padding: 5px !important;text-indent: 0;text-align: center;}
		';
        $doc->addStyleDeclaration($style);
		

		$defaults = array(
			'thumbnail' => array(
				'width' => 150,
				'height' => 150
			),
			'small' => array(
				'width' => 100,
				'height' => 100
			),
			'medium' => array(
				'width' => 250,
				'height' => 250
			),
			'large' => array(
				'width' => 706,
				'height' => 598
			)
		);

		$params      = Pago::get_instance( 'config' )->get();
		$image_sizes = $params->get( 'media.image_sizes', $defaults );

		if ( empty( $image_sizes ) ) {
			$image_sizes = $defaults;
		}

		if ( !is_array( $image_sizes ) ) {
			$image_sizes = (array) $image_sizes;
		}
		$vals = json_decode($this->value);
		
		foreach($vals as $k=>$val){
			if(!$val) $vals->$k = '0';
		}
		
		$layout='
		<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value=\''.$this->value.'\' >
		<div id="'.$name.'" class="b2jtumblayout">
			<div class="sizes"><div class="desc">'.JText::_("PAGO_CATEGORIES_CATEGORY_SETTINGS_IMAGE_SIZE").'</div>
				<select id="'.$name.'_image_size">';
					$isi = 1;
					foreach ($image_sizes as $ik => $iv) {
						if($vals->image_size == $isi){
							$layout .='	<option selected="selected" value='.$isi.'>'.$ik.'</option>';
						}else{
							$layout .='	<option value='.$isi.'>'.$ik.'</option>';
						}
						$isi++; 			
					}
		$layout .='</select>
			</div>
			<div class="padding">
				<div class="desc">Padding</div>
				<input type="text" id="'.$name.'_padding_left" value="'.$vals->padding_left.'">
				<input type="text" id="'.$name.'_padding_right" value="'.$vals->padding_right.'">
				<input type="text" id="'.$name.'_padding_top" value="'.$vals->padding_top.'">
				<input type="text" id="'.$name.'_padding_bottom" value="'.$vals->padding_bottom.'">
			</div>
			<div class="border">
				<div class="desc">Border</div>
				<input type="text" id="'.$name.'_border_left" value="'.$vals->border_left.'">
				<input type="text" id="'.$name.'_border_right" value="'.$vals->border_right.'">
				<input type="text" id="'.$name.'_border_top" value="'.$vals->border_top.'">
				<input type="text" id="'.$name.'_border_bottom" value="'.$vals->border_bottom.'">
			</div>
			<div class="margin">
				<div class="desc">Margin</div>
				<input type="text" id="'.$name.'_margin_left" value="'.$vals->margin_left.'">
				<input type="text" id="'.$name.'_margin_right" value="'.$vals->margin_right.'">
				<input type="text" id="'.$name.'_margin_top" value="'.$vals->margin_top.'">
				<input type="text" id="'.$name.'_margin_bottom" value="'.$vals->margin_bottom.'">
			</div>
		</div>';
		$script='
			var value'.$name.' = \''.$this->value.'\';
			if(value'.$name.'==""){
				value'.$name.'=new Array();
			}else{
				value'.$name.' = JSON.parse(value'.$name.');
			}
			jQuery(window).load(function(){
				
				jQuery("#'.$name.' input").change(function(){
					
					var elem = jQuery(this).attr("id");
					elem = elem.replace("'.$name.'"+"_","");
					value'.$name.'[elem] = jQuery(this).val();
					jQuery("#'.$this->id.'").attr("value",stringify(value'.$name.'));
				});
				jQuery("#'.$name.' select").change(function(){					
					var elem = jQuery(this).attr("id");
					elem = elem.replace("'.$name.'"+"_","");
					value'.$name.'[elem] = jQuery(this).val();
					jQuery("#'.$this->id.'").attr("value",stringify(value'.$name.'));
				});
			});
			function stringify(arr){
				var result="{";
				for(key in arr){
					if (typeof arr[key] !== "function") {
						result+="\""+key+"\":"+"\""+arr[key]+"\",";
					}
				}
				if(result!="{") result = result.slice(0,-1)+"}";
				else return "{}";
				
				return result;
				
			}
		';
        $doc->addScriptDeclaration($script);
		return $layout;
    }

}

?>