<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/	

defined('_JEXEC') or die;

class modPagoBestsellHelper
{
	public static function bestSellingAvg($selected, $sale_start_date='', $sale_end_date='')
	{		
		// Module params
		$html = '';

		$items = modPagoBestsellHelper::best_selling_items($selected, $sale_start_date, $sale_end_date);

		if ($items) 
		{			 
			$counter = 1;
			
			foreach ($items as $item)
			{
				if(array_key_exists('file_name',$item)){
					if($item['file_name']!=""){
						$image = $item['file_name'] ;
					}
					else{
						$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
					}			
				}
				else{
					$image = JURI::root() . "administrator/components/com_pago/css/img-new/category-noimage.jpg";
				}
				$imagePath = explode("administrator", $image);
				$imgPath1 = explode("ajax", $imagePath[1]);
					$img = $imagePath[0] . $imgPath1[1];
				
				$html .= "<tr>";
				$html .= "<td class ='pg-numbering'>" . $counter . "</td>";
				$html .= "<td class = 'pg-preview'>";
				$html .= "<div class ='pg-preview-small-image' style = 'background:url(" . $img . ")'></div>";
				$html .= "</td>";
				$html .= "<td>";
				$html .= "<a class = 'product-name' href='" . JRoute::_('index.php?option=com_pago&controller=items&task=edit&view=items&cid[]="' . $item['id']) ."'>";//
				$html .= $item['name'];
				$html .= "</a>";
				$html .= "<span class ='product-category'>" . $item['catName']->name . "</span>";
				$html .= "</td>";
				$html .= "<td class = 'pg-price'>" . Pago::get_instance('price')->format($item['price']) . "</td>";
				$html .= "<td class = 'pg-qty'>" . $item['quantity'] . "</td>";
				$html .= "</tr>";
				$counter++; 
			}
				echo $html;
		}
		else
		{
				echo JText::_('PAGO_NO_ITEMS_SOLD');
		}

	}



