<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

// myPrint(json_decode('[{"attr":{"id":"node_2","rel":"drive"},"data":"Tools","state":"closed"}]'));die();
/*
Array
(
    [0] => stdClass Object
        (
            [attr] => stdClass Object
                (
                    [id] => node_2
                    [rel] => drive
                )

            [data] => Tools
            [state] => closed
        )

)
*/
?>
<script type="text/javascript">
jQuery(function() {

jQuery("#files_folders")
	.jstree({
		// List of active plugins
		"plugins" : [
			"themes","json_data","ui","crrm","cookies","dnd","types","hotkeys","contextmenu"
		],

		// I usually configure the plugin that handles the data first
		// This example uses JSON as it is most common
		"json_data" : {
			// This tree is ajax enabled - as this is most common, and maybe a bit more complex
			// All the options are almost the same as jQuery's AJAX (read the docs)
			"ajax" : {
				// the URL to fetch the data
				"url" : "index.php?option=com_pago&view=files&task=tree",
				// the `data` function is executed in the instance's scope
				// the parameter is the node being loaded
				// (may be -1, 0, or undefined when loading the root nodes)
				"data" : function (n) {
					// the result is fed to the AJAX request `data` option
					return {
						"operation" : "get_children",
						"path" : n.attr ? n.attr("path") : 'root'
					};
				}
			}
		},
		// Using types - most of the time this is an overkill
		// read the docs carefully to decide whether you need types
		"types" : {
			// I set both options to -2, as I do not need depth and children count checking
			// Those two checks may slow jstree a lot, so use only when needed
			"max_depth" : -2,
			"max_children" : -2,
			// I want only `drive` nodes to be root nodes
			// This will prevent moving or creating any other type as a root node
			"valid_children" : [ "root" ],
			"types" : {
				// The default type
				"file" : {
					// I want this type to have no children (so only leaf nodes)
					// In my case - those are files
					"valid_children" : "none"
				},
				// The `folder` type
				"folder" : {
					// can have files and other folders inside of it, but NOT `drive` nodes
					"valid_children" : [ "folder", "file" ]
				},
				// The `root` node
				"root" : {
					// can have files and folders inside, but NOT other `drive` nodes
					"valid_children" : [ "folder", "file" ],
					// those prevent the functions with the same name to be used on `drive` nodes
					// internally the `before` event is used
					"start_drag" : false,
					"move_node" : false,
					"delete_node" : false,
					"remove" : false
				}
			}
		},
		// UI & core - the nodes to initially select and open will be overwritten by the cookie plugin

		// the UI plugin - it handles selecting/deselecting/hovering nodes
		'ui' : {
			// this makes the node with ID node_4 selected onload
			"initially_select" : [ "root" ]
		},
		// the core plugin - not many options here
		'core' : {
			// just open those two nodes up
			// as this is an AJAX enabled tree, both will be downloaded from the server
			"initially_open" : [ "root" ]
		},
		'crrm' : {
			'move' : {
				'always_copy' : true,
				'check_move' : function (m) { return true; }
			}
		},
		'contextmenu' : {
			'select_node' : false,
			'show_at_node' : true,
			'items' : function(node){
				if ( 'file' == node.attr('rel') ) {
					_return = {};

					if ( node.hasClass('new-file') ) {
						_return.createfile = {
							'separator_before' : false,
							'separator_after'  : true,
							'label'            : "Add file",
							'action'           : function(obj) { this.addfile(obj); }
						};
					} else {
						_return.editfile = {
							'separator_before' : false,
							'separator_after'  : true,
							'label'            : "Edit file",
							'action'           : function(obj) { this.editfile(obj); }
						};
					}

					_return.removeany = {
						'separator_before' : false,
						'separator_after'  : true,
						"label"            : "Delete file",
						"action"           : function (obj) {
							if ( this.is_selected( obj ) ) {
								this.remove();
							} else {
								this.remove(obj);
							}
						}
					};

					return _return;
				} else {
					return {
						'createfile' : {
							'separator_before' : false,
							'separator_after'  : true,
							'label'            : "Add files",
							'action'           : function(obj) { this.createfile(obj); }
						},
						"createfolder" : {
							'separator_before' : false,
							'separator_after'  : true,
							"label"            : "Create folder",
							"action"           : function (obj) { this.create(obj); }
						},
						"removeany" : {
							'separator_before' : false,
							'separator_after'  : true,
							"label"            : "Delete folder",
							"action"           : function (obj) {
								if ( this.is_selected( obj ) ) {
									this.remove();
								} else {
									this.remove(obj);
								}
							}
						},
						'create' : false,
						'remove' : false,
						'rename' : false,
						'ccp' : false
					}
				}
			}
		}
	})
	.bind("create.jstree", function (e, data) {
		jQuery.post(
			"index.php?option=com_pago&view=files&task=tree",
			{
				"operation" : "create_node",
				"path" : data.rslt.parent.attr("path"),
				"title" : data.rslt.name
			},
			function (r) {
				if(r[0].status) {
					jQuery(data.rslt.obj).attr("path", r[0].attr.path);
				} else {
					jQuery.jstree.rollback(data.rlbk);
				}
			}, 'json'
		);
	})
	.bind("remove.jstree", function (e, data) {
		data.rslt.obj.each(function() {
			jQuery.post(
				"index.php?option=com_pago&view=files&task=tree",
				{
					"operation" : "remove_node",
					"path" : jQuery(data.rslt.obj).attr('path'),
					"type" : jQuery(data.rslt.obj).attr('rel'),
					"file" : jQuery(data.rslt.obj).attr('id')
				},
				function (r) {
					if(!r[0].status) {
						data.inst.refresh();
						alert( r[0].msg );
					}
				}, 'json'
			);
		});
	});
});

