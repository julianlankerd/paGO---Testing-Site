<div class="row">
	<label class="col-sm-7">
		<span>Credit Card Number</span>
		<input type="text" name="cc_number" data-ng-model="$root.cardDetails.cc_number" required>
		<div data-ng-messages="paygateForm.cc_number.$error" data-ng-show="paygateForm.cc_number.$touched || paygateForm.$submitted">
			<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
		</div>
	</label>
	<label class="col-sm-5">
		<span>CVV</span>
		<input type="text" name="cc_cvv" data-ng-model="$root.cardDetails.cc_cvv" required>
		<div data-ng-messages="paygateForm.cc_cvv.$error" data-ng-show="paygateForm.cc_cvv.$touched || paygateForm.$submitted">
			<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
		</div>
	</label>
<!--
    <label class="col-sm-8">
    	<span>Expire date</span>
    	<input type="text" name="cc_exp" data-ng-model="cc_exp" required placeholder="mm/yyyy" maxlength="7"/>
    	<div data-ng-messages="paygateForm.cc_exp.$error" data-ng-show="paygateForm.cc_exp.$touched || paygateForm.$submitted">
    		<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
    	</div>
    </label>
-->
</div>
<div class="row">
	<label class="col-sm-7">
		<span>Expire Month</span>
		<select name="cc_month" data-ng-model="$root.cardDetails.cc_month" data-ng-options="m.index as (m.name + ' (' + m.index + ')') for m in months" required>
			<option value="">Select</option>
		</select>
		<!--<input type="text" name="cc_month" data-ng-model="$root.cardDetails.cc_month" required>-->
		<div data-ng-messages="paygateForm.cc_month.$error" data-ng-show="paygateForm.cc_month.$touched || paygateForm.$submitted">
			<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
		</div>
	</label>
	<label class="col-sm-5">
		<span>Year</span>
		<select name="cc_year" data-ng-model="$root.cardDetails.cc_year" data-ng-options="y for y in years" required>
			<option value="">Select</option>
		</select>
		<!--<input type="text" name="cc_year" data-ng-model="$root.cardDetails.cc_year" required>-->
		<div data-ng-messages="paygateForm.cc_year.$error" data-ng-show="paygateForm.cc_year.$touched || paygateForm.$submitted">
			<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
		</div>
	</label>
</div>