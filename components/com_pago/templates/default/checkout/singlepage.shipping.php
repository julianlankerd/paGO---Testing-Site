<fieldset class="" data-ng-form="shipperForm" data-ng-submit="applyShipper(); saved = true;" novaldiate>
	<div data-ng-if="$root.user.id && 0 == $root.skipShipping">
		<legend>{{$root.config.language.PAGO_ACCOUNT_ORDER_SHIPPING_METHOD_LABEL}}</legend>
		
		<div data-ng-show="$root.user.id">
			<p data-ng-show="$root.addresses == null || $root.addresses.length < 2">{{$root.config.language.PAGO_CHECKOUT_SELECT_SHIPPING_ADDRESS}}</p>
			
			<div data-ng-show="showShippingOptions()">
				<div class="form-group" data-ng-repeat="(name, s) in $root.shippers">
					<p><strong>{{name}}</strong></p>
					<label class="radio" data-ng-repeat="option in s">
						<input type="radio" name="shipperCode" data-ng-model="$root.shipper" data-ng-value="option" data-ng-change="option.text = $parent.name">
						<span>{{option.name}} - {{option.format_value}}</span>
					</label>
				</div>
				<div data-ng-messages="shipperForm.shipperCode.$error" data-ng-show="shipperForm.shipperCode.$touched || shipperForm.$submitted">
					<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
				</div>
			</div>
			
			<div data-ng-show="loading">
				<p class="muted">Loading...</p>
			</div>
			
			<div data-ng-show="showShippingNotice()">
				<p>{{$root.config.language.PAGO_OPC_NO_SHIP}}</p>
			</div>
			
			<div data-ng-show="$parent.saved">
				<p>{{shipper.name}}<br>
				{{cart.format.shipping}}</p>
				<p><button type="button" title="{{$root.config.language.PAGO_CHECKOUT_CHANGE}} {{$root.config.language.PAGO_ACCOUNT_ORDER_SHIPPING_METHOD_LABEL}}" data-ng-click="$parent.saved = false">{{$root.config.language.PAGO_CHECKOUT_CHANGE}} {{$root.config.language.PAGO_ACCOUNT_ORDER_SHIPPING_METHOD_LABEL}}</button></p>
			</div>
		</div>
	</div>
</fieldset>