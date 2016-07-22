<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

class PagoControllerComments extends PagoController
{
	public function add($tpl = null) // ajax
	{
		$config = Pago::get_instance( 'config' )->get('global');
		$show_comments = $config->get('comments.show_comments');
		if($show_comments == 0){
			return false;
		}

		$guest_comment = $config->get('comments.comment_guest_submition');

		$item_id = JFactory::getApplication()->input->getInt( 'itemId' );

		$user = JFactory::getUser();

		$data = array();

		$data['item_id'] = $item_id;
		

		if($user->id){
			$data['author_id'] = $user->id;
		}else{
			if($guest_comment == 0){
				return false;
			}
			$data['author_id'] = 0; 
			$data['author_name'] = JFactory::getApplication()->input->get( 'commentName' );			
			$data['author_email'] = JFactory::getApplication()->input->get( 'commentEmail' );			
			$data['author_web_site'] = JFactory::getApplication()->input->get( 'commentWebSite' );			
		}
		
		$data['parent_id'] = JFactory::getApplication()->input->get( 'commentParentId' );
		$data['text'] = JFactory::getApplication()->input->getString( 'commentMessage' );

		JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
		$model = JModelLegacy::getInstance('Comments','PagoModel');

		$return = array();

		$comment = $model->addComment($data);
		if($comment){
			$config = Pago::get_instance( 'config' )->get('global');
			$comment_moderation = $config->get('comments.comment_moderation');
			$replay_comment = $config->get('comments.comment_replay');
			if($comment_moderation == 1){
				$return['status'] = "pending";
				$return['message'] = JTEXT::_('PAGO_COMMENT_PANDING');
			}else{
				$comment->created = date("F d, Y, H:i", strtotime($comment->created));
				$return['comment'] = $comment;
				$return['status'] = "success";
				$return['replay'] = $replay_comment;
			}
		}else{
			$return['status'] = "error";
		}
		$return["comment_avatar"] = PagoHelper::getAvatar()['avatarPath'];
		$return = json_encode($return);
		echo $return;
		exit();
	}
   public function getComments(){
        $user = JFactory::getUser();
        $itemId = (int)JFactory::getApplication()->input->get( 'itemId');
        $start = (int)JFactory::getApplication()->input->get( 'lastShowCommentid');

        JModelLegacy::addIncludePath( JPATH_SITE . '/administrator/components/com_pago/models/' );
        $commentsModel = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
        $comments = $commentsModel->getItemComments($itemId,$start); 
        
        $commentsHtml = PagoHelper::load_template( 'common', 'tmpl_comments' );
        $return = array();

        ob_start();
            require $commentsHtml;
            $return['commentsHtml'] = ob_get_contents();
        ob_end_clean();
        
        $return['status'] = "success";

        ob_clean();
        echo json_encode($return);
        exit();
    }
}
