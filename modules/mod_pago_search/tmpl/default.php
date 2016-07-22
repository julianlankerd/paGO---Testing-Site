<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
PagoHtml::behaviour_jqueryui();
$Itemid = JFactory::getApplication()->input->getInt('Itemid');
$searchQuery=JFactory::getApplication()->input->get( 'search_query');

 
if(!isset($Itemid))
{
	$config = Pago::get_instance('config')->get();
	$Itemid = $config->get('checkout.pago_cart_itemid');
}
 ?>
<form name="pg-search" method="get" class="pg-search-form-m">
	<input type="text" style="max-width:100px" name="search_query" value="<?php echo $searchQuery ?>" class="search_query_input-m" placeholder="search" id="<?php if($params->get('ajax_search_enable') == 1){ echo "ajx-search-ena"; }else{echo "ajx-search-dis";}?>"/>
	<input type="submit" style="max-width:100px"  value="Search" class="pg-btn-gray pago-btn-search"/>
	<input type="hidden" name="option" value="com_pago" />
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
</form>

<script type = "text/javascript">
	jQuery(document).ready(function(){
  /*  jQuery(".pago-btn-search").on("click",function(){
      if(jQuery(".search_query_input-m").val()==""){
       return false;
      }
    })*/
		String.prototype.escapeHTML = function() {
        return this.replace(/&/g, "")
                   .replace(/</g, "")
                   .replace(/>/g, "")
                   .replace(/"/g, "")
                   .replace(/'/g, "");
    	}
    	jQuery('.pg-search-form-m').submit(function() {
    		var val = jQuery('.search_query_input-m').val().escapeHTML();
    		jQuery('.search_query_input-m').val(val);
    		return true;
    	});
		
				jQuery(document).on('keyup','#ajx-search-ena',function(){
		jQuery(this).autocomplete({
			 source : function(request, response) {
	            jQuery.ajax({
	            	type: 'POST',
					url: 'index.php',
					data: 'option=com_pago&controller=item&task=get_items&search_query=' +request.term+ '',
					dataType : 'json',
	                success : function(data) {
	                  //  jQuery('#ajx-search-ena').removeClass('itemLoading');
	                    response(data, function(item) {
	                        return item;
	                    });
	                }
	            });
	       },
	        minLength : 3,
	        select : function(event, ui) {
				
	            jQuery('#ajx-search-ena').val(ui.item.label);
	            return false;
	        },
	        search : function(event, ui) {
	           // jQuery('#ajx-search-ena').addClass('itemLoading');
	        }
	    }).data("ui-autocomplete")._renderItem = function (a, b) {
		var Itemid = <?php echo $Itemid ?>;
    return jQuery("<li></li>")
        .data("item.autocomplete", b)
        .append('<a href="<?php echo JURI::root()?>index.php?option=com_pago&view=item&Itemid='+Itemid+'&id=' + b.value + '"> ' + b.label + '</a>  ')
        .appendTo(a);
};
	});

	})
</script>