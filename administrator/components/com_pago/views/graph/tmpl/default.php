<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
PagoHtml::behaviour_jquery();
PagoHtml::behaviour_jqueryui();
$dispatcher = KDispatcher::getInstance();

PagoHtml::add_js( JURI::base() . 'components/com_pago/javascript/com_pago_config.js' );
PagoHtml::uniform();
PagoHtml::apply_layout_fixes();

JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');
$dispatcher = KDispatcher::getInstance();
include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'menu_config.php');
PagoHtml::pago_top( $menu_items, 'tabs', false );
?>
<script type="text/javascript">

Joomla.submitbutton = function (pressbutton) {
		submitbutton(pressbutton);
	}
	submitbutton = function (pressbutton) 
	{
		var form = document.adminForm;
		submitform(pressbutton);
		
	}

</script>

<div class="pg-dashboard-data-container">

<?php if($this->type == 'purchase'): ?>
<!-- purchased Item start -->

<div class="pg-container-header"><?php echo JTEXT::_("COM_PAGO_PURCHASED_ITEMS"); ?><div class="pg-right no-margin"></div></div>		
<div class="pg-pad-20 pg-white-bckg pg-border best-selling-block">
	<div class="pg-graphd-purchased-block">
		<div class="pggraphDiv">
		
			<?php if(count($this->purchasedItems) > 0): ?>	
				<a rel="nofollow" href="index.php?option=com_pago&view=graph&task=exportdata&export=purchaseItem"><span class="icon-print"></span></a> 
			<?php endif; ?>
				<div class="pgDivHeading">
								   <div class="pgDivRowSR">ID</div>
								  <div class="pgHeadDivRow pg-graph-name-lbl"><?php echo JTEXT::_("COM_PAGO_PURCHASED_ITEM_NAME"); ?>  </div>
								  <div class="pgHeadDivRow pg-graph-price-lbl"><?php echo JTEXT::_("COM_PAGO_PURCHASED_ITEM_PRICE"); ?>  </div>
								  <div class="pgHeadDivRow pg-graph-qty-lbl"><?php echo JTEXT::_("COM_PAGO_PURCHASED_ITEM_QUANTITY"); ?>  </div>
				</div>
				
				<div class="pgTableBody">
				
					<?php for($p = 0; $p < count($this->purchasedItems); $p++): ?>

						 <div class="pgDivGraphDeatils">
							<div class="pgDivLeft pg-graph-numbering"><?php echo $p+1; ?></div>
							<div class="pgDivLeft pg-graph-name"><a href="<?php echo JURI::root(); ?>administrator/index.php?option=com_pago&amp;controller=items&amp;task=edit&amp;view=items&amp;cid[]=<?php echo $this->purchasedItems[$p]->id; ?>" class="product-name"><?php echo $this->purchasedItems[$p]->name; ?>	</a></div>
							<div class="pgDivLeft pg-graph-price"><?php echo Pago::get_instance( 'price' )->format($this->purchasedItems[$p]->price); ?></div>
							<div class="pgDivLeft pg-graph-qty"><?php echo $this->purchasedItems[$p]->quantity; ?></div>
						</div>
					<?php endfor; ?>							
				</div>
				
		</div>
	</div>
</div>
<!-- purchased Item end -->
<br />
	
<!-- Unpurchased Item start -->	
<div class="pg-container-header"><?php echo JTEXT::_("COM_PAGO_UNPURCHASED_ITEMS"); ?><div class="pg-right no-margin"></div></div>		
<div class="pg-pad-20 pg-white-bckg pg-border best-selling-block">
	<div class="pg-graphd-unpurchased-block">
		<div class="pggraphDiv">
		<?php if(count($this->unpurchasedItems) > 0): ?>	
			<a rel="nofollow" href="index.php?option=com_pago&view=graph&task=exportdata&export=unpurchaseItem"><span class="icon-print"></span></a> 
		<?php endif; ?>
			<div class="pgDivHeading">
				  <div class="pgDivRowSR">ID</div>
				  <div class="pgHeadDivRow pg-graph-name-lbl"><?php echo JTEXT::_("COM_PAGO_UNPURCHASED_ITEM_NAME"); ?>  </div>
				  <div class="pgHeadDivRow pg-graph-price-lbl"><?php echo JTEXT::_("COM_PAGO_UNPURCHASED_ITEM_PRICE"); ?>  </div>
			</div>
			
			<div class="pgTableBody">
				<?php for($p = 0; $p < count($this->unpurchasedItems); $p++): ?>

					 <div class="pgDivGraphDeatils">
						<div class="pgDivLeft pg-graph-numbering"><?php echo $p+1; ?></div>
						<div class="pgDivLeft pg-graph-name"><a href="<?php echo JURI::root(); ?>administrator/index.php?option=com_pago&amp;controller=items&amp;task=edit&amp;view=items&amp;cid[]=<?php echo $this->unpurchasedItems[$p]->id; ?>" class="product-name"><?php echo $this->unpurchasedItems[$p]->name; ?>	</a></div>
						<div class="pgDivLeft pg-graph-price"><?php echo Pago::get_instance( 'price' )->format($this->unpurchasedItems[$p]->price); ?></div>
					</div>
				<?php endfor; ?>							
			</div>
			
		</div>
	</div>
