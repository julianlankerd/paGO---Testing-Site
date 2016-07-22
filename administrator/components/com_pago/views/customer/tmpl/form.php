<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$uri = str_replace( '/administrator','',JURI::base(true) );

?>
<style>
#detailscustomer_note{width:100%}

.left_col{width:50%;float:left}
.col_wrap{padding:10px;border:1px solid #ccc;border-width:0 1px 0 1px}
.right_col{width:49%;float:right}
.clear{clear:both}
</style>
<div class="pg-content"> <!-- Start of pago conent -->
    <form action="index.php" method="post" name="adminForm">


    <?php echo PagoHtml::module_top( JText::_( 'PAGO ORDER DETAILS' ) ) ?>
        <div class="col_wrap">
            <div class="left_col">
            <?php if( JFactory::getApplication()->input->get( 'cid', array(0), 'array' )  && !JFactory::getApplication()->input->get( 'copy' ) ): ?>
            	<?php echo JText::_('User ID') ?>: <?php echo $this->user->id ?><br />
                <?php echo JText::_('Username') ?>: <?php echo $this->user->username ?>
			<?php else: ?>
            	<?php echo $this->details ?>
            <?php endif ?>
            </div>
            <div class="right_col" style="width:425px">
           <?php echo $this->grouplist ?>
             </div>
             <div style="clear:right"></div>
         </div>
    <?php echo PagoHtml::module_bottom() ?>


	<?php echo PagoHtml::module_top( JText::_( 'PAGO ADDRESS DETAILS' ) ) ?>



        <div class="col_wrap">
            <div class="left_col">
            <h3>&nbsp;<?php echo JText::_( 'PAGO BILLING ADDRESS' ) ?></h3>
            <?php echo $this->address_billing ?>
            </div>
            <div class="right_col">
            <h3>&nbsp;<?php echo JText::_( 'PAGO MAILING ADDRESS' ) ?></h3>
             <?php echo $this->address_shipping ?>
             </div>
             <div class="clear"></div>
          </div>
        <?php echo PagoHtml::module_bottom() ?>

    	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
        <input type="hidden" name="option" value="com_pago" />
        <input type="hidden" name="task" value="cancel" />
        <input type="hidden" name="view" value="customer" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div><!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');