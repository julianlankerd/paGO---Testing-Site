<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>

<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.0/css/font-awesome.css" rel="stylesheet">

<?php

defined('_JEXEC') or die('Restricted access');
$task = JFactory::getApplication()->input->get( 'task' );
$doc = JFactory::getDocument();
$uri = str_replace( '/administrator','',JURI::base(true) );

//PagoHtml::behaviour_jquery( 'jqueryui' );
PagoHtml::tooltip();
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHtml::_('behavior.keepalive');
JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');

$users_l = json_encode($this->all_users);
$doc->addScriptDeclaration('var $USERS = '.$users_l.';');
PagoHtml::pago_top( $menu_items );
?>
<div class="pg-content pg-customers"> <!-- Start of pago conent -->
<div class="pg-customers-inner">
	<button class="btn new_user">REGISTER NEW USER</button>
	<button class="btn existing_user">CHOOSE EXISTING USER</button>
	<div class="register_new_user">
		<div class="registration pg_user_register" style="display:none">
			<div id="pg-system-messages"></div>
			<form id='adminForm' method="post" class="form-horizontal" enctype="multipart/form-data">
				<div style="margin-top: 25px;">
					<div>
						<label id="jform_name-lbl" for="jform_name" class="hasTip required" title="Name::Enter your name">
							Name:<span class="star">&nbsp;*</span>
						</label>
					</div>
					<input autocomplete="off" type="text" name="jform[name]" class="required" id="jform_name1" value="" size="30">
				</div>
				<div>
					<div>
						<label id="jform_email-lbl" for="jform_email" class="hasTip required" title="Email Address::Enter your email address">
							Email Address:<span class="star">&nbsp;*</span>
						</label>
					</div>
					<input autocomplete="off" type="text" name="jform[email]" class="validate-email required" id="jform_email1" value="" size="30">
				</div>
				<div>
					<div>
						<label id="jform_password-lbl" for="jform_password" class="hasTip required" title="Password::Enter your desired password - Enter a minimum of 4 characters">
							Password:<span class="star">&nbsp;*</span>
						</label>
					</div>
					<input type="password" name="jform[password1]" id="jform_password" value="" autocomplete="off" class="validate-password required" size="30">
				</div>
				<div>
					<div>
						<label id="jform_password-lbl" for="jform_password" class="hasTip required" title="Confirm Password::Confirm your password">
							Confirm Password:<span class="star">&nbsp;*</span>
						</label>
					</div>
					<input type="password" name="jform[password2]" id="jform_password" value="" autocomplete="off" class="validate-password required" size="30">
				</div>
				
				<div class="pg-register-hidden-fields">
					<input type="hidden" name="option" value="com_pago" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="view" value="customers" />
					<?php echo JHtml::_('form.token');?>
				</div>
				<div class="pg-register-button-container">
		            <button type="button" class="pg-register-button btn"><?php echo JTEXT::_('PAGO_NEXT'); ?></button>
		        </div>
			</form>
		</div>
	</div>
	<div class="existing_users_details" style="display:none">
		<div>
			<div>
				<label for="users_list" class="hasTip required" title="Select user">
					Search user by name or email
				</label>
			</div>
			<div class="existing_users_details_input">
				<i class="fa fa-search"></i>
					<input type="text" class="ui-autocomplete-input" id="users-list-add" name="users_list" autocomplete="off" aria-autocomplete="list" aria-haspopup="true">
				<i class="fa fa-times"></i>
			</div>
		</div>
	</div>
	<div class="add_billing_adress" style="display:none">
	</div>
	</div>
</div><!-- end pago content -->
<?php echo JHTML::_('behavior.keepalive');?>

<script>
jQuery( document ).ready(function() {
		jQuery(document).on("keyup","#users-list-add",function(){
			jQuery(this).autocomplete({
				source : $USERS,
		        messages: {
			        noResults: "",
			        results: function() {}
			    },
		    	minLength : 1,
		        select : function(event, ui) {
		        	getUserAccount(ui.item.value);
		        	return false;
		        },
		    });
		});

		jQuery.urlParam = function(name){
	    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	    if (results==null){
	       return null;
	    }
		    else{
		       return results[1] || 0;
		    }
		}
	 
		if(jQuery.urlParam('user_id')){
			getUserAccount(jQuery.urlParam('user_id'));
		}
	})

</script>