</div>
<!-- Unpurchased Item end -->	
<?php endif; ?>


<!-- Total revenue start -->
<?php
$generated = JFactory::getApplication()->input->getInt('generated');
 if($this->type == 'revenue'): ?>

<div class="pg-container-header"><?php echo JTEXT::_("COM_PAGO_GRAPH_REVENUE"); ?><div class="pg-right no-margin"></div></div>		
<div class="pg-pad-20 pg-white-bckg pg-border best-selling-block">
	<div class="pg-graphd-revenue-block">
		<div class="pggraphDiv">
			
		<div class="pg-row">
			<div class="pg-col-6">
			<form action="<?php echo 'index.php?option=com_pago'; ?>" method="post" name="adminForm" id="adminForm">
				<fieldset class="">
					<?php 
					$myDate = JFactory::getApplication()->input->get('startdate');
					$currentDate = JFactory::getApplication()->input->get('enddate');
					
					if(!$currentDate)
					{
						$currentDate = date("Y-m-d"); 
					}
					
					if(!$myDate)
					{
						$myDate = date("Y-01-01"); 
					}
					
					?>
					<div class="pgrevnurDt">From: <?php echo JHTML::calendar($myDate,'revenue_start_date','revenue_start_date','%Y-%m-%d'); ?></div><div class="pgrevnurDt">To: <?php echo JHTML::calendar($currentDate,'revenue_end_date','revenue_end_date','%Y-%m-%d'); ?></span></div>
					<div><input name="submitbtn" id="submitbtn" type="submit" value="Generate Revenue"></div>
				</fieldset>
				<input type="hidden" name="option" value="com_pago" />
				<input type="hidden" name="task" value="generateRevenue" />
				<input type="hidden" name="view" value="graph" />
				<?php echo JHTML::_('form.token'); ?>
		</form>

			</div>
		</div>
		<?php if($this->generated == '1'):
				if(isset($this->revenueDetails[0]->total)): ?>
					<a rel="nofollow" href="index.php?option=com_pago&view=graph&task=exportdata&export=revenue&rtotal=<?php echo $this->revenueDetails[0]->total?>&rsubtl=<?php echo $this->revenueDetails[0]->subtotal?>&startdt=<?php echo $myDate; ?>&enddt=<?php echo $currentDate; ?>"><span class="icon-print"></span></a> 
					
				<div class="pgDivHeading">
					  <div class="pgHeadDivRow pg-graph-rev-lbl"><?php echo JTEXT::_("COM_PAGO_TOTAL_REV_LBL"); ?>  </div>
					  <div class="pgHeadDivRow pg-graph-totlrev"><?php echo Pago::get_instance( 'price' )->format($this->revenueDetails[0]->total); ?>  </div>
					   <div class="pgHeadDivRow pg-graph-rev-lbl"><?php echo JTEXT::_("COM_PAGO_SUBTOTAL_REV_LBL"); ?>  </div>
					  <div class="pgHeadDivRow pg-graph-totlrev"><?php echo Pago::get_instance( 'price' )->format($this->revenueDetails[0]->subtotal); ?>  </div>
				</div>
		<?php else:
				echo '<div class="pg-grpahres-ntfound">' . JTEXT::_("COM_PAGO_GRAPH_RESULT_NOT_FOUND") . '<div class="pg-right no-margin"></div></div>';
				endif;
			endif;
		?>
			
		</div>
	</div>
</div>	 			

<?php endif; ?>
<!-- Total revenue  end -->

<!-- Average order start -->
<?php
 if($this->type == 'avgord'): ?>
