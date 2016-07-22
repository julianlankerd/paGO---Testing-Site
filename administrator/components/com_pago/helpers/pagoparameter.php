<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( "_JEXEC" ) or die();

jimport('joomla.form.form');

class PagoParameter
{
	function __construct( $data=false, $path = '' )
	{
		//static getInstance($data, $name= 'form', $file=true, $options=array())

		$this->path = $path;
		$this->data = $data;
	}

	/**
	 * render
	 *
	 */
	function render( $control = 'params', $fieldset = 'basic', $title = 'Parameters', $unique_class = 'default', $buttons = null, $table = true, $padding = true, $subtitle = false, $fielheading = true  )
	{
		$form = JForm::getInstance( $control.$fieldset, $this->path, array( 'control' => $control ) );

		if( $this->data  && is_array($this->data)){
			$data = (array)$this->data[ $control ];
			//need to get rid of jtable objects
			foreach( $data as $k=>$v ){
				if( !strstr($k, '*') ){
					$bind_data[ $k ] = $v;
				}
			}

			$form->bind( $bind_data );
		}
		ob_start();


		if ($table){
			if ( $unique_class == "meta-parameters" || $unique_class == "image-parameters" ) {
				foreach ( $form->getFieldset( $fieldset ) as $field ) {
					echo $field->input;
				}
			} 
			else {
			?>
				<?php if($title || $buttons){ ?>
				<tr class="pg-sub-heading">
					<td>
						<?php if($title){
							echo $title;
						} ?>
						<?php if ( $buttons ) { ?>
							<div class="pg-sub-title-button-wrap"><?php echo $buttons; ?></div>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
				<tr class="pg-table-content">
					<td>
						<div class="pg-table-content-wrap <?php echo $unique_class; ?>">

							<?php foreach ( $form->getFieldset( $fieldset ) as $field ) : ?>

								<?php
									$class 			= $form->getFieldAttribute( $field->fieldname, 'class', null );
									$column_state	= $form->getFieldAttribute( $field->fieldname, 'column', null );
									$column_class	= $form->getFieldAttribute( $field->fieldname, 'column_class', null );
									$width_class	= $form->getFieldAttribute( $field->fieldname, 'width_class', null );
									$field_type		= $form->getFieldAttribute( $field->fieldname, 'type', null );
									$field_type		= 'pg-' . strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field_type ) );
									if ( stristr( $column_state, "begin" ) ) {
										echo '<div class="pg-column ' . $class . ' ' . $column_class . '">';
									}
									if ( !$class) {
										$class = strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field->description ) );
									}
								?>
								
								<?php if ( ( $field->type != "Hidden" ) && ( $field->type != "Radio" ) && ( $class != "pago-item-meta" ) ) { ?>
									<div class="<?php echo $class . ' ' . $width_class . ' ' . $field_type; ?>">
								<?php } ?>
								
								<?php if ( ( $field->type == "Radio" ) ) { ?>
									<div class="<?php echo $class . ' ' . $width_class; ?>">
								<?php } ?>

								<?php if( $field->description == 'full_width' || $class == "pago-item-meta" ): ?>
									<?php echo $field->input; ?>
								<?php else: ?>
									<span class="field-heading"><?php echo $field->label; ?></span>
									<?php echo $field->input; ?>
								<?php endif ?>

								<?php if ( $field->type != "Hidden" && $class != "pago-item-meta" ) { ?>
									</div>
								<?php } ?>

								<?php
									if ( stristr( $column_state, "end" ) ) {
										echo '</div>';
									}
								?>

							<?php endforeach; ?>
							<div class="clear"></div>
						</div>
					</td>
				</tr>
			<?php } ?>
			<?php

