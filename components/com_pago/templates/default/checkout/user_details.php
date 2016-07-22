<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 

$doc =& JFactory::getDocument();

$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.js' );
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/jquery.chained.mini.js' );
$doc->addScript( JURI::root(true) . '/components/com_pago/javascript/json2.js' );

$doc->addScriptDeclaration("
	jQuery.noConflict();
	jQuery(document).ready(function() {
		$('#state').chainedTo('#country');
		$('#m_state').chainedTo('#m_country');
	});
");

$form = $this->form;     

if( isset( $this->user_fields ) ){
	foreach( $this->user_fields as $field ){
		
		switch ( $field->type ) {
			case 'text':
				switch ( $field->name ) {
					case '-first_name':
						$form->addField( $field->type, $field->name, JText::_( $field->title ), $field->required, $this->user->first_name );
						break;
					case '-last_name':
						$form->addField( $field->type, $field->name, JText::_( $field->title ), $field->required, $this->user->last_name );
						break;
					default:
						$form->addField( $field->type, $field->name, JText::_( $field->title ), $field->required );
				}
				break;
			case 'delimiter':
				
				$form->addItem('<h4>' . JText::_( $field->title ) . '</h4>');
				break;
			case 'countries':
				$form->addField( 'select', $field->name, 
						JText::_( $field->title ), $field->required, 
						false, $this->countries );
				break;
			case 'states':
				$form->addField( 'select', $field->name, 
						JText::_( $field->title ), $field->required, 
						false, $this->states['options'], $this->states['attribs'] );
				break;
			default:
				
				if( isset( $this->user_fields_values[ $field->fieldid ] ) ){
					$form->addField( $field->type, $field->name, 
						JText::_( $field->title ), $field->required, 
						false, $this->user_fields_values[ $field->fieldid ] );
				} else {
					$form->addField( $field->type, $field->name, JText::_( $field->title ), $field->required );
				}
		}
	}
	
	
		
    $form->validator("agreed", "termValidator");
	
	if($this->form_data){
		
		$form->fields[ 'div' ] = $form->fields[ '0' ];
		$form->fields[ 'div' ]['content'] = '<div style="position:absolute;top:-16px;right:0">';
		
		foreach($form->fields as $field=>$v){	
			$v['init_value'] = false;		
			if(isset($this->form_data_m[$field])){
				$v['init_value'] = $this->form_data_m[$field];
			}
			$form->fields[ 'm_' . $field ] = $v;	
		}
		
		$form->fields[ 'enddiv' ] = $form->fields[ '0' ];
		$form->fields[ 'enddiv' ]['content'] = '</div>';
		
		$form->fields[ 'm_0' ]['content'] = '<h4>' . JText::_( 'PAGO_USER_FORM_MAILTO_LBL' ) . '</h4>';
		
		$form->validator("m_agreed", "termValidator");
		
		unset($form->fields[ 'm_1' ]);
		unset($form->fields[ 'm_agreed' ]);
	
	}
	
	$form->addField( 'hidden', 'task', false, false, 'user_details_save' );
		
	ob_start();
        $form->display(JText::_( 'PAGO_UPDATE_USER_DETAILS' ), "user_details");		
		$billing_form = ob_get_clean();
}
?>

<style>
.pago_user_details_forms {position:relative;}
.pago_user_details_forms label{float:left;width:130px;}

label.m_agreed{width:250px;}
.pago_user_details_forms select{width:160px;}
#submit_user_details{margin:50px -50px}
</style>

<div class="pago_user_details_forms">
	<?php echo $billing_form ?>
</div>

<div style="clear:both"><!-- --></div>


<?php if( !$form->getErrors() && $this->form_data ): ?>
<div style="text-align:center;margin:10px">
<form style="display: inline;" method="post" action="<?php echo JRoute::_( 'index.php?view=checkout' ) ?>">
	<input type="hidden" name="step" value="shipping" />
    <input type="submit" class="pg-button" value="<?php echo JText::_( 'PAGO_CHECKOUT_CANCEL_CONTINUE' ) ?>" />
</form>
</div>
<?php endif ?>

<?php		
		//setup form
        /*$form->set("title", "Example form");
        $form->set("name", "form_example");
        $form->set("linebreaks", false);       
        $form->set("showDebug", true);
        $form->set("divs", true);                
        $form->set("errorPosition", "in_before");
        $form->set("submitMessage", "Form submitted!");
        $form->set("showAfterSuccess", true);
        $form->JSprotection("36CxgD");*/

       /* //sample optionlist form radiobuttons and selects
        $optionlist=Array("First value" => "first", "Second value" => "second","Third value" => "third");
        //simple data loading
        $loader=Array("username"=>"John Doe", "email"=>"john@doe.com");
        $form->loadData($loader);

        //mapped data loading (To hide eg. DB field names)
        $loader=Array("dbmessage"=>"Sample message");
        $map=Array("dbmessage"=>"message");
        $form->loadData($loader, $map);

        //add input & misc fields
        $form->addText("Fields marked with * must be filled.");
        $form->addItem("<h2>H2 Example!</h2>");
        $form->addField("text", "username","Name", true);
        $form->addField("text", "email","E-mail", true);
        $form->addField("text", "date","Date", true);
        $form->addField("text", "sub1","Joined", false, false, "class='small'"); 
        $form->addField("text", "sub2",false, false, false, "class='small'");         
        $form->addField("checkbox", "terms","Accept terms", true, false, "I accept the terms.");     
        $form->addField("checkbox", "checkbox","Initially checked", false, true, "waddawadda.");             
        $form->addField("radio", "radiobuttons","Choose one", false, false,  $optionlist);        
        $form->addField("select", "selector","Choose", false, "third", $optionlist);        
        $form->addField("checkbox", "checklist","Choose some", false, "second, first", $optionlist);
        $form->addField("textarea", "message","Message", true, false, "cols='40' rows='7'");
        $form->button("reset", "resetter", "Reset form");        

        //assign validators to certain fields
        $form->validator("username", "textValidator", 2, 20);       
        $form->validator("email", "regExpValidator", "/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/", "Not a valid e-mail address");
        $form->validator("terms", "termValidator");     

        $form->join("sub1", "sub2");   

        //display the form
       // $form->display("Submit", "form1_submit");  
        //save the valid data for further use      
         $result=($form->getData());
         // it is advised to unset the form after saving the data
         unset($form);         */


//------create second form
        /*$form2=new Form();
        $form2->set("showDebug", true);
        $form2->set("title", "Second example form");
        $form2->set("name", "secondform");
        $form2->set("linebreaks", false);
        $form2->set("divs", true);        
        $form2->set("errorPosition", "in_after");
        $form2->set("errorTitle", "Arrrggghhh!!!");
        $form2->set("errorLabel", "<i><sup>DUH!</sup></i> ");
        $form2->set("showAfterSuccess", false);
        $form2->JSprotection("36CxsaegD", "prtcode2");

        //sample errors for debugging
        echo "<p>Some debug messages: </p>";       
        $form2->set("naasdfsme", "xyzsfkjl");
        $form2->set("errorPosition", "somewhere");
        $form2->validator("username2", "textasdValidator", 2, 20);

        $form2->addText("Wazzup!");

        $optionlist=Array("First value" => "first", "Second value" => "second");
       
        $form2->addItem("<fieldset><legend>fieldset</legend>");
        $form2->addField("text", "username2","Name", true, "test", "maxlength='10'");
        $form2->addField("text", "email2","E-mail", true);
        $form2->addItem("</fieldset>");
        $form2->addField("hidden", "hiddy","Hidden", false);
        $form2->addField("password", "pass","Password", true);        
        $form2->addField("file", "file","File", true);
        $form2->addField("checkbox", "checkbox2","Accept terms", true, "I accept the terms.");
        

        $form2->validator("username2", "textValidator", 2, 20);
        $form2->validator("email2", "regExpValidator", "/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/", "Not valid e-mail");
        $form2->validator("checkbox2", "termValidator");
        $form2->button("submit", "submit2", "Hit me!");
        $form2->button("reset", "resetter", "Reset");
        $form2->display();        
        $result2=($form2->getData());
        unset($form2);


//------use data from the first form
        if ($result){
            echo "<p>Data from form1 (Example form):</p>";
            foreach ($result as $name =>$item){
                echo "<p>". $name . ": ". $item . "</p>";
            }
        }*/
        ?>