<div class="pg-container-header"><?php echo JTEXT::_("COM_PAGO_GRAPH_AVG_ORDER"); ?><div class="pg-right no-margin"></div></div>		
<div class="pg-pad-20 pg-white-bckg pg-border best-selling-block">
	<div class="pg-graphd-revenue-block">
		<div class="pggraphDiv">
			
		<div class="pg-row">
			<div class="pg-col-6">
			<form action="<?php echo 'index.php?option=com_pago'; ?>" method="post" name="adminForm" id="adminForm">
				<fieldset class="">
					<?php 
					$myDate = JFactory::getApplication()->input->get('startdate');
					$currentDate = JFactory::getApplication()->input->get('enddate');
					
					if(!$currentDate)
					{
						$currentDate = date("Y-m-d"); 
					}
					
					if(!$myDate)
					{
						$myDate = date("Y-01-01"); 
					}
					?>
					<div class="pgrevnurDt">From: <?php echo JHTML::calendar($myDate,'order_start_date','order_start_date','%Y-%m-%d'); ?></div><div class="pgrevnurDt">To: <?php echo JHTML::calendar($currentDate,'order_end_date','order_end_date','%Y-%m-%d'); ?></span></div>
					<div><input name="submitbtn" id="submitbtn" type="submit" value="Generate Average Order"></div>
				</fieldset>
				<input type="hidden" name="option" value="com_pago" />
				<input type="hidden" name="task" value="generateAvgOrder" />
				<input type="hidden" name="view" value="graph" />
				<?php echo JHTML::_('form.token'); ?>
		</form>

			</div>
		</div>
		<?php if($this->avgOrder == '1')
			{ 
				if(isset($this->orderDetails[0]->total))
				{
				?>
		<a rel="nofollow" href="index.php?option=com_pago&view=graph&task=exportdata&export=avgorder&rtotal=<?php echo $this->orderDetails[0]->total?>&rsubtl=<?php echo $this->orderDetails[0]->subtotal?>&startdt=<?php echo $myDate; ?>&enddt=<?php echo $currentDate; ?>"><span class="icon-print"></span></a> 
				<div class="pgDivHeading">
					  <div class="pgHeadDivRow pg-graph-rev-lbl"><?php echo JTEXT::_("COM_PAGO_TOTAL_ORDERAVG_LBL"); ?>  </div>
					  <div class="pgHeadDivRow pg-graph-totlrev"><?php echo Pago::get_instance( 'price' )->format($this->orderDetails[0]->total); ?>  </div>
					   <div class="pgHeadDivRow pg-graph-rev-lbl"><?php echo JTEXT::_("COM_PAGO_SUBTOTAL_ORDERAVG_LBL"); ?>  </div>
					  <div class="pgHeadDivRow pg-graph-totlrev"><?php echo Pago::get_instance( 'price' )->format($this->orderDetails[0]->subtotal); ?>  </div>
				</div>
		<?php 
				}
				else
				{
					echo '<div class="pg-grpahres-ntfound">' . JTEXT::_("COM_PAGO_GRAPH_RESULT_NOT_FOUND") . '<div class="pg-right no-margin"></div></div>';
				}
		}?>
			
		</div>
	</div>
</div>	 			

<?php endif; ?>
<!-- Average order  end -->


<?php if($this->type == 'search')
{ 
$listOrder = 'count';
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<!-- Keywords start -->

<div class="pg-container-header"><?php echo JTEXT::_("COM_PAGO_SEARCH_KEYWORDS"); ?><div class="pg-right no-margin"></div></div>		
<div class="pg-pad-20 pg-white-bckg pg-border best-selling-block">
	<div class="pg-graphd-keyword-block">
	<form action="<?php echo JRoute::_('index.php?option=com_pago&view=graph&type=search'); ?>" method="post" name="adminForm" id="adminForm">

		<div class="pggraphDiv">
		<?php if(count($this->keywords) > 0){?><a rel="nofollow" href="index.php?option=com_pago&view=graph&task=exportdata&export=keywords"><span class="icon-print"></span></a><?php } ?>
				<div class="pgDivHeading">
								   <div class="pgDivRowSR">ID</div>
								  <div class="pgHeadDivRow pg-graph-name-lbl"><?php echo JTEXT::_("COM_PAGO_GRAPH_KEYWORD"); ?>  </div>
								  <div class="pgHeadDivRow pg-graph-count-lbl"><?php echo JHtml::_('grid.sort', 'COM_PAGO_GRAPH_KEYWORD_COUNT', 'count', $listDirn, $listOrder); ?></div>
				</div>
				
				<div class="pgTableBody">
					<?php for($p = 0; $p < count($this->keywords); $p++): ?>

						 <div class="pgDivGraphDeatils">
							<div class="pgDivLeft pg-graph-numbering"><?php echo $p+1; ?></div>
							<div class="pgDivLeft pg-graph-name"><?php echo $this->keywords[$p]->pgkeyword; ?>	</div>
							<div class="pgDivLeft pg-graph-count"><?php echo $this->keywords[$p]->count; ?></div>
						</div>
					<?php endfor; ?>							
				</div>
				
		</div>
		<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="grpah" />
		<input type="hidden" name="type" value="search" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
		</form>
	</div>
</div>
<?php
} ?>
<!-- keywords end -->	

