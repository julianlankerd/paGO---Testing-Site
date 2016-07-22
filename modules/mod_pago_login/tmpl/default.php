<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php if($type == 'logout') : ?>
	<div class="pg-module<?php echo $params->get( 'moduleclass_sfx' ) ?> pg_logedin pg-main-container clearfix">

        <div class="pg_logoutBtn clearfix">
                <span class="pg_user_image pull-left">
                   <?php $avatar = PagoHelper::getAvatar(); ?>
                   <img src='<?php echo $avatar['avatarPath']; ?>'>
               </span>
               <span class="pg-login-greeting  pull-left">
                <?php if ($params->get('name')) : {
                    echo $user->email;
                } else : {
                    echo $user->name;
                } endif; ?>

            </span>
            <span class="pg-logoutBox-toggle"><i class="fa  fa-chevron-down"></i></span>
        </div>
        <div class="pg_logoutBox clearfix">
            <div class="pg-logout-dropdown">
                <?php if ($params->get('link_to_dashboard', 1)) : ?>
                <p><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account' ); ?>"><?php echo JText::_( 'PAGO_DASHBOARD_TITLE' ); ?></a></p>
            <?php endif; ?>
            <?php if ($params->get('link_to_account_settings', 1)) : ?>
            <p><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=account_settings' ); ?>"><?php echo JText::_( 'MOD_PAGO_ACCOUNT_SETTINGS' ); ?></a></p>
        <?php endif; ?>
        <?php if ($params->get('link_to_billing_settings', 1)) : ?>
        <p><a href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_history' ); ?>"><?php echo JText::_( 'MOD_PAGO_BILLING_SETTINGS' ); ?></a></p>
    <?php endif; ?>
</div>
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure', 0)); ?>" method="post" name="login" class="pg-login-form">
    <button type="submit" class="pg-logout"><?php echo JText::_( 'PAGO_MENU_BUTTON_LOGOUT'); ?></button>
    <input type="hidden" name="option" value="com_users" />
    <input type="hidden" name="task" value="user.logout" />
    <input type="hidden" name="return" value="<?php echo $return; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>

</div>

<?php else : ?>

	<?php if(JPluginHelper::isEnabled('authentication', 'openid')) {
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
      ' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'PAGO_MENU_WHAT_IS_OPENID' ).'\';'.
      ' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'PAGO_MENU_LOGIN_WITH_OPENID' ).'\';'.
      ' JLanguage.NORMAL_LOGIN = \''.JText::_( 'PAGO_MENU_NORMAL_LOGIN' ).'\';'.
      ' var modlogin = 1;';
      $document = JFactory::getDocument();
      $document->addScriptDeclaration( $langScript );
      JHTML::_('script', 'openid.js');
  } ?>
  <?php if($params->get('dropdown', 0)): ?>
  <div class="pg_dropdown_login pg-module<?php echo $params->get( 'moduleclass_sfx' ) ?> clearfix">
   <span class="pg_loginBtn">Login</span>
   <div class="pg_dropdown">
   <?php endif; ?>
   <div class="pg-module pg-login pg-main-container pg-login-module " >
    <div class='pg-login-notice pg-notification-message pg-error'></div>
    <form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure', 0)); ?>" method="post" name="login" class="pg-login-form" >
        <p class="pg-login-form-username">
            <input type="text" name="username" placeholder="<?php echo JText::_('MOD_PAGO_LOGIN_EMAIL') ?>" class="pg-inputbox" alt="username" size="18" />
        </p>
        <p class="pg-login-form-password">
            <input type="password" name="password" placeholder="<?php echo JText::_('MOD_PAGO_LOGIN_PASSWORD') ?>" class="pg-inputbox" size="18" alt="password" />
        </p>
        <?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
        <p class="pg-login-form-remember">
            <input id="pg-mod-login-remember" type="checkbox" name="remember" class="pg-checkbox" value="yes" alt="Remember Me" />
            <label for="pg-mod-login-remember"><?php echo JText::_('MOD_PAGO_REMEMBER_ME') ?></label>
        </p>
    <?php endif; ?>

    <input type="submit" name="Submit" class="pg-button" value="<?php echo JText::_('PAGO_LOGIN') ?>" />

    <?php if ($params->get('forgot', 0)) : ?>
    <div class="pg-login-forgot-password">
        <a href="<?php echo JRoute::_( 'index.php?option=com_users&view=reset' ); ?>">
            <?php echo JText::_('PAGO_MENU_FORGOT_YOUR_PASSWORD'); ?></a>
        </div>
    <?php endif; ?>

    <?php
    $usersConfig = JComponentHelper::getParams('com_users');
    if ($usersConfig->get('allowUserRegistration')) : ?>
    <div class = "pg-login-create-account">
        <a href="<?php echo JRoute::_('index.php?option=com_pago&view=register'); ?>">
            <?php echo JText::_('PAGO_REGISTER'); ?>
        </a>
    </div>
    <?php endif;
    ?>

    <input type="hidden" name="option" value="com_users" />
    <input type="hidden" name="task" value="user.login" />
    <input type="hidden" name="return" value="<?php echo $return; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
<?php if($params->get('dropdown', 0)): ?>
</div>
</div>
<?php endif; ?>

<?php endif; ?>
