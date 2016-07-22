<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Plugins component
 *
 * @static
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.0
 */
class PagoViewPlugins extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		switch( $this->_layout ){
			case 'form': 
				$this->display_form(); 
				parent::display( $tpl ); 
				return;
		}
		
		$status = JFactory::getApplication()->input->getInt( 'status' );
		JLoader::register( 'PluginsHelper', JPATH_ADMINISTRATOR . '/components/com_plugins/helpers/plugins.php' );
		
		JFactory::getLanguage()->load('com_plugins');

		$this->folder_options = PluginsHelper::folderOptions();
  		$version = new JVersion();
		if($version->RELEASE <= 3){
			$this->stateOptions = PluginsHelper::stateOptions();
		}else{
			$this->stateOptions = PluginsHelper::publishedOptions();
		}

		foreach( $this->folder_options as $k=>$option ){
			if( (!strstr( $option->value, 'pago_' )) || (!strstr( $option->value, '_gateway' ) && !strstr( $option->value, '_shippers' ) && !strstr( $option->value, '_security' )) ){
				
				unset( $this->folder_options[ $k ] );
			}
		}
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if($status == 1){
			$message = JText::_('PAGO_PLUGINS_SUCCESSFULLY_SAVED');
			$this->assignRef( 'message', $message );
			$messageType = 'message';
			$this->assignRef( 'messageType', $messageType );
		}

		$this->assignRef( 'extensionId', $extensionId );
		
		//$this->addToolbar();

		$top_menu[] = array('task' => 'edit', 'text' => JTEXT::_('PAGO_EDIT'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'publish', 'text' => JTEXT::_('PAGO_ENABLE'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'unpublish', 'text' => JTEXT::_('PAGO_DISABLE'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'ckeckin', 'text' => JTEXT::_('PAGO_CHECKIN'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'options', 'text' => JTEXT::_('PAGO_OPTIONS'), 'class' => 'edit pg-btn-medium pg-btn-dark');
		
		$this->assignRef( 'top_menu',  $top_menu );
		
		parent::display($tpl);
	}
	public function display_form($tpl = null)
	{
		$status = JFactory::getApplication()->input->getInt( 'status' );
		// If not checked out, can save the item.	
		$extensionId = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );
		$extensionId = $extensionId[0];

		if($status == 1){
			$message = JText::_('PAGO_PLUGINS_SUCCESSFULLY_SAVED');
			//$this->assignRef( 'message', $message );
			$messageType = 'message';
			//$this->assignRef( 'messageType', $messageType );

			JFactory::getApplication()->enqueueMessage($message, $messageType);
		}

		$this->assignRef( 'extensionId', $extensionId );

		$top_menu[] = array('task' => 'apply', 'text' => JTEXT::_('PAGO_SAVE'), 'class' => 'apply pg-btn-medium pg-btn-dark pg-btn-green');
		$top_menu[] = array('task' => 'save', 'text' => JTEXT::_('PAGO_SAVE_AND_CLOSE'), 'class' => 'save pg-btn-medium pg-btn-dark');
		$top_menu[] = array('task' => 'cancel', 'text' => JTEXT::_('PAGO_CANCEL'), 'class' => 'cancel pg-btn-medium pg-btn-dark');

		$this->assignRef( 'top_menu',  $top_menu );

		// JToolBarHelper::apply();
		// JToolBarHelper::save();
		// JToolBarHelper::cancel();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= PluginsHelper::getActions();
		
		//JToolBarHelper::title(JText::_('COM_PLUGINS_MANAGER_PLUGINS'), 'plugin');

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('publish', 'JTOOLBAR_ENABLE', true);
			JToolBarHelper::unpublish('unpublish', 'JTOOLBAR_DISABLE', true);
			JToolBarHelper::divider();
			JToolBarHelper::checkin('checkin');
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_plugins');
		}
	}
}