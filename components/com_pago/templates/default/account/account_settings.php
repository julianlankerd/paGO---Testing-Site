<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
	
$this->document->setTitle( JText::_('PAGO_ACCOUNT_SETTINGS') );
$this->pathway->addItem( JText::_('PAGO_ACCOUNT_SETTINGS') , JRoute::_( '&view=account&layout=account_settings' ) );

// Load sidemenu template (account/account_menu.php)
/*if ( $menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $menu;*/

//this has to be done in preperation for tmpl_user_fields
$this->preset_number = 0;
$this->prefix = 'p';
//$this->addresses = $this->addresses[ $this->prefix ];
?>
<?php echo $this->load_header(); ?>
<div id="pg-account">
	<div id="pg-account-menu">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>
<?php 
$path       = JURI::root( true );
?>	

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".pg_upload_avatar_btn").uploadifive({
			'uploadScript':		'<?php echo $path; ?>/index.php?option=com_pago&view=account&task=uploadAvatar',
			'cancelImg':		'<?php echo $path; ?>/components/com_pago/javascript/uploadify/cancel.png',
			'sizeLimit':		'5000000',
			'simUploadLimit':	'1',
			'multi':			false,
			'auto':				true,
			'buttonText'   : '',
			onUploadComplete:	function (file, data) {
				jQuery(".uploadifive-queue").hide();
				result = JSON.parse(data);
				if(result.status == 1){
					rand = Math.random();
					image = result.filePath+'?num='+rand;
					jQuery('#pago .pg-account-info-image').css('background','url(' + image + ')');	
				}
				if(result.message){
					jQuery("#avatar_notice").html(result.message);
					jQuery("#avatar_notice").fadeIn('fast');
					setTimeout(function(){
							jQuery("#avatar_notice").fadeOut('fast');
							jQuery("#avatar_notice").html('');
						}, 2000);
				}
				
			}
		});

		jQuery(".account-edit-btn").click(function(){
			var form = jQuery(this).parents('form');;
			var data = new Object();
			var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			var errors = '';

			form.find('input').each(function(){
				data[jQuery(this).attr('name')] = jQuery(this).val();
			})

			if(!data['name'])
			{
				errors +='<div class="pg-error">Name is required</div>';
			}
			if((!filter.test(data['user_email']) && data['user_email'])) {
        		errors +='<div class="pg-error">Wrong Email format</div>';
    		}
    		
			if((data['user_email'] || data['new_email']) && data['user_email'] != data['new_email'])
			{
				errors +='<div class="pg-error">Confirm Email is wrong</div>';
			}		
    		
			if((data['txt_NewPassword'] || data['txt_ConfirmPassword']) && data['txt_NewPassword'] != data['txt_ConfirmPassword'])
			{
				errors +='<div class="pg-error">Confirm Password is wrong</div>';
			}
    		

			jQuery.ajax({
				type: 'POST',
				url: 'index.php',
				data: ({
					option: "com_pago", // global field
					controller: "account",
					task : "checkEmail",
					dataType : 'json',
					email : data['user_email'],
					userId : data['user_id'],
				}),
				success: function( response ) {
					if(response != 'NULL')
					{
						errors +='<div class="pg-error">Email is already exist</div>';
					}
					if(errors != '')
					{
						jQuery('#pg-system-messages').html("<div class='alert alert-danger'>"+errors+"</div>");
						return false;
					}
					else
					{
						jQuery('#pg-system-messages').html("");
						form.submit();
					}
				}
			});
		});
	});
</script>	
<style type="text/css">

