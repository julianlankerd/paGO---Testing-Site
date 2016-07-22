<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php


$cid = JFactory::getApplication()->input->getInt('cid', array(0), 'array');
$view = JFactory::getApplication()->input->getInt('view');
$id = JFactory::getApplication()->input->getInt('id');

$payment_error = false;
if(isset($this->payment_error) && $this->payment_error)
{
    $payment_error = true;
}

$prd_name = 'MiscError';

if($id){
$model = $this -> getModel('contact_info');
$prd_name = $model->getProductName($id);
} else {
    //this basically means there is some sort of error popup
    //that is not associated with a product. Maybe an error
    //with paGO configuration
    $payment_error = false;
    $cid = false;
}
    
?>
 <div class="contact-messages">
    </div>
<div id="contact-form">
<form class="contact_form" name="frcontactinfo" action="<?php echo JURI::root(); ?>" method="post">
        <div class="contact_form_left_container">
            <div class = "contact_form_left">
                <label><?php echo JTEXT::_('PAGO_COMMENT_NAME'); ?><span class = "required">*</span></label>
                <div class = "input_block">
                    <input type="text" name="from_name" id="from_name" class="required" value="" required />
                    <div class="input_focus"></div>
                </div>

                <label><?php echo JTEXT::_('PAGO_COMMENT_EMAIL'); ?><span class = "required">*</span></label>
                <div class = "input_block">
                    <input type="text" name="from_email" id="from_email" class="required"  value="" required />
                    <div class="input_focus"></div>
                </div>
               <?php if(!$payment_error){ ?>

                <label><?php echo JTEXT::_('COM_PAGO_SUBJECT'); ?></label>
                <div class = "input_block">
                    <input type="text" name="subject" id="subject" value="Inquiry for <?php echo $prd_name; ?>" readonly>
                    <div class="input_focus"></div>
                </div>
            <?php
            }
            else{
            ?>
             <label><?php echo JTEXT::_('COM_PAGO_SUBJECT'); ?></label>
                <div class = "input_block">
                    <input type="text" name="subject" id="subject" value="<?php echo JTEXT::_('COM_PAGO_CHECKOUT_PAYMENT_ERROR'); ?>" readonly>
                    <div class="input_focus"></div>
                </div>
        <?php   } ?>
            </div>
        </div>
        <div class="contact_form_right_container">
            <div class="contact_form_right">
                <label><?php echo JTEXT::_('PAGO_COMMENT_MESSAGE'); ?><span class="required">*</span></label>
                <textarea name="your_message" id="your_message" cols="40" rows="10" class="required"  required></textarea><br>
                <input type="submit" name='submit1' class="pg-contact-submit" value="<?php echo JText::_('COM_PAGO_SEND'); ?>" />
            </div>
        </div>


        <?php if(!$payment_error): ?>
            <input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>"/>
            <input type="hidden" name="view" id="view" value="contact_info"/>
            <input type="hidden" name="task" id="task" value="sendcontactmail"/>
            <input type="hidden" name="option" id="option" value="com_pago"/>
            <input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>"/>
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>"/>
            <?php if ($prd_name == 'MiscError'): ?>
                <input type="hidden" name="holy_wtf_batman" value="Ssad_but_true"/>
            <?php endif ?>
            
        <?php else:?>
            <input type="hidden" name="view" id="view" value="contact_info"/>
            <input type="hidden" name="task" id="task" value="sendPyamenErrorEmail"/>
            <input type="hidden" name="option" id="option" value="com_pago"/>
        <?php endif;?>
        <div class="clearfix"></div>

    </form>

</div>
