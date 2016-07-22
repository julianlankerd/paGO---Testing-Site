<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class PagoViewComments extends JViewLegacy
{
	protected $categories;
	protected $comments;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */

	public function display( $tpl = null )
	{
		switch( $this->_layout ){
			case 'form': $this->display_form(); parent::display( $tpl ); return;
		}

		$this->comments	    = $this->get( 'Items' );
		$this->pagination	= $this->get( 'Pagination' );
		$this->state		= $this->get( 'State' );
		
		if($this->comments){
			foreach ($this->comments as $comment) {
				$model = JModelLegacy::getInstance( 'Comments', 'PagoModel' );
				$comment->replays = $model->getCommentReplays($comment->id);
			}
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		//$this->addToolbar();

		////////// Our tool bar

		$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_PUBLISHED'), 'class' => 'publish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_UNPUBLISH'), 'class' => 'unpublish pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'remove', 'text' => JTEXT::_('PAGO_DELETE'), 'class' => 'delete pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );

		parent::display($tpl);
	}

	function display_form(){		
		$cid = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;
		$model = JModelLegacy::getInstance( 'Comments', 'PagoModel' );

		Pago::load_helpers( 'pagoparameter' );

		$model->setId( $cid[0] );

		$comment = $model->getData();

		/*JToolBarHelper::save();
		JToolBarHelper::apply();

		if ( JFactory::getApplication()->input->get('cid',array(0),'array') )  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel( 'cancel', JText::_( 'PAGO_CANCEL' ) );
		}*/

		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'cancel pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );

		$comment = (array)$comment;

		foreach( (array)$comment as $k=>$v ){
			if( !strstr($k, '*') ){
				$comment['basic'][ $k ] = $v;
			}
		}

		$comment_bind=array(
			'params' => (array)$comment['basic'],
			'base' => (array)$comment['basic']
		);

		$comment = (object)$comment;

		// PagoParameter Overrides JParameter render to put pago class names in html

		$params = new PagoParameter( $comment_bind,  $cmp_path . 'views/comments/metadata.xml' );

		JForm::addfieldpath( array(
			$cmp_path . DS . 'elements'
			)
		);

		if ( !$base_params = $params->render( 'params', 'base', JText::_( 'PAGO_COMMENTS_GENERAL_TAB' ) ) )
			$base_params = JText::_( 'PAGO_COMMENTS_ERROR_GENERAL_PARAMETERS' );

		
		$this->assignRef( 'base_params',      			$base_params );
		$this->assignRef( 'comment',             			$comment );
	}


	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{

		$user	= JFactory::getUser();

		JToolBarHelper::publish('publish');
		JToolBarHelper::unpublish('unpublish');
		JToolBarHelper::editList('edit');
		JToolBarHelper::deleteList('delete');
		PagoHtml::behaviour_jquery();
		PagoHtml::add_css( JURI::root(true) . '/components/com_pago/css/thickbox.css');
		PagoHtml::add_js( JURI::root(true)
			. '/components/com_pago/javascript/jquery.thickbox-3.1.js');
		//JToolBarHelper::divider();
		//JToolBarHelper::preferences('com_pago');
		//JToolBarHelper::divider();
	}
}
