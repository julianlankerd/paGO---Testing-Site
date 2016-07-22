<legend>{{addressTitle}} address</legend>

<div data-ng-show="showInfo()">
	<p>{{address.address_1}} {{address.address_2}}<br>
	{{address.city}} - {{address.state}} - {{address.country}}<br>
	{{address.zip}}</p>
	<p><a class="pg-btn" href="javascript:void(0);" title="{{$root.config.language.PAGO_ACCOUNT_ADDRESSES_EDIT_ADDRESS}}" data-ng-click="address.saved = false; change = false;">{{$root.config.language.PAGO_ACCOUNT_ADDRESSES_EDIT_ADDRESS}}</a></p>
	<p><a class="pg-btn" href="javascript:void(0);" title="User another address" data-ng-click="change = true; address.saved = false; address.id = null" data-ng-show="$parent.userAddresses.length > 0">User another address</a></p>
	<label class="checkbox" data-ng-show="addressType == 's'">
		<input type="checkbox" name="same-for-billing" data-ng-model="$parent.sameForBilling">
		<span>Use as billing address</span>
	</label>
	<alert type="$parent.alert.type" message="$parent.alert.message"></alert>
</div>

<div data-ng-form="addressForm" data-ng-submit="apply( address, addressForm )" novalidate>
	
	<div data-ng-show="showSelect()">
		<label>
			<span>Chose an address</span>
			<select name="id" data-ng-model="address.id" data-ng-options="addr.id as addr.address_1 for addr in $parent.userAddresses" data-ng-change="fetch()" ng-required="showSelect()">
				<option value="">Select an option</option>
			</select>
			<div data-ng-messages="addressForm.id.$error" data-ng-show="addressForm.id.$touched || addressForm.$submitted">
				<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
			</div>
		</label>
		<!--
		<button type="button" data-ng-click="fetch();">{{$root.config.language.PAGO_CHECKOUT_ADDRESSES_USE_THIS_ADDRESS}}</button>
		-->
		<button type="button" data-ng-click="add()">{{$root.config.language.PAGO_ACCOUNT_ADDRESSES_ADD_ADDRESS_TITLE}}</button>
		
		<!--<alert type="$parent.alert.type" message="$parent.alert.message"></alert>-->
	</div>
	
	<div data-ng-show="showForm()">
		<div class="row">
			<label class="col-sm-12">
				<span>{{$root.config.language.PAGO_COMPANY}}</span>
				<input type="text" name="company" data-ng-model="address.company">
				<div data-ng-messages="addressForm.company.$error" data-ng-show="addressForm.company.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
			<label class="col-sm-12">
				<span>{{$root.config.language.PAGO_NAME}} *</span>
				<input type="text" name="name" data-ng-model="address.name" data-ng-required="!address.first_name">
				<div data-ng-messages="addressForm.name.$error" data-ng-show="addressForm.name.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
		</div>
		<div class="row">
			<label class="col-sm-12">
				<span>{{$root.config.language.PAGO_PHONE}} *</span>
				<input type="text" name="phone_1" name="phone_1" data-ng-model="address.phone_1" required>
				<div data-ng-messages="addressForm.phone_1.$error" data-ng-show="addressForm.phone_1.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
			<label class="col-sm-12">
				<span>{{$root.config.language.PAGO_EMAIL}} *</span>
				<input type="email" name="user_email" name="user_email" data-ng-model="address.user_email" data-ng-init="address.user_mail = $root.user.email" required>
				<div data-ng-messages="addressForm.user_email.$error" data-ng-show="addressForm.user_email.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
		</div>
		<label>
			<span>{{$root.config.language.PAGO_ADDRESS1}} *</span>
			<input type="text" name="address_1" data-ng-model="address.address_1" required>
			<div data-ng-messages="addressForm.address_1.$error" data-ng-show="addressForm.address_1.$touched || addressForm.$submitted">
				<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
			</div>
		</label>
		<label>
			<span>{{$root.config.language.PAGO_ADDRESS2}}</span>
			<input type="text" name="address-2" data-ng-model="address.address_2">
		</label>
		<div class="row">
			<label class="col-sm-6">
				<span>{{$root.config.language.PAGO_CITY}} *</span>
				<input type="text" name="city" data-ng-model="address.city" required>
				<div data-ng-messages="addressForm.city.$error" data-ng-show="addressForm.city.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
			<label class="col-sm-6">
				<span>{{$root.config.language.PAGO_ZIP_CODE}} *</span>
				<input type="text" name="zip" data-ng-model="address.zip" required>
				<div data-ng-messages="addressForm.zip.$error" data-ng-show="addressForm.zip.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
		</div>
		<div class="row">
			<label class="col-sm-12">
				<span>{{$root.config.language.PAGO_COUNTRY}} *</span>
				<select name="country" data-ng-model="address.country" data-ng-init="$parent.loadCountries()" data-ng-options="name for (name, abbr) in $parent.countries" required>
					<option value="">Select</option>
				</select>
				<div data-ng-messages="addressForm.country.$error" data-ng-show="addressForm.country.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
			<label class="col-sm-12">
				<span>{{$root.config.language.PAGO_STATE}} *</span>
				<select name="state" data-ng-model="address.state" data-ng-init="$parent.loadStates()" data-ng-options="name as name for (name, abbr) in $parent.states | filterCountry:address.country" required data-ng-show="showStateField()"></select>
				<input type="text" name="state" data-ng-model="address.state" required data-ng-show="!showStateField()">
				<div data-ng-messages="addressForm.state.$error" data-ng-show="addressForm.state.$touched || addressForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</label>
		</div>
		
		<label class="checkbox" data-ng-show="addressType == 's'">
			<input type="checkbox" name="same-for-billing" data-ng-model="$parent.sameForBilling">
			<span>Use as billing address</span>
		</label>
		
		<div data-ng-show="showSaveButton()">
			<button type="button" data-ng-click="$parent.apply(address, addressForm)" data-ng-disabled="loading">{{$root.config.language.PAGO_CHECKOUT_SAVE_SHIPPING_ADDRESS}}</button> 
			<a href="javascript:void(0);" data-ng-show="$parent.userAddresses.length > 0" data-ng-click="cancelAdd()" class="pg-btn pg-btn-bordered">{{$root.config.language.PAGO_CANCEL_BUTTON}}</a>
		</div>
		<div data-ng-show="loading">
			<p class="muted">Loading...</p>
		</div>
		
		<alert type="$parent.alert.type" message="$parent.alert.message"></alert>
	</div>
	
</div>