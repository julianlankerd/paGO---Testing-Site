;"use strict";

angular
	.module(
		"pagoWingmanStripe",
		[]
	)
	.config(function () {
		Stripe.setPublishableKey("pk_test_tZ3SKiYxN7xrHbJ81pujizUZ");
	})
	.factory(
		"StripeValidator",
		function ()
		{
			var sv = function()
			{
				this.number = Stripe.card.validateCardNumber;
				this.expiry = Stripe.card.validateExpiry;
				this.cvc    = Stripe.card.validateCVC;
				this.type   = Stripe.card.cardType;
			};
			
			return new sv();
		}
	);