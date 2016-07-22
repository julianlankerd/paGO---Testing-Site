<?php defined('_JEXEC') or die('Restricted access');
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

$user_data = JFactory::getApplication()->input->get('user_data', array(0), 'array');
$user_addresses = JFactory::getApplication()->input->get('user_address', array(0), 'array');
PagoHtml::uniform();

?>
<div id="pg-account">
	<div id="pg-account-dashboard" class="pg-account-dashboard">
			<div id="pg-account-info" class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_PROFILE'); ?></h3>

				<?php if( $user_data ) : ?>
				<div class = "clearfix">
					<div class = "clearfix">
						<?php $avatar = PagoHelper::getAvatar($user_data[0]['id']); ?>
						<div class = "pg-account-info-image" style="background: url('<?php echo $avatar['avatarPath']; ?>')">
							<!-- <div class = "pg_upload_avatar_con" >
								<div class = "pg_upload_avatar_btn" ><?php echo JTEXT::_('PAGO_ACCOUNT_UPLOAD_AVATAR'); ?></div>	
								<?php if($avatar['havaAvatar']){ ?>
									<div class = "pg_upload_avatar_delete"><?php echo JTEXT::_('PAGO_ACCOUNT_UPLOAD_DELETE'); ?></div>	
								<?php } ?>
							</div> -->	
						</div>
						
						<div class = "pg-account-info-left">
							<div class="pg-account-name-label">
								<?php echo JTEXT::_('PAGO_CUSTOMER_NAME'); ?>
							</div>
							<div class="pg-account-email-label">
								<?php echo JTEXT::_('PAGO_USER_EMAIL'); ?>
							</div>
							<div class="pg-account-registered-date-label">
								<?php echo JTEXT::_('PAGO_ACCOUNT_REGISTERED_DATE'); ?>
							</div>
							<div class="pg-account-last-visited-date-label">
								<?php echo JTEXT::_('PAGO_ACCOUNT_LAST_VISITED_DATE'); ?>
							</div>
						
						</div>
						<div class = "pg-account-info-right">
							<div class="pg-account-name" userid="<?php echo $user_data[0]['id']; ?>">
								<?php echo $user_data[0]['name']; ?>
							</div>
							<div class="pg-account-email">
								<?php echo $user_data[0]['email']; ?>
							</div>
							<div class="pg-account-registered-date">
								<?php 
									$date = strtotime($user_data[0]['registerDate']);
									$new_date = date('l, d F Y', $date);

									echo $new_date; 
								?>
							</div>
							<div class="pg-account-last-visited-date">
								<?php 
									$date = strtotime($user_data[0]['lastvisitDate']);
									$new_date = date('l, d F Y', $date);

									echo $new_date;
								?>
							</div>
						</div>
					</div>

				<?php else : ?>
					<h3><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_ACCOUNT_INFO'); ?></h3>
					<div>
						<p><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_CREATE_ACCOUNT_MESSAGE'); ?><br /><a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&layout=addresses'); ?>" title="<?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_CREATE_ACCOUNT_LINK'); ?>"><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_CREATE_ACCOUNT'); ?></a>
						</p>
				<?php endif; ?>
				</div>
			</div>
			<div>			
				<div class="cshipping">
					<?php
						$selectList = '<select class = "pg-checkout-shipping-addresses">';
						$addressesBlocks = '';
					?>
                    <div class="pg-checkout-shipping-addresses pg-wrapper-container clearfix">
                        <h3><?php echo JText::_('PAGO_SHIPPING_ADDRESS'); ?></h3>
                        <div class="cshipping_select">
	                        <?php 
	                        	$s = 0;
	                        	foreach( $user_addresses as $user_address ){
	                        		if($user_address->address_type == 's'){
		                        		$s = 1;
		                        		break;
		                        	}
		                    	}

		                    	$selectList .= $s == 0 ? '<option value="0" selected="selected">'.JTEXT::_('PAGO_CHECKOUT_SELECT_SHIPPING_ADDRESS').'</option>' : '';
	                
	                        	foreach( $user_addresses as $user_address ){
	                        		if($user_address->address_type == 's'){
										//changed
				                        $checked = '';
				                        $selected = '';
									
											$selectList .= '<option address_id="'.$user_address->id.'">'.$user_address->address_1.'</option>';
										if($s == 1){
											$addressesBlocks .= '<div class="pg-checkout-shipping-address address_block_'.$user_address->id.'" >';
											$s=2;
										}
										else{
											$addressesBlocks .= '<div class="pg-checkout-shipping-address address_block_'.$user_address->id.'" style="display:none">';
										}
				                        
				                        	$addressesBlocks .= '<fieldset class="pg-fieldset pg-shipping-address-fieldset">';
												$addressesBlocks .= '<div class="pg-shipping-address-first-name">';
													$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_FIRST_NAME').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->first_name.'</span>';
			                        			$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-shipping-address-last-name">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_LAST_NAME').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->last_name.'</span>';
		                        				$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-shipping-address-street">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_ADDRESS1').': </span>';
				                        			$addressesBlocks .= $user_address->address_1;
				                        		$addressesBlocks .= '</div>';
				                        
						                        if( !empty( $user_address->address_2 ) ){
						                        	$addressesBlocks .= '<div class="pg-shipping-address-street">';
						                        		$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_ADDRESS2').': </span>';
						                        		$addressesBlocks .= $user_address->address_2;
						                        	$addressesBlocks .= '</div>';
						                        }
				                        		
				                        		$addressesBlocks .= '<div class="pg-shipping-address-city">';
				                        			$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_CITY').': </span>';
				                        			$addressesBlocks .= '<span>'.$user_address->city.'</span>'; 
			                        			$addressesBlocks .= '</div>';

			                        			if (!empty($user_address->state)){
			                        				$addressesBlocks .= '<div class="pg-shipping-address-state">';
			                        					$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_STATE').': </span>';
				                       					$addressesBlocks .= '<span>'.$user_address->state.'</span>';
		                        					$addressesBlocks .= '</div>';
				                       			}

				                       			$addressesBlocks .= '<div class="pg-shipping-address-zip">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_ZIP').': </span>';
				                        			$addressesBlocks .= '<span>'.$user_address->zip.'</span>';
				                        		$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-shipping-address-country">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_COUNTRY').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->country.'</span>';
			                        			$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-shipping-address-phone">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_SHOPPER_FORM_PHONE').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->phone_1.'</span>';
			                        			$addressesBlocks .= '</div>';
			                        		$addressesBlocks .= '</fieldset>';
		                        			$addressesBlocks .= '<a href="#" addr_id="'.$user_address->id.'" class="pg-checkout-edit-link pg-green-text-btn">'.JText::_('PAGO_EDIT').'</a>';
		                        			$addressesBlocks .= '<a href="#" addr_id="'.$user_address->id.'" class="pg-checkout-delete-link pg-green-text-btn s">'.JText::_('PAGO_DELETE').'</a>';
				                        $addressesBlocks .= '</div>';
	                        		}
		                        } 
	                        $selectList .= '<option addr_type="s">'.JTEXT::_('PAGO_ADD_ADDRESS').'</option>';

	                        	$selectList .= '</select>';
	                        	echo $selectList;
                        	?>                        	
                        </div>
                        <?php if($addressesBlocks != ""){echo "<hr/>";} ?>
                        <div class = "shipping-addresses-list">
                        		<?php echo $addressesBlocks; ?>
                        </div>
                    </div>
				</div>
				<div class="cbilling">
					<?php
						$selectList = '<select class = "pg-checkout-billing-addresses">';
						$addressesBlocks = '';
					?>
                    <div class="pg-checkout-billing-addresses pg-wrapper-container clearfix">
                        <h3><?php echo JText::_('PAGO_BILLING_ADDRESS'); ?></h3>
                        <div class="cbilling_select">
	                        <?php
	                        	$s = 0;
	                        	foreach( $user_addresses as $user_address ){
	                        		if($user_address->address_type == 'b'){
		                        		$s = 1;
		                        		break;
		                        	}
		                    	}
		                    	$selectList .= $s == 0 ? '<option>'.JTEXT::_('PAGO_CHECKOUT_SELECT_BILLING_ADDRESS').'</option>' : '';

	                        	foreach( $user_addresses as $user_address ){
									if($user_address->address_type == 'b'){
										//changed
				                        $checked = '';
				                        $selected = '';
									
											$selectList .= '<option address_id="'.$user_address->id.'">'.$user_address->address_1.'</option>';
											if($s == 1){
												$addressesBlocks .= '<div class="pg-checkout-billing-address address_block_'.$user_address->id.'">';
												$s=2;
											}
											else{
												$addressesBlocks .= '<div class="pg-checkout-billing-address address_block_'.$user_address->id.'" style="display:none">';
											}
				                        
				                        	$addressesBlocks .= '<fieldset class="pg-fieldset pg-billing-address-fieldset">';
												$addressesBlocks .= '<div class="pg-billing-address-first-name">';
													$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_FIRST_NAME').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->first_name.'</span>';
			                        			$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-billing-address-last-name">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_LAST_NAME').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->last_name.'</span>';
		                        				$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-billing-address-street">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_ADDRESS1').': </span>';
				                        			$addressesBlocks .= $user_address->address_1;
				                        		$addressesBlocks .= '</div>';
				                        
						                        if( !empty( $user_address->address_2 ) ){
						                        	$addressesBlocks .= '<div class="pg-billing-address-street">';
						                        		$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_ADDRESS2').': </span>';
						                        		$addressesBlocks .= $user_address->address_2;
						                        	$addressesBlocks .= '</div>';
						                        }
				                        		
				                        		$addressesBlocks .= '<div class="pg-billing-address-city">';
				                        			$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_CITY').': </span>';
				                        			$addressesBlocks .= '<span>'.$user_address->city.'</span>'; 
			                        			$addressesBlocks .= '</div>';

			                        			if (!empty($user_address->state)){
			                        				$addressesBlocks .= '<div class="pg-billing-address-state">';
			                        					$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_STATE').': </span>';
				                       					$addressesBlocks .= '<span>'.$user_address->state.'</span>';
		                        					$addressesBlocks .= '</div>';
				                       			}

				                       			$addressesBlocks .= '<div class="pg-billing-address-zip">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_ZIP').': </span>';
				                        			$addressesBlocks .= '<span>'.$user_address->zip.'</span>';
				                        		$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-billing-address-country">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_USER_COUNTRY').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->country.'</span>';
			                        			$addressesBlocks .= '</div>';
			                        			$addressesBlocks .= '<div class="pg-billing-address-phone">';
			                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_SHOPPER_FORM_PHONE').': </span>';
			                        				$addressesBlocks .= '<span>'.$user_address->phone_1.'</span>';
			                        			$addressesBlocks .= '</div>';
			                        		$addressesBlocks .= '</fieldset>';
				                        	$addressesBlocks .= '<a href="#" addr_id="'.$user_address->id.'" class="pg-checkout-edit-link pg-green-text-btn">'.JText::_('PAGO_EDIT').'</a>';
				                        	$addressesBlocks .= '<a href="#" addr_id="'.$user_address->id.'" class="pg-checkout-delete-link pg-green-text-btn b">'.JText::_('PAGO_DELETE').'</a>';
				                        $addressesBlocks .= '</div>';
									}
		                        } 
	                         $selectList .= '<option addr_type="b">'.JTEXT::_('PAGO_ADD_ADDRESS').'</option>';
	                        	$selectList .= '</select>';
	                        	echo $selectList;
                        	?>
                        	
                        </div>
                        <?php if($addressesBlocks != ""){echo "<hr/>";} ?>
                        <div class = "billing-addresses-list">
                        	<?php echo $addressesBlocks;?>
                        </div>
                    </div>
				</div>
			</div>
	</div>
