<?php defined( '_JEXEC' ) or die();

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
    $document = JFactory::getDocument();
    $document->addScriptDeclaration('var $PAGO_LOGIN_LOGIN_FAILED = "'.JTEXT::_("PAGO_LOGIN_LOGIN_FAILED").'"');
?>
<script>
jQuery(document).on('click', '.pg-login-form input[type=submit].pg-button', function(e){
    e.preventDefault();
    var data = new Object();
    jQuery(this).parents('form').find('input').each(function(){
        data[jQuery(this).attr('name')] = jQuery(this).val();
    })
    data['tmpl'] = 'component';
    var url = jQuery(this).parents('form').attr('action');
    var noticeCon = jQuery(this).parents('.pg-login-form').find( '.pg-login-notice' );
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
                if(jQuery('#guest-submision .redirectAfterLogin').val() == 'comment'){
                    var searchUrl = window.location.search;
                    var redurectUrl = window.location;
                    
                    if(searchUrl.indexOf("?") > '-1'){
                        redurectUrl = window.location+"&addComment=1"; 
                    }else{
                        redurectUrl = window.location+"?addComment=1"; 
                    }
                    window.location = redurectUrl;
                }else{
                    window.location = window.location;
                }
                return;
            }
            
            jQuery(noticeCon).html($PAGO_LOGIN_LOGIN_FAILED);
            jQuery(noticeCon).fadeIn();
            jQuery('.pg_checkout_notice').html(systemMessage.html())
        }
    });
})
</script>
<div class = "pg-login-form">
    <div class="pg-module pg-login pg-main-container">
        <div class='pg-login-notice'></div>
        <form method="post" name="login" class="pg-login-form">
            <p class="pg-login-form-username">
                <input type="text" name="username" placeholder="<?php echo JText::_('PAGO_GUEST_SUBMITION_MODAL_LOGIN_NAME'); ?>" class="pg-inputbox" alt="username" size="18" />
            </p>
            <p class="pg-login-form-password">
                <input type="password" name="password" placeholder="<?php echo JText::_('PAGO_GUEST_SUBMITION_MODAL_LOGIN_PASSWORD') ?>" class="pg-inputbox" size="18" alt="password" />
            </p>
            
            <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
                <p class="pg-login-form-remember">
                    <input id="pg-login-remember" type="checkbox" name="remember" class="pg-checkbox" value="yes" alt="<?php echo JText::_('PAGO_REMEMBER_ME'); ?>" />
                    <label for="pg-login-remember"><?php echo JText::_('PAGO_REMEMBER_ME'); ?></label>
                </p>
            <?php endif; ?>

            <input type="submit" name="Submit" class="pg-button pg-gray-background-btn" value="<?php echo JText::_('PAGO_MENU_LOGIN') ?>" />
        
            <div class = "pg-login-forgot-password">
                <a href="<?php echo JRoute::_( 'index.php?option=com_users&view=reset' ); ?>">
                <?php echo JText::_('PAGO_MENU_FORGOT_YOUR_PASSWORD'); ?></a>
            </div>
            
            <?php
            $usersConfig = JComponentHelper::getParams('com_users');
            if ($usersConfig->get('allowUserRegistration')) : ?>
            <div class = "pg-login-create-account">
                <a href="<?php echo JRoute::_('index.php?option=com_pago&view=register'); ?>">
                    <?php echo JText::_('PAGO_REGISTER'); ?>
                </a>
            </div>
            <?php endif; ?>
            <input type="hidden" name="redirectAfterLogin" class="redirectAfterLogin" value="0">
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <?php echo JHTML::_( 'form.token' ); ?>
        </form>
    </div>
</div>