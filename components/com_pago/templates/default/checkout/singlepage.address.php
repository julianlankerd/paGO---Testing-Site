<fieldset class="">
    <div data-ng-show="user.id">
        <pago-address-form 
            data-address-type="s" 
            data-address-title="Shipping"
            ng-hide="1 == $root.skipShipping"></pago-address-form>
        <pago-address-form 
            data-address-type="b"
            data-address-title="Billing"
            data-ng-show="(!sameForBilling && addresses.length >= 1) || 1 == $root.skipShipping"></pago-address-form>
    </div>
</fieldset>