			$output = ob_get_clean();
			return $output;
		}else{

			// if ( $unique_class == "meta-parameters" || $unique_class == "image-parameters" ) {
			// 	foreach ( $form->getFieldset( $fieldset ) as $field ) {
			// 		echo $field->input;
			// 	}
			// }
			?>
				
			<?php if ($subtitle) : ?>
				<div class = "pg-container-subheader">
					<?php echo $title;?>
				</div>
			<?php endif; ?>
			
			<?php 
				$class= '';
				if ($padding){
					$class = 'pg-pad-20 pg-border';
				}
			?>

			<div class="pg-tab-content <?php echo $class.' '.$unique_class; ?>">
				<?php foreach ( $form->getFieldset( $fieldset ) as $field ) : ?>

					<?php
						$class 			= $form->getFieldAttribute( $field->fieldname, 'class', null );
						$column_parent	= $form->getFieldAttribute( $field->fieldname, 'column_parent', null );
						$column_state	= $form->getFieldAttribute( $field->fieldname, 'column', null );
						$column_class	= $form->getFieldAttribute( $field->fieldname, 'column_class', null );
						$width_class	= $form->getFieldAttribute( $field->fieldname, 'width_class', null );
						$field_type		= $form->getFieldAttribute( $field->fieldname, 'type', null );
						
						$field_type		= 'pg-' . strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field_type ) );
						
						if ($column_parent && $column_parent != 'end'){
							echo '<div class="column-parent '.$column_parent.'">';
						}

						if ( stristr( $column_state, "begin" ) ) {
							echo '<div class="pg-row ' . $class . ' ' . $column_class . '">';
						}

						if ( !$class) {
							$class = strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field->description ) );
						}
					?>
					
					<?php if ( ( $field->type != "Hidden" ) && ( $field->type != "Radio" ) && ( $class != "pago-item-meta" ) ) { ?>
						<div class="<?php echo $class . ' ' . $width_class . ' ' . $field_type; ?>">
					<?php } ?>
					
					<?php if ( ( $field->type == "Radio" ) ) { ?>
						<div class="<?php echo $class . ' ' . $width_class; ?>">
					<?php } ?>

					<?php if( $field->description == 'full_width' || $class == "pago-item-meta" ): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
						<?php if ($fielheading): ?>
							<span class="field-heading"><?php echo $field->label; ?></span>
						<?php endif; ?>
						<div><?php echo $field->input; ?></div>
					<?php endif ?>

					<?php if ( $field->type != "Hidden" && $class != "pago-item-meta" ) { ?>
						</div>
					<?php } ?>

					<?php
						if ( stristr( $column_state, "end" ) ) {
							echo '</div>';
						}

						if ( stristr( $column_parent, "end" ) ) {
							echo '</div>';
						}
					?>

				<?php endforeach; ?>
			</div>
			<?php

			$output = ob_get_clean();

			return $output;
		}
	}

	function render_config( $control = 'params', $fieldset = 'basic', $title = 'PAGO_CONFIGURATION', $unique_class = 'default', $header_footer = 'yes', $buttons = null, $subhead_buttons = null ){
		$form = JForm::getInstance(
			$control.$fieldset, $this->path, array( 'control' => $control )
		);

		if( $this->data ){

			$form->bind( $this->data[ $control ] );
		}

		ob_start();

		foreach ( $form->getfieldsets( $fieldset ) as $name => $fs ) {
			// if ( $header_footer == 'yes' ) {
			// 	echo PagoHtml::module_top( JText::_( $fs->label ), null, null, null, 'pg-configuration-options' );
			// }
			
			$style = false;
			$class = false;
			
			if(isset($fs->hide) && $fs->hide) $style = "display:none";
			if(isset($fs->class) && $fs->class) $class = $fs->class;
			
			// $fs->description can be used for tool tips for headers
			?>
			<div id="fs-<?php echo $fs->name ?>" class="<?php echo $class ?>" style="<?php echo $style ?>">
				<div class="pg-sub-heading">
					<div>
						<?php
	
							if ( $control == 'theme' ) {
								$label = $fs->subheading;
							} else {
								$label = $fs->label;
							}
	
							if ( $fs->description ) {
								$label = $fs->description;
							}
	
							// if ( $label ) {
							// 	echo JText::_( $label );
							// } else {
							// 	echo JText::_( $title );
							// }
	
							if ( $buttons ) {
								echo '<div class="pg-title-button-wrap pg-sub-title-button-wrap">';
								echo $buttons;
								echo '</div>';
							}
	
						?>
					</div>
				</div>
				<div class="pg-table-content">
					<div>
						<div class="pg-table-content-wrap <?php echo $unique_class; ?>">
							<?php foreach ( $form->getFieldset( $name ) as $field ) : ?>
								<?php
									$class			= $form->getFieldAttribute( $field->fieldname, 'class', null, $field->group );
									$column_state	= $form->getFieldAttribute( $field->fieldname, 'column', null, $field->group );
									$column_class	= $form->getFieldAttribute( $field->fieldname, 'column_class', null, $field->group );
									$width_class	= $form->getFieldAttribute( $field->fieldname, 'width_class', null, $field->group );
									$row_state		= $form->getFieldAttribute( $field->fieldname, 'row', null, $field->group );
									$subhead		= $form->getFieldAttribute( $field->fieldname, 'subhead', null, $field->group );
									$subhead_label	= $form->getFieldAttribute( $field->fieldname, 'subhead_label', null, $field->group );
									$field_type		= $form->getFieldAttribute( $field->fieldname, 'type', null, $field->group );
									$field_type		= 'pg-' . strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field_type ) );
	
									if ( stristr( $column_state, "begin" ) ) {
										echo '<div class="pg-column ' . $class . ' ' . $column_class . '">';
									}
									// if ( $subhead == 'yes' ) {
									// 	echo '</div></td></tr><tr class="pg-sub-heading"><td>' . JText::_( $subhead_label ) . '<div class="pg-title-button-wrap pg-sub-title-button-wrap">' . $subhead_buttons . '</div>' . '</td></tr><tr class="pg-table-content"><td><div class="' . $unique_class . '">';
									// }
	
								?>
								<div class="<?php echo $class . ' ' . $width_class . ' ' . $field_type; ?>">
									<?php if ( !$subhead ) { ?>
									<span class="field-heading"><?php echo $field->label; ?></span>
									<?php } ?>
									<?php echo $field->input; ?>
								</div>
	
								<?php
									if ( stristr($column_state, "end") ) {
										echo '</div>';
									}
									if ( stristr($row_state, "end") ) {
										echo '<div class="clear"></div>';
									}
								?>
							<?php endforeach; ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<?php
			// if ( $header_footer == 'yes' ) {
			// 	echo PagoHtml::module_bottom();
			// }
		}

		$output = ob_get_clean();
		return $output;
	}

	function render_quickpay_options( $control = 'params', $fieldset = 'basic', $title = 'PAGO_CONFIGURATION', $unique_class = 'default', $header_footer = 'yes', $buttons = null, $subhead_buttons = null ){
		$form = JForm::getInstance(
			$control.$fieldset, $this->path, array( 'control' => $control )
		);

		if( $this->data ){

			$form->bind( $this->data[ $control ] );
		}

		ob_start();

		foreach ( $form->getfieldsets( $fieldset ) as $name => $fs ) {
			// if ( $header_footer == 'yes' ) {
			// 	echo PagoHtml::module_top( JText::_( $fs->label ), null, null, null, 'pg-configuration-options' );
			// }

			// $fs->description can be used for tool tips for headers
			?>
			<div class="pg-sub-heading">
				<div>
					<?php

						if ( $control == 'theme' ) {
							$label = $fs->subheading;
						} else {
							$label = $fs->label;
						}

						if ( $fs->description ) {
							$label = $fs->description;
						}

						// if ( $label ) {
						// 	echo JText::_( $label );
						// } else {
						// 	echo JText::_( $title );
						// }

						if ( $buttons ) {
							echo '<div class="pg-title-button-wrap pg-sub-title-button-wrap">';
							echo $buttons;
							echo '</div>';
						}

					?>
				</div>
			</div>
			<div class="pg-table-content">
				<div>
					<div class="pg-table-content-wrap <?php echo $unique_class; ?>">
						<div class="pg-row">
						<?php foreach ( $form->getFieldset( $name ) as $field ) : ?>
							<?php
								$class			= $form->getFieldAttribute( $field->fieldname, 'class', null, $field->group );
								$column_state	= $form->getFieldAttribute( $field->fieldname, 'column', null, $field->group );
								$column_class	= $form->getFieldAttribute( $field->fieldname, 'column_class', null, $field->group );
								$width_class	= $form->getFieldAttribute( $field->fieldname, 'width_class', null, $field->group );
								$row_state		= $form->getFieldAttribute( $field->fieldname, 'row', null, $field->group );
								$subhead		= $form->getFieldAttribute( $field->fieldname, 'subhead', null, $field->group );
								$subhead_label	= $form->getFieldAttribute( $field->fieldname, 'subhead_label', null, $field->group );
								$field_type		= $form->getFieldAttribute( $field->fieldname, 'type', null, $field->group );
								$field_type		= 'pg-' . strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field_type ) );

								if ( stristr( $column_state, "begin" ) ) {
									echo '<div class="pg-column ' . $class . ' ' . $column_class . '">';
								}
								// if ( $subhead == 'yes' ) {
								// 	echo '</div></td></tr><tr class="pg-sub-heading"><td>' . JText::_( $subhead_label ) . '<div class="pg-title-button-wrap pg-sub-title-button-wrap">' . $subhead_buttons . '</div>' . '</td></tr><tr class="pg-table-content"><td><div class="' . $unique_class . '">';
								// }

							?>
							<div class="pg-col-6 <?php echo $class . ' ' . $width_class . ' ' . $field_type; ?>">
								<?php if ( !$subhead ) { ?>
								<span class="field-heading"><?php echo $field->label; ?></span>
								<?php } ?>
								<?php echo $field->input; ?>
							</div>

							<?php
								if ( stristr($column_state, "end") ) {
									echo '</div>';
								}
								if ( stristr($row_state, "end") ) {
									echo '<div class="clear"></div>';
								}
							?>
						<?php endforeach; ?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<?php
			// if ( $header_footer == 'yes' ) {
			// 	echo PagoHtml::module_bottom();
			// }
		}

		$output = ob_get_clean();
		return $output;
	}

	function render_quickpay( $control = 'params', $fieldset = 'basic', $title = 'PAGO_CONFIGURATION', $unique_class = 'default', $header_footer = 'yes', $buttons = null, $subhead_buttons = null ){
		$form = JForm::getInstance(
			$control.$fieldset, $this->path, array( 'control' => $control )
		);

		if( $this->data ){

			$form->bind( $this->data[ $control ] );
		}

		ob_start();

		foreach ( $form->getfieldsets( $fieldset ) as $name => $fs ) {
			// if ( $header_footer == 'yes' ) {
			// 	echo PagoHtml::module_top( JText::_( $fs->label ), null, null, null, 'pg-configuration-options' );
			// }

			// $fs->description can be used for tool tips for headers
			?>
			<div class="pg-sub-heading">
				<div>
					<?php

						if ( $control == 'theme' ) {
							$label = $fs->subheading;
						} else {
							$label = $fs->label;
						}

						if ( $fs->description ) {
							$label = $fs->description;
						}

						// if ( $label ) {
						// 	echo JText::_( $label );
						// } else {
						// 	echo JText::_( $title );
						// }

						if ( $buttons ) {
							echo '<div class="pg-title-button-wrap pg-sub-title-button-wrap">';
							echo $buttons;
							echo '</div>';
						}

					?>
				</div>
			</div>
			<div class="pg-table-content">
				<div>
					<div class="pg-table-content-wrap <?php echo $unique_class; ?>">
						<div class="pg-row">
							<?php foreach ( $form->getFieldset( $name ) as $field ) : ?>
								<?php
									$class			= $form->getFieldAttribute( $field->fieldname, 'class', null, $field->group );
									$column_state	= $form->getFieldAttribute( $field->fieldname, 'column', null, $field->group );
									$column_class	= $form->getFieldAttribute( $field->fieldname, 'column_class', null, $field->group );
									$width_class	= $form->getFieldAttribute( $field->fieldname, 'width_class', null, $field->group );
									$row_state		= $form->getFieldAttribute( $field->fieldname, 'row', null, $field->group );
									$subhead		= $form->getFieldAttribute( $field->fieldname, 'subhead', null, $field->group );
									$subhead_label	= $form->getFieldAttribute( $field->fieldname, 'subhead_label', null, $field->group );
									$field_type		= $form->getFieldAttribute( $field->fieldname, 'type', null, $field->group );
									$field_type		= 'pg-' . strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $field_type ) );

									if ( stristr( $column_state, "begin" ) ) {
										echo '<div class="pg-column ' . $class . ' ' . $column_class . '">';
									}
									// if ( $subhead == 'yes' ) {
									// 	echo '</div></td></tr><tr class="pg-sub-heading"><td>' . JText::_( $subhead_label ) . '<div class="pg-title-button-wrap pg-sub-title-button-wrap">' . $subhead_buttons . '</div>' . '</td></tr><tr class="pg-table-content"><td><div class="' . $unique_class . '">';
									// }

								?>
									<div class="pg-col-6 <?php echo $class . ' ' . $width_class . ' ' . $field_type; ?>">
										<?php if ( !$subhead ) { ?>
										<span class="field-heading"><?php echo $field->label; ?></span>
										<?php } ?>
										<?php echo $field->input; ?>
									</div>
								<?php
									if ( stristr($column_state, "end") ) {
										echo '</div>';
									}
									if ( stristr($row_state, "end") ) {
										echo '<div class="clear"></div>';
									}
								?>
							<?php endforeach; ?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<?php
			// if ( $header_footer == 'yes' ) {
			// 	echo PagoHtml::module_bottom();
			// }
		}

		$output = ob_get_clean();
		return $output;
	}
}
?>
