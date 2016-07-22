<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

$this->document->setTitle( JText::_('PAGO_ACCOUNT_DASHBOARD_TITLE') );
$this->pathway->addItem( JText::_('PAGO_ACCOUNT_DASHBOARD_HOME'), JRoute::_( '&view=account' ) );
?>

<?php echo $this->load_header(); ?>
<?php 

// $path       = JURI::root( true );
$this->document->addScriptDeclaration('
		var billingUrl="'.JRoute::_("index.php?option=com_pago&view=account&layout=add_address&addr_type=b").'"
		var shippingUrl="'.JRoute::_("index.php?option=com_pago&view=account&layout=add_address&addr_type=s").'"
');
?>
<div id="pg-account">
	<!--<div class = "pg-account-header">
		<h2 class = "pg-account-dashboard-title"><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_TITLE'); ?></h2>
		<p class = "pg-account-dashboard-desc"><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_DESC'); ?></p>
	</div>-->
		<div id="pg-account-menu">
			<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
			<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
		</div>
		<div id="pg-account-dashboard">
			<div id="pg-account-info" class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_PROFILE'); ?></h3>

				<?php if( $this->user_info ) : ?>
				<div class = "clearfix">
					<!--<dl class="clearfix">
						<dt><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_ACCOUNT_ID'); ?>:</dt>
							<dd><?php echo $this->user_info->email;?></dd>
						<dt><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_ACCOUNT_NUM'); ?>:</dt>
							<dd><?php echo $this->user_info->id; ?></dd>
						<dt><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_ADDRESS'); ?>:</dt>
							<dd>
								<?php echo $this->user_info->address_1; ?><br />
								<?php echo ( $this->user_info->address_2 ) ? $this->user_info->address_2 . '<br />' : ''; ?>
								<?php echo $this->user_info->city; ?>, <?php echo $this->user_info->state; ?> <?php echo $this->user_info->zip; ?> <?php echo $this->user_info->country; ?>
							</dd>
					</dl>-->

					<div class = "clearfix">
						<?php $avatar = PagoHelper::getAvatar(); ?>
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
								<?php echo JTEXT::_('PAGO_ACCOUNT_DASHBOARD_NAME'); ?>
							</div>
							<div class="pg-account-email-label">
								<?php echo JTEXT::_('PAGO_ACCOUNT_DASHBOARD_EMAIL'); ?>
							</div>
							<div class="pg-account-registered-date-label">
								<?php echo JTEXT::_('PAGO_ACCOUNT_DASHBOARD_REGISTERED_DATE'); ?>
							</div>
							<div class="pg-account-last-visited-date-label">
								<?php echo JTEXT::_('PAGO_ACCOUNT_DASHBOARD_LAST_VISITED_DATE'); ?>
							</div>
						
						</div>
						<div class = "pg-account-info-right">
							<div class="pg-account-name">
								<?php echo $this->user_info->name; ?>
							</div>
							<div class="pg-account-email">
								<?php echo $this->user_info->email; ?>
							</div>
							<div class="pg-account-registered-date">
								<?php 
									$date = strtotime($this->user_info->registerDate);
									$new_date = date('l, d F Y', $date);

									echo $new_date; 
								?>
							</div>
							<div class="pg-account-last-visited-date">
								<?php 
									$date = strtotime($this->user_info->lastvisitDate);
									$new_date = date('l, d F Y', $date);

									echo $new_date;
								?>
							</div>
						</div>
					</div>
					<!-- <label id="avatar_notice"></label> -->
					<div class = "edit-profile">
						<a class = "pg-green-text-btn" title="<?php echo JText::_( 'PAGO_ACCOUNT_DASHBOARD_EDIT_PROFILE' ); ?>" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=account_settings' ); ?>"><?php echo JText::_( 'PAGO_ACCOUNT_DASHBOARD_EDIT_PROFILE' ); ?></a>
					</div>

				<?php else : ?>
					<h3><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_ACCOUNT_INFO'); ?></h3>
					<div>
						<p><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_CREATE_ACCOUNT_MESSAGE'); ?><br /><a href="<?php echo JRoute::_('index.php?option=com_pago&view=account&layout=addresses'); ?>" title="<?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_CREATE_ACCOUNT_LINK'); ?>"><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_CREATE_ACCOUNT'); ?></a>
						</p>
				<?php endif; ?>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-6">
					<?php
						$saved_addresses = array();
						$addresses = array();
						$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
						$saved_addresses  = $user_model->get_user_shipping_addresses();

						$selectList = '<select class = "pg-checkout-shipping-addresses">';
						$addressesBlocks = '';
					?>
                    <div class="pg-checkout-shipping-addresses pg-wrapper-container clearfix">
                        <h3><?php echo JText::_('PAGO_SHIPPING_ADDRESS'); ?></h3>
                        <div>
	                        <?php 
	                           if(!$saved_addresses){
	                           		$selectList .= '<option value="0" selected="selected">'.JTEXT::_('PAGO_CHECKOUT_SELECT_SHIPPING_ADDRESS').'</option>';
	                           }
	                        	foreach( $saved_addresses as $user_address ){
										//changed
			                        $checked = '';
			                        $selected = '';
								
										$selectList .= '<option>'.$user_address->address_1.'</option>';
									
			                        $addressesBlocks .= '<div class="pg-checkout-shipping-address">';
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
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_ADDRESS1').': </span>';
			                        			$addressesBlocks .= $user_address->address_1;
			                        		$addressesBlocks .= '</div>';
			                        
					                        if( !empty( $user_address->address_2 ) ){
					                        	$addressesBlocks .= '<div class="pg-shipping-address-street">';
					                        		$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_ADDRESS2').': </span>';
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
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_ZIP_CODE').': </span>';
			                        			$addressesBlocks .= '<span>'.$user_address->zip.'</span>';
			                        		$addressesBlocks .= '</div>';
		                        			$addressesBlocks .= '<div class="pg-shipping-address-country">';
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_COUNTRY').': </span>';
		                        				$addressesBlocks .= '<span>'.$user_address->country.'</span>';
		                        			$addressesBlocks .= '</div>';
		                        			$addressesBlocks .= '<div class="pg-shipping-address-phone">';
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_PHONE').': </span>';
		                        				$addressesBlocks .= '<span>'.$user_address->phone_1.'</span>';
		                        			$addressesBlocks .= '</div>';
		                        		$addressesBlocks .= '</fieldset>';
	                        			$addressesBlocks .= '<a href="'.JRoute::_('index.php?option=com_pago&view=account&layout=edit_address&addr_id=' . $user_address->id).'" class="pg-checkout-edit-link pg-green-text-btn">'.JText::_('PAGO_EDIT').'</a>';
			                        $addressesBlocks .= '</div>';
		                        } 
	                        $selectList .= '<option addr_type="s">'.JTEXT::_('PAGO_ADD_ADDRESS').'</option>';

	                        	$selectList .= '</select>';
	                        	echo $selectList;
                        	?>
                        	<div class = "shipping-addresses-list">
                        		<?php echo $addressesBlocks; ?>
                        	</div>
                        </div>
                    </div>
				</div>
				<div class="col-sm-6">
					<?php
						$saved_addresses = array();
						$addresses = array();
						$user_model = JModelLegacy::getInstance( 'Customers', 'PagoModel' );
						$saved_addresses  = $user_model->get_user_billing_addresses();

						$selectList = '<select class = "pg-checkout-billing-addresses">';
						$addressesBlocks = '';
					?>
                    <div class="pg-checkout-billing-addresses pg-wrapper-container clearfix">
                        <h3><?php echo JText::_('PAGO_BILLING_ADDRESS'); ?></h3>
                        <div>
	                        <?php
	                        if(!$saved_addresses){
		                        $selectList .= '<option>'.JTEXT::_('PAGO_CHECKOUT_SELECT_BILLING_ADDRESS').'</option>';
	                        } 
	                        	foreach( $saved_addresses as $user_address ){
										//changed
			                        $checked = '';
			                        $selected = '';
								
										$selectList .= '<option>'.$user_address->address_1.'</option>';
									
			                        $addressesBlocks .= '<div class="pg-checkout-billing-address">';
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
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_ADDRESS1').': </span>';
			                        			$addressesBlocks .= $user_address->address_1;
			                        		$addressesBlocks .= '</div>';
			                        
					                        if( !empty( $user_address->address_2 ) ){
					                        	$addressesBlocks .= '<div class="pg-billing-address-street">';
					                        		$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_ADDRESS2').': </span>';
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
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_ZIP_CODE').': </span>';
			                        			$addressesBlocks .= '<span>'.$user_address->zip.'</span>';
			                        		$addressesBlocks .= '</div>';
		                        			$addressesBlocks .= '<div class="pg-billing-address-country">';
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_COUNTRY').': </span>';
		                        				$addressesBlocks .= '<span>'.$user_address->country.'</span>';
		                        			$addressesBlocks .= '</div>';
		                        			$addressesBlocks .= '<div class="pg-billing-address-phone">';
		                        				$addressesBlocks .= '<span class = "pg-address-field-name">'.JTEXT::_('PAGO_PHONE').': </span>';
		                        				$addressesBlocks .= '<span>'.$user_address->phone_1.'</span>';
		                        			$addressesBlocks .= '</div>';
		                        		$addressesBlocks .= '</fieldset>';
			                        	$addressesBlocks .= '<a href="'.JRoute::_('index.php?option=com_pago&view=account&layout=edit_address&addr_id=' . $user_address->id).'" class="pg-checkout-edit-link pg-green-text-btn">'.JText::_('PAGO_EDIT').'</a>';
			                        $addressesBlocks .= '</div>';
		                        } 
	                         $selectList .= '<option addr_type="b">'.JTEXT::_('PAGO_ADD_ADDRESS').'</option>';
	                        	$selectList .= '</select>';
	                        	echo $selectList;
                        	?>
                        	<div class = "billing-addresses-list">
                        		<?php echo $addressesBlocks; ?>
                        	</div>
                        </div>
                    </div>
				</div>
			</div>

			<div id="pg-account-recent-orders" class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_RECENT_ORDER_STATUS'); ?> <span>(<a title="View All Recent Order Status" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_history&all=1' ); ?>"><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_VIEW_ALL'); ?></a>)</span></h3>
				<div>
					<?php if ( $recent_table = PagoHelper::load_template( 'account', 'order_recent_table' ) ) require $recent_table; ?>
				</div>
			</div>
			<div id="pg-account-recent-purchased" class = "pg-wrapper-container">
				<h3><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_RECENT_PURCHASED'); ?> <span>(<a title="View All Recently Purchased Products" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=products_purchased' ); ?>"><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_VIEW_ALL'); ?></a>)</span></h3>
				<div>
					<?php if ( !$this->orders_item ) : ?>
					<p><?php echo JText::_( 'PAGO_ACCOUNT_DASHBOARD_NO_RECENT_PURCHASES' ); ?></p>
					<?php else : ?>
					<table class="pg-account-table">
						<tbody>
						<tr>
							<th><?php echo JTEXT::_('PAGO_DATE'); ?></th>
							<th><?php echo JTEXT::_('PAGO_NAME'); ?></th>
							<th><?php echo JTEXT::_('PAGO_ORDER_NUMBER'); ?></th>
							<th></th>
						</tr>
						<?php foreach ( $this->orders_item as $item ) { ?>
							<tr>
								<td class="pg-account-recent-purchase-date">
									<?php echo date( 'n/j/Y', strtotime( $item->created ) ); ?>
								</td>
								<td class="pg-account-recent-purchase-item">
								<?php
								$Itemid = $this->nav->getItemid($item->id, $item->primary_category);
								$link = JRoute::_('index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->primary_category .'&Itemid=' . $Itemid);
								?>
									<a title="<?php echo $item->name; ?>" target="_blank" href="<?php echo $link; ?>">
										<?php echo $item->name; ?>
									</a>
								</td>

								<td class="pg-account-recent-purchase-item-number"><?php echo $item->sku; ?>
									<?php
										$order 	= Pago::get_instance('orders')->get($item->order_id);
										
										if($item->type == '2' || $order['details']->order_status == 'C')
										{
											$downloadRes = $this->nav->getMediaInfo($item->id);
											
											if(count($downloadRes) > 0)
											{
												for($k = 0; $k < count($downloadRes); $k++)
												{
													 if( $downloadRes[$k]->access == 2)
													 {
														echo "<br/>";
														$link = 'index.php?option=com_pago&controller=item&task=downloadFiles&fileid=' . $downloadRes[$k]->id;
														echo "<a class='downloadLink' href='" . $link . "'>" . JTEXT::_("PAGO_DOWNLOAD") . $downloadRes[$k]->file_name . "</a>";
													}
													
											
												}
											}
										}
									?>
								</td>

								<td class="pg-account-recent-purchase-invoice">
									<a title="View Invoice (new window)" target="_blank" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_receipt&order_id=' . $item->order_id ); ?>">
										<?php echo JTEXT::_("PAGO_VIEW_ORDER"); ?>
									</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php echo $this->load_footer(); ?>