</div>
<script>
jQuery(document).ready(function() {
	jQuery('select').chosen({'disable_search': true, 'disable_search_threshold': 6 });
	jQuery('.pg-checkout-billing-addresses, .pg-checkout-shipping-addresses').change(function() {
		if(jQuery(this).val() == 'Add Address')
		{
			var user_id = jQuery('.pg-account-name').attr('userid');
			var addr_type = jQuery(this).find('option:selected'); 
			addr_type = addr_type.attr('addr_type');
			jQuery.ajax({
		    	type: "POST",
		    	url: 'index.php',
				data: 'option=com_pago&view=customers&task=getAddAddress&async=1',
		    	success: function(response){
		    		jQuery('#pg-account').html(response);
					if(addr_type == 'b'){
						jQuery('.pg-account-addresses > h3').html("<? echo JText::_('PAGO_ACCOUNT_ADDRESSES_BILLING_TITLE'); ?>");
						jQuery('.addr_type').val('b');
					}else{
						jQuery('.pg-account-addresses > h3').html("<? echo JText::_('PAGO_ACCOUNT_ADDRESSES_SHIPPING_TITLE'); ?>");
						jQuery('.addr_type').val('s');
					}
					jQuery('.user_id').val(user_id);
		    	}
			});
		}
		else{
			if(jQuery(this).val() != "Select a Shipping address" && jQuery(this).val() != "Select a Billing address"){
				var address_id = jQuery(this).find('option:selected'); 
					address_id = address_id.attr('address_id');
				var block_class = ".address_block_"+address_id;
				jQuery(block_class).parent().children().css('display','none');
				jQuery(block_class).css('display', 'block');
			}
		}
	});

	jQuery('.pg-checkout-edit-link').click(function() {
		var addr_id = jQuery(this).attr('addr_id');
		jQuery.ajax({
	    	type: "POST",
	    	url: 'index.php',
			data: 'option=com_pago&view=customers&task=getAddAddress&addr_id='+addr_id+'&async=1',
	    	success: function(response){
	    		jQuery('#pg-account').html(response);
				jQuery('.user_id').val('<?php echo $user_data[0]["id"] ?>');
	    	}
		});
	});

	jQuery('.pg-checkout-delete-link').click(function() {
		var addr_id = jQuery(this).attr('addr_id');
		var user_id = jQuery('.pg-account-name').attr('userid');
		jQuery.ajax({
	    	type: "POST",
	    	url: 'index.php',
			data: 'option=com_pago&view=customers&task=romoveAddress&id='+addr_id,
	    	success: function(response){
	    		if(response == 1){
	    			jQuery.ajax({
				    	type: "POST",
				    	url: 'index.php',
						data: 'option=com_pago&view=customers&task=getCustomerAccount&userId='+user_id+'&async=1',
				    	success: function(response){
				    		jQuery('.add_billing_adress').html(response);
				    		jQuery('.add_billing_adress').fadeIn();
				    	}
					});		
	    		}
	    	}
		});
	});
});
</script>
