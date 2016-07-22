<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('var $MOD_PAGO_LOGIN_LOGIN_FAILED = "'.JTEXT::_("MOD_PAGO_LOGIN_LOGIN_FAILED").'"');
?>
<div class = "row">
	<div class = "col-sm-6">
		<div class="pg-login-form">
		     <div class="pg-module pg-login pg-main-container">
                <div class='pg-login-notice pg-notification-message pg-error'></div>
                <form method="post" name="login" class="pg-login-form" action="<?php echo JRoute::_( 'index.php', true); ?>">
                    <p class="pg-login-form-username">
                        <input type="text" name="username" placeholder="<?php echo JText::_('PAGO_EMAIL'); ?>" class="pg-inputbox" alt="username" size="18" />
                    </p>
                    <p class="pg-login-form-password">
                        <input type="password" name="password" placeholder="<?php echo JText::_('PAGO_PASSWORD') ?>" class="pg-inputbox" size="18" alt="password" />
                    </p>
                    
                    <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
                        <p class="pg-login-form-remember">
                            <input id="pg-login-remember" type="checkbox" name="remember" class="pg-checkbox" value="yes" alt="<?php echo JText::_('PAGO_REMEMBER_ME'); ?>" />
                            <label for="pg-login-remember"><?php echo JText::_('PAGO_REMEMBER_ME'); ?></label>
                        </p>
                    <?php endif; ?>

                    <input type="submit" name="Submit" class="pg-button" value="<?php echo JText::_('PAGO_LOGIN') ?>" />
                    
                    <?php
                    $usersConfig = JComponentHelper::getParams('com_users');
                    if ($usersConfig->get('allowUserRegistration')) : ?>
                    <div class = "pg-login-create-account">
                        <a href="<?php echo JRoute::_('index.php?option=com_pago&view=register'); ?>">
                            <?php echo JText::_('PAGO_REGISTER'); ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <input type="hidden" name="option" value="com_users" />
                    <input type="hidden" name="task" value="user.login" />
                    <?php echo JHTML::_( 'form.token' ); ?>
                </form>
            </div>
		</div>
	</div>
	<div class = "col-sm-6">
		<div id="pg-account-create">
		    <h2><?php echo JText::_('PAGO_REGISTER_TITLE'); ?></h2>
		    <div>
		        <p><?php echo JText::_('PAGO_REGISTER_DESC'); ?></p>
		        <a href="<?php echo JRoute::_('index.php?option=com_pago&view=register'); ?>" title="<?php echo JText::_('PAGO_REGISTER'); ?>" class="pg-button pg-green-text-btn">
		            <?php echo JText::_('PAGO_REGISTER'); ?>
		        </a>
		    </div>
		</div>
	</div>
</div>
<script>
jQuery(document).ready(function() {
	jQuery(document).on('click', '#pg-account-action .pg-login-form input[type=submit].pg-button', function(e){
		e.preventDefault();
		
		var username = jQuery(this).parents('form').find('input[name="username"]').val();
		var noticeCon = jQuery(this).parents('.pg-module').find( '.pg-login-notice' );
		
		var data = new Object();
		jQuery(this).parents('form').find('input').each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		})
		
		jQuery.ajax({
				type: 'POST',
				url: '',
				async: false,
				data: {option : 'com_pago', view : 'account', task: 'getUsernameFromEmail', email : username},
			success:function(response){
				if(response == "NULL"){
					jQuery(noticeCon).html($MOD_PAGO_LOGIN_LOGIN_FAILED);
					jQuery(noticeCon).fadeIn();
					return false;
				} else {
					data['tmpl'] = 'component';
					data['username'] = response;
					var url = jQuery(this).parents('form').attr('action');

					jQuery(noticeCon).html();
					jQuery.ajax({
							type: 'POST',
							url: url,
							async: false,
							data:data,
						success:function(response){
							if(response==''){
								window.location = window.location;	
								return;
							}
							systemMessage = jQuery(response).find('#system-message');
							if(typeof systemMessage === "undefined" || !systemMessage.length){
								window.location = window.location;	
								return;
							} 
							jQuery(noticeCon).html($MOD_PAGO_LOGIN_LOGIN_FAILED);
							jQuery(noticeCon).fadeIn();
						}
					});
				}
			}
		});
		
	})
})
</script>
