<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
	$count = 1;
	$view = JFactory::getApplication()->input->get('view');
	$items_model = JModelLegacy::getInstance('Itemslist', 'PagoModel');
?>

<div id="pg-categories" class="clearfix">
    <div class="pg-category-info">
    <?php if( $view == 'frontpage' ) { ?>
    	<h2 class="pg-cat-title"><?php echo JText::_('PAGO_BROWSE_CATEGORIES'); ?></h2>
	<?php } else { ?>
        <h1 class="pg-cat-title"><?php echo $this->category->name; ?></h1>
        <div class="pg-cat-description">
        	<?php echo $this->category->description; ?>
        </div>
	<?php } ?>
    </div>

<?php if( $this->category->has_children() ) { ?>
	<div id="pg-cat-children" class="category-cells grid-4">
	<?php if( $view != 'frontpage' && $this->menu_config->get( 'show_categories', 1 ) ) { ?>
    	<h2><?php echo JText::_('PAGO_CATEGORIES_ADDITIONAL'); ?> <?php echo $this->category->name; ?> <?php echo JText::_('PAGO_CATEGORIES'); ?>:</h2>
	<?php foreach( $this->category->get_children() as $cat ) : ?>
    <?php JLoader::register('NavigationHelper', JPATH_COMPONENT . '/helpers/navigation.php');
          $nav = new NavigationHelper;
          $Itemid = $nav->getItemid(0, $cat->id); 
		  $item_count = $items_model->getItemCount($cat->id);
		  if($item_count > 0):
		  ?>


        <div id="cell-cat-<?php echo $count; ?>" class="cat-cell">
        	<div class="pg-category">
			<?php if( $view == 'frontpage' ) { ?>
            	<div class="cat-image">
                    <a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=category&cid=' . $cat->id . "&Itemid=" . $Itemid); ?>">
                        <img alt="<?php echo $cat->name; ?>-thumbnail" src="http://placehold.it/165x185" />
                    </a>
                </div>
            <?php } ?>
                <div class="cat-title">
                <?php
                 $catfiles = Pago::get_instance( 'categoriesi' )->getDefaultCategoryMedia( $cat->id );
                    $_img_obj = (object) array(
                        'id'                => $catfiles[0]->file_id,
                        'title'             => 'View Details for ' . html_entity_decode($cat->name), //$item->file_title,
                        'alias'             => $catfiles[0]->file_alias,
                        'caption'           => $catfiles[0]->file_caption,
                        'item_id'           => $cat->id,
                        'type'              => $catfiles[0]->file_type,
                        'file_name'         => $catfiles[0]->file_file_name,
                        'file_meta'         => $catfiles[0]->file_file_meta,
                        'primary_category'  => $cat->id
                    );
                    $image = PagoImageHandlerHelper::get_image_from_object( $_img_obj, 'medium', false );

                 ?>
                 <?php if( $image ) : ?>
                <div class="pg-item-image">
                        <?php echo $image; ?>
                </div>
                <?php endif; ?>
				<?php 
				//$item_count = $items_model->getItemCount($cat->id);
				
				if($item_count > 0 ) : ?>
					<a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=category&cid=' . $cat->id . "&Itemid=" . $Itemid ); ?>" class="pg-cat-name"><?php echo $cat->name; ?></a> <span class="pg-cat-itemcount">(<?php echo $item_count ?>)</span>
                <?php else: ?>

	                <?php echo $cat->name; ?> <span class="pg-cat-itemcount">(<?php echo $item_count ?>)</span>
		        <?php endif; ?>
                </div>
            </div>
        </div>
	    <?php $count++; ?>
	<?php 
	endif;
	endforeach; ?>
    <?php } ?>
	</div>
<?php } ?>
</div>
