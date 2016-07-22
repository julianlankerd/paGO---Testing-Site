<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldTierInput extends JFormField
{
    /**
     * Element name
     *
     * @access       public
     * @var          string
     */
    protected $type = 'TierInput';

    public function getInput()
    {
        $name = $this->name;
		$value = $this->value;
		$node = $this->element;
		$control_name = $this->id;

		$values = json_decode($value);

        $value = $value ? $value : '[{"1":"test","2":"test2"}]';

        $html = '<script type="text/javascript" src="'.JURI::root( true ).'/components/com_pago/javascript/jquery.js"></script>';
        $html .= '<script type="text/javascript">/* <![CDATA[ */ jQuery.noConflict(); ';
        $html .= 'jQuery(document).ready( function($){jQuery("#paramsrate").change( switchItem ); switchItem();});';
        $html .= 'function switchItem(){ rateVal=jQuery("#paramsrate").val(); if( rateVal=="percent" || rateVal=="flat" ){jQuery("#paramsamount-lbl").closest("tr").css("display","");jQuery("#paramstier-lbl").closest("tr").css("display","none")}else{jQuery("#paramstier-lbl").closest("tr").css("display","");jQuery("#paramsamount-lbl").closest("tr").css("display","none")} }';
        $html .= 'function addRow(){jQuery(\'.jpane-slider\').height(\'auto\');jQuery(\'#tierTbl > tbody:last\').append(\'<tr><td><input style="width:5em" type="text" name="qty" value="" onchange="updateData();"/></td><td><input style="width:5em" type="text" name="amt" value="" onchange="updateData();"/></td><td><input type="button" name="delInput" value="Delete" onclick="delRow(this);updateData();return false;"/></td></tr>\');}';
        $html .= 'function delRow(button){jQuery(button).closest(\'tr\').remove();}';
        $html .= 'function updateData(){var data = new Object(); jQuery("#tierTbl > tbody: input").each(function(){if("qty"!=this.name){return;} data[jQuery(this).val()] = jQuery(this).parent().next().children("input").val() }); jQuery("#paramsTier").val(JSON.stringify(data));}';
        $html .= '/* ]]> */</script>';
        $html .= '<table id="tierTbl" summary="">';
        $html .= '<thead><tr><th>Quantity</th><th>Amount</th><th>&nbsp;</th></tr></thead>';
        $html .= '<tfoot><tr><td><input type="button" name="addInput" value="Add" onclick="addRow();return false;"/></td><td><input id="paramsTier" type="hidden" name="params[tier]" value="'.$value.'"/></tr></tfoot>';
        $html .= '<tbody>';

        if ( is_a($values,'stdClass')){
            foreach ($values as $qty => $amt ){
                if ($qty == 1){
                    $html .= '<tr><td><input style="width:5em" type="text" disabled="disabled" name="qty" value="1"/></td><td><input style="width:5em" type="text" name="amt" value="'.$amt.'" onchange="updateData();"/></td><td>&nbsp;</td></tr>';
                } else {
                   $html .= '<tr><td><input style="width:5em" type="text" name="qty" value="'.$qty.'" onchange="updateData();"/></td><td><input style="width:5em" type="text" name="amt" value="'.$amt.'" onchange="updateData();"/></td><td><input type="button" name="delInput" value="Delete" onclick="delRow(this);updateData();return false;"/></td></tr>';
                }
            }
        } else {
            $html .= '<tr><td><input style="width:5em" type="text" disabled="disabled" name="qty" value="1"/></td><td><input style="width:5em" type="text" name="amt" value="" onchange="updateData();"/></td><td>&nbsp;</td></tr>';
        }
        $html .= '';
        $html .= '</tbody></table>';

        return $html;
    }
}