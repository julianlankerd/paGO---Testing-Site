<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class JFormFieldMetadata extends JFormField
{
	protected $type = 'Metadata';

	public function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;
		$meta_type = (string)$this->element['meta'];

		return $this->html( $name, $value, $node, $meta_type );
	}

	public function html( $name, $value, $node, $meta_type )
	{
		$id   = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$meta = Pago::get_instance( 'meta' );

		ob_start();
	?>
		<div class="pg-row">
			<div class = "<?php echo $meta_type == 'item' ? "pg-col-4" : "pg-col-12"; ?>">
				<div class="pg-container-subheader">
					<?php echo JText::_( 'PAGO_ELEMENTS_META_TITLE_AUTHOR_ROBOTS' ); ?>
				</div>
		
				<div class="meta-textbox-parameters pg-pad-20 pg-border">
					<span class="field-heading"><label for="params[meta][title]" id="params[meta][title]-lbl"><?php echo JText::_( 'PAGO_ELEMENTS_META_TAG_TITLE' ); ?></label></span>
					<input type="text" size="50" class="text_area" value="<?php echo htmlentities( $meta->get( $meta_type, $id[0], 'title', true ), ENT_QUOTES ); ?>" id="params[meta][title]" name="params[meta][title]">

					<span class="field-heading"><label for="params[meta][author]" id="params[meta][author]-lbl"><?php echo JText::_( 'PAGO_ELEMENTS_META_TAG_AUTHOR' ); ?></label></span>
					<input type="text" size="50" class="text_area" value="<?php echo htmlentities( $meta->get( $meta_type, $id[0], 'author', true ), ENT_QUOTES ); ?>" id="params[meta][author]" name="params[meta][author]">

					<div class="meta-tag-robots no-margin">
						<span class="field-heading"><label for="params[meta][robots]" id="params[meta][robots]-lbl"><?php echo JText::_( 'PAGO_ELEMENTS_META_TAG_ROBOTS' ); ?></label></span>
						<?php $_value = $meta->get( $meta_type, $id[0], 'robots', true ); ?>
						<select class="inputbox" id="params[meta][robots]" name="params[meta][robots]">
							<option <?php if ( '' == $_value ) { echo 'selected="selected"'; } ?> value=""><?php echo JText::_( 'PAGO_ELEMENTS_SEL_DO_NOT_DISPLAY_A_ROBOT_TAG' ); ?></option>
							<option <?php if ( 'index, follow' == $_value ) { echo 'selected="selected"'; } ?> value="index, follow"><?php echo JText::_( 'PAGO_ELEMENTS_SEL_INDEX_FOLLOW' ); ?></option>
							<option <?php if ( 'noindex, follow' == $_value ) { echo 'selected="selected"'; } ?> value="noindex, follow"><?php echo JText::_( 'PAGO_ELEMENTS_SEL_NOINDEX_FOLLOW' ); ?></option>
							<option <?php if ( 'noindex, nofollow' == $_value ) { echo 'selected="selected"'; } ?> value="noindex, nofollow"><?php echo JText::_( 'PAGO_ELEMENTS_SEL_NOINDEX_NOFOLLOW' ); ?></option>
						</select>
					</div>
					<div class="clear"></div>
				</div>
			</div>

			<?php if ( $meta_type == 'category') : ?>
				<div class = "pg-mb-20"></div>
			<?php endif; ?>

			<div class = "<?php echo $meta_type == 'item' ? "pg-col-8" : "pg-col-12"; ?>">
				<div class="pg-container-subheader">
					<?php echo JText::_( 'PAGO_ELEMENTS_SEL_KEYWORDS_AND_DESCRIPTION' ); ?>
				</div>

				<div class="meta-textarea-parameters pg-pad-20 pg-border no-margin">
					<div class = "pg-row">
						<div class="pg-col-6">					
							<span class="field-heading"><label for="params[meta][keywords]" id="params[meta][keywords]-lbl"><?php echo JText::_( 'PAGO_ELEMENTS_META_TAG_KEYWORDS' ); ?></label></span>
							<textarea id="params[meta][keywords]" class="text_area" rows="5" cols="60" name="params[meta][keywords]"><?php echo htmlentities( $meta->get( $meta_type, $id[0], 'keywords', true ), ENT_QUOTES ); ?></textarea>
						</div>

						<div class="meta-tag-description pg-col-6">
							<span class="field-heading"><label for="params[meta][description]" id="params[meta][description]-lbl"><?php echo JText::_( 'PAGO_ELEMENTS_META_TAG_DESCRIPTION' ); ?></label></span>
							<textarea id="params[meta][description]" class="text_area" rows="5" cols="60" name="params[meta][description]"><?php echo htmlentities( $meta->get( $meta_type, $id[0], 'description', true ), ENT_QUOTES ); ?></textarea>							
						</div>
					</div>

					<div class="clear"></div>
				</div>
			</div>
		</div>

		<?php
		$return = ob_get_clean();

		return $return;
	}
}
