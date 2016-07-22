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
class JFormFieldselectimagesize extends JFormField {

    protected $type = 'selectimagesize';

    protected function getInput() {
		$name = $this->name;	
		$doc = JFactory::$document;
		
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
		$layout='
		<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value=\''.$this->value.'\' >
		<div class="selectimagesize">
			<div class="sizes">
				<select id="selectimagesize_'.$this->id.'">';
					$isi = 1;
					foreach ($image_sizes as $ik => $iv) {
						if($this->value == $isi){
							$layout .='	<option selected="selected" value='.$isi.'>'.$ik.'</option>';
						}else{
							$layout .='	<option value='.$isi.'>'.$ik.'</option>';
						}
						$isi++; 			
					}
		$layout .='</select>
			</div>
		</div>';

		$script='			
			jQuery(window).load(function(){
				jQuery("#selectimagesize_'.$this->id.'").change(function(){
					jQuery("#'.$this->id.'").attr("value",jQuery(this).val());	
				});
			});
		';
        $doc->addScriptDeclaration($script);
		return $layout;
    }

}

?>