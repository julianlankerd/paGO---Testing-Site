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
$params = $this->params;
PagoHtml::add_js( JURI::base() . 'components/com_pago/javascript/com_pago_config.js' );
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs', $this->top_menu );
$defaultImage = $this->defaultImage;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( "
	jQuery(document).ready(function(){
		jQuery('#uniform-undefined').append(' ($defaultImage)');
		
	})
	" );

function getToolbar()
{
	$buttons = PagoHelper::addCustomButton(
		JText::_( 'PAGO_ADD' ),
		"return imgsize_add();",
		'add pg-sub-title-button',
		'javascript:void(0);',
		'toolbar',
		''
	);

	$buttons .= PagoHelper::addCustomButton(
		JText::_( 'PAGO_DELETE' ) ,
		"return delete_img_config_row();",
		'delete pg-sub-title-button',
		'javascript:void(0);',
		'toolbar',
		''
	);

	return $buttons;
}


?>
<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>
<div class="pg-content pg-tab-content"> <!-- Start of pago conent -->
	<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div id="tabs">
			<div class="pg-tabs">
				<ul>
					<li><a href="#tabs-1"><?php echo JText::_( 'PAGO_CONFIG_GENERAL' ) ?></a></li>
					<li><a href="#tabs-2"><?php echo JText::_( 'PAGO_CONFIG_ACCOUNT' ) ?></a></li>
					<li><a href="#tabs-3"><?php echo JText::_( 'PAGO_CONFIG_CHECKOUT' ) ?></a></li>
					<li><a href="#tabs-4"><?php echo JText::_( 'PAGO_CONFIG_TEMPLATE' ) ?></a></li>
					<li><a href="#tabs-5"><?php echo JText::_( 'PAGO_CONFIG_MEDIA' ) ?></a></li>
					<li><a href="#tabs-6"><?php echo JText::_( 'PAGO_CONFIG_CART' ) ?></a></li>
					<li><a href="#tabs-7"><?php echo JText::_( 'PAGO_CONFIG_COMMENTS' ) ?></a></li>
					<li><a href="#tabs-8"><?php echo JText::_( 'PAGO_CONFIG_SEARCH' ) ?></a></li>
					<?php /* <li><a href="#tabs-5"><?php echo JText::_( 'PAGO_CONFIG_MAILCHIMP' ) ?></a></li>
					<li><a href="#tabs-6"><?php echo JText::_( 'PAGO_CONFIG_OTHER' ) ?></a></li> */ ?>

					<?php
					// Needs to be a filter, otherwise the $counter gets lost
					$counter = 9;

					$dispatcher->trigger( 'backend_config_tab_name', array( &$counter ) );
					?>
				</ul>
				<div class="clear"></div>
			</div>

			<div id="tabs-1">
				<?php
					echo PagoHtml::module_top( JText::_( 'PAGO_CONFIG_GENERAL_BASIC' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
					echo $params->render_config( 'params', 'general', JText::_( 'PAGO_GENERAL_CONFIGURATION' ), 'general-configuration pg-pad-20 pg-border','no' );
					echo PagoHtml::module_bottom();
				?>
			</div><!-- end tab-1 -->

			<div id="tabs-2">
				<?php 
					echo PagoHtml::module_top( JText::_( 'PAGO_CONFIG_ACCOUNT_OPTIONS' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
					echo $params->render_config( 'params', 'account', null , 'pg-pad-20 pg-border','no' );
					echo PagoHtml::module_bottom();
				?>
			</div><!-- end tab-2 -->

			<div id="tabs-3">
				<?php 
					echo PagoHtml::module_top( JText::_( 'PAGO_CONFIG_CHECKOUT_OPTIONS' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
					echo $params->render_config( 'params', 'checkout', null , 'pg-pad-20 pg-border','no' );
					echo PagoHtml::module_bottom();
				?>
			</div><!-- end tab-3 -->

			<div id="tabs-4">
				<?php 
				echo PagoHtml::module_top( JText::_( 'PAGO_CONFIG_CHECKOUT_OPTIONS' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
				echo $params->render_config( 'params', 'template', null , 'pg-pad-20 pg-border','no'  );
				echo $this->template_params->render_config( 'theme', 'options',null , 'pg-pad-20 pg-border','no' );
				echo PagoHtml::module_bottom();
				?>
			</div><!-- end tab-4 -->

			<div id="tabs-5">
				<?php
					echo PagoHtml::module_top( JText::_( 'PAGO_MEDIA_CONFIGURATION' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
					echo $params->render_config( 'params', 'media', null,  'pg-pad-20 pg-border', 'no', null, getToolbar() );
					echo PagoHtml::module_bottom();
				?>
			</div><!-- end tab-5 -->
			<div id="tabs-6">
				<?php 
					echo PagoHtml::module_top( JText::_( 'PAGO_CART_CONFIGURATION' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
					echo $params->render_config( 'params', 'cart', null , 'pg-pad-20 pg-border','no' );
					echo PagoHtml::module_bottom();
				?>
			</div><!-- end tab-6 -->
		
			<div id="tabs-7">
				<?php 
				echo PagoHtml::module_top( JText::_( 'PAGO_COMMENTS_CONFIGURATION' ), null, null, null, 'pg-configuration-options pg-pad-20 pg-border pg-white-bckg',null,null,false );
				echo $params->render_config( 'params', 'comments', null , 'pg-pad-20 pg-border','no' );
				echo PagoHtml::module_bottom();
				?>
			</div>

			<div id="tabs-8">
				<div class="pg-tab-content pg-white-bckg pg-pad-20 pg-border">
					<div class="pg-row">
						<div class="pg-col-6">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_SEARCH_TITLE_BASIC_SETTINGS' ), null, null, null, null, null, null, false ); ?>
							<div class="pg-border pg-pad-20">
								<?php echo $params->render_config( 'params', 'search_product_settings' ) ?>
							</div>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
						<div class="pg-col-6">
							<?php echo PagoHtml::module_top( JText::_( 'PAGO_SEARCH_TITLE_ADVANCE_SETTINGS' ), null, null, null, null, null, null, false ); ?>
							<div class="pg-border pg-pad-20">
								<?php echo $params->render_config( 'params', 'search_product_social_settings' ) ?>
								<?php echo $params->render_config( 'params', 'search_product_grid_settings' ) ?>
							</div>
							<?php echo PagoHtml::module_bottom(); ?>
						</div>
					</div>
				</div>
			</div>

			<?php
			// Needs to be a filter, otherwise the $counter gets lost
			$conter = 9;
			$dispatcher->trigger( 'backend_config_tab_data', array( &$counter ) );
			?>

		</div>

		<input type="hidden" name="option" value="com_pago" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="view" value="config" />

	</form>
</div>

<?php echo JHTML::_('behavior.keepalive');

PagoHtml::add_css( JURI::base() . 'components/com_pago/javascript/jquery-ui/css/jquery-ui.tooltip.css' );
PagoHtml::add_js( JURI::base() . 'components/com_pago/javascript/jquery-ui/js/jquery.ui.tooltip.js' );