<!-- Abandoned cart start -->
<?php if($this->type == 'cart'):

	if(count($this->abandonedCart) > 0):
?>
<div class="pg-container-header"><?php echo JTEXT::_("COM_PAGO_ABANDONED_CART"); ?><div class="pg-right no-margin"></div></div>		
<div class="pg-pad-20 pg-white-bckg pg-border best-selling-block">
	<div class="pg-graphd-keyword-block">
<a rel="nofollow" href="index.php?option=com_pago&view=graph&task=exportdata&export=abandoned"><span class="icon-print"></span></a>
				<div class="pgDivHeading">
					  <div class="pgDivRowSR">ID</div>
					  <div class="pgHeadDivRow abandonedLbl"><?php echo JTEXT::_("COM_PAGO_ABANDONED_CART_AMOUNT"); ?>  </div>
					  <div class="pgHeadDivRow abandonedLbl"><?php echo JTEXT::_("COM_PAGO_ABANDONED_CART_DATE"); ?>  </div>
					  <div class="pgHeadDivRow abandonedLbl"><?php echo JTEXT::_("COM_PAGO_ABANDONED_CART_USER_ID"); ?>  </div>
					   <div class="pgHeadDivRow abandonedLbl"><?php echo JTEXT::_("COM_PAGO_ABANDONED_CART_USER_EMAIL"); ?>  </div>
				</div>
				
				<div class="pgTableBodyCart">
					<?php 
					$s = 0;
					$temp = false;
					for($p = 0; $p < count($this->abandonedCart); $p++)
					{
						$data = json_decode( $this->abandonedCart[$p]->data, true );
						$email ='';
						
						if($this->abandonedCart[$p]->user_id !=0)
						{
							$model = $this->getModel('Graph','PagoModel');
							$email = $model->getEmail($this->abandonedCart[$p]->user_id);
						}
						//if($abandonedCart[$p]->user_id = '688'){echo '<pre/>';print_r($data);}
							if(array_key_exists('cart_0', $data))
							{
								
								if((array_key_exists('cart_' . $this->abandonedCart[$p]->user_id, $data)) && (count($data['cart_' . $this->abandonedCart[$p]->user_id]['items']) == 0))				
								{
									continue;
								}
								
								if(array_key_exists('cart_' . $this->abandonedCart[$p]->user_id, $data))
								{
									$price = Pago::get_instance( 'price' )->format($data['cart_' . $this->abandonedCart[$p]->user_id]['total']);
								}
								else
								{
									$price = Pago::get_instance( 'price' )->format($data['cart_0']['total']);
								}
								
								$temp = true;
							?>
	 
							 <div class="pgDivGraphDeatils">
								<div class="pgDivLeft pg-graph-numbering"><?php echo ++$s; ?></div>
								<div class="pgDivLeft abandonedVal"><?php  echo $price; ?></div>
								<div class="pgDivLeft abandonedVal"><?php echo $this->abandonedCart[$p]->created; ?></div>
								<div class="pgDivLeft abandonedVal"><?php echo $this->abandonedCart[$p]->user_id; ?></div>
								<div class="pgDivLeft abandonedVal"><?php echo $email; ?></div>
							</div> 
						<?php } 
					}
					
					if($temp == false)
					{
						echo '<div class="pg-grpahres-ntfound">' . JTEXT::_("COM_PAGO_GRAPH_RESULT_NOT_FOUND") . '<div class="pg-right no-margin"></div></div>';
					}
					?>							
				</div>
	</div>
</div>
<?php
	endif;
 endif; ?>
<!-- Abandoned cart end -->	



<?php
 PagoHtml::pago_bottom();