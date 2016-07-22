<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class PagoModelComments extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'comment.id',
				'created', 'comment.created',
				'text', 'comment.text',
				'published', 'comment.published',
				'name', 'users.name',
			);
		}

		parent::__construct($config);
	}

	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'DISTINCT comment.*'
			)
		);

		$query->from( '`#__pago_comments` AS comment' );


		// Join over the primary category
		$query->select( 'users.name AS name' );

		$query->select( 'item.name AS item_name' );

		$query->join(
			'LEFT',
			'`#__users` AS users ON comment.author_id = users.id'
		);

		$query->select( 'item.primary_category AS primary_category' );

		$query->join(
			'LEFT',
			'`#__pago_items` AS item ON comment.item_id = item.id'
		);
		
		$query->where('comment.parent_id = 0');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%');
			
			$query->where('(comment.text LIKE ' . $search . ')');
		}

		$filter_var = $this->getState('filter.published');

		if (!empty($filter_var) || $filter_var === '0' ) {
				$query->where('comment.published = ' . $filter_var );
		}


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		//$query->group('item.id');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		//$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		//$id	.= ':'.$this->getState('filter.type');
		//$id	.= ':'.$this->getState('filter.price_type');
		//$id .= ':'.$this->getState('filter.primary_category');

		return parent::getStoreId($id);
	}


	protected function populateState($ordering = null, $direction = null)
	{

		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// $primary_category = $this->getUserStateFromRequest(
		// 	$this->context.'.filter.primary_category',
		// 	'filter_primary_category',
		// 	''
		// );
		// $this->setState('filter.primary_category', $primary_category);

		$published = $this->getUserStateFromRequest(
			$this->context.'.filter.published',
			'filter_published',
			''
		);
		$this->setState('filter.published', $published);

		//$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '');
		//$this->setState('filter.type', $type);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_pago');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('id', 'desc');
	}
	
	function store()
	{
		$data = JFactory::getApplication()->input->getArray($_POST);

		$id   = $data['id'];
		$data = $data['params'];
		$data['id'] = $id;

		$db = JFactory::getDBO();
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );
		$row = JTable::getInstance( 'Comments', 'Table' );
		

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );

		if (!$row->check()){
			JError::raiseWarning( 500, $row->getError() );
			return false;
		}

		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );


		$db = $row->getDBO();

		if($db){
			return $id;
		}else{
			return false;
		}
	}

	function addComment($data)
	{
		$db = JFactory::getDBO();
		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );
		$row = JTable::getInstance( 'Comments', 'Table' );

		$config = Pago::get_instance( 'config' )->get('global');
		$comment_moderation = $config->get('comments.comment_moderation');
		
		$data['created'] = date( 'Y-m-d H:i:s', time() );
		if($comment_moderation == 1){
			$data['published'] = 0;
		}else{
			$data['published'] = 1;
		}
		
		if(isset($data['author_web_site']) AND  strlen ( $data['author_web_site'] ) > 3){			
			if (strpos($data['author_web_site'],'http') !== false) {
			
			}else{
				$data['author_web_site'] = 'http://'.$data['author_web_site'];	
			}
		}

		if (!$row->bind($data)) return JError::raiseWarning( 500, $row->getError() );

		if (!$row->check()){
			JError::raiseWarning( 500, $row->getError() );
			return false;
		} 

		if (!$row->store()) return JError::raiseWarning( 500, $row->getError() );

		$db = $row->getDBO();

		if($comment_moderation == 1){
			return true;
		}

		if( $insert_id = $db->insertid() ){
			$comment = $this->getCommentById($insert_id);
			if($config->get('comments.comment_replay_notification') == 1){
				if(isset($data['parent_id']) AND $data['parent_id'] != 0){
					if($comment){
						$this->sendReplayMail($comment);	
					}
				}
			}
			return $comment;	
		}
		return false;
	}
	function sendReplayMail($replay){
		$comment = $this->getCommentById($replay->parent_id);

		$config = JFactory::getConfig(); 
		$formMail = $config->get( 'mailfrom' );

		$mailHeader = JTEXT::_("PAGO_COMMENTS_REPLAY_MAIL_TITLE");
		$mailMessage = JTEXT::_("PAGO_COMMENTS_REPLAY_MAIL");
		$commentItemLink = JTEXT::_("PAGO_COMMENTS_REPLAY_MAIL_LINK_TITLE");

		require_once(JPATH_SITE.'/components/com_pago/helpers/navigation.php');
		$nav = new NavigationHelper();
		$itemModel = JModelLegacy::getInstance( 'item', 'PagoModel' );
		$item = $itemModel->getItem($comment->item_id);
		$itemid = $nav -> getItemid($item->id, $item->primary_category);

		$link = "<a href=".JURI::ROOT() . 'index.php?option=com_pago&view=item&id=' . $item->id . '&cid=' . $item->primary_category . '&Itemid=' . $itemid."  target='_black'>{$commentItemLink}</a>";
		$mailMessage = str_replace("[resiver_name]", $comment->author_name, $mailMessage);
		$mailMessage = str_replace("[replay_link]", $link ,$mailMessage);
		$mailMessage = str_replace("[replay_author]", $replay->author_name, $mailMessage);
        
        // $headers  = 'MIME-Version: 1.0' . "\r\n";
        // $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        // $headers .= 'From: '.$formMail . "\r\n";

		$mail = JFactory::getMailer();
		$mail->addRecipient($comment->author_email);
		$mail->setSender($formMail);
		$mail->setSubject($mailHeader);
		$mail->isHtml(true);
		$mail->setBody ($mailMessage);
		if($mail->send()){
        	return true;
		}
        //mail($comment->author_email,'=?UTF-8?B?'.base64_encode($mailHeader).'?=', $mailMessage, $headers);
	}
	function deleteItems( $ids ) {

		JTable::addIncludePath( JPATH_COMPONENT . '/tables' );

		$db = JFactory::getDBO();

		$table = JTable::getInstance( 'Comments', 'Table' );

		foreach( $ids as $comment_id ){
			$table->delete( $comment_id );
			$table->reset();
			
			$replays = $this->getCommentReplays($comment_id);
			if($replays){
				foreach ($replays as $replay) {
					$table->delete( $replay );
					$table->reset();
				}
			}
		}
	}
	function getCommentById($commentId){
		$db = JFactory::getDBO();
		$commentId = (int)$commentId;

		$query = "SELECT * FROM #__pago_comments 
					WHERE id = {$commentId} 
					AND published = 1";
		$db->setQuery( $query );
		$comment = $db->loadObject();

		return $this->getCommentUserInfo($comment);	
	}
	function getItemComments($itemId,$start = false){
		$db = JFactory::getDBO();
		
		if($start){
			$query = "SELECT * FROM #__pago_comments 
						WHERE item_id = {$itemId} 
						AND published = 1
						AND parent_id = 0
						AND id < {$start}
						ORDER by `created` DESC LIMIT 4";
		}else{
			$query = "SELECT * FROM #__pago_comments 
						WHERE item_id = {$itemId} 
						AND published = 1
						AND parent_id = 0
						ORDER by `created` DESC LIMIT 4";
		}

		$db->setQuery( $query );
		$comments = $db->loadObjectList();
		
		if($comments){
			foreach ($comments as $comment) {
				if($comment->author_id != 0){
					$comment = $this->getCommentUserInfo($comment);		
				}
				$query = "SELECT * FROM #__pago_comments 
					WHERE item_id = {$itemId} 
					AND published = 1
					AND parent_id = {$comment->id}
					ORDER by `created` DESC";	
				$db->setQuery( $query );
				$replays = $db->loadObjectList();

				if($replays){
					foreach ($replays as $replay) {
						$replay = $this->getCommentUserInfo($replay);		
					}
					$comment->replays = $replays;
				}
			}
			return $comments;
		}
		return false;
	}
	function getCommentsCount($itemId){
		$db = JFactory::getDBO();
		
		$query = "SELECT COUNT(*) as `count` FROM #__pago_comments 
					WHERE item_id = {$itemId} 
					AND published = 1
					AND parent_id = 0
					ORDER by `created` DESC";
		
		$db->setQuery( $query );
		$commentCount = $db->loadObject();
		if($commentCount){
			return $commentCount->count;
		}
		return false;
	}
	function getCommentReplays($commentId){
		
		$is_publish_filter = $this->getState('filter.published');
		
		$published_query = false;
		
		if (!empty($is_publish_filter) || $is_publish_filter === '0' ) {
			$published_query = " AND published = '{$is_publish_filter}'";
		}

		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM #__pago_comments
					WHERE `parent_id` = {$commentId}
					{$published_query}
					ORDER by `created` DESC";
		$db->setQuery( $query );
		$replays = $db->loadObjectList();
	
		if($replays){
			foreach ($replays as $replay) {
				$replay = $this->getCommentUserInfo($replay);		
			}
			return $replays;
		}

		return false;
	}
	function getCommentUserInfo($comment){
		if($comment->author_id != 0){
			$db = JFactory::getDBO();
		
			$query = "SELECT `name`,`email` FROM #__users 
						WHERE id = {$comment->author_id} ";
			$db->setQuery( $query );
			$userInfo = $db->loadObject();
			if($userInfo){
				$comment->author_name = $userInfo->name;
				$comment->author_email = $userInfo->email;
			}
		}
		return $comment;	
	}
	function setId( $id )
	{
		// Set id and wipe data
		$this->_id   = $id;
		$this->_data = null;
	}
	function getData()
	{
		// Load the data
		$this->_data = JTable::getInstance( 'comments', 'table' );
		$this->_data->load( $this->_id );
		
		return $this->_data;
	}
}