</style>
<div id="pg-account-settings">
	<!--<p>Change your STORE ID, password, and default address (* required fields)</p>-->
	<div id="pg-system-messages"></div>
	<div id="account-store_name_id" class="inner-column pg-form-container">
		<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" enctype="multipart/form-data" name="userform" autocomplete="off" class="pg-form form-validate account-update">
			<div class = "pg-account-info-container">					
				<div class = "pg-account-info-title">
					<span><?php echo JTEXT::_('PAGO_ACCOUNT_PROFILE_INFORMATION')?></span>
				</div>
				<div class = "pg-account-info clearfix">
					<div class = "pg-account-info-left">
						<?php $avatar = PagoHelper::getAvatar(); ?>
						<div class = "pg-account-info-image" style="background: url('<?php echo $avatar['avatarPath']; ?>')">
							<div class = "pg_upload_avatar_con" >
								<div class = "pg_upload_avatar_btn" ><?php echo JTEXT::_('PAGO_ACCOUNT_UPLOAD_AVATAR'); ?></div>	
								<?php if($avatar['havaAvatar']){ ?>
									<div class = "pg_upload_avatar_delete"></div>	
								<?php } ?>
							</div>
						</div><br/>
						<label id="avatar_notice"></label>
						<!-- <input type="file" class="upload_avatar" name="avatar"/> -->
					</div>
					<!--<legend>STORE NAME ID</legend>-->
					<div class = "pg-account-info-right">
						<div class="pg-text">
							<label for="name" id="name"><?php echo JTEXT::_('PAGO_ACCOUNT_NAME'); ?></label>
							<input id="name" name="name" type="text" value="<?php echo $this->user_info->name; ?>" title="Name" />
						</div>
						<div class="pg-text">
							<label for="user_email" id="user_email"><?php echo JTEXT::_('PAGO_ACCOUNT_EMAIL'); ?></label>
							<input id="user_email" name="user_email" type="text" value="<?php echo $this->user_info->email; ?>" class="required" title="Current Email*. This is a required field" />
						</div>
						<div class="pg-text required">
							<label for="new_email" id="new_email"><?php echo JTEXT::_('PAGO_ACCOUNT_CONFIRM_EMAIL'); ?></label>
							<input id="new_email" name="new_email" type="text" value="<?php echo $this->user_info->email; ?>" title="New Email*. This is a required field" />
						</div>
						<!--<div class="pg-text required">
							<label for="new_email2" id="Re-new_email2">Re-enter New Email*</label>
							<input id="new_email2" name="new_email2" type="text" class="required" title="Re-enter New Email*. This is a required field" />
						</div>-->
						<div class="pg-password required">
							<label for="txt_NewPassword" id="NewPassword-ariaLabel"><?php echo JTEXT::_('PAGO_ACCOUNT_PASSWORD'); ?><span>*</span></label>
							<input id="txt_NewPassword" name="txt_NewPassword" type="password" aria-labelledby="NewPassword-ariaLabel" class="required" title="New Password*. This is a required field" />
						</div>
						<div class="pg-password required">
							<label for="txt_ConfirmPassword" id="ConfirmPassword-ariaLabel"><?php echo JTEXT::_('PAGO_ACCOUNT_CONFIRM_PASSWORD'); ?><span>*</span></label>
							<input id="txt_ConfirmPassword" name="txt_ConfirmPassword" type="password" aria-labelledby="ConfirmPassword-ariaLabel" class="required" title="Confirm Password*. This is a required field" />
						</div>
					</div>
				</div>
			</div>
			<div class="pg-submit">
				<input type="button" class="pg-button pg-green-text-btn account-edit-btn" value="<?php echo JTEXT::_('PAGO_ACCOUNT_UPDATE_PROFILE'); ?>" />
			</div>
			<input type="hidden" name="user_id" value="<?php echo $this->user_info->id; ?>" />
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="view" value="account" />
			<input type="hidden" name="layout" value="account_settings" />
			<input type="hidden" name="task" value="update_account" />
			<!-- <input type="hidden" name="task" value="update_password" /> -->
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div><!-- end #account-store_name_id //-->

	<!--<div class="outer column-2of2">
		<div id="account-password" class="inner-column">
			<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" name="userform" autocomplete="off" class="pg-form form-validate">
				<fieldset>
					<legend>Password</legend>
					<p>To change your password you must enter your a new password and 
					then re-enter the new password for confirmation. A good password is at least 8 
					characters long containing both numbers and letters.</p>
					<div class="row pg-password required">
						<label for="txt_NewPassword" id="NewPassword-ariaLabel">New Password*</label>
						<input id="txt_NewPassword" name="txt_NewPassword" type="password" aria-labelledby="NewPassword-ariaLabel" class="required" title="New Password*. This is a required field" />
					</div>
					<div class="row pg-password required">
						<label for="txt_ConfirmPassword" id="ConfirmPassword-ariaLabel">Confirm Password*</label>
						<input id="txt_ConfirmPassword" name="txt_ConfirmPassword" type="password" aria-labelledby="ConfirmPassword-ariaLabel" class="required" title="Confirm Password*. This is a required field" />
					</div>
					<div class="row pg-submit">
						<input type="submit" class="pg-button" value="Submit" />
						<input type="hidden" name="user_id" value="<?php echo $this->user_info->user_id; ?>" />
						<input type="hidden" name="option" value="com_pago" />
						<input type="hidden" name="view" value="account" />
						<input type="hidden" name="layout" value="account_settings" />
						<input type="hidden" name="task" value="update_password" />
						<?php echo JHTML::_( 'form.token' ); ?>
					</div>
				</fieldset>
			</form>
		</div>
	</div>--><!-- end #account-password //-->


	<!--<div id="pg-account-addresses" class="pg-account-right clearfix">
		<h2><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES'); ?></h2>
		
		<form id="pg-account-address-form" action="<?php echo JRoute::_('index.php'); ?>" method="POST">
			<div id="pg-account-address-shipping" class="pg-account-address">
				<h3><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_UPDATE_ADDRESS'); ?></h3>
				<div>
					<?php if ( $user_fields = PagoHelper::load_template( 'common', 'tmpl_user_fields' ) ) require $user_fields; ?>
					<input type="hidden" name="address[<?php echo $this->prefix; ?>][email]" value="<?php echo $this->user->email; ?>" />
					<input type="hidden" name="address[<?php echo $this->prefix; ?>][save]" value="save" />
				</div>
			</div>
			<input type="hidden" name="address[<?php echo $this->prefix; ?>][id]" value="<?php echo $addr_id; ?>" />
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="view" value="account" />
			<input type="hidden" name="task" value="update_primary_address" />
			<?php echo JHTML::_( 'form.token' ) ?>
			<button type="submit" class="pg-button"><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_UPDATE_ACCOUNT_BUTTON'); ?></button>
		</form>
		<form action="<?php echo JRoute::_('index.php'); ?>" method="POST">
			<input type="hidden" name="id" value="<?php echo $addr_id; ?>" />
			<input type="hidden" name="option" value="com_pago" />
			<input type="hidden" name="view" value="account" />
			<input type="hidden" name="task" value="delete_address" />
			<?php echo JHTML::_( 'form.token' ) ?>
			<button type="submit" class="pg-button"><?php echo JText::_('PAGO_ACCOUNT_ADDRESSES_REMOVE_ADDRESS_BUTTON'); ?></button>
		</form>
	</div>-->

</div><!-- end #pg-account_settings //-->
</div>

<?php echo $this->load_footer(); ?>