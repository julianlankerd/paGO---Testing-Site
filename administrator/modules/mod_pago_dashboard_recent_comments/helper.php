<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
//include( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_pago' .DS. 'helpers' .DS. 'helper.php');
class modPagoRecentCommentsHelper
{
	public static function latestComments($params)
	{
		// Module params
		$moduleclass_sfx = $params->get('moduleclass_sfx', '');
		$comments = self::get_recent_comments("months");
		?>
		<div class = "pg-dashboard-comments-container">
			<div class = "pg-container-header">
				<?php echo JTEXT::_('PAGO_RECENT_COMMENTS')?>
				<div class = "pg-right pg-container-header-buttons">
					<a href="<?php echo JRoute::_('index.php?option=com_pago&view=comments') ?>" class = "pg-btn-small pg-btn-dark"><?php echo JTEXT::_('PAGO_VIEW_ALL'); ?></a>
				</div>
			</div>
			<div class = "pg-pad-20 pg-white-bckg pg-border">
				<div class="pg-dashboard-comment-block">
					<?php if ($comments) :?>
						<?php foreach ($comments as $v) {?>
							<?php $image = PagoHelper::getAvatar($v->customer_id);?>
							<div class="pg-dashboard-comment pg-clear">
								<div class="pg-dashboard-comment-user-avatar" style = "background:url('<?php echo $image['avatarPath']?>')"></div>
								<div class="pg-dashboard-comment-info-container">
									<div class="pg-dashboard-comment-info pg-pad-20">
									<span class = "comment-username"><?php echo $v->customer_name ?></span>
									<span class = "comment-product-name"><?php echo $v->item_name ?></span>
									<div class = "comment-date"><?php echo JHTML::_('date', $v->created , 'H:i / d.m.Y'); ?> </div>
									<a class = "comment-text" href="<?php echo JRoute::_('index.php?option=com_pago&view=comments&task=edit&cid[]='.$v->id) ?>"><?php echo $v->comment ?> </a>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php else:?>
							<div  style="float:right">
							<?php echo JText::_('PAGO_NO_COMMENTS');?>
							</div>
					<?php endif; ?>
				</div>
				<div id="commentLoader" style="display:none;"><img src="<?php echo JURI::root() ?>components/com_pago/images/loadingAnimation.gif" /></div>
			</div>
		</div>
		<?php
	}


	// Get Recent Orders
	public static function get_recent_comments($sale, $sale_start_date='', $sale_end_date='')
	{
		$db = JFactory::getDBO();
		
		if($sale == "months")
		{
			$sql = "SELECT  c.id as id, c.text as comment, c.created as created, u.name as customer_name, u.id as customer_id, i.name as item_name
			FROM #__pago_comments AS c, #__users AS u, #__pago_items AS i
			WHERE c.created > DATE_SUB( NOW( ) , INTERVAL 30 DAY) AND  u.id = c.author_id AND i.id = c.item_id AND c.parent_id = 0
			ORDER BY c.created DESC LIMIT 0, 4";

		}
		else if($sale == "days")
		{
			$sql = "SELECT  c.id as id, c.text as comment, c.created as created, u.name as customer_name, u.id as customer_id, i.name as item_name
			FROM #__pago_comments AS c, #__users AS u, #__pago_items AS i
			WHERE c.created > DATE_SUB( NOW( ) , INTERVAL 7 DAY) AND  u.id = c.author_id AND i.id = c.item_id AND c.parent_id = 0
			ORDER BY c.created DESC LIMIT 0, 4";
		}
		else if($sale == "year")
		{
			$startyear = date("Y-m-d", strtotime(date("Y") . "-12-31"));
			$date_six_year_ago = strtotime("-6 year", strtotime(date("Y") . "-1-01"));
			$endyear = date("Y-m-d", $date_six_year_ago);
			$sql = "SELECT  c.id as id, c.text as comment, c.created as created, u.name as customer_name, u.id as customer_id, i.name as item_name
			FROM #__pago_comments AS c, #__users AS u, #__pago_items AS i
			WHERE c.created BETWEEN '" . $endyear . "' AND '" . $startyear . "' AND  u.id = c.author_id AND i.id = c.item_id AND c.parent_id = 0
			ORDER BY c.created DESC LIMIT 0, 4";
		}
		else if($sale == "customdate")
		{
			$sql = "SELECT  c.id as id, c.text as comment, c.created as created, u.name as customer_name, u.id as customer_id, i.name as item_name
			FROM #__pago_comments AS c, #__users AS u, #__pago_items AS i
			WHERE c.created BETWEEN '" . $sale_start_date . "' AND '" . $sale_end_date . "' AND  u.id = c.author_id AND i.id = c.item_id AND c.parent_id = 0
			ORDER BY c.created DESC LIMIT 0, 4";
		}
		
		$db->setQuery($sql);

		return $db->loadObjectList();
	}
	
	public static function latestCommentsAvg($sale, $sale_start_date='', $sale_end_date='')
	{
		// Module params
		$html = '';
		$comments = modPagoRecentCommentsHelper::get_recent_comments($sale, $sale_start_date, $sale_end_date);
		if ($comments) 
		{
			 foreach ($comments as $v) 
			 {//$html .= '<a href="' . JRoute::_('index.php?option=com_pago&view=comments&task=edit&cid[]=' . $v->id . ')">' . $v->comment . '</a>';
					$image = PagoHelper::getAvatar($v->customer_id);
					$imagePath = explode("administrator", $image['avatarPath']);
					$imgPath1 = explode("ajax", $imagePath[1]);
					$img = $imagePath[0] . $imgPath1[1];
					$html .= '<div class="pg-dashboard-comment pg-clear">';
					$html .= '<div class="pg-dashboard-comment-user-avatar" style="background:url(' . $img . ')">';
					$html .= '</div>';
					$html .= '<div class="pg-dashboard-comment-info-container">';
					$html .= '<div class="pg-dashboard-comment-info pg-pad-20">';
					$html .= '<span class = "comment-username">"' . $v->customer_name . $sale . '"</span>';
					$html .= '<span class = "comment-product-name">"' . $v->item_name . '"</span>';
					$html .= '<div class = "comment-date">"' . JHTML::_('date', $v->created , 'H:i / d.m.Y') . '"</div>';
					$html .= '<a href="JRoute::_(index.php?option=com_pago&view=comments&task=edit&cid[]=' . $v->id . ')">' . $v->comment . '</a>';
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>' ;
					
					
			 } 
		
		}
		else
		{
				$html .= '<div  style="float:right">No comments have been created yet.</div>';
		}
		
		echo $html;
		exit;
	}
}

					
