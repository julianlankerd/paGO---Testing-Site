<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>

<div id="pg-gallery-full-image">
	<?php PagoImageHelper::display_image($this->images, 'gallery', $this->item->name, $this->item->name); ?>
</div>
<div class="pg-gallery-thumbs">
    <?php template_functions::list_gallery_images($this->images, 'small', 'images', 'gallery', $this->item->name, $this->item->name); ?>
</div>