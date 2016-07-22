<?php defined( '_JEXEC' ) or die();
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>
<div class = "row">
	<div class = "col-sm-12">
		<span class = "submition-modal-module-title"><?php echo JTEXT::_('PAGO_GUEST_SUBMITION_MODAL_LOGIN_TITLE'); ?></span>
		<?php require PagoHelper::load_template( 'common', 'tmpl_login' ); ?>
		<div class="pg-login-forgot-password text-center">
			<br>
			<a href="javascript:void(0);" data-dismiss="modal" aria-label="Close">Cancel</a>
		</div>
	</div>
</div>