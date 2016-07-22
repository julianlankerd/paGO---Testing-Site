<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
// Displays the full item detail view.
?>
<div id="pg-gallery-wrap" class="clearfix">
    <div class="pg-gallery-title">
        <h2><?php echo $this->item->name; ?><span class="pg-gallery-close"><?php echo JText::_('PAGO_GALLERY_CLOSE'); ?></span></h2>
    </div>
    <?php if ( $image_gallery = PagoHelper::load_template( 'common', 'tmpl_image_gallery' ) ) require $image_gallery; ?>
</div>
