<fieldset>
	<legend>Review order</legend>
	<table class="table review-table">
		<thead>
			<tr>
				<th colspan="3">
					Items
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">Items</td>
				<td>{{cart.format.subtotal}}</td>
			</tr>
			<tr data-ng-show="0 == $root.skipShipping">
				<td colspan="2">Shipping</td>
				<td>{{cart.format.shipping}}</td>
			</tr>
			<tr data-ng-show="cart.coupon.length > 0">
				<td colspan="2">Discount</td>
				<td>{{cart.format.discount}}</td>
			</tr>
			<tr >
				<td colspan="2">Tax</td>
				<td>{{cart.format.tax}}</td>
			</tr>
			<tr>
				<th colspan="2">Total</th>
				<td class="text-right"><strong>{{cart.format.total}}</strong></td>
			</tr>
		</tfoot>
		<tbody>
			<tr data-ng-repeat="i in cart.items">
				<td>{{i.name}}</td>
				<td class="text-right">{{i.cart_qty}}</td>
				<td>{{i.format_price}}</td>
			</tr>
			<tr>
				<th colspan="3">
					Subtotal
				</th>
			</tr>
		</tbody>
	</table>
</fieldset>

<label class="checkbox" data-ng-if="$root.config.checkout.terms_services">
	<input type="checkbox" name="terms_accepted" data-ng-model="terms_accepted" required>
	<span>{{ $root.config.language.PAGO_I_AGREE_TO_TOS }} <a href="#terms" data-toggle="modal">available here</a>.</span>
	<div data-ng-messages="checkoutForm.terms_accepted.$error" data-ng-show="checkoutForm.terms_accepted.$touched || checkoutForm.$submitted">
		<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
	</div>
</label>

<!-- Modal -->
<div id="terms" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">{{ $root.config.language.PAGO_OPC_TERMS_MODAL_HEADER }}</h3>
			</div>
			<div class="modal-body">
				<p data-ng-bind-html="$root.terms"></p>
			</div>
			<div class="modal-footer">
				<a href="javascript:void(0);" class="pg-btn pg-btn-bordered" data-dismiss="modal" aria-hidden="true">Close</a>
				<a href="javascript:void(0);" class="pg-btn" data-dismiss="modal" aria-hidden="true" data-ng-click="terms_accepted = true">Agree</a>
			</div>
		</div>
	</div>
</div>

<button type="submit" data-ng-hide="disabledCheckout()" data-ng-disabled="loading">
	<span data-ng-show="loading">Loading</span>
	<span data-ng-show="!loading">{{$root.config.language[checkoutText()]}}</span>
</button>