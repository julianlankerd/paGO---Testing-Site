jQuery(document).ready(function() {
	jQuery(".pg-currency-converter select").chosen({
		disable_search: true,
		width: 'auto'
	});

	jQuery(".currrency-list a").click(function(e){
		jQuery(".currencyChanger").attr("value", jQuery(this).data("id"));
		jQuery(".currencyForm").submit()

		e.preventDefault();
	})
})