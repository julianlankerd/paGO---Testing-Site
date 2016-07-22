jQuery(document).ready(function($) {

cats_expand_all = function(){
	jQuery.jstree._reference($('#pago_category_tree')).open_all();
	return false;
}

$(function () {
	
	$('#pago_category_tree').bind("move_node.jstree", function (e, data) { 
	
		var treeInstance = data.inst;
		var node = data.rslt.o;
		var parentNode = treeInstance._get_parent(node);
		var oldParentNode = data.rslt.op;
					
		//$.notifier.broadcast({ttl:'hello', msg:'this is a message', skin:'rounded'}); // custom skin
	
		var n_order = [];
	
		$(parentNode).children('ul').children('li').each(function(index) {
			n_order.push( $(this).attr('node') );
		});
	
		$.get( PAGO_CAT_TREE_VIEW_URL, {
			task: 'move', 
			id: node.attr('node'), 
			parent_id: parentNode.attr('node'),
			old_parent_id: oldParentNode.attr('node'),
			n_order: JSON.stringify(n_order)
		});
		
	}).bind('rename.jstree', function(e,data) { 
	
		var node = data.rslt.obj;
		
		$.get( PAGO_CAT_TREE_VIEW_URL , { task: 'rename', id: node.attr('node'), name: data.rslt.new_name  } );
	
	}).bind('create.jstree', function(e,data) { 
	
		var parent_id = data.rslt.parent.attr('node');
		var name = data.rslt.name;
		var new_node = data.rslt.obj;
		//console.log(data.rslt.name, data.rslt.obj,  data.rslt.parent );
	 
	 	$.get( PAGO_CAT_TREE_VIEW_URL , { task: 'create', parent_id: parent_id, name: name } ,function( new_id ){
     		new_node.attr('node', new_id);
   		});	
	
	}).bind('check_node.checkbox.jstree', function(e,data) { 
		
		var node = data.inst._get_node();
		
		if(node.attr('node') == 1){
			//return false;
		}
				
		$.get( PAGO_CAT_TREE_VIEW_URL , { task: 'publish', id: node.attr('node') } );
	 
	}).bind('uncheck_node.checkbox.jstree', function(e,data) { 
	
	 	var node = $(data.rslt.getParent().getParent());	
		
		if(node.attr('node') == 1){
			//return false;
		}
		
		$.get( PAGO_CAT_TREE_VIEW_URL , { task: 'unpublish', id: node.attr('node') } );
	
	}).jstree({ 
		'core':{
			initially_open:['node1']
		},
		'ui' : {	
			'selected_parent_close' : false
		},
		"crrm" : { 
			"move" : {
				"check_move" : function (m) { 
					//make sure you can't move into root node level
					if(m.cr < 1){						
						return false;
					}					
					return true;
				}
			}
		},
		contextmenu: {
			items: {
					//rename: false,
					remove : {
						label : "Delete",
						action : function (node, tree_obj) {
							
							var id = node.attr('node');
							
							if(id == 1){
								$.notifier.broadcast({ttl:'Error', msg:'You cannot remove the root node!', skin:'rounded'});
								return false;
							}
							
							if (confirm('Are you sure you want to delete?')) {
							
							$.get( PAGO_CAT_TREE_VIEW_URL , { task: 'delete', id: id  } );
							
							node.remove();
							
							}
						}
					},
					ccp: false,
					create: {
						label: 'Create Child',
						
						seperator_after : false,
						seperator_before : false
					}/*,
					details: {
						label: 'View Details',
						action: function (obj){
							$('#cat_details').load( PAGO_CAT_TREE_VIEW_URL , { task: 'details', id: obj.attr('node')  });
						},
						seperator_after: false,
						seperator_before: true
					}*/
				} // end items
		},

		'plugins' : [ 'themes', 'html_data', 'ui', 'crrm', 'contextmenu', 'dnd' ]
	});
});

});
