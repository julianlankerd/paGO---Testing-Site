<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldItemAttributes extends JFormField
{
	protected $type = 'ItemAttributes';
	private $item_data = array();
	private $attribute_data = array();

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$this->get_attribute_data();
		$this->get_item_data();
		return $this->get_html();
	}

	private function get_item_data()
	{
		// Get attribute data associated to items
		$db = Jfactory::getDBO();
		$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
		$item_id = (int) $array[0];

		$query = "SELECT * FROM #__pago_items_attr WHERE item_id = $item_id";
		$db->setQuery( $query );
		$result = $db->loadAssocList();

		if ( empty( $result ) ) {
			return;
		}

		foreach ( $result as $item ) {
			$this->item_data[$item['attr_id']][$item['attr_opt_id']] = $item;
		}
		unset($result);
	}

	private function get_attribute_data()
	{
		// Get attributes and options
		$db = Jfactory::getDBO();

		$query = "SELECT attr.id, attr.name, attr.type, attr.pricing, opts.id as opt_id,
			opts.name as opt_name, attr.ordering
			FROM #__pago_attr AS attr
			LEFT JOIN #__pago_attr_opts AS opts ON opts.attr_id = attr.id ORDER BY attr.ordering";
		$db->setQuery( $query );
		$result = $db->loadAssocList();

		if ( empty( $result ) ) {
			return;
		}

		$opts = array();
		foreach ( $result as $attrib ) {
			if ( !array_key_exists( $attrib['id'], $this->attribute_data ) ) {
				$this->attribute_data[$attrib['id']]['name'] = $attrib['name'];
				$this->attribute_data[$attrib['id']]['type'] = $attrib['type'];
				$this->attribute_data[$attrib['id']]['pricing'] = $attrib['pricing'];
			}
			$opts['id'] = $attrib['opt_id'];
			$opts['name'] = $attrib['opt_name'];
			$this->attribute_data[$attrib['id']]['options'][] = $opts;
		}

		// free up temp variables
		unset($opts);
		unset($result);
	}

	private function get_html()
	{
		ob_start();
	?>
<table id="pg-item-attributes" class="pg-table" width="100%" cellspacing="1">
	<tbody>
	<?php
		foreach( $this->attribute_data as $k => $attribute ) {
	?>
		<tr>
			<td>
				<?php echo $attribute['name'] ?> :
			</td>
	<?php
		switch( $attribute['type'] ) {
			case '0':
				echo $this->get_text( $attribute['options'], $attribute['pricing'], $k );
				break;
			case '1':
				echo $this->get_text_area( $attribute['options'], $attribute['pricing'], $k );
				break;
			case '2':
				echo $this->get_select( $attribute['options'], $attribute['pricing'], $k );
				break;
			case '3':
				echo $this->get_multi_select( $attribute['options'], $attribute['pricing'], $k );
				break;
			case '4':
				echo $this->get_date( $attributes['options'], $attribute['pricing'], $k );
				break;
		}
	?>
		</tr>
	<?php
		}
	?>
	</tbody>
</table>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	private function get_select( $options, $pricing, $attr_id )
	{
		ob_start();
	?>
	<td>
		<table class="pg-table">
			<tbody>
				<tr>
					<td>
						<p class="pg-scroll-attr">
	<?php
		foreach ( $options as $option ) {
			if ( isset( $this->item_data[$attr_id][$option['id']] ) ) {
				$price = $this->item_data[$attr_id][$option['id']]['price'];
				$checked = 'checked';
			} else {
				$checked = '';
				$price = '';
			}
	?>
		<label class="pg-scroll-attr-options">
			<input type="radio" name="params[attribute][<?php echo $attr_id ?>]" value="<?php echo $option['id']?>" <?php echo $checked ?> /> <?php echo $option['name']?>

		<?php
			if ( $pricing ) {
				?>
					Price : <input type="input" name="params[attribute_price][<?php echo $option['id']?>][price]" value="<?php echo $price ?>" />
				<?php
			}
		?>
		</label>
		<br />
	<?php
		}
	?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	</td>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	private function get_multi_select( $options, $pricing, $attr_id )
	{
		ob_start();
	?>
	<td>
		<table class="pg-table">
			<tbody>
				<tr>
					<td>
						<p class="pg-scroll-attr">
	<?php
		foreach ( $options as $option ) {
			if ( isset( $this->item_data[$attr_id][$option['id']] ) ) {
				$price = $this->item_data[$attr_id][$option['id']]['price'];
				$checked = 'checked';
			} else {
				$checked = '';
				$price = '';
			}
	?>
		<label class="pg-scroll-attr-options">
			<input type="checkbox" name="params[attribute][<?php echo $attr_id ?>][<?php echo $option['id'] ?>]"  <?php echo $checked ?> />  <?php echo $option['name']?>


		<?php
			if ( $pricing ) {
				?>
					Price : <input type="input" name="params[attribute_price][<?php echo $option['id']?>][price]" value="<?php echo $price ?>" />

				<?php
			}
		?>
		</label>
		<br />
	<?php
		}
	?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	private function get_text( $options, $pricing, $attr_id )
	{
		ob_start();
	?>
	<td>
		<table class="pg-table">
			<tbody>
				<tr>
					<td>
						<p class="pg-scroll-attr">
	<?php
		foreach ( $options as $option ) {
			if ( isset( $this->item_data[$attr_id][$option['id']] ) ) {
				$price = $this->item_data[$attr_id][$option['id']]['price'];
				$value = $this->item_data[$attr_id][$option['id']]['value'];
			} else {
				$price = '';
				$value = '';
			}
	?>
		<label class="pg-scroll-attr-options">
		<?php echo $option['name'] ?> : <input type="text" name="params[attribute][<?php echo $attr_id ?>][<?php echo $option['id'] ?>]" value="<?php echo htmlentities( $value, ENT_QUOTES ) ?>" />
		<?php
			if ( $pricing ) {
				?>

					Price : <input type="input" name="params[attribute][<?php echo $attr_id ?>][<?php echo $option['id']?>][price]" value="<?php echo $price ?>" />
				</td>
				<?php
			}
		?>
		</label>
		<br />
	<?php
		}
	?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	</td>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	private function get_text_area( $options, $pricing, $attr_id )
	{
		ob_start();
	?>
	<td>
	<?php
		foreach ( $options as $option ) {
			if ( isset( $this->item_data[$attr_id][$option['id']] ) ) {
				$price = $this->item_data[$attr_id][$option['id']]['price'];
				$value = $this->item_data[$attr_id][$option['id']]['value'];
			} else {
				$price = '';
				$value = '';
			}
	?>
		<label class="pg-scroll-attr-options">
			<textarea name="params[attribute][<?php echo $attr_id ?>][<?php echo $option['id'] ?>]" cols="45" rows="10" ><?php echo $value ?></textarea>
		</label>
	<?php
		}
	?>
	</td>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	private function get_date( $options, $pricing, $attr_id )
	{
		ob_start();
	?>
	<td>
	</td>
	<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
