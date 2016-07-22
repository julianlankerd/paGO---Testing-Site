<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');  ?>

<div class="pg-content"> <!-- Start of pago conent -->
    <form action="index.php" method="post" name="adminForm">
		<?php echo PagoHtml::module_top( JText::_( 'PAGO_MAIN_PARAMETERS' ) ) ?>
            <?php echo $this->params->render( 'params', 'params' ) ?>
        <?php echo PagoHtml::module_bottom() ?>

        <?php echo PagoHtml::module_top( JText::_( 'PAGO_CUSTOM_PARAMETERS' ) ) ?>
            <?php echo $this->params->render( 'custom', 'custom' ) ?>
        <?php echo PagoHtml::module_bottom() ?>

    	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
        <input type="hidden" name="option" value="com_pago" />
        <input type="hidden" name="task" value="cancel" />
        <input type="hidden" name="view" value="location" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>
</div><!-- end pago content -->
<?php //echo JHTML::_('behavior.keepalive');
