<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted Access' );

jimport('joomla.application.component.controller');

class PagoControllerNewsletter extends PagoController
{
    function __construct()
    {
		parent::__construct();
    }

    function save()
    {
		$error = '';

		unset($_SESSION['MCping']);

		$MCapi = trim(JFactory::getApplication()->input->post->get( 'MCapi', '', 'string' ));

		if(!$MCapi){
			$msg = JText::_('PAGO_NL_INVALID_API_CLIENT_ID');
		} else {
			$db = & JFactory::getDBO();
			$query  = "SELECT `name` FROM #__pago_params WHERE `name` ='MCapi' ";
			$db->setQuery($query);
			$isSet = $db->loadResult();

			if( $db->loadResult() ){
				$query  = "UPDATE #__pago_params SET `value` = '$MCapi' WHERE `name` = 'MCapi' ";
				$db->setQuery($query);
				$db->query();
			} else {
				$query  = "INSERT INTO #__pago_params
								SET
								`name` = 'MCapi',
								`value` = '$MCapi',
								`serialized` = '0',
								`namespace` = 'global',
								`group` = 'mailchimp'";
				$db->setQuery($query);
				$db->query();
			}
		}

		$link = 'index.php?option=com_pago&view=newsletter';
		$this->setRedirect( $link, $msg, $error );
    }

    function copy()
    {
		$db    = JFactory::getDBO();
		$cid   =  JFactory::getApplication()->input->post->get( 'cid', '', 'string' );
		$query = "SELECT cdata FROM #__pago_nl_campaigns WHERE `cid` = '".$cid."'";
		$db->setQuery($query);
		$cdata = json_decode($db->loadResult());

		JFactory::getApplication()->input->set('cid',   $cid);
		foreach( $cdata as $k => $v ){
			JFactory::getApplication()->input->set( $k, $v );
		}

		JFactory::getApplication()->input->set( 'view',   'nl_create' );
		JFactory::getApplication()->input->set( 'layout', 'default'  );
		JFactory::getApplication()->input->set( 'action', 'copy'  );
		JFactory::getApplication()->input->set( 'hidemainmenu', 0 );
		JFactory::getApplication()->input->set( 'offset', 0 );
		parent::display();
    }

    function edit()
    {
		$db    = JFactory::getDBO();
		$cid   =  JFactory::getApplication()->input->post->get( 'campaign', '', 'string' );
		$query = "SELECT cdata, folder_id FROM #__pago_nl_campaigns WHERE `creation_date` = '".$cid."'";
		$db->setQuery($query);
		$result = $db->loadAssocList();
		$cdata = json_decode($result[0]['cdata']);

		JFactory::getApplication()->input->set('cid',   $cid);
		foreach( $cdata as $k => $v ){
			JFactory::getApplication()->input->set( $k, $v );
		}
		JFactory::getApplication()->input->set( 'view',   'nl_create' );
		JFactory::getApplication()->input->set( 'action', 'edit' );
		JFactory::getApplication()->input->set( 'layout', 'default'  );
		JFactory::getApplication()->input->set( 'hidemainmenu', 0 );
		JFactory::getApplication()->input->set( 'offset', 0 );
		parent::display();
    }

    function send()
    {
		$cid  = JFactory::getApplication()->input->post->get( 'campaign', '', 'string' );
		$link = 'index.php?option=com_pago&view=nl_send&campaign='.$cid;
		$this->setRedirect( $link );
    }

    function archive()
    {
		$cid  = JFactory::getApplication()->input->post->get( 'cid', '', 'string' );
		$msg  = 'Campaign archived: '.$cid;
		$link = 'index.php?option=com_pago&view=newsletter';
		$this->setRedirect( $link, $msg );
    }

    function templates()
    {
		$url = 'index.php?option=com_pago&view=nl_templates';
		$this->setRedirect($url);
    }

    function extensions()
    {
		$url = 'index.php?option=com_pago&view=nl_extensions';
		$this->setRedirect($url);
    }

    function hideSetupInfo()
    {
		$db    = JFactory::getDBO();
		$query = "INSERT INTO #__pago_nl_misc ( type, value ) VALUES ( 'setup_info', '1' ) ";
		$db->setQuery( $query );
		$db->query();

		$return['success'] = 1;
		echo json_encode( $return );
    }

    function showSetupInfo()
    {
		$db    = JFactory::getDBO();
		$query = "DELETE FROM #__pago_nl_misc WHERE `type` = 'setup_info' ";
		$db->setQuery( $query );
		$db->query();

		$return['success'] = 1;
		echo json_encode( $return );
    }
}
