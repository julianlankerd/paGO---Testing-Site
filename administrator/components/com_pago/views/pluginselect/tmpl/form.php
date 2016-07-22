<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
//print_r($this);die();

$doc =& JFactory::getDocument();

/*$doc->addStyleSheet( JURI::root(true) . '/administrator/components/com_pago/library/js/comboselect_plugin/jquery.comboselect.css' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/jquery.js' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/comboselect_plugin/jquery.selso.js' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/comboselect_plugin/jquery.comboselect.js' );*/

/*$doc->addStyleSheet( JURI::root(true) . '/administrator/components/com_pago/library/js/jQuery.crossSelect/jquery.crossSelect.css' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/jquery.js' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/jQuery.crossSelect/jQuery.crossSelect-0.5.js' );*/

$doc->addStyleSheet( JURI::root(true) . '/administrator/components/com_pago/library/js/dropdown-check-list.0.9/css/ui.dropdownchecklist.css' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/dropdown-check-list.0.9/js/jquery-min.js' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/dropdown-check-list.0.9/js/ui.core-min.js' );
$doc->addScript( JURI::root(true) . '/administrator/components/com_pago/library/js/dropdown-check-list.0.9/js/ui.dropdownchecklist-min.js' );
$doc->addScriptDeclaration("
 $.noConflict();
  jQuery(document).ready(function($) {
    	//$('#secondary_category').comboselect({ sort: 'both', addbtn: '+',  rembtn: '-' });
		//$('#secondary_category').crossSelect();
		$('#secondary_category').dropdownchecklist({ maxDropHeight: 400, textFormatFunction: function(options) {
                var selectedOptions = options.filter(\":selected\");
                var countOfSelected = selectedOptions.size();
                var size = options.size();
                switch(countOfSelected) {
                    case 0: return \"Nobody\";
                    case 1: return selectedOptions.text();
                    case size: return \"Everybody\";
                    default: return countOfSelected + \" People\";
                }
            } });

  });

");

?>


<?php
jimport('joomla.html.pane');
		JFilterOutput::objectHTMLSafe( $row );

		$db		= &JFactory::getDBO();
		$editor = &JFactory::getEditor();
        // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
		$pane	= &JPane::getInstance('sliders', array('allowAllClose' => true));

		JHTML::_('behavior.tooltip');
		?>
		

		<form action="index.php" method="post" name="adminForm">

		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top" width="50%">
				<?php //ContentView::_displayArticleDetails( $row, $lists ); ?>
                
                <table class="adminform" cellspacing="0" cellpadding="0" border="0" width="100%">
<tbody>
    <tr>
        <td><label for="title"> Title </label></td>
        <td><input class="inputbox" name="name" id="title" size="40" maxlength="255" value="<?php echo $this->item->name;?>" type="text"></td>
        
        <?php 
			
			$active = 0;
			$inactive = 0;
			
			if( $this->item->published ){
				$active = 'checked="checked"';
			} else {
				$inactive = 'checked="checked"';
			}
			//$attribs       .= ' multiple="multiple"';
			//category stuff
			$options[] = array(
				'id' => 1,
				'name' => 'Root'
			);
				
			foreach($this->cats as $cat){
				//$html .= str_repeat('>', $cat['depth'] ).$cat['name'].'<br/>';
				$options[] = array(
					'id' => $cat['id'],
					'name' => str_repeat('.', $cat['depth'] ).$cat['name']
				);
			}
		?>
        
        <td><label for="catid"> Primary Category </label></td>
        <td><?php echo JHTML::_('select.genericlist',$options, 'primary_category', false, 'id', 'name', $this->item->primary_category, 'primary_category') ?></td>
    </tr>
    <tr>
        <td><label for="alias"> Alias </label></td>
        <td><input class="inputbox" name="alias" id="alias" size="40" maxlength="255" value="<?php echo $this->item->alias;?>" title="Leave this blank and Joomla! will fill in a default value, which is the title in lower case and with dashes instead of spaces. You may enter the Alias manually. Use lowercase letters and hypens (-). No spaces or underscores are allowed. The Alias will be used in the SEF URL. Default value will be a date and time if the title is typed in non-latin letters." type="text"></td>
        
        
        <?php 
			$attribs = false;
			$options = array();
			$options[] = array(
				'id' => 1,
				'name' => 'Root'
			);
				
			foreach($this->cats as $cat){
				//$html .= str_repeat('>', $cat['depth'] ).$cat['name'].'<br/>';
				$options[] = array(
					'id' => $cat['id'],
					'name' => str_repeat('.', $cat['depth'] ).$cat['name']
				);
			}
			
			$attribs       .= ' multiple="multiple"';
		?>
        
        <td><label> Secondary Category(s) </label></td>
        <td><?php echo JHTML::_('select.genericlist',$options, 'secondary_category[]', $attribs, 'id', 'name', $this->item->secondary_categories, 'secondary_category') ?></td>
    </tr>
    <tr>
        <td><label> Published </label></td>
        <td><input name="published" id="state0" value="0" <?php echo $inactive ?> type="radio">
            <label for="state0">No</label>
            <input name="published" id="state1" value="1" <?php echo $active ?> type="radio">
            <label for="state1">Yes</label></td>
        
        
    </tr>
</tbody>
</table>


				<table class="adminform">
				<tr>
					<td>
						<?php
						// parameters : areaname, content, width, height, cols, rows
						echo $editor->display( 'content',  $this->item->content , '100%', '500', '75', '20' ) ;
						?>
					</td>
				</tr>
				</table>
			</td>
			<td valign="top" width="320" style="padding: 7px 0 0 5px">
            
            
             <fieldset class="adminform">
       
        
        
            <table width="100%" style="border: 0px dashed silver; padding: 5px; margin-bottom: 10px;">
    <tbody>
        <tr>
            <td><strong>Item ID:</strong></td>
            <td> <?php echo $this->item->id ?> </td>
        </tr>
        <tr>
            <td><strong>State</strong></td>
            <td><?php echo ($this->item->published ? '<span style="color:green">Published</span>' : '<span style="color:red">Unpublished</span>' )?></td>
        </tr>
        <!--
        <tr>
            <td><strong>Hits</strong></td>
            <td> 43 <span>
                <input type="button" onclick="submitbutton('resethits');" value="Reset" class="button" name="reset_hits">
                </span></td>
        </tr>
        <tr>
            <td><strong>Revised</strong></td>
            <td> 7 Times </td>
        </tr>
        -->
        <tr>
            <td><strong>Created</strong></td>
            <td><?php echo date( 'l jS \of F Y h:i:s A', strtotime( $this->item->created )) ?></td>
        </tr>
        <tr>
            <td><strong>Modified</strong></td>
            <td><?php echo date( 'l jS \of F Y h:i:s A', strtotime($this->item->modified )) ?></td>
        </tr>
    </tbody>
</table>



			<?php
				//ContentView::_displayArticleStats($row, $lists);

				$title = JText::_( 'Parameters - Access Control Level' );
				echo $pane->startPane("content-pane");
				echo $pane->startPanel( $title, "detail-page" );
				//echo $form->render('details');

				$title = JText::_( 'Parameters - Plugin Control' );
				echo $pane->endPanel();
				echo $pane->startPanel( $title, "params-page" );
				//echo $form->render('params', 'advanced');

				$title = JText::_( 'Metadata Information' );
				echo $pane->endPanel();
				echo $pane->startPanel( $title, "metadata-page" );
				//echo $form->render('meta', 'metadata');

				echo $pane->endPanel();
				echo $pane->endPane();
			?>
            
            </fieldset>
            
			</td>
		</tr>
		</table>

		
		<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="option" value="com_pago" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="cancel" />
<input type="hidden" name="view" value="items" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
		echo JHTML::_('behavior.keepalive');