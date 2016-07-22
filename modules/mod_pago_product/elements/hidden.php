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

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
jimport('joomla.application.component.helper');
jimport('joomla.error.error');

class JFormFieldpagohidden extends JFormField {

    protected $type = 'pagohidden';

    protected function getLabel() {
        return null;
    }

     protected function getInput() {
        $doc = JFactory::$document;

		$style='
		#product_grid-options + .pane-slider .panelform .adminformlist li{
			float:left;
		}
		#jformparamsproduct_image_settings{
			margin-top:25px;
		}
		#jformparamsproduct_image_settings_image_size{
			margin-top:55px;
			margin-left:37px;
		}
		#product_grid-options + .pane-slider fieldset.panelform fieldset.radio label{
			min-width: 45px !important;
		}
		';
        $doc->addStyleDeclaration($style);


        $script='
        jQuery(document).ready(function(){
   			jQuery("input[name=\'jform[params][product_settings_short_desc]\']").on("change",function(){
				configProductModule();
			})
			jQuery("input[name=\'jform[params][product_settings_desc]\']").on("change",function(){
				configProductModule();
			})
			jQuery("input[name=\'jform[params][category]\']").on("change",function(){
				configProductModule();
			})
			jQuery("input[name=\'jform[params][product_settings_inherit_settings]\']").on("change",function(){
				configProductModule();
			})
			jQuery("input[name=\'jform[params][product_settings_view_mode]\']").on("change",function(){
				configProductModule();
			})
			configProductModule();
			function configProductModule(){
				if(jQuery("#jform_params_product_settings_short_desc input:checked").attr("value") == 0){
					jQuery("#jform_params_product_settings_short_desc_limit-lbl").css("display","none");
					jQuery("#jform_params_product_settings_short_desc_limit").css("display","none");
				}else{
					jQuery("#jform_params_product_settings_short_desc_limit-lbl").css("display","block");
					jQuery("#jform_params_product_settings_short_desc_limit").css("display","block");
				}

				if(jQuery("#jform_params_product_settings_desc input:checked").attr("value") == 0){
					jQuery("#jform_params_product_settings_desc_limit-lbl").css("display","none");
					jQuery("#jform_params_product_settings_desc_limit").css("display","none");
				}else{
					jQuery("#jform_params_product_settings_desc_limit-lbl").css("display","block");
					jQuery("#jform_params_product_settings_desc_limit").css("display","block");
				}

				if(jQuery("#jform_params_category input:checked").attr("value") == 1){
					jQuery("#jform_params_category_selector-lbl").css("display","none");
					jQuery("#jform_params_category_selectorjformparamscategory_selector").css("display","none");
					jQuery("#jform_params_product_settings_show_child_item-lbl").css("display","none");
					jQuery("#jform_params_product_settings_show_child_item").css("display","none");
				}else{
					jQuery("#jform_params_category_selector-lbl").css("display","block");
					jQuery("#jform_params_category_selectorjformparamscategory_selector").css("display","block");
					jQuery("#jform_params_product_settings_show_child_item-lbl").css("display","block");
					jQuery("#jform_params_product_settings_show_child_item").css("display","block");
				}

				if(jQuery("#jform_params_product_settings_inherit_settings").attr("checked")){
					jQuery("#jform_params_inherit_category-lbl").css("display","block");
					jQuery("#jform_params_inherit_categoryjformparamsinherit_category").css("display","block");		
				}else{
					jQuery("#jform_params_inherit_category-lbl").css("display","none");
					jQuery("#jform_params_inherit_categoryjformparamsinherit_category").css("display","none");			
				}

				if(jQuery("#jform_params_product_settings_view_mode input:checked").attr("value") == 0){
					jQuery("#jform_params_product_grid_extra_small-lbl").css("display","none");
					jQuery("#jform_params_product_grid_extra_small").css("display","none");

					jQuery("#jform_params_product_grid_small-lbl").css("display","none");
					jQuery("#jform_params_product_grid_small").css("display","none");
					
					jQuery("#jform_params_product_grid_medium-lbl").css("display","none");
					jQuery("#jform_params_product_grid_medium").css("display","none");

					jQuery("#jform_params_product_grid_large-lbl").css("display","none");
					jQuery("#jform_params_product_grid_large").css("display","none");

					
				}else{
					jQuery("#jform_params_product_grid_extra_small-lbl").css("display","block");
					jQuery("#jform_params_product_grid_extra_small").css("display","block");

					jQuery("#jform_params_product_grid_small-lbl").css("display","block");
					jQuery("#jform_params_product_grid_small").css("display","block");
					
					jQuery("#jform_params_product_grid_medium-lbl").css("display","block");
					jQuery("#jform_params_product_grid_medium").css("display","block");

					jQuery("#jform_params_product_grid_large-lbl").css("display","block");
					jQuery("#jform_params_product_grid_large").css("display","block");
				}
			}
		});	
		';
        $doc->addScriptDeclaration($script);
		
		return null;
    }

}

?>

