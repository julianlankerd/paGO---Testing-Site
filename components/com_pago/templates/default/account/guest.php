<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); ?>
<?php $this->load_header(); ?>
<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang = &JFactory::getLanguage();
		$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
		$langScript = 	'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var comlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration( $langScript );
		JHTML::_('script', 'openid.js');
endif; 
?>
<div id="pg-account">
	<h1><?php echo JText::_('PAGO_ACCOUNT_MY_ACCOUNT'); ?></h1>
	<div id="pg-account-action">
	    <?php $this->register_view = 'account'; ?>
    	<?php if ( $login_register = PagoHelper::load_template( 'common', 'tmpl_login_register' ) ) require $login_register; ?>
        <!-- For future feature of order tracking without account.
        <div id="pg-account-track">
        </div>
        -->
    </div>
</div>
<?php $this->load_footer(); ?>