jQuery.jstree.plugin('crrm', {
	_fn : {
		createfile : function(obj) {
			// jQuery.jstree._reference('#files_folders').refresh('li[path="/test"]');
			if ( 'file' == jQuery(obj).attr('rel') ) {
				return;
			}

			pull_upload_form( 0, 'files', escape( jQuery(obj).attr('path') ) );
		},
		addfile : function(obj) {
			if ( 'file' != jQuery(obj).attr('rel') ) {
				return;
			}

			jQuery.post(
				"index.php?option=com_pago&view=files&task=tree",
				{
					"operation" : "add_file",
					"path" : jQuery(obj).attr('path'),
					"file" : jQuery(obj).attr('id')
				},
				function (r) {
					if(r[0].status) {
						jQuery.jstree._reference('#files_folders')
							.refresh('li[path="' +jQuery(obj).attr('path')+ '"]');
						alert( 'File added' );
					}
				}, 'json'
			);
		},
		editfile : function(obj) {
			if ( 'file' != jQuery(obj).attr('rel') ) {
				return;
			}

			pull_edit_form( 0, 'file', escape( jQuery(obj).attr('id') ),
				escape( jQuery(obj).attr('path') ) );
		}
	}
});
</script>
<?php if ( 'files' == JFactory::getApplication()->input->get( 'view' ) ) {
	PagoHtml::apply_layout_fixes();
	JHTML::_('behavior.tooltip');
	$dispatcher = KDispatcher::getInstance();
	include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
	PagoHtml::pago_top( $menu_items );
	?>
<div class="pg-content">
<?php echo PagoHtml::module_top( false ); ?>
    <div id="admin_grid" class="pg-module-content">
        <table id="list" style="width:100%">
<?php } ?>
        	<div id="files_folders" class="files_folders jstree-default" style="height:200px;"></div>
<?php if ( 'files' == JFactory::getApplication()->input->get( 'view' ) ) { ?>
        </table>
    </div>
<?php echo PagoHtml::module_bottom() ?>
</div>
<?php PagoHtml::pago_bottom(); ?>
<?php } ?>

