<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
$config = Pago::get_instance('config')->get();
?>

<?php $mode = $params->get( 'mode','0' ); ?>
<div id="pg-currency-converter" class="pg-currency-converter pg-module<?php echo $params->get( 'moduleclass_sfx' ) ?> pg-main-container">
	<div class="pg-view-currency-converter">
		<?php if ($mode == 1) { ?>
			<span class="pg-currency-text">
				<?php echo JText::_('MOD_PAGO_CURRENCY_CHANGE_CURRENCY'); ?>
			</span>
			<form action="" method="post" id="currencyForm" class="currencyForm">
				<select onchange="this.form.submit();" name="currencyChanger" id="currencyChanger">
					<?php
						$html = '';
						foreach ($currencies as $currency) {
							$html .="<option ";
							if($currentCurrencyId == $currency->id){
								$html .=" selected='selected' "; 	
							}
							$html .=" value=".$currency->id.">".$currency->code.'-'.$currency->symbol."</option>";
						}
						echo $html;
					?>
				</select>
			</form>
		<?php } if ($mode == 0) { ?>
			<form action="" method="post" id="currencyForm" class="currencyForm">
				<div class="currrency-list">
					<?php
						$html = '';
						foreach ($currencies as $currency) {
							$html .="<a href='#' ";
							if($currentCurrencyId == $currency->id){
								$html .=" class='selected'"; 	
							}
							$html .=" data-id='".$currency->id."'>".$currency->code.'-'.$currency->symbol."</a>";
						}
						echo $html;
					?>
				</div>
				<input type="hidden" name="currencyChanger" class="currencyChanger" id="currencyChanger" value="">
			</form>
		<?php } ?>
    </div>
</div>