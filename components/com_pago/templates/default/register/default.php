<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$this->load_header();

$version = new JVersion();
$document = JFactory::getDocument();
$document->addScriptDeclaration('var $JOOMLA_VERSION = "'.$version->RELEASE.'"');

$security_level = $this->config->get( 'checkout.checkout_register_security', 0 );

if($security_level == 1){
	$plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
	$params = new JRegistry($plugin->params);
	
	$captcha_public_key = $params->get('public_key',0);
}

?>
<div class="registration pg_user_register pg-form-container">
<?php if ($this->tmpl_params->get('show_page_heading')) : ?>
	<h1><?php echo $this->escape($this->tmpl_params->get('page_heading')); ?></h1>
<?php endif; ?>
	<div id="pg-system-messages"></div>
	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
		<div>
			<div>
				<label id="jform_name1-lbl" for="jform_name1" class="hasTip required" title="Name::Enter your name">
					Name:<span class="star">&nbsp;*</span>
				</label>
			</div>
			<input autocomplete="off" type="text" name="jform[name1]" class="required" id="jform_name1" value="" size="30">
		</div>
		<div>
			<div>
				<label id="jform_email1-lbl" for="jform_email1" class="hasTip required" title="Email Address::Enter your email address">
					Email Address:<span class="star">&nbsp;*</span>
				</label>
			</div>
			<input autocomplete="off" type="text" name="jform[email1]" class="validate-email required" id="jform_email1" value="" size="30">
		</div>
		<div>
			<div>
				<label id="jform_password1-lbl" for="jform_password1" class="hasTip required" title="Password::Enter your desired password - Enter a minimum of 4 characters">
					Password:<span class="star">&nbsp;*</span>
				</label>
			</div>
			<input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password required" size="30">
		</div>
		<div>
			<div>
				<label id="jform_password2-lbl" for="jform_password2" class="hasTip required" title="Confirm Password::Confirm your password">
					Confirm Password:<span class="star">&nbsp;*</span>
				</label>
			</div>
			<input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password required" size="30">
		</div>
		<?php if($security_level == 1): ?>
			<div id="pago_captcha">
				<div id="pago_register_recaptcha"></div>
			</div>
			<script>Recaptcha.create('<?php echo $captcha_public_key ?>', "pago_register_recaptcha");</script>
		<?php endif ?>
		<div class="pg-register-hidden-fields">
			<input type="hidden" name="hp" id="jform_hp">
			<input type="hidden" name="jform[email2]" id="jform_email2" size="30">
			<input type="hidden" name="jform[name]" id="jform_name">
			<input type="hidden" name="jform[username]" id="jform_username">
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="task" value="register" />
			<input type="hidden" name="view" value="register" />
			<?php echo JHtml::_('form.token');?>
		</div>
		<div class="pg-register-button-container">
            <button type="button" class="pg-register-button"><?php echo JTEXT::_('PAGO_REGISTER_BUTTON'); ?></button>
        </div>
	</form>
</div>