<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
class modPagoOrderHelper
{
	public static function latestOrdersAvg($selected, $sale_start_date='', $sale_end_date='')
	{
		$html = '';
		$orders = modPagoOrderHelper::get_recent_orders($selected, $sale_start_date, $sale_end_date);
		//$html .= '<tbody id="recentOrders">';
		if(count($orders) > 0)
		{
			foreach ($orders as $order)
			{
				if(array_key_exists('file_name',$order))
				{
					if($order['file_name']!="")
					{
						$image = $order['file_name'] ;
					}
					else
					{
						$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
					}
				}
				else
				{
					$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
				}
				
				$html .= '<tr>';
				$html .= '<td class = "pg-preview">';
				$html .= '<div class ="pg-preview-small-image" style = "background:url(' . $image . ')"></div>';
				$html .= '</td>';
				$html .= '<td class = "pg-preview">' . $order['name'] . '</td>';
				$html .= '<td>' . $order['first_name'] . ' ' . $order['last_name'] .  '</td>';
				$html .= '<td class = "pg-created">';
				$html .= '<div class = "product-created-date">' . JHTML::_("date", $order["cdate"] , JText::_("Y-m-d")) . '</div>';
				$html .= '<div class = "product-reated-time">' . JHTML::_("date", $order["cdate"] , JText::_("H:i:s")) . '</div>';
				$html .= '</td>';
				$html .= '<td class = "pg-price">' . Pago::get_instance("price")->format($order["order_total"]) . '</td>';
				$html .= '</tr>';
			}
		}
		else
		{
			$html .= 'No Orders Available';
		}
							
		//$html .= '</tbody>';
		echo $html;
		exit;
	}
	public static function latestOrders($params)
	{

		$doc = JFactory::getDocument();
		$scripts = "
				jQuery(document).on('change', '#recentOrdersCount', function(event){

    	var ordersCount = jQuery('#recentOrdersCount :selected').val();
		
					jQuery.ajax({
							type: 'POST',
							url: 'index.php',
							data: 'option=com_pago&controller=orders&task=getRecentOrdersList&ordersCount=' +ordersCount+ '',
							
							success: function( response ) {
								if(response){
									
									result = JSON.parse(response);
							
									jQuery('#recentOrders').html(result);
								}
							}
						});
				});
				";
		$doc->addScriptDeclaration($scripts);
		// Module params
		$moduleclass_sfx = $params->get('moduleclass_sfx', '');
		$orders = self::get_recent_orders("months");
		
		?>
		<div class = "pg-dashboard-orders-container">
			<div class = "pg-container-header">
				<?php echo JTEXT::_('PAGO_RECENT_ORDERS')?>
				<div class = "pg-right pg-container-header-buttons">
					<a href="<?php echo JRoute::_('index.php?option=com_pago&view=ordersi') ?>" class = "pg-btn-small pg-btn-dark"><?php echo JTEXT::_('PAGO_VIEW_ALL'); ?></a>
				</div>
			</div>
			
			<div class = "pg-pad-20 pg-white-bckg pg-border">
				<div class = "pg-dashboard-orders-block">
					<?php if ($orders) :?>
						<table class="pg-quick-overview">
							<thead>
								<tr>
									<td class = "pg-preview"><?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_PREVIEW');?></td>
									<td><?php echo JText::_('PAGO_DASHBOARD_PRODUCT_TITLE');?></td>
									<td><?php echo JText::_('PAGO_DASHBOARD_BUYER_NAME');?></td>
									<td class = "pg-created"><?php echo JText::_('PAGO_DASHBOARD_ORDER_DATE');?></td>
									<td class = "pg-price"><?php echo JText::_('PAGO_DASHBOARD_PRICE');?></td>
									<!-- <td><?php //echo JText::_('PAGO_DASHBOARD_STATUS');?></td> -->
								</tr>
							</thead>
						
							<tbody id="recentOrders">
								<?php foreach ($orders as $order) : 
									if(array_key_exists('file_name',$order)){
										if($order['file_name']!=""){
											$image = $order['file_name'] ;
										}
										else{
											$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
										}
									}
									else{
										$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
									}
								?>
									
									<tr>
										<td class = "pg-preview">
											<div class ="pg-preview-small-image" style = "background:url('<?php echo $image; ?>')"></div>
										</td>
										<td><?php echo $order['name']; ?></td>
										<td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
										<td class = "pg-created">
											<div class = "product-created-date"><?php echo JHTML::_('date', $order['cdate'] , JText::_('Y-m-d')); ?></div>
											<div class = "product-reated-time"><?php echo JHTML::_('date', $order['cdate'] , JText::_('H:i:s')); ?></div>
										</td>
										<td class = "pg-price"><?php echo Pago::get_instance('price')->format($order['order_total']); ?></td>
										<!-- <td><span title="<?php// echo $order['order_status']; ?>" class="pg-icon pg-<?php// echo strtolower(preg_replace("/[^A-Za-z0-9]/", "-", $order['order_status'])); ?>"><?php echo $order->order_status; ?></span></td> -->
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else:?>
						<?php echo JText::_('PAGO_NO_ORDERS');?>
					<?php endif; ?>
				</div>
				<div id="dashOrdersLoader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
			</div>
		</div>
	<?php
	}

