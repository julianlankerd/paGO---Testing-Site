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

jimport( 'joomla.plugin.plugin' );

class JFormFieldFilesMeta extends JFormField
{
	protected $type = 'filesmeta';
	var $_default = array(array('version','1.0'));
	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$db  = JFactory::getDBO();
		$doc = JFactory::getDocument();

		$ctrl       = $name;
		$params     = Pago::get_instance( 'config' )->get();
		$files_meta = $params->get( 'media.files_meta', $this->_default );

		if ( empty( $files_meta ) ) {
			$files_meta = json_decode( $this->_default, true );
		}

		if ( !is_array( $files_meta ) ) {
			$files_meta = (array) $files_meta;
		}

		$doc->addScriptDeclaration( 'var filesmeta_counter = ' .( count( $files_meta ) + 1 ). ';
			function filemeta_add( el ) {
				name = \'' .$ctrl. '[\' +filesmeta_counter+ \'][]\';
				html = \'<tr><td><input type="text" name="\' +name+ \'" value=""></td><td><input type="text" name="\' +name+ \'" value=""></td><td><a href="javascript:void(0);" onClick="return delete_config_row(this);"><img src="' .JURI::root(true). '/administrator/components/com_pago/css/images/pg-remove.gif" alt="Delete item" /></a> <a href="javascript:void(0);" onClick="return filemeta_add(this);"><img src="' .JURI::root(true). '/administrator/components/com_pago/css/images/pg-add.gif" alt="Add new item" /></a></td></tr>\';

				jQuery(el).parent().parent().after( html );
				filesmeta_counter++;
			}' );

		$html = '<table class="pg-files-meta-data"><thead><tr><th class="pg-files-meta-title"><span class="field-heading"><label>' . JText::_( 'PAGO_META_TITLE' ) . '</label></span></th><th><span class="field-heading"><label>' . JText::_( 'PAGO_DEFAULT_VALUE' ) . '</label></span></th><th></th></tr></thead>';
		ob_start();
		$counter = 1;
		foreach ( $files_meta as $meta => $default_val ) {
			$_name  = $name . '['.$counter.'][]';
		?>
			<tr>
				<td class="pg-files-meta-title"><input type="text" name="<?php echo $_name; ?>" value="<?php echo $default_val[0]; ?>" /></td>
				<td class="pg-files-meta-default-value"><input type="text" name="<?php echo $_name; ?>" value="<?php echo $default_val[1]; ?>" /></td>
				<td class="pg-add-or-remove"><a href="javascript:void(0);" onClick="return filemeta_add(this);"><img src="<?php echo JURI::root(true); ?>/administrator/components/com_pago/css/images/pg-add.gif" alt="Add new item" /></a></td>
			</tr>
		<?php
			$counter++;
		}

		$html .= ob_get_clean() . '</table>';

		return $html;
	}
}
