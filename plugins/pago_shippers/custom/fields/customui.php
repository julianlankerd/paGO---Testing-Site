<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 

jimport('joomla.html.html');
jimport('joomla.form.formfield');//import the necessary class definition for formfield

class JFormFieldCustomui extends JFormField
{
 		
	/**
	  * The form field type.
	  *
	  * @var  string
	  * @since	1.6
	  */
		protected $type = 'customui'; //the form field type
	
		/**
	  * Method to get content articles
	  *
	  * @return	array	The field option objects.
	  * @since	1.6
	  */
		protected function getInput()
        {
			return $this->html( $this->name, $this->value );
        }
		
		function _get_groups_select( $value=null ){
			
			$acl		=& JFactory::getACL();		  
			$gtree = $acl->get_group_children_tree( null, 'USERS', false );
			
			$gtree[0]->text = 'Guest';
			$gtree[0]->value = 'guest';
			
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
		
		function getToolbar() {
 
 
			$bar = new JToolBar( 'acl-Toolbar' );
			$bar->appendButton( 'Standard', 'new', JText::_('New'), '', false );
			//$bar->appendButton( 'Separator' );
			$bar->appendButton( 'Standard', 'delete', JText::_('Delete'), '', false );
			$bar->appendButton( 'Standard', 'help', JText::_('Help'), '', false );


			return $bar->render();
 
        }

		function html( $name, $value ){
		
		if(!$value){
			$value = '[]';
		}
		
		$doc = JFactory::getDocument();
		
		$doc->addStyleSheet( JURI::root(true) . '/media/system/css/modal.css' );
		
		$doc->addScript( JURI::root(true) . '/media/system/js/modal.js' );
		$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.js' );
		$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.tablednd_0_5.js' );
		$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/json2.js' );
		$doc->addScript( JURI::root(true) . '/plugins/pago_shippers/custom/javascript.js' );
		
		$doc->addScriptDeclaration("
			
			ACL_RULES = '{$value}';
			JURI_ROOT = '".JURI::root()."';
		");
		
		ob_start();
			?>
           
   
   <div class="acl_grid" style="margin-top:-5px">
   	
    <div style="float:left;width:65%;">
    
    <img src="<?php echo JURI::root(true) ?>/plugins/jent/acl/images/icon.png" style="float:left;margin:0 30px 0 0" />
    
   <?php echo'<div style="padding:10px;">' . JText::_('
	   Add and Configure Custom Shipping Rules. You can change the order of rules by clicking an empty part of the row - hold and drag.
   ') . '</div>' ?>
   
    </div>
    <?php echo $this->getToolbar() ?>
   
   	<table class="adminlist">
        <tbody style="display:none" class="add_row">
          <tr class="row0" style="cursor: move;">
          	<td><input type="checkbox" /></td>
			<td><input type="text" style="width:100%" class="name_input" /><?php //echo $this-> _get_name_input() ?></td>
			<td><select class="rule_input" >
				<option value="lt" >Less Than</option>
				<option value="lte" >Less Than or Equal To</option>
				<option value="e" >Equal To</option>
				<option value="gte" >Greater Than or Equal To</option>
				<option value="gt" >Greater Than</option>
				</select>
				<input type="text" style="width:100%" class="weight_input" /><?php //echo $this->_get_roles_select() ?></td>
            <td><input type="text" style="width:100%" class="price_input" /><?php //echo $this->_get_actions_select() ?></td>
          </tr> 
        </tbody>
      </table>
      
      <table  id="acl_table" class="adminlist">
        <thead>
          <tr>
          	<th width="20">
				
			</th>
            <th class="title"><?php echo JText::_('Name') ?></th>
            <th width="30%"><?php echo JText::_('Weight Rule') ?></th>
            <th width="30%" nowrap="nowrap"><?php echo JText::_('Price') ?></th>
          </tr>
        </thead>
        <tbody class="acl_grid">
         
        </tbody>
      </table>
      </div>
      
      <input class="acl_grid_json" style="width:99%" name="<?php echo $name ?>" type="hidden" value="" />
        <?php	
			$return = ob_get_contents();		
		ob_end_clean();
		
		return $return;
		}
}
