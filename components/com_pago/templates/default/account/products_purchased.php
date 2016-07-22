<?php 
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access'); 
defined('_JEXEC') or die('Restricted access'); 

$this->document->setTitle( 'Products Purchased' ); 
$this->pathway->addItem( 'Products Purchased' , JRoute::_( '&view=account' ) );

?>
<?php $this->load_header(); ?>
<div id="pg-account">
	<div id="pg-account-menu" class="pg-wrapper-left">
		<?php echo $this->modules->render_position( 'pago_account_menu' ); ?>
		<?php if ( $pago_account_menu = PagoHelper::load_template( 'account', 'account_menu' ) ) require $pago_account_menu; ?>
	</div>
	<div class = "pg-wrapper-container">
		<h3><?php echo JText::_('PAGO_ACCOUNT_DASHBOARD_RECENT_PURCHASED'); ?></h3>
		<div>
			<?php if ( !$this->orders_item ) : ?>
				<p><?php echo JText::_('No Recently Purchased Products'); ?></p>
			<?php else : ?>
				<table class="pg-account-table">
					<tbody>
					<tr>
						<th><?php echo JTEXT::_('PAGO_DATE'); ?></th>
						<th><?php echo JTEXT::_('PAGO_NAME'); ?></th>
						<th><?php echo JTEXT::_('PAGO_ORDER_NUMBER'); ?></th>
						<th></th>
					</tr>
					<?php foreach ( $this->orders_item as $item ) { ?>
						<tr>
							<td class="pg-account-recent-purchase-date">
								<?php echo date( 'n/j/Y', strtotime( $item->created ) ); ?>
							</td>
							<td class="pg-account-recent-purchase-item">
							<?php
							$Itemid = $this->nav->getItemid($item->id, $item->primary_category);
							$link = JRoute::_('index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->primary_category .'&Itemid=' . $Itemid);
							?>
								<a title="<?php echo $item->name; ?>" target="_blank" href="<?php echo $link; ?>">
									<?php echo $item->name; ?>
								</a>
							</td>

							<td class="pg-account-recent-purchase-item-number"><?php echo $item->sku; ?>
								<?php
									$order 	= Pago::get_instance('orders')->get($item->order_id);

									if($item->type == '2' || $order['details']->order_status == 'C')
									{
										$downloadRes = $this->nav->getMediaInfo($item->id);
										
										if(count($downloadRes) > 0)
										{
											for($k = 0; $k < count($downloadRes); $k++)
											{
												if( $downloadRes[$k]->access == 2)
												{
													echo "<br/>";
													$link = 'index.php?option=com_pago&controller=item&task=downloadFiles&fileid=' . $downloadRes[$k]->id;
													echo "<a class='downloadLink' href='" . $link . "'>" . JTEXT::_("PAGO_DOWNLOAD") . $downloadRes[$k]->file_name . "</a>";
												}
												
										
											}
										}
									}
								?>
							</td>

							<td class="pg-account-recent-purchase-invoice">
								<a title="View Invoice (new window)" target="_blank" href="<?php echo JRoute::_( 'index.php?option=com_pago&view=account&layout=order_receipt&order_id=' . $item->order_id ); ?>">
									<?php echo JTEXT::_("PAGO_VIEW_ORDER"); ?>
								</a>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php echo $this->load_footer(); ?>