	// Get Recent Orders
	public static function get_recent_orders($sale, $sale_start_date='', $sale_end_date='')
	{
		Pago::load_helpers( 'imagehandler' );
		$db = JFactory::getDBO();
			
		if($sale == "months")
		{
			$sql = "SELECT DISTINCT  o.*, u.first_name, u.last_name, i.item_id,i.varation_id FROM #__pago_orders AS o LEFT JOIN #__pago_orders_addresses AS u ON o.order_id = u.order_id LEFT JOIN #__pago_orders_items AS i ON o.order_id=i.order_id WHERE o.cdate > DATE_SUB( NOW( ) , INTERVAL 30 DAY) AND u.address_type = 'b' AND o.order_status <> 'A' ORDER BY o.order_id DESC LIMIT 5";

		}
		else if($sale == "days")
		{
			$sql = "SELECT DISTINCT  o.*, u.first_name, u.last_name, i.item_id,i.varation_id FROM #__pago_orders AS o LEFT JOIN #__pago_orders_addresses AS u ON o.order_id = u.order_id LEFT JOIN #__pago_orders_items AS i ON o.order_id=i.order_id WHERE o.cdate > DATE_SUB( NOW( ) , INTERVAL 7 DAY) AND u.address_type = 'b' AND o.order_status <> 'A' ORDER BY o.order_id DESC LIMIT 5";
		}
		else if($sale == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$sql = "SELECT DISTINCT  o.*, u.first_name, u.last_name, i.item_id,i.varation_id FROM #__pago_orders AS o LEFT JOIN #__pago_orders_addresses AS u ON o.order_id = u.order_id LEFT JOIN #__pago_orders_items AS i ON o.order_id=i.order_id WHERE o.cdate BETWEEN '" . $endyear . "' AND '" . $startyear . "'  AND u.address_type = 'b' AND o.order_status <> 'A' ORDER BY o.order_id DESC LIMIT 5";
		}
		else if($sale == "customdate")
		{
			$sql = "SELECT DISTINCT  o.*, u.first_name, u.last_name, i.item_id,i.varation_id FROM #__pago_orders AS o LEFT JOIN #__pago_orders_addresses AS u ON o.order_id = u.order_id LEFT JOIN #__pago_orders_items AS i ON o.order_id=i.order_id WHERE o.cdate BETWEEN '" . $sale_start_date . "' AND '" . $sale_end_date . "'  AND u.address_type = 'b' AND o.order_status <> 'A' ORDER BY o.order_id DESC LIMIT 5";
		}

		$db->setQuery($sql);

		$orders = $db->loadObjectList();
		$arr=array();		
		foreach($orders as $order){
			if($order->item_id == null){
				continue;
			}
			$order=(array)$order;
			$order['primary_category']=modPagoOrderHelper::getItemPrimaryCat($order['item_id']);
			$orderItemArray= modPagoOrderHelper::getItemName($order['item_id']);
			
			if($orderItemArray)
			{
				$order['name']= $orderItemArray->name;
			}
			else
			{
				$order['name']= ''; 
			}

			$itemImage = PagoImageHandlerHelper::get_item_files($order['item_id']);

			if(count($itemImage)>0){
				$order['file_name']=JURI::ROOT().'media/pago/items/'.$order['primary_category'].'/'.$itemImage[0]->file_name;

			}
			
			$order['catName'] = modPagoOrderHelper::getCategoryName($order['primary_category']);
			 
			 if($order['varation_id']!=0){

			 	$order['file_name']=modPagoOrderHelper::getVarationImages($order['varation_id'],"-large",false);
			 	$order['name']=modPagoOrderHelper::getVarationName($order['varation_id']);

			 }
			 array_push ($arr,$order);
		}

		return $arr;
	}
	
