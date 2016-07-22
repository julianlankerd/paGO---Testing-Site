<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
$dispatcher = KDispatcher::getInstance();

PagoHtml::add_js( JURI::base() . 'components/com_pago/javascript/com_pago_config.js' );
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs', false );

$c = $this->customer;

?>
<?php PagoHtml::deploy_tabpanel( 'tabs' ) ?>

	<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">

        <div class="pg-tabs">
            <!-- Nav tabs -->
            <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header" role="tablist">
                <li role="presentation" class="active ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab" class="ui-tabs-anchor">paGO QuickPay</a></li>
                <li role="presentation" class="ui-state-default ui-corner-top"><a href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab" class="ui-tabs-anchor">3rd Party Gateways</a></li>
            </ul>

        </div>

        <style>
            .pg-pad-20 {
                padding-bottom: 0px !important;
            }
        </style>
        <!-- Tab panes -->
        <div class="tab-content tabs-content pg-pad-20 pg-white-bckg pg-border">
            <!-- Instant Pay -->
            <div role="tabpanel" class="ui-tabs-panel ui-widget-content ui-corner-bottom tab-pane active" id="tab-1">
                <div class="pg-tab-content">
                    <div class="pg-row">
                        <div class="pg-col-6">
                            <?php
                                $payoptions = $this->params->data['params']->get('payoptions');

                                $bgcolor = '#e5ffe5';

                                if(!@$payoptions->active){
                                    $bgcolor = '#eee';
                                } elseif(!$payoptions->livemode){
                                    $bgcolor = '#ffe5e5';
                                }

                            ?>
                            <div id="pago-payoptions" style="background:<?php echo $bgcolor ?>">
                            <?php
					            echo $this->params->render_quickpay( 'params', 'payoptions_livetoggle', JText::_( 'PAGO_GENERAL_CONFIGURATION' ), 'general-configuration pg-pad-20 pg-border','no' );
					       ?>
					       </div>
					       <?php if(!@$payoptions->active) : ?>
					       <div class="pg-mt-20 pg-border" style="background: #ffe5e5; padding: 20px !important;">
					           <span class="icon-warning" style="font-size:24px; float: left; display: inline-block; height: 24px; line-height: 37px; margin-right: 20px; width: 24px;"></span> 
					           <p style="margin: 0;">paGO QuickPay is currently not active and unable to accept payments. Please activate to start accepting payments</p>
					       </div>
					       <?php endif; ?>
					       <br>
					       <div id="payoptions_test" style="display:none">


                                    <ul class="nav nav-tabs">
                                        <li class="nav active"><a href="#pago_po_account" data-toggle="tab">Account</a></li>
                                        <li class="nav"><a href="#pago_po_legal_entity" data-toggle="tab">Legal Entity</a></li>
                                        <li class="nav"><a href="#pago_po_banking" data-toggle="tab">Banking</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade in active" id="pago_po_account">
                                            <?php echo $this->params->render_config(
                				                'params', 'payoptions',
                				                0,
                				                'general-configuration pg-pad-20','no'
                				            ); ?>
                                        </div>

                                        <div class="tab-pane fade" id="pago_po_legal_entity">
                                            <div id="verification_live">
                    					       	<?php echo $this->params->render_config(
                					       		    'params',
                					       		    'payoptions_verification',
                					       		    0,
                					       		    'general-configuration pg-pad-20','no'
                    					       	);  ?>
        					               </div>
                                        </div>

                                        <div class="tab-pane fade" id="pago_po_banking">
                                            <?php echo $this->params->render_config(
                                                'params',
                                                'payoptions_banking',
                                                0,
                                                'general-configuration pg-pad-20','no'
                                            ); ?>
                                        </div>
                                    </div>

                            </div>

					       <div id="payoptions_live" style="display:none">


                                    <ul class="nav nav-tabs">
                                        <li class="nav active"><a href="#A" data-toggle="tab">Account</a></li>
                                        <li class="nav"><a href="#B" data-toggle="tab">Legal Entity</a></li>
                                        <li class="nav"><a href="#C" data-toggle="tab">Banking</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade in active" id="A">
                                            <?php echo $this->params->render_config(
                				                'params', 'payoptions_live',
                				                JText::_( 'PAGO_GENERAL_CONFIGURATION' ),
                				                'general-configuration pg-pad-20','no'
                				            ); ?>
                                        </div>

                                        <div class="tab-pane fade" id="B">
                                            <div id="verification_live">
                    					       	<?php echo $this->params->render_config(
                					       		    'params',
                					       		    'payoptions_verification_live',
                					       		    JText::_( 'PAGO_GENERAL_CONFIGURATION' ),
                					       		    'general-configuration pg-pad-20','no'
                    					       	);  ?>
        					               </div>
                                        </div>

                                        <div class="tab-pane fade" id="C">
                                            <?php echo $this->params->render_config(
                                                'params',
                                                'payoptions_banking_live',
                                                JText::_( 'PAGO_GENERAL_CONFIGURATION' ),
                                                'general-configuration pg-pad-20','no'
                                            ); ?>
                                        </div>
                                    </div>
                            </div>

					       <!--
					       <div id="payoptions_live" style="display:none">
					       <?php
					            //echo $this->params->render_config( 'params', 'payoptions_live', JText::_( 'PAGO_GENERAL_CONFIGURATION' ), 'general-configuration pg-pad-20 pg-border','no' );
					       ?>
					       </div>
					       -->

					        <?php
					            echo $this->params->render_config( 'params', 'payoptions_control', JText::_( 'PAGO_GENERAL_CONFIGURATION' ), 'general-configuration pg-pad-20 pg-border','no' );
					       ?>
					       <br>

                        </div>
                        <div class="pg-col-6">
                            <div class="quickpay-desc">

                                <p><img src="<?php echo JURI::root(); ?>administrator/components/com_pago/css/images/paGO-QP-<?php echo mt_rand(1,3); ?>.jpg" alt="paGO Quickpay"></p>

                                <p>Quickly get the funds transferred into your account in less
                                than 2 business days*  after making the sale. No more waiting 3-4 business days. 
                                No more managing your funds through clunky interfaces. 
                                paGO QuickPay is simple and straightforward. Fill in the 
                                fields and click submit. Within seconds you will be up and running. 
                                paGO QuickPay - a better way to get paid.</p>

                                <ul>
                                    <li>3.45% + 35¢ per successful charge</li>
                                    <li>No Monthly fees. You only get charged when you earn money through paGO QuickPay</li>
                                    <li>No hidden fees. Thats right - no other fees, no monthly fees, no card storage fees, nothing.</li>
                                </ul>
                                
                                <p>* United States Companies are set to a 2 business day rolling cycle and all other countries are 7 business day rolling cycle. 
                                <a target="_blank" href="https://www.corephp.com/contact">Contact us</a> for further questions.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gateway -->
            <div role="tabpanel" class="ui-tabs-panel ui-widget-content ui-corner-bottom tab-pane" id="tab-2">
                <div class="pg-tab-content">

                    <div class="accordion" id="accordion2">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                                    <h3>Account Setup<i style="float:right;" class="fa fa-chevron-down"></i></h3>
                                </a>
                            </div>
                            <?php
                                $in = false;
                                if(!$c->get('id')){
                                    $in = 'in';
                                }
                            ?>
                            <div id="collapseOne" class="accordion-body collapse <?php echo $in ?>">
                                <div class="accordion-inner">
                                    <div style="margin:20px 0px 20px 0">
                                        Set up your customer account. Enter your details here and then activate a payment gateway
                                         by accessing it's individual configuration area.
                                    </div>
                                    <div class="pg-row">
                                        <div class="pg-col-6">
                							<h3>Billing Details</h3>
                                            <hr class="ui-separator">
                                            <div class="pg-row">
                                                <div class="pg-col-12">
                                                    <label for="label1">Name on Card</label>
                                                    <input type="text" id="pago_customer_name" value="<?php echo $c->get('name', 'John Doe') ?>">
                                                    <label for="label2">Email Address</label>
                                                    <input type="text" id="pago_customer_email" value="<?php echo $c->get('email', 'me@example.com') ?>">
                                                    <label for="label3">Telephone Number</label>
                                                    <input type="text" id="pago_customer_phone"  value="<?php echo $c->get('phone', '123123123') ?>">
                                                </div>
                                            </div>
                						</div>
                						<div class="pg-col-6">
                							<h3>Credit Card</h3>
                							<hr class="ui-separator"/>
                							<div class="pg-row">
                								<div class="pg-col-8">
                									<label for="credicard">Credit Card Number</label>
                									<input type="text" id="pago_customer_number" value="<?php echo $c->get('number', '4111111111111111') ?>">
                								</div>
                								<div class="pg-col-4">
                									<label for="security">CVV</label>
                									<input type="text" id="pago_customer_cvc"  value="<?php echo $c->get('cvc', '123') ?>">
                								</div>
                							</div>
                							<div class="pg-row">
                								<div class="pg-col-12"><label>Expiration Date</label></div>
                								<div class="pg-col-6">
                									<select id="pago_customer_exp_month" class="inputbox pg-left pg-mb-20" >

                                                        <?php $months = cal_info(0);
                                                        $selected = false;
                                                        foreach($months['months'] as $value=>$name):
                                                            if($c->get('exp_month') == $value) $selected = 'selected="selected"';
                                                        ?>
                                                            <option <?php echo $selected ?> value="<?php echo $value ?>"><?php echo $name ?></option>
                                                        <?php
                                                            $selected = false;
                                                        endforeach ?>

                                                    </select>
                								</div>
                								<div class="pg-col-6">
                									<select id="pago_customer_exp_year" class="inputbox pg-left pg-mb-20" >
                                                        <?php 
                                                        $curYear = date("Y");
                                                        $selected = false;
                                                        for ($x = $curYear; $x < $curYear + 15; $x++):
                                                            if($c->get('exp_year') == $x) $selected = 'selected="selected"';
                                                        ?>
                                                            <option <?php echo $selected ?> value="<?php echo $x ?>"><?php echo $x ?></option>
                                                        <?php
                                                            $selected = false;
                                                        endfor ?>

                                                    </select>
                								</div>
                							</div>
                                            <div class="pg-col-12">
                                                 <input id="pago_customer_id" value="<?php echo $c->get('id') ?>" type="hidden" />
                                                <span class="field-heading"><label for="params_payoptions_recipient_id_display" class="">
                	                            Customer ID: <span style="color:red" id="pago_customer_id_text"><?php echo $c->get('id', 'Your customer ID will show here after successfully adding your credit card.') ?></span></label></span>

                                                <div style="margin-top:15px;text-align:right">
                									<button id="pago_customer_button" class="pg-btn-large pg-btn-light pg-btn-green" onclick="paGOapi.customers.post();return false">
                									    Validate/Update Account
                									</button>
                									<?php
                									    $delete_style = 'display:none';
                									    if($c->get('id')) $delete_style = '';
                									?>
                									&nbsp;
                									<button style="<?php echo $delete_style ?>" id="pago_customer_button_delete" class="pg-btn-large pg-btn-light pg-btn-red" onclick="paGOapi.customers.delete();return false">
                									    Delete Account
                									</button>
                									<span style="display:none" id="pago_customer_saving">&nbsp;&nbsp;Saving&nbsp;&nbsp;
                									    <span class="spin">&nbsp;</span>
                									</span>
                							    </div>

                                            </div>
                						</div>
                					</div>
                                </div>
                            </div>
                        </div>
                    </div>

					<div style="margin:20px 0px 20px 0">
					    Each of these API’s are end points managed by paGO Commerce Gateway System.
					    We update when the vendors update so you don’t have to worry about it and your store
					    will continue to operate. So why is there a monthly cost to use these payment processors?
					    Simple: We are providing a robust and continually managed service to ensure that your payments are successfully
					    processed and the code used on your site is fully up to date.
					</div>

                    <div class="pg-row">

                        <?php foreach($this->paygates as $pg): ?>

                        <div class="pg-col-3">
                            <?php
                                $enabled_class = 'ui-box-disabled';
                                $status_ind_class = 'fa fa-times';
                                $price = '$'.$pg->price;

                                if($pg->enabled){
                                    $enabled_class = 'ui-box-enabled';
                                    $status_ind_class = 'fa fa-check-square-o';
                                    $price = '';
                                }

                                if(@$pg->enabled->cancel_at_period_end){
                                    $price = 'Unsubscribed!';
                                }
                            ?>
                            <div id="<?php echo $pg->id ?>-ui-box" class="ui-box <?php echo $enabled_class ?>">
                                <img src="<?php echo $pg->img ?>" alt="<?php echo $pg->id ?>">
                                <hr class="ui-separator" />
                                <div id="<?php echo $pg->id ?>-ui-indicator" class="pg-row">
                                    <div class="pg-col-6 ui-status-indicator"><i class="<?php echo $status_ind_class ?>"><?php echo $price ?></i></div>
                                    <div class="pg-col-6 text-right"><a href="javascript:void(0)" data-toggle="modal" data-target="#<?php echo $pg->id ?>">Configure</a></i>
                                    </div>
                                </div>
                            </div>
							<!-- Box 1 Modal -->
							<div style="height:600px;overflow:auto" class="modal fade ui-modal" id="<?php echo $pg->id ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $pg->id ?>Modal">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h1 class="modal-title" id="<?php echo $pg->id ?>Modal"><?php echo $pg->name ?></h1>
										</div>
										<div class="modal-body">
											<div class="pg-row">

												<div class="pg-col-6">
													<h3>Configuration
													<button id="pago_pg_save_<?php echo $pg->id ?>" class="pg-btn-medium pg-btn-light pg-btn-green" onclick="paGOapi.config.save('<?php echo $pg->id ?>');return false">
                									    Save
                									</button>
                									<span id="pago_pg_save_spin_<?php echo $pg->id ?>" style="display:none">&nbsp;&nbsp;Saving&nbsp;&nbsp;
                            									    <span class="spin">&nbsp;</span>
                            						</h3>
                                                    <hr class="ui-separator">
                                                    <div class="pg-row">
                                                        <div class="pg-col-12">
                                                            <form id="pg_config_form_<?php echo $pg->id ?>">
                                                            <?php foreach($pg->fields as $field=>$value): ?>

                                                                <?php if(is_array($value)): ?>
                                                                    <label for="label1"><?php echo ucfirst($field) ?></label>
                                                                    <?php $checked='';foreach($value as $val):

                                                                            if(strstr($val, '*')){
                                                                                $checked='checked';
                                                                                $val = str_replace('*', '', $val);
                                                                            }

                                                                    ?>
                                                                        <input style="display:inline" type="radio" name="<?php echo $field ?>" value="<?php echo $val ?>" <?php echo $checked ?> />&nbsp;<?php echo $val ?>&nbsp;
                                                                    <?php $checked='';endforeach ?>
                                                                    <br /><br />

                                                                <?php elseif(is_bool($value)):
                                                                    $true='checked';
                                                                    $false='checked';

                                                                    if($value) $false = false;
                                                                    else $true = false;
                                                                ?>
                                                                    <label for="label1"><?php echo ucfirst($field) ?></label>
                                                                    <input style="display:inline" type="radio" name="<?php echo $field ?>" value="1" <?php echo $true ?> />&nbsp;Yes&nbsp;
                                                                    <input style="display:inline" type="radio" name="<?php echo $field ?>" value="0" <?php echo $false ?> />&nbsp;No
                                                                    <br /><br />

                                                                <?php else: ?>
                                                                    <label for="label1"><?php echo ucfirst($field) ?></label>
                                                                    <input type="text" name="<?php echo $field ?>" value="<?php echo $value ?>" />
                                                                <?php endif ?>

                                                            <?php endforeach ?>
                                                            </form>
                                                        </div>
                                                    </div>
												</div>

												<div class="pg-col-6">
													<h3>Subscription</h3>
                                                    <hr class="ui-separator">
                                                    <div class="pg-row">
                                                        <div class="pg-col-12">

                                                            <?php
                                                                $pago_pg_yescc = 'display:none';
                                                                $pago_pg_nocc = '';
                                                                $user_subscribed = !empty($pg->enabled);

                                                                $subscribe_disable = 'display:none';
                                                                $resubscribe_disable = 'display:none';
                                                                $unsubscribe_disable = 'display:none';

                                                                if(@$pg->enabled->cancel_at_period_end){
                                                                    $resubscribe_disable = '';
                                                                } elseif($user_subscribed) {
                                                                    $unsubscribe_disable = '';
                                                                } else {
                                                                    $subscribe_disable = '';
                                                                }

                                                                if($c->get('id')){
                                                                    $pago_pg_yescc = '';
                                                                    $pago_pg_nocc = 'display:none';
                                                                }
                                                            ?>
                                                            <div style="<?php echo $pago_pg_yescc ?>" class="pago_pg_yescc">

                                                                <div style="<?php echo $subscribe_disable ?>" id="<?php echo $pg->id ?>_subscribe_group">
                                                                    <div>
                                                                        Subscribing will give you immediate access to this Paygate.
                                                                    </div>
                                                                    <br />
                                                                    <button id="<?php echo $pg->id ?>_subscribe_button" class="pg-btn-medium pg-btn-light pg-btn-green" onclick="paGOapi.payum.post('<?php echo $pg->id ?>', false);return false">
                                									    Subscribe
                                									</button>
                                								</div>

                            									<div style="<?php echo $resubscribe_disable ?>" id="<?php echo $pg->id ?>_resubscribe_group">
                            									    <div>
                                                                        You have until the end of this pay period to re-subscribe otherwise you will be revoked access from this Paygate.
                                                                    </div>
                                                                    <br />
                                									<button style="" id="<?php echo $pg->id ?>_subscribe_undelete_button" class="pg-btn-medium pg-btn-light pg-btn-green" onclick="paGOapi.payum.post('<?php echo $pg->id ?>');return false">
                                									    Resume Subscription
                                									</button>
                                								</div>

                                								<div style="<?php echo $unsubscribe_disable ?>" id="<?php echo $pg->id ?>_unsubscribe_group">
                                								    <div>
                                                                        Thanks! You are now subscribed to this to this Paygate. Subscription cancellation will only come into effect at the end of the current pay period.
                                                                    </div>
                                                                    <br />
                                									<button style="" id="<?php echo $pg->id ?>_subscribe_delete_button" class="pg-btn-medium pg-btn-light pg-btn-red" onclick="paGOapi.payum.delete('<?php echo $pg->id ?>');return false">
                                									    Cancel Subscription
                                									</button>
                                								</div>
                                								<input type="hidden" id="<?php echo $pg->id ?>_subscription_id" value="<?php echo @$pg->enabled->id ?>" />
                            									<br />
                            									<span style="display:none" id="<?php echo $pg->id ?>_subscribe_saving">&nbsp;&nbsp;Saving&nbsp;&nbsp;
                            									    <span class="spin">&nbsp;</span>
                            									</span>
                        									</div>

									                        <div style="<?php echo $pago_pg_nocc ?>" class="pago_pg_nocc">
									                            Please verify your credit card so that you can subscribe to this paygate. Return here after verfiying your credit card.
									                        </div>


                                                        </div>
                                                    </div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

										</div>
									</div>
								</div>
							</div>
                        </div>
                        <?php endforeach ?>

                    </div>
                </div>
            </div>

        </div>
    </div>

<?php PagoHtml::pago_bottom();
