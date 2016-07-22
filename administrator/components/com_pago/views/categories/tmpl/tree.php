<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 

$doc =& JFactory::getDocument();

//$doc->addStyleSheet( JURI::root(true) . '/administrator/components/com_pago/library/js/dropdown-check-list.0.9/css/ui.dropdownchecklist.css' );
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.js' );
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/json2.js' );
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.notifier.js' );
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jsTree/jquery.jstree.js' );
$doc->addScript(
	JURI::root(true) . '/administrator/components/com_pago/views/categories/tmpl/js/tree.js?123'
);

$PAGO_CAT_TREE_VIEW_URL =
	JURI::root(true) . '/administrator/index.php?option=com_pago&view=categories';

$doc->addScriptDeclaration( "PAGO_CAT_TREE_VIEW_URL = '$PAGO_CAT_TREE_VIEW_URL';" );
?>

<p>Right click to create, edit or remove categories. Drag and drop to change order or parent category.</p>
        
<button onclick="cats_expand_all()">Expand All</button>
<div id="pago_category_tree">
                       
<ul>
	<li class="node parent jstree-undetermined" node="1" id="node1" depth="1"> 
		<a class="node" href="index.php?option=com_pago&amp;view=categories&amp;cid=1">Root</a>
		<?php echo $this->cat_ul; ?>
	</li>
</ul>
</div>
