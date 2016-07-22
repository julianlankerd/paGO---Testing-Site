<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<div class = "row">
	<div class = "col-sm-6">
		<span class = "submition-modal-module-title"><?php echo JTEXT::_('PAGO_GUEST_SUBMITION_MODAL_LOGIN_TITLE'); ?></span>
		<?php require PagoHelper::load_template( 'common', 'tmpl_login' ); ?>
	</div>
	<div class = "col-sm-6">
		
		<form name="addComment" id="pg-addComment" method="post" action="<?php echo JRoute::_( 'index.php' ) ?>">
			<span class = "submition-modal-module-title"><?php echo JTEXT::_('PAGO_GUEST_SUBMITION_MODAL_GUEST_TITLE'); ?></span>
			
			<div class = "input_block">
				<input type="text" name="comment_name" placeholder="<?php echo JTEXT::_('PAGO_COMMENT_NAME'); ?>" value='<?php echo $this->user->guest ? '':$this->user->name?>' <?php echo $this->user->guest ? '':'disabled="disabled"'?>>	
				<div class="input_focus"></div>
			</div>

			<div class = "input_block">
				<input type="text" name="comment_email" placeholder="<?php echo JTEXT::_('PAGO_COMMENT_EMAIL'); ?>" value='<?php echo $this->user->guest ? '':$this->user->email?>' <?php echo $this->user->guest ? '':'disabled="disabled"'?>>	
				<div class="input_focus"></div>
			</div>

			<div class = "input_block">
				<input type="text" name="comment_web_site" <?php echo $this->user->guest ? '':'disabled="disabled"'?> placeholder="<?php echo JTEXT::_('PAGO_COMMENT_WEBSITE'); ?>">
				<div class="input_focus"></div>
			</div>

			<input type="hidden" name="comment_userId" value='<?php echo $this->user->guest ? '0':$this->user->id?>'>

			<textarea rows="4" cols="50" name="comment_message" placeholder="<?php echo JTEXT::_('PAGO_COMMENT_MESSAGE'); ?>"></textarea><br/>
			<input type="hidden"  name="comment_parentId" value="0" >
			<input type="button" class="addCommentBtn pg-green-text-btn" name="addCommentBtn" value="<?php echo JTEXT::_('PAGO_COMMENT_POST_REVIEW');?>" >
		</form>
	</div>
</div>