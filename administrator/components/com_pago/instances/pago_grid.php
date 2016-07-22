<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

class pago_grid
{
	public
	$pager_selector = 'pager',
	$grid_selector = 'grid',
	$where=false,
	$disable_tools,
	$nav_tools;

	function run_task(){

		$task_method = 'task_' . JFactory::getApplication()->input->get( 'task' );

		$view_name = $this->view->get( '_name' );

		if ( method_exists( $this->view, $task_method ) ) {
            $this->view->$task_method( $this );
        } elseif ( method_exists( $this, $task_method ) ) {
            $this->$task_method();
        } elseif( JFactory::getApplication()->input->get( 'task' ) ) {
			JError::raiseWarning( 500, JText::_( 'Task Not Found: ' . JFactory::getApplication()->input->get( 'task' ) ) );
        	$app = JFactory::getApplication();
 			$app->redirect("index.php?option=com_pago&view={$view_name}");
		}
	}

	function task_editgrid(){

		$app = JFactory::getApplication();

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

		$table = JTable::getInstance( $this->jtable, 'Table' );

		$table_name = $table->get( '_tbl' );
		$index = $table->get( '_tbl_key' );

		$data = JFactory::getApplication()->input->getArray($_POST);

		$error = false;

		if( (int)JFactory::getApplication()->input->get( 'id' ) > 0 ){
			$data[ $index ] = JFactory::getApplication()->input->get( 'id' );
		} else {
			$data[ $index ] = 0;
		}

		if( !$table->bind( $data ) ) $error = true;
		if( !$table->store() ) $error = true;

		if( $error ){
			echo $table->getError();
		}

		exit();
	}

	function task_publish(){

		$app = JFactory::getApplication();

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

		$table = JTable::getInstance( $this->jtable, 'Table' );

		$table_name = $table->get( '_tbl' );
		$index = $table->get( '_tbl_key' );

		$cid = explode( ',', JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		$error = false;

		foreach( $cid as $id ){
			$data[ $index ] = $id;
			$data[ $this->publish_field ] = 1;

			if( !$table->bind( $data ) ) $error = true;
			if( !$table->store() ) $error = true;
		}

		if( !$error ){
			$app->enqueueMessage( JText::_( 'Successfully Published Record(s)' ) );
		} else {
			JError::raiseWarning( 500, $table->getError() );
		}

		$view_name = $this->view->get( '_name' );

 		$app->redirect("index.php?option=com_pago&view={$view_name}");
	}

	function task_unpublish(){

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

		$table = JTable::getInstance( $this->jtable, 'Table' );

		$table_name = $table->get( '_tbl' );
		$index = $table->get( '_tbl_key' );

		$cid = explode( ',', JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		$error = false;

		foreach( $cid as $id ){
			$data[ $index ] = $id;
			$data[ $this->publish_field ] = 0;

			if( !$table->bind( $data ) ) $error = true;
			if( !$table->store() ) $error = true;
		}

		$app = JFactory::getApplication();

		if( !$error ){
			$app->enqueueMessage( JText::_( 'Successfully Unpublished Record(s)' ) );
		} else {
			JError::raiseWarning( 500, $table->getError() );
		}

		$view_name = $this->view->get( '_name' );

 		$app->redirect("index.php?option=com_pago&view={$view_name}");
	}

	function task_delete(){

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

		$table = JTable::getInstance( $this->jtable, 'Table' );

		$table_name = $table->get( '_tbl' );
		$index = $table->get( '_tbl_key' );

		$cid = explode( ',', JFactory::getApplication()->input->get( 'cid', array(0), 'array' ) );

		$error = false;

		$view_name = $this->view->get( '_name' );

		foreach( $cid as $id ){
			$data[ $index ] = $id;

			if( !$table->bind( $data ) )  $error = true;
			if( !$table->delete() )  $error = true;
		}

		$app = JFactory::getApplication();

		if( !$error ){
			$app->enqueueMessage( JText::_( 'Successfully Deleted Record(s)' ) );
		} else {
			JError::raiseWarning( 500, $table->getError() );
		}

 		$app->redirect("index.php?option=com_pago&view={$view_name}");
	}

	function task_get_data(){

		//$model 	= new JModel;
		//$table = $model->getTable( $this->jtable );

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

		$table = JTable::getInstance( $this->jtable, 'Table' );

		$table_name = $table->get( '_tbl' );
		$index = $table->get( '_tbl_key' );

		$app = JFactory::getApplication();

		$view_name = $this->view->get( '_name' );

		$db = JFactory::getDBO();

		$page = $app->getUserStateFromRequest( "{$view_name}_grid.page", 'page', 1 );
		$rows = $app->getUserStateFromRequest( "{$view_name}_grid.rows", 'rows', 20 );
		$sidx = $app->getUserStateFromRequest( "{$view_name}_grid.sidx", 'sidx', $this->default_sort_field );
		$sord = $app->getUserStateFromRequest( "{$view_name}_grid.sord", 'sord', 'desc' );

		$order = " ORDER BY {$table_name}.{$sidx} {$sord}";

		$start = ( $rows * $page ) - $rows;

		$where = array();

		if ( JFactory::getApplication()->input->get( '_search' ) == 'true' ) {
			$where[] = $this->get_search_query();
		}

		if( $this->where ){
			$where[] = $this->where;
		}

		$where 	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$sql = "SELECT SQL_CALC_FOUND_ROWS {$table_name}.*
					FROM {$table_name}
						{$where} $order
							LIMIT {$start}, {$rows}";

		$db->setQuery( $sql );

		$items = $db->loadObjectList();

		$db->setQuery( 'SELECT FOUND_ROWS()' );

		$total = $db->loadResult();
		$totalpages = ceil( $total/$rows );

		if( is_array($items) )
		foreach($items as $item){

			if( isset( $this->edit_field ) ){

				$link = $this->edit_link;
				$edit_field = $this->edit_field;

				if( isset( $item->$edit_field ) ){
					$item->editlink = "<a href=\"{$link}{$item->$index}\">{$item->$edit_field}</a>";
				}
			}

			$link = JRoute::_( "index.php?option=com_pago&view={$view_name}&cid=". $item->$index );

			if( isset( $this->publish_field ) ){

				$publish_field = $this->publish_field;

				if( isset( $item->$publish_field ) ){
					if( $item->$publish_field ){

						$item->ispublishedlink = "<a href=\"{$link}&task=unpublish\"><img src=\"templates/bluestork/images/admin/tick.png\" /></a>";
					} else {

						$item->ispublishedlink = "<a href=\"{$link}&task=publish\"><img src=\"templates/bluestork/images/admin/publish_x.png\" /></a>";
					}
				} else {
					JError::raiseWarning( 500, 'Grid publish field '.$this->publish_field.' not found in ' . $table_name );
				}
			}

		}

		$json = array(
			'page' => $page,
			'total' => $totalpages,
			'records' => $total,
			'rows' => $items
		);

		exit( json_encode( $json ) );
	}

	function get_search_query(){

		$db = JFactory::getDBO();


		$model 	= new JModelLegacy;
		$table = $model->getTable( $this->jtable );

		$table_name = $table->_tbl;

		$field 	= $db->escape( JFactory::getApplication()->input->get( 'searchField' ) );
		$oper 	= $db->escape( JFactory::getApplication()->input->get( 'searchOper' ) );
		$val 	= JFactory::getApplication()->input->get( 'searchString' );

		$ops  = array(
			'eq'=>'=', //equal
			'ne'=>'<>',//not equal
			'lt'=>'<', //less than
			'le'=>'<=',//less than or equal
			'gt'=>'>', //greater than
			'ge'=>'>=',//greater than or equal
			'bw'=>'LIKE', //begins with
			'bn'=>'NOT LIKE', //doesn't begin with
			'in'=>'LIKE', //is in
			'ni'=>'NOT LIKE', //is not in
			'ew'=>'LIKE', //ends with
			'en'=>'NOT LIKE', //doesn't end with
			'cn'=>'LIKE', // contains
			'nc'=>'NOT LIKE'  //doesn't contain
		);

		if($oper == 'bw' || $oper == 'bn') $val .= '%';
		if($oper == 'ew' || $oper == 'en' ) $val = '%'.$val;
		if($oper == 'cn' || $oper == 'nc' || $oper == 'in' || $oper == 'ni') $val = '%'.$val.'%';

		$val 	= $db->Quote( $db->escape( $val ) );
		//$table 	= $this->_table;

		return "{$table_name}.{$field} {$ops[$oper]} {$val}";
	}

	function deploy( $echo=false ){

		$app = JFactory::getApplication();

		$view_name = $this->view->get( '_name' );

		$column_names = array();
		$column_model = array();

		foreach( $this->column_model as $column ){
			$column_names[] = $column['title'];
			$column_model[] = $column['model'];
		}

		$this->config = array(
			'url' => "index.php?option=com_pago&view={$view_name}&task=get_data",
			'editurl' => "index.php?option=com_pago&view={$view_name}&task=editgrid",
			'datatype' => 'json',
			'mtype' => 'get',
			'colNames' => $column_names,
			'colModel' => $column_model,
			'jsonReader' => array(
			  'root'=>'rows',
			  'page'=>'page',
			  'total'=>'total',
			  'records'=>'records',
			  'repeatitems'=>false,
			  'id'=>'0'
		   ),
		  	'search' =>array(
			'closeAfterSearch'=> true,'closeAfterReset' =>true,'closeOnEscape' =>true
			),
			'autowidth'=> true,
			'multiselect' => true,
			'multiboxonly' => true,
			'height' => 'auto',
			'pager'=>$this->pager_selector,
			'rowNum'=>$app->getUserStateFromRequest( "{$view_name}_grid.rows", 'rows', 20 ),
			'page'=>$app->getUserStateFromRequest( "{$view_name}_grid.page", 'page', 1 ),
			'rowList'=>$this->row_list,
			'sortname'=> $app->getUserStateFromRequest( "{$view_name}_grid.sidx", 'sidx', $this->default_sort_field ),
			'sortorder'=> $app->getUserStateFromRequest( "{$view_name}_grid.sord", 'sord', 'desc' ),
			'viewrecords'=> true
		);

		$this->header();
	}

	function html(){

		return '<table id="' . $this->grid_selector . '" style="width:100%"></table><div id="' . $this->pager_selector . '"></div>';
	}

	function add_tool( $type, $args=array() ){

		switch($type){
			case 'custom':
			call_user_func_array( array( 'JToolBarHelper', 'custom' ), $args );
			break;
			case 'search':
			call_user_func_array( array( 'JToolBarHelper', 'custom' ), array( 'search', 'search', 'search.png', JText::_('Search'), false, false ) );
			break;
			default:
			call_user_func_array( array( 'JToolBarHelper', $type ), $args );
		}
	}

	function header(){

		$selector = '#' . $this->grid_selector;
		$pager_selector = '#' . $this->pager_selector;

		$doc = JFactory::getDocument();

		$doc->addStyleSheet( JURI::base(true) . '/components/com_pago/javascript/jquery.jqGrid/css/ui.jqgrid.css' );
		$doc->addScript( JURI::base(true) . '/components/com_pago/javascript/jquery.jqGrid/js/i18n/grid.locale-en.js' );
		$doc->addScript( JURI::base(true) . '/components/com_pago/javascript/jquery.jqGrid/js/jquery.jqGrid.min.js' );

		$doc->addStyleDeclaration("
		#admin_grid .ui-helper-clearfix { overflow: hidden;}
		#admin_grid .ui-searchFilter table td {padding: 10px;}
		#admin_grid .ui-jqgrid .ui-jqgrid-htable th div {
			height: 20px;
		}
		#admin_grid .ui-jqgrid table {
			border-collapse: separate;
			border-spacing: 0;
		}
		#admin_grid .ui-subgrid .tablediv div{
			border:0
		}
		#admin_grid .ui-corner-all {
			-moz-border-radius: 0 0 0 0;
		}
		#admin_grid th.ui-state-default{
			background-image: none;
			border-color:#CCCCCC;
			color: #0B55C4;
			font-weight:bold;
		}
		#admin_grid tr.jqgrow td {
			padding:5px;
			height: 40px;
			vertical-align:middle;
		}
		#admin_grid .tablediv{
			padding:10px;
		}
		#admin_grid .tablediv td{
			border:0px;
		}
		#admin_grid td.subgrid-data {
			border-width: 0 0 1px 0 !important;
		}
		.icon-32-search {
			background-image: url(".JURI::base()."components/com_pago/css/images/search.png);
		}
		");

		$onCellSelect = "function( rowId, iCol){
			if(iCol <1) return false;
			jQuery('$selector').editGridRow(rowId, true);
		}";

		$this->config['onCellSelect'] = '<onCellSelect>';

		$config = json_encode($this->config);
		$config = str_replace( '"<onCellSelect>"', $onCellSelect, $config);

		/*if($subgrid_config){
			$subgrid_config = 'function(subgrid_id, row_id) {
			   var subgrid_table_id, pager_id;
			   subgrid_table_id = subgrid_id+"_t";
			   pager_id = "p_"+subgrid_table_id;
			   jQuery("#"+subgrid_id).html("<table id=\'"+subgrid_table_id+"\' class=\'subgrid scroll\' ></table><div id=\'"+pager_id+"\' class=\'scroll\'></div>");
			   jQuery("#"+subgrid_table_id).jqGrid('.json_encode($subgrid_config).').jqGrid("navGrid","#"+pager_id,{edit:false,add:false,del:false})
			}';

			$config = str_replace( '"<subGridRowExpanded>"', $subgrid_config, $config);
			$config = str_replace( '\"+row_id"', '"+row_id', $config);
			$config = str_replace( '"pager_id"', 'pager_id', $config);
		}*/

		if( !$this->nav_tools ){
			$nav = array(
				'search'=>true,
				'edit'=>true,
				'add'=>true,
				'del'=>true,
				'searchtext'=>'Search'
			);
		} else {
			$nav = $this->nav_tools;
		}

		$nav = json_encode($nav);

		$view_name = $this->view->get( '_name' );

		$doc->addScriptDeclaration("

			jQuery(function(){
				jQuery('$selector')
				.jqGrid($config)
				.navGrid('$pager_selector', $nav)
				.searchGrid({ closeAfterSearch: true,closeAfterReset: true,closeOnEscape: true } );
				  jQuery('span.ui-icon-close').click();

				jQuery(function(){
					jQuery('li#toolbar-search').find('a.toolbar').attr('onclick', '').click(function(){
					  jQuery('span.ui-icon-search').click();
					});
				});

				jQuery('li#toolbar-delete').find('a.toolbar').attr('onclick', '').click(function(){
						var id = jQuery('$selector').getGridParam('selarrrow');

						if(id){
							window.location='index.php?option=com_pago&view={$view_name}&task=delete&cid=' + id;
						} else {
							alert('Please make a selection');
						}
					});

			});
		");

		if( !$this->disable_tools ){
		$doc->addScriptDeclaration("

			jQuery(function(){

				jQuery('li#toolbar-new').find('a.toolbar').attr('onclick', '').click(function(){

				  jQuery('span.ui-icon-plus').click();
				  //window.location='index.php?option=com_pago&view={$view_name}&layout=form&task=new';

				});

				jQuery('li#toolbar-edit').find('a.toolbar').attr('onclick', '').click(function(){

				  var id = jQuery('$selector').getGridParam('selrow');

				  if(id){
					//window.location='index.php?option=com_pago&view={$view_name}&layout=form&task=edit&cid=' + id;
					jQuery('span.ui-icon-pencil').click();
				  } else {
					alert('Please make a selection');
				  }

				});



				jQuery('li#toolbar-publish').find('a.toolbar').attr('onclick', '').click(function(){

				  var id = jQuery('$selector').getGridParam('selarrrow');

				  if(id){
					window.location='index.php?option=com_pago&view={$view_name}&task=publish&cid=' + id;
				  } else {
					alert('Please make a selection');
				  }

				});



				jQuery('li#toolbar-unpublish').find('a.toolbar').attr('onclick', '').click(function(){

				  var id = jQuery('$selector').getGridParam('selarrrow');

				  if(id){
					window.location='index.php?option=com_pago&view={$view_name}&task=unpublish&cid=' + id;
				  } else {
					alert('Please make a selection');
				  }

				});
			});
		");
		}
	}
}
