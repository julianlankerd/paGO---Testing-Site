<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport( 'joomla.plugin.plugin');

class JFormFieldImageSizes extends JFormField
{
	protected $type = 'imagesizes';
	protected $_defaults = array(
		'thumbnail' => array(
			'width' => 150,
			'height' => 150,
			'crop' => 1
		),
		'small' => array(
			'width' => 100,
			'height' => 100,
			'crop' => 0
		),
		'medium' => array(
			'width' => 250,
			'height' => 250,
			'crop' => 1
		),
		'large' => array(
			'width' => 706,
			'height' => 598,
			'crop' => 0
		)
	);

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;
		
		$show_checkbox = (isset($this->element['show_checkbox'])) ? $this->element['show_checkbox'] : '1';

		$db  = JFactory::getDBO();
		$doc = JFactory::getDocument();

		$ctrl        = $name;
		$params      = Pago::get_instance( 'config' )->get();
		$image_sizes = $params->get( 'media.image_sizes', $this->_default );

		if ( empty( $image_sizes ) ) {
			$image_sizes = $this->_defaults;
		}

		if ( !is_array( $image_sizes ) ) {
			$image_sizes = (array) $image_sizes;
		}

		$doc->addScriptDeclaration( 'var imgsizes_counter = ' .( count( $image_sizes ) + 1 ). ';
			function imgsize_add() {
				name = \'' .$ctrl. '[\' +imgsizes_counter+ \'][]\';
				html = \'<tr class="pg-new">\';
				show = "'. $show_checkbox .'";
				
				if (show == 1)
					html += \'<td class="pg-checkbox"><input type="checkbox"/><label></label></td>\';

				html += \'<td class="pg-size-name"><input type="text" name="\' +name+ \'" value=""></td><td class="pg-width"><input type="text" name="\' +name+ \'" value="" size="5"></td><td class="pg-height"><input type="text" name="\' +name+ \'" value="" size="5"></td><td class="pg-crop"><select name="\' +name+ \'"><option value="0" selected="selected">' .JText::_('PAGO_NO'). '</option><option value="1">' .JText::_('PAGO_YES'). '</option></select></td></tr>\';
				
				jQuery("#pg-configuration-image-sizes tr.pg-new").removeClass("pg-new");
				jQuery("#pg-configuration-image-sizes tr:last").after( html );
				jQuery("#pg-configuration-image-sizes tr.pg-new").find("td.pg-crop select").chosen({"disable_search": true, "disable_search_threshold": 6});
				//jQuery("#pg-configuration-image-sizes tr.pg-new").find("td.pg-checkbox input").uniform();
				imgsizes_counter++;
			}

			function delete_img_config_row() {
				jQuery("#pg-configuration-image-sizes td.pg-checkbox input:checked").each(function(el) {
					jQuery(this).closest("tr").remove();
				});
			}

			' );

		$html = '<table id="pg-configuration-image-sizes" class="pg-configuration-image-sizes"><thead><tr>';
		
		if ($show_checkbox == 1)
			$html .= '<th class="pg-checkbox"></th>';
			
		$html .= '<th class="pg-size-name"><span class="field-heading"><label>' . JText::_( 'PAGO_SIZE_NAME' ) . '</label></span></th><th class="pg-width"><span class="field-heading"><label>' . JText::_( 'PAGO_WIDTH' ) . '</label></span></th><th class="pg-height"><span class="field-heading"><label>' . JText::_( 'PAGO_HEIGHT' ) . '</label></span></th><th class="pg-crop"><span class="field-heading"><label>' . JText::_( 'PAGO_CROP' ) . '</label></span></th></tr></thead><tbody>';
		
		ob_start();
		$counter = 1;
		foreach ( $image_sizes as $size => $sizes ) {
			$sizes = (object) $sizes;
			$_name  = $ctrl . "[{$counter}][]";
			$show_delete = true;
			if ( in_array( $size, array( 'thumbnail', 'small', 'medium', 'large' ) ) ) {
				$show_delete = false;
			}
		?>
			<tr>
				<?php if ($show_checkbox == 1) : ?>
				<td class="pg-checkbox"><?php if ( $show_delete ) { ?><input type="checkbox" title="Checkbox for row <?php echo $counter; ?>" id="row<?php echo $counter; ?>"/><label  for="row<?php echo $counter; ?>"></label><?php } else { ?><label class="pg-disabled-checkbox" for="row<?php echo $counter; ?>"><?php } ?></label></td>
				<?php endif; ?>
				<td class="pg-size-name"><input type="text" name="<?php echo $_name; ?>" value="<?php echo $size; ?>" <?php echo (!$show_delete) ? 'readonly="readonly"' : ''; ?> /></td>
				<td class="pg-width"><input type="text" name="<?php echo $_name; ?>" value="<?php echo $sizes->width; ?>" size="5" /></td>
				<td class="pg-height"><input type="text" name="<?php echo $_name; ?>" value="<?php echo $sizes->height; ?>" size="5" /></td>
				<td class="pg-crop">
					<select name="<?php echo $_name; ?>">
						<option value="0" <?php echo (!$sizes->crop) ? 'selected="selected"' : ''; ?>><?php echo JText::_('PAGO_NO'); ?></option>
						<option value="1" <?php echo (1 == $sizes->crop) ? 'selected="selected"' : ''; ?>><?php echo JText::_('PAGO_YES'); ?></option>
					</select>
				</td>
			</tr>
		<?php
			$counter++;
		}

		$html .= ob_get_clean() . '</tbody></table>';

		return $html;
	}
}
