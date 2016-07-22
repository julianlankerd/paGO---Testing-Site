<fieldset class="" data-ng-form="paygateForm">
	<div data-ng-if="$root.user.id && ( 1 == $root.skipShipping || 0 == $root.skipShipping && $root.shipper )">
		
		<div data-ng-show="saved && user.id">
			<legend>{{$root.config.language.PAGO_CHECKOUT_PAYMENT_METHOD}}</legend>
			<p>{{paygate.name}}</p>
			<p data-ng-show="paygate.cc_form == 1">{{cardDetails.cc_number}} <br>
			{{cardDetails.cc_month}}/{{cardDetails.cc_year}}</p>
			<p>
				<a href="javascript:void(0)" data-ng-click="saved = false">
					<i class="fa fa-pencil"></i>
					<span>Edit payment</span>
				</a>
			</p>
		</div>
		<div data-ng-show="!saved && user.id">
			<div data-ng-if="paygatesQty() <= 0">
				<p>No payment methods available in the moment. Please, contact the administrator for further assistance.</p>
			</div>
			<div data-ng-if="paygatesQty() == 1">
				<div data-ng-repeat="(name, pgt) in paygates">
					<div data-ng-if="pgt.cc_form == 1">
						<div class="hidden" data-ng-init="$root.paygate = pgt"></div>
						<legend>{{$root.config.language.COM_PAGO_PAYMENT_INFORMATION_LBL}}</legend>
						<div class="form-group">
							<pago-credit-card-form></pago-credit-card-form>
						</div>
					</div>
					<div data-ng-if="pgt.cc_form == 0">
						<div class="hidden" data-ng-init="$root.paygate = pgt"></div>
					</div>
				</div>
			</div>
			<div data-ng-if="paygatesQty() > 1">
				<legend>{{$root.config.language.COM_PAGO_PAYMENT_INFORMATION_LBL}}</legend>
				<div class="form-group">
					<label class="radio" data-ng-repeat="(name, pgt) in paygates">
						<!--<img data-ng-src="{{pgt.logo}}">-->
						<input type="radio" name="paygate" data-ng-model="$root.paygate" data-ng-value="pgt" data-ng-change="pgt.key = name" required>
						<span>{{pgt.name}}</span>
						<div data-ng-if="pgt.cc_form == 1 && $root.paygate == pgt">
							<pago-credit-card-form></pago-credit-card-form>
						</div>
					</label>
					<div data-ng-messages="paygateForm.paygate.$error" data-ng-show="paygateForm.paygate.$touched || paygateForm.$submitted">
						<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
					</div>
				</div>
			</div>
		</div>
		
		<alert type="$root.alert.type" message="$root.alert.message"></alert>
	</div>
</fieldset>