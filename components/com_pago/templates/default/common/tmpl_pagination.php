<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>

<div class="pg-pagination clearfix">
	<div class="pg-results-sort">
        <div class="pg-results"><?php echo $this->pagination->getResultsCounter() ?></div>
        <div class="pg-sortby">
            <span><?php echo JText::_('PAGO_SORT_BY'); ?>: </span>
           <?php 
			$sortOrder = $this->nav->getSortByList();
			$sortBy = JFactory::getApplication()->input->get('sortby');?>
            <form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post">
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="view" value="<?php echo $this->view; ?>" />
                <input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />  
				<?php
				echo $lists['order_by'] = JHtml::_('select.genericlist',$sortOrder,'sortby','class="inputbox pg-select" id="pg-sort" size="1" onChange="this.form.submit();"','value','text',$sortBy);					                ?>

            </form>
        </div>
    </div>
	<div class="pg-pages">
        <div class="pg-limitbox">				
            <span><?php echo JText::_('PAGO_ITEMS_PER_PAGE'); ?>: </span>
            <form action="" method="post">
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="view" value="<?php echo $this->view; ?>" />
                <input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />  
				 <input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->get('Itemid'); ?>" /> 
                <?php echo $this->pagination->getLimitBox() ?>
            </form>
        </div>
		<div class="pg-pagelinks"><?php echo $this->pagination->getPagesLinks() ?></div>
	</div>
</div>