	public static function getVarationName($varationId){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__pago_product_varation WHERE id=".$varationId;
			// die($sql);
		$db->setQuery($sql);


		return  $db->loadResult();

	}
	
	public static function getCategoryName($id)
	{
		$db = JFactory::getDBO();
		$item = array();
		
		if($id)
		{
			$db->setQuery( "SELECT `name` FROM #__pago_categoriesi
				WHERE id = {$id}" );
			$db->query();
			$item = $db->loadObject();
		}
		if( !$item ) {
			return false;
		}
		return $item;
	}
	
	public static function getItemPrimaryCat($id)
	{
		$db = JFactory::getDBO();
		$sql = 'SELECT primary_category FROM #__pago_items WHERE id = ' . $id;
		$db ->setQuery($sql);
		$result = $db ->loadResult();
		return $result;
	}
	
	public static function getItemName($id){
		$db = JFactory::getDBO();
		$db ->setQuery( "SELECT `name` FROM #__pago_items
			WHERE id = {$id}" );
		$db ->query();
		$item = $db->loadObject();

		if( !$item ) {
			return false;
		}
		return $item;
	}
	
	public static function getVarationImages($varationId,$type='-1',$image_tag=true){
		$path = JPATH_ROOT .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varationId .DIRECTORY_SEPARATOR;
		$urlPath = JURI::root() . 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'product_variation' .DIRECTORY_SEPARATOR. $varationId .DIRECTORY_SEPARATOR;

		$files = false;
		$havaImage = false;
		$html = '';
		if ( $type != "-1" ) {
			if(file_exists($path)){
				if ($handle = opendir($path)){
					$pathinfo = pathinfo($path);
					$folder_name = $pathinfo['basename'];

					while (false !== ($entry = readdir($handle))) {
						if($entry!="." && $entry!=".." && !is_dir($path.DIRECTORY_SEPARATOR .$entry) && $entry!='fields.ini' && $entry!='index.html'  && !strpos($entry, "-")){
							$file = new stdClass;
							$file->img = $entry;
							$files[] = $file;
						}
					}
					$fields_file = $path . 'fields.ini';

					if(file_exists($fields_file)){
						$content = file_get_contents($fields_file);
						if($content!=''){
							$content = json_decode($content,true);
							if($files){
								foreach ($files as $file) {
									if(isset($content[$file->img])){
										$file->content = $content[$file->img];
									}
								}
							}
						}
					}
				}
			}
			if( $files ) {
	
				foreach ($files as $image) {
					$alt = '';
					if(isset($image->content)){
						$alt = $image->content['alt'];
					}
					$ext = explode('.', $image->img);
					$filename = $ext[0];
					$filetype = $ext[1];

					if ($image_tag){
						$html .= "<img title='".$alt."' imageType='images' type='varation' itemId='".$varationId."' fullurl='".$urlPath.$filename.$type.'.'.$filetype."' src='".$urlPath.$filename.$type.'.'.$filetype."' >";
					}
					else{
						$html .= $urlPath.$filename.$type.'.'.$filetype;	
					}
					$havaImage = true;
				}
			}
		}
		if($havaImage){
			return $html;
		}else{
			return false;
		}
	}

}
