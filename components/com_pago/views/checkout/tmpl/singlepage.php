<?php 
	/**
	 * @package paGO Commerce
	 * @author 'corePHP', LLC
	 * @copyright (C) 2015 - 'corePHP' LLC and paGO Commerce
	 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
	**/
	
	defined('_JEXEC') or die('Restricted access');
?>
<div id="pago">
	<div id="app" data-ng-app="pagoSinglePageApp" data-ng-cloak>
		<h1>Checkout</h1>
		<p data-ng-if="$root.config.checkout.checkout_welcome_message">{{ $root.config.checkout.checkout_welcome_message }}</p>
		<form name="checkoutForm" id="checkout-form" class="checkout-form row" novalidate data-ng-submit="checkout()" data-ng-controller="CartController">
			<div class="col-sm-12">
				
				<div data-ng-show="user.id && $root.userType < 2">
					Hi {{user.name}}! 
					 You can <a href="javascript:void(0);" title="log out" data-ng-click="logout();">log out</a> anytime. 
				</div>
				
				<div data-ng-show="showGuestRegistrationAlert()">
					Hi guest! You can also <a href="javascript:void(0);" title="register" data-ng-click="$root.userType = 1; $root.user.id = null">register</a> and save your data to future use, or <a href="javascript:void(0);" title="sign in" data-ng-click="$root.userType = null; $root.user.id = null">sign in</a> and load or information.
				</div>
				
			</div>
			<div class="col-sm-8">
				<div class="row">
					<pago-users class="col-sm-12"></pago-users>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<pago-addresses class="col-sm-12"></pago-addresses>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<pago-shipping class="col-sm-12"></pago-shipping>
							<pago-paygate class="col-sm-12"></pago-paygate>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="row">
					<pago-order class="col-sm-12"></pago-order>
				</div>
			</div>
		</form>
	</div>
</div>
