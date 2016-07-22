jQuery(document).ready(function($) {
				
	var t = setTimeout ( "jQuery('div.jpane-slider').css('height', 'auto')", 400 ); 
	
	$('div.acl_grid').parent().attr('colspan', 2).siblings().remove();
	
	var acl_rules = ACL_RULES;
	
	var acl_init = function(){
		
		$('input.acl_grid_json').val( acl_rules );
		
		var acl_obj = JSON.parse(acl_rules);
			
		$.each(acl_obj, function(index, rule) { 
	
			var tr = $('tbody.add_row').children('tr').clone();
			
			tr.find('input.name_input').val(rule.name);
			tr.find('select.rule_input').val(rule.rule);
			tr.find('input.weight_input').val(rule.weight);
			tr.find('input.price_input').val(rule.price);
		
			tr.appendTo('tbody.acl_grid')
			
			$('#acl_table').tableDnD({
				onDrop: export_acl
			});
			
			$('tbody.acl_grid').find('input').change(function() {
				export_acl();
			});
		});
	}
	
	acl_init();
	
	var export_acl = function(){
		
		var rules = [];
		
		$('tbody.acl_grid').children().each(function(i,e){
			
			rules.push({
				name : $(this).find('.name_input').val(),
				rule: $(this).find('.rule_input').val(),
				weight: $(this).find('.weight_input').val(),
				price: $(this).find('.price_input').val()
			});

			
			
		});
		
		$('input.acl_grid_json').val( JSON.stringify(rules) );
	}
	
	$('tbody.acl_grid').find('input').change(function() {
	  export_acl();
	});

	$('#acl_table').tableDnD({
		onDrop: export_acl
	});
	
	$('li[id=acl-Toolbar-new]').children('a').css('outline-width', '0').css('outline-style', 'none').attr('onClick', '').click(function() {
	
		jQuery('div.jpane-slider').css('height', 'auto');
		
		$('tbody.add_row').children('tr').clone().appendTo('tbody.acl_grid');
		
		$('#acl_table').tableDnD({
			onDrop: export_acl
		});
		
		export_acl();
		
		$('tbody.acl_grid').find('input').change(function() {
			export_acl();
		});
		
		return false;
	});
	
	$('li[id=acl-Toolbar-help]')
		.children('a')
			.css('outline-width', '0')
			.css('outline-style', 'none')
				.attr('onClick', '')
				.attr('class', 'aclmodal')
				.attr('rel', '{size: {x: 550, y: 500}}')
				.attr('href', JURI_ROOT + 'plugins/pago_shippers/custom/docs.html');
	
	$('li[id=acl-Toolbar-delete]').children('a').css('outline-width', '0').css('outline-style', 'none').attr('onClick', '').click(function() {
	
		if($('tbody.acl_grid').find('input:checked').length <1){
			$('tbody.acl_grid').find('input[type=checkbox]').parent().css('border-color', 'red');
			return false;
		}
		
		$('tbody.acl_grid').find('input[type=checkbox]').parent().css('border-color', '#fff');
		$('tbody.acl_grid').find('input:checked').parent().parent().remove();
		$('#acl_table').tableDnD({
			onDrop: export_acl
		});
		
		export_acl();
		
		return false;
	});
	
	SqueezeBox.initialize({});

	$$('a.aclmodal').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
		});
	});
});