	public static function bestSelling($params)
	{		
		// Module params
		$moduleclass_sfx = $params->get('moduleclass_sfx', '');
		

		$doc = JFactory::getDocument();
		$scripts = "
				jQuery(document).on('change', '#bestSellingCount', function(event){

    	var itemsCount = jQuery('#bestSellingCount :selected').val();
		
					jQuery.ajax({
							type: 'POST',
							url: 'index.php',
							data: 'option=com_pago&controller=items&task=getBestsellingItemsList&itemsCount=' +itemsCount+ '',
							
							success: function( response ) {
								if(response){
									
									result = JSON.parse(response);
							
									jQuery('#itemsList').html(result);
								}
							}
						});
				});
				";
		$doc->addScriptDeclaration($scripts);
		$moduleclass_sfx = $params->get('moduleclass_sfx', '');
		$items = self::best_selling_items("months", $sale_start_date='', $sale_end_date='');

		
		?>
		<div class = "pg-dashboard-best-selling-container">
			<div class = "pg-container-header">
				<?php echo JTEXT::_('PAGO_BEST_SELLING');?>
				<div class = "pg-right no-margin">
					
				</div>
			</div>
			
			<div class = "pg-pad-20 pg-white-bckg pg-border best-selling-block">
				<div class = "pg-dashboard-best-selling-block">
					<?php if ($items) :?>
						<table class="pg-quick-overview">
							<thead>
								<tr>
									<td class = "pg-numbering"></td>	
									<td class = "pg-preview"><?php echo JText::_('PAGO_ELEMENTS_MEDIA_IMG_PREVIEW');?></td>
									<td><?php echo JText::_('PAGO_PRODUCT_INFO');?></td>
									<!--<td><?php echo JText::_('PAGO_DASHBOARD_CATEGORY');?></td>-->
									<td class = "pg-price"><?php echo JText::_('PAGO_DASHBOARD_PRICE');?></td>
									<td class = "pg-qty"><?php echo JText::_('PAGO_DASHBOARD_QTY');?></td>
								</tr>
							</thead>
							
							<tbody id="itemsList" class="bestSelling">
								<?php 
									$counter = 1;
									foreach ($items as $item) : 
										if(array_key_exists('file_name',$item)){
											if($item['file_name']!=""){
												$image = $item['file_name'] ;
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
										<td class = "pg-numbering"><?php echo $counter; ?></td>
										<td class = "pg-preview">
											<div class ="pg-preview-small-image" style = "background:url('<?php echo $image; ?>')">
												
											</div>
										</td>
										<td>
											<a class = "product-name" href="<?php echo JRoute::_('index.php?option=com_pago&controller=items&task=edit&view=items&cid[]='.$item['id']); ?>">
												<?php echo $item['name']; ?>
											</a>
											<span class = "product-category"><?php echo $item['catName']->name; ?></span>
										</td>
										<td class = "pg-price"><?php echo Pago::get_instance('price')->format($item['price']); ?></td>
										<td class = "pg-qty"><?php echo $item['quantity']; ?></td>
									</tr>
									<?php $counter++; ?>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else:?>
						<?php echo "No Items Sold";?>
					<?php endif;?>
				</div>
				<div id="bestSellerLoader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
			</div>
		</div>

		<?php
	}




	// Get Best Selling Items
	public static function best_selling_items($sale, $sale_start_date='', $sale_end_date='')
	{
		$db = JFactory::getDBO();
		//$itemModel = JModelLegacy::getInstance( 'item', 'PagoModel' );
		//$attributeModel = JModelLegacy::getInstance( 'attribute', 'PagoModel' );
			
		if($sale == "months")
		{
			$sql = "SELECT *
			FROM (
				SELECT i.id, i.name,o.price,o.varation_id , SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				LEFT JOIN #__pago_orders AS os ON os.order_id = o.order_id
				WHERE os.cdate > DATE_SUB( NOW( ) , INTERVAL 30 DAY) 
				AND os.order_status <> 'A'
				GROUP BY i.id, o.varation_id
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT 5";

		}
		else if($sale == "days")
		{
			$sql = "SELECT *
			FROM (
				SELECT i.id, i.name,o.price,o.varation_id , SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				LEFT JOIN #__pago_orders AS os ON os.order_id = o.order_id
				WHERE os.cdate > DATE_SUB( NOW( ) , INTERVAL 7 DAY) 
				AND os.order_status <> 'A'
				GROUP BY i.id, o.varation_id
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT 5";
		}
		else if($sale == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$sql = "SELECT *
			FROM (
				SELECT i.id, i.name,o.price,o.varation_id , SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				LEFT JOIN #__pago_orders AS os ON os.order_id = o.order_id
				WHERE os.cdate BETWEEN '" . $endyear . "' AND '" . $startyear . "'
				AND os.order_status <> 'A'
				GROUP BY i.id, o.varation_id
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT 5";
			
		}
		else if($sale == "customdate")
		{
			$sql = "SELECT *
			FROM (
				SELECT i.id, i.name,o.price,o.varation_id , SUM( o.qty ) AS quantity
				FROM #__pago_items AS i
				LEFT JOIN #__pago_orders_items AS o ON i.id = o.item_id
				LEFT JOIN #__pago_orders AS os ON os.order_id = o.order_id
				WHERE os.cdate BETWEEN '" . $sale_start_date . "' AND '" . $sale_end_date . "'
				AND os.order_status <> 'A'
				GROUP BY i.id, o.varation_id
			) AS q
			WHERE q.quantity IS NOT NULL
			ORDER BY q.quantity DESC
			LIMIT 5";
		}

		$db->setQuery($sql);

		$items =  $db->loadObjectList();
	
			Pago::load_helpers( 'imagehandler' );
		
			$arr=array();
		foreach($items as $item){
			$item=(array)$item;
			$item['primary_category']=modPagoBestsellHelper::getItemPrimaryCat($item['id']);
			$itemImage = PagoImageHandlerHelper::get_item_files($item['id']);
			if(count($itemImage)>0){
				$item['file_name']=JURI::ROOT().'media/pago/items/'.$item['primary_category'].'/'.$itemImage[0]->file_name;

			}
			
			$item['catName'] = modPagoBestsellHelper::getCategoryName($item['primary_category']);
			 
			 if($item['varation_id']!=0){

			 	$item['file_name']=modPagoBestsellHelper::getVarationImages($item['varation_id'],"-large",false);
			 	$item['name']=modPagoBestsellHelper::getVarationName($item['varation_id']);

			 }
			 array_push ($arr,$item);
			
		}

		return $arr;
	}
	
	public static function getItemPrimaryCat($id){
		$id - (int)$id;
		$db = JFACtory::getDBO();
		$sql = 'SELECT primary_category FROM #__pago_items WHERE id = ' . $id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	
	public static function getCategoryName($id)
	{
		$db = JFACtory::getDBO();
		$db->setQuery( "SELECT `name` FROM #__pago_categoriesi
			WHERE id = {$id}" );
		$db->query();
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
	
	public static function getVarationName($varationId){
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__pago_product_varation WHERE id=".$varationId;
			// die($sql);
		$db->setQuery($sql);


		return  $db->loadResult();

	}

		
}
