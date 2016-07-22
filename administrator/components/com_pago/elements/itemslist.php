<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class JFormFieldItemslist extends JFormField
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        protected $type = 'Itemslist';

        function getInput()
        {
			$name = $this->name;
			$value = $this->value;
			$node = $this->element;
			$control_name = $this->id;

			$cid = JFactory::getApplication()->input->get('cid',  0, 'array');
			$this->order_id = (int)$cid[0];

			$attr = $node->attributes();

			$this->attr_name = $attr['name'];
			$this->callback = $attr['callback'];
			//print_r($this->name);

			return $this->html( $name, $value );
        }

		function _get_groups_select( $value=null ){

			$acl   = JFactory::getACL();
			$gtree = $acl->get_group_children_tree( null, 'USERS', false );

			$gtree[0]->text = 'Guest';
			$gtree[0]->value = 'guest';

			//print_r($gtree);

			foreach($gtree as $k=>$tree){
			 if(strstr($tree->text, 'Public')){
				unset($gtree[$k]);
			 }
			}

			$all = new stdClass;
			$all->value = 'allgroups';
			$all->text = JText::_('All Groups');

			array_unshift($gtree, $all);

			$group_select = JHTML::_('select.genericlist', $gtree, false, 'class="acl_group" size="1" style="width:100%"', 'value', 'text', $value );
			$group_select = str_replace('.', '', $group_select);
			$group_select = str_replace('-', '', $group_select);
			$group_select = str_replace('&nbsp;', '', $group_select);
			//$group_select = str_replace('selected=""', 'selected="selected"', $group_select);

			return $group_select;
		}

		function _get_name_input( $value=null ){

			return '<input type="text" value="'.$value.'" class="name_input" />';
			return JHTML::_('input.text', 'name', $value, 'style="width:100%"', 'label');

			$db = & JFactory::getDBO();
            $db->setQuery( 'SELECT * FROM #__jent_roles' );

			$rows = $db->loadAssocList();

			if( is_array( $rows ) ){
				array_unshift($rows, array(
					'id' => 'allroles',
					'name' => JText::_('All Roles')
				));
			} else {
				$rows[] = array(
					'id' => 'allroles',
					'name' => JText::_('All Roles')
				);
			}

			$rows_select = JHTML::_('select.genericlist', $rows, false, 'class="acl_role" size="1" style="width:100%"', 'id', 'name', $value );

			return $rows_select;

		}

		function _get_actions_select( $value=null ){

			$actions = array(
				array(
					'id' => 1,
					'name' => JText::_('Allow')
				),
				array(
					'id' => -1,
					'name' => JText::_('Deny')
				)
			);

			return JHTML::_('select.genericlist', $actions, false, 'class="acl_action" size="1" style="width:100%"', 'id', 'name', $value );
		}

		function getToolbar()
		{
			$bar = new JToolBar( $this->attr_name . '-Toolbar' );
			$bar->appendButton( 'Standard', 'new', JText::_('Insert'), 'new', false );
			//$bar->appendButton( 'Separator' );
			$bar->appendButton( 'Standard', 'delete', JText::_('Delete'), 'delete', false );
			//$bar->appendButton( 'Standard', 'help', JText::_('Help'), 'delete', false );

			return $bar->render();
        }

		function html( $name, $value ){

		if(!$value){
			$value = '[]';
		}

		JHTML::_('behavior.modal');

		$doc = JFactory::getDocument();

		$uri = str_replace( '/administrator','',JURI::base(true) );

		$doc->addScript( $uri . '/components/com_pago/javascript/jquery.tablednd_0_5.js' );
		$doc->addScript( $uri . '/components/com_pago/javascript/json2.js' );

		ob_start();
			?>



<script>
//<![CDATA[
//$.noConflict();

ITEM_LIST_DONE = false;





			jQuery(function($) {


				//if(ITEM_LIST_DONE) return false;


				var main_id = '<?php echo $this->attr_name ?>';

				var main_div = jQuery('div.'+main_id+'_grid');
				var main_table = jQuery('#'+main_id+'_table');
				var main_tbody = jQuery('tbody.'+main_id+'_grid');

				var t = setTimeout ( "jQuery('div.jpane-slider').css('height', 'auto')", 400 );

				main_div.parent().attr('colspan', 2).siblings().remove();

				var acl_rules = '<?php echo $value ?>';
				var acl_init = function(){

					jQuery('input.<?php echo $this->attr_name ?>').val( acl_rules );

					var acl_obj = JSON.parse(acl_rules);


					jQuery.each(acl_obj, function(index, data) {


						var tr = jQuery('tbody.'+main_id+'_add_row').children('tr').clone();
						//qty : jQuery(this).find('input.qty').val(),
						tr.find('input.qty').val( data.qty );
						tr.find('td.id').text( data.id );
						tr.find('td.name').text(data.name);
						tr.find('td.price').text(data.price);
						tr.find('td.total').text(data.total);

						//total: jQuery(this).find('td.total').text()
						tr.appendTo(main_tbody)

						main_table.tableDnD({
							onDrop: export_acl
						});

						/*main_tbody.find('input').change(function() {
							export_acl();
						});*/


					});


					//$('input.acl_grid_json').val( JSON.stringify(rules) );
				}

				if(!ITEM_LIST_DONE){
					acl_init();
				}

				var export_acl = function(){

					var rules = [];

					main_tbody.children().each(function(i,e){

						rules.push({
							qty : jQuery(this).find('input.qty').val(),
							id : jQuery(this).find('td.id').text(),
							name: jQuery(this).find('td.name').text(),
							price: jQuery(this).find('td.price').text(),
							total: jQuery(this).find('td.total').text()
						});



					});

					//console.log(rules);

					jQuery('input.'+main_id).val( JSON.stringify(rules) );

					/*jQuery.post('index.php?option=com_pago&view=order&format=ship_calcs', { order_id:<?php echo $this->order_id ?>, items: JSON.stringify(rules) },
						function(data) {
							jQuery('.shippers').html(data);
						}
					);*/
				}

				main_tbody.find('input').change(function() {
				  export_acl();
				});

				main_table.tableDnD({
							onDrop: export_acl
						});

				jQuery('td[id='+main_id+'-Toolbar-new]').children('a').css('outline-width', '0').css('outline-style', 'none')
				.attr('onClick', '')
				//.addClass('modal')
				.attr('class', 'aclmodal')
				.attr('rel', '{handler: \'iframe\', size: {x: 790, y: 590}}')
				.attr('href', '<?php echo JURI::base(true) ?>/index.php?option=com_pago&view=items&format=rawgrid&callback=<?php echo $this->callback ?>');

				//jQuery('td[id='+main_id+'-Toolbar-delete]').children('a').css('outline-width', '0').
				jQuery('td[id='+main_id+'-Toolbar-delete]').children('a').css('outline-width', '0').css('outline-style', 'none').attr('onClick', '').click(function() {
				//$('button[id=delete_rule]').click(function() {

					if(main_tbody.find('input:checked').length <1){
						main_tbody.find('input[type=checkbox]').parent().css('border-color', 'red');
						return false;
					}
					main_tbody.find('input[type=checkbox]').parent().css('border-color', '#fff');
					main_tbody.find('input:checked').parent().parent().remove();
					main_table.tableDnD({
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

				ITEM_LIST_DONE = true;

			});

//]]>
</script>

   <div class="<?php echo $this->attr_name ?>_grid" style="margin-top:-5px">


    <?php echo $this->getToolbar() ?>

   	<table class="adminlist">
        <tbody style="display:none" class="<?php echo $this->attr_name ?>_add_row">
          <tr class="row0" style="cursor: move;">
          	<td><input type="checkbox" /></td>
           	<td><input style="width:40px;"type="text" class="qty" /></td>
            <td class="id"></td>
            <td class="name"></td>
            <td align="right" class="price"></td>
            <td align="right" class="total"></td>
            <td style="width:20px;background:url(<?php echo JURI::base() ?>components/com_filenavigator/images/drag.jpg) center no-repeat"></td>
          </tr>
        </tbody>
      </table>

      <table  id="<?php echo $this->attr_name ?>_table" class="adminlist">
        <thead>
          <tr>
          	<th width="20">

			</th>
             <th width="10%" class="title"><?php echo JText::_('Qty') ?></th>
            <th class="title"><?php echo JText::_('Id') ?></th>
            <th class="title"><?php echo JText::_('Name') ?></th>
            <th width="15%"><?php echo JText::_('Price') ?></th>
             <th width="15%"><?php echo JText::_('Total') ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody class="<?php echo $this->attr_name ?>_grid">

        </tbody>
      </table>
      </div>

      <input class="<?php echo $this->attr_name ?>" style="width:99%" name="params[<?php echo $name ?>]" type="hidden" value="" />


      <script>

var <?php echo $this->callback ?> = function(rows){

	var main_id = '<?php echo $this->attr_name ?>';
	var main_div = jQuery('div.'+main_id+'_grid');
	var main_table = jQuery('#'+main_id+'_table');
	var main_tbody = jQuery('tbody.'+main_id+'_grid');

	var export_acl = function(){

		var rules = [];

		main_tbody.children().each(function(i,e){

			rules.push({
				qty : jQuery(this).find('input.qty').val(),
				id : jQuery(this).find('td.id').text(),
				name: jQuery(this).find('td.name').text(),
				price: jQuery(this).find('td.price').text(),
				total: jQuery(this).find('td.total').text()
			});



		});

		jQuery('input.'+main_id).val( JSON.stringify(rules) );

		/*jQuery.post('index.php?option=com_pago&view=order&format=ship_calcs', { order_id:<?php echo $this->order_id ?>, items: JSON.stringify(rules) },
            function(data) {
                jQuery('.shippers').html(data);
            }
        );*/
	}

	jQuery.each(rows, function(key, data) {

		var tr = jQuery('tbody.<?php echo $this->attr_name ?>_add_row').children('tr').clone();

		tr.find('input.qty').val( 1 );
		tr.find('td.id').text( data.id );
		tr.find('td.name').text(data.name);
		tr.find('td.price').text(data.price);
		tr.find('td.total').text(data.price);

		tr.find('a.aclmodal').attr('style', '');
		//tr.find('a.aclmodal').attr('href', '../media/' + data[0] + file);

		tr.appendTo('tbody.<?php echo $this->attr_name ?>_grid')

		main_table.tableDnD({
			onDrop: export_acl
		});

		main_tbody.find('input').change(function() {
			export_acl();
		});


	});

	export_acl();
}

</script>
        <?php
			$return = ob_get_contents();
		ob_end_clean();

		return $return;
		}
}
