<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldFiles extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Files';
	var $_data = array();

	function __construct()
	{
		Pago::load_helpers( 'imagehandler' );
	}

	function get_item_id()
	{
		static $id = 0;

		if ( !$id ) {
			$array = JFactory::getApplication()->input->get( 'cid',  0, 'array' );
			$id = (int) $array[0];
		}

		return $id;
	}

	function getInput()
	{
		$name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$attr = $node->attributes();

		$this->name = $attr['name'];
		$this->callback = $attr['callback'];

		return $this->html( $name, $value );
	}

	function getToolbar()
	{
		$item_id = $this->get_item_id();
		$bar = new JToolBar( $this->name . '-Toolbar' );

		switch ( $this->callback ) {
			case 'category':
				$title = 'Image';
				break;

			default:
				$title = ucfirst( $this->callback );
				break;
		}

		PagoHelper::addCustomButton( 'Add ' . ucfirst( $title ),
			"javascript:pull_upload_form( {$item_id}, '"  .ucfirst( $this->callback ). "' );",
			'new', '#', 'toolbar', '', $bar );
		PagoHelper::addCustomButton( 'Delete',
			"javascript:delete_files( '."  .$this->callback. "-checkboxes' );",
				'delete', '#', 'toolbar', '', $bar );

		return $bar->render();
	}

	function html( $_name, $value )
	{
		if ( !$value ) {
			$value = '[]';
		}

		$doc = JFactory::getDocument();
		$item_id = $this->get_item_id();

		// Add Styles/Scripts
		PagoHtml::thickbox();
		PagoHtml::add_js( JURI::root( true )
			. '/administrator/components/com_pago/javascript/com_pago.js' );

		$data = PagoImageHandlerHelper::get_item_files( $item_id, true, array( $this->callback ) );

		ob_start();
?>
<div class="<?php echo $this->name ?>_grid" style="margin-top:-5px; text-align:left;">
	<div id="files-from">
		<?php Pago::display_view( 'files', 'default', 'files' ); ?>
	</div>
	<div id="to-from-buttons">
		<button id="addfile">>></button><br />
		<button id="removefile"><<</button>
	</div>
	<div id="files-to">
		<div id="files-in-product" class="jstree-default" style="height:200px;"></div>
	</div>
</div>
<script type="text/javascript">
jQuery(function() {

jQuery("#files-in-product")
	.jstree({
		// List of active plugins
		"plugins" : [
			"themes","json_data","ui","crrm","cookies","dnd","types","hotkeys"
		],

		// I usually configure the plugin that handles the data first
		// This example uses JSON as it is most common
		"json_data" : {
			// This tree is ajax enabled - as this is most common, and maybe a bit more complex
			// All the options are almost the same as jQuery's AJAX (read the docs)
			"data" : [{"attr":{"path":"\/","rel":"root","id":"root"},"data":"Root","state":"closed"}]
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
		}
	}).bind("move_node.jstree",function(e,data) {
	   alert(1);
	})
});

jQuery.jstree.plugin('crrm', {
	_fn : {
		addfile : function(obj) {
			// jQuery.jstree._reference('#files_folders').refresh('li[path="/test"]');
			if ( 'file' == jQuery(obj).attr('rel') ) {
				return;
			}

			inst = jQuery.jstree._reference('#files_folders');
			slct = this.get_selected().eq(0);
			jQuery('#files_folders').jstree('move_node', slct, slct.prev(), 'before' );
			// jQuery.jstree.move_node();
		},
		removefile : function(obj) {
			// jQuery.jstree._reference('#files_folders').refresh('li[path="/test"]');
			if ( 'file' == jQuery(obj).attr('rel') ) {
				return;
			}

			console.log(1);
		},
	}
});
</script>
<?php
$return = ob_get_clean();

ob_start();
?>
jQuery(document).ready(function(){
	jQuery('#to-from-buttons button').click(function(){
		jQuery('#files_folders').jstree(this.id);
		return false;
	});
});
<?php
$js = ob_get_clean();
$doc->addScriptDeclaration( $js );

		return $return;
	}
}