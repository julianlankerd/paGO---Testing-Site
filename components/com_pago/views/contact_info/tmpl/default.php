<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php
$cid = JFactory::getApplication()->input->get('cid');
$view = JFactory::getApplication()->input->get('view');
$id = JFactory::getApplication()->input->get('id');
$set = JFactory::getApplication()->input->get('set');
if ($set==1)
{
    echo JText::_('PAGO_MAIL_SEND_SUCCESSFULLY');
}
else
{
    $model = $this -> getModel('contact_info');
    $prd_name = $model->getProductName($id);
?>
<div id="contact-form">
	<div class="contact-messages">
	</div>
<form class="contact_form" name="frcontactinfo" action="<?php echo JURI::root(); ?>" method="post">
    <div class="contact_form_left_container">
        <div class = "contact_form_left">
            <label><?php echo JTEXT::_('PAGO_COMMENT_NAME'); ?><span class = "required">*</span></label>
            <div class = "input_block">
                <input type="text" name="from_name" id="from_name" value="" required />
                <div class="input_focus"></div>
            </div>

            <label><?php echo JTEXT::_('PAGO_COMMENT_EMAIL'); ?><span class = "required">*</span></label>
            <div class = "input_block">
                <input type="text" name="from_email" id="from_email" value="" required />
                <div class="input_focus"></div>
            </div>

            <label><?php echo JTEXT::_('COM_PAGO_SUBJECT'); ?></label>
            <div class = "input_block">
                <input type="text" name="subject" id="subject" value="Inquiry for <?php echo $prd_name; ?>" readonly>
                <div class="input_focus"></div>
            </div>
        </div>
    </div>
    <div class="contact_form_right_container">
        <div class="contact_form_right">
            <label><?php echo JTEXT::_('PAGO_COMMENT_MESSAGE'); ?><span class="required">*</span></label>
            <textarea name="your_message" id="your_message" cols="40" rows="10" required></textarea><br>

            <input type="submit" name='submit' class="pg-contact-submit" value="<?php echo JText::_('COM_PAGO_SEND'); ?>" />
        </div>
    </div>

	<input type="hidden" name="view" id="view" value="contact_info"/>
	<input type="hidden" name="task" id="task" value="sendcontactmail"/>
	<input type="hidden" name="option" id="option" value="com_pago"/>
	<input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>"/>
	<input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>

    <div class="clearfix"></div>
</form>
</div>
<?php
}
?>