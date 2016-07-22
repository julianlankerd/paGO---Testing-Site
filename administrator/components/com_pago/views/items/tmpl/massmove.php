<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/styles.css' );

if(!isset($msg))
{
$post = JFactory::getApplication()->input->get('catStr', '', "string");

if(isset($post)) {
    $catStr = $post;
}

?>
<script type="text/javascript">
function AssignCategory()
{
	var form = document.adminForm;
	var catVal = document.getElementById('assign_primary_category').value;
	 
	if(catVal == '')
	{
		alert('<?php echo JText::_('COM_PAGO_PLEASE_SELECT_CATEGORY');?>');
		return false;
	}
	 else
	 {
	 	form.task.value = 'AssignCategory';
		form.submit();
	 }
	
}
</script>
    <?php if(isset($catStr)){ ?>
    <div style="margin-top:100px;">
        <form action="<?php echo JRoute::_('index.php?option=com_pago&view=' . $this->get( '_name' ) ); ?>" method="post" name="adminForm" id="adminForm">
        <div class="itemAssignCat" style="font-size:13px;padding-top: 5px;"> 
                                        <div class="assignCatDropBox" style="float:left">
                                        <select name="assign_primary_category" class="inputbox" id="assign_primary_category" style="width:150px;">
                                                <option value=""><?php echo JText::_('PAGO_SEL_ASSIGN_PRIMARY_CAT');?></option>
                                                <?php echo JHtml::_('select.options', $this->categories, 'value', 'text');?>
                                        </select>
                                        </div>
                                        <div id="pg-button-reset" class="pg-button pg-button-grey pg-button-clear assignCatDropButton " tabindex="0" style="float:left;padding-left: 12px;"><button onclick="return AssignCategory()" type="button"  onclick=";this.form.submit();"><?php echo JText::_('PAGO_MOVE_ITEM'); ?></button></div>
                 </div> 
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="controller" value="items" />

        <input type="hidden" name="cid" value="<?php echo $catStr; ?>" />
        <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
    <?php } ?>
<?php }
