<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/ 
defined('_JEXEC') or die('Restricted access'); 
	// This file will display on the front page if Custom Frontpage is not choosen in the menu manager. It is a list of all the root level categories.
?>
<?php $this->load_header(); ?>
<div id="pg-frontpage">
	<div id="pg-frontpage-left">
		<div id="pg-store-info">
			<?php if( $this->tmpl_params->get( 'show_store_desc', 1 ) ) : ?>
			<div class="pg-store-description">
				<?php echo html_entity_decode( $this->config->get( 'general.description' ) ); ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div id="pg-frontpage-right">
	<?php if($this->tmpl_params->get( 'show_featured', 1 ) ) : ?>
		<h2><?php echo JText::_('PAGO_FRONTPAGE_FEATURED_ITEMS_MODULE_TITLE'); ?></h2>
		<?php echo $this->modules->render_position( 'pago_frontpage_featured' ); ?>
	<?php endif; ?>
	</div>
	<div id="pg-frontpage-bottom">
	<?php if($this->tmpl_params->get( 'show_latest', 1 ) ) : ?>
		<h2><?php echo JText::_('PAGO_FRONTPAGE_LATEST_ITEMS_MODULE_TITLE'); ?></h2>
		<?php echo $this->modules->render_position( 'pago_frontpage_latest' ); ?>
	<?php endif; ?>
	</div>
</div>
<?php $this->load_footer(); ?>