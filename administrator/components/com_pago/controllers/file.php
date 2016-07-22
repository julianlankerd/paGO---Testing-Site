<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.database.table' );
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_pago/tables');

class PagoControllerFile extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'unpublish', 'publish' );
		$this->registerTask( 'default', 'makeDefault' );
		$this->registerTask( 'removedefault', 'removeDefault' );
	}

	function multisave()
	{
		$model = $this->getModel( 'file' );
		$images = JFactory::getApplication()->input->get( 'files', array(), 'array' );
		$selectedTab = JFactory::getApplication()->input->get( 'selectedTab');


		foreach ( $images as $id => $image ) {
			// Prepare variables for later
			$image['id'] = $id;

			if ( $id = $model->store( $image ) ) {
				$msg = JText::_( 'Successfully saved' );
			} else {
				$msg = JText::_( 'An error has occurred: ' . $model->getError() );
			}
		}

		$script = '<script type="text/javascript">window.parent.document.getElementById("TB_closeWindowButton").click();</script>';
		$script .= '<script type="text/javascript">window.parent.Joomla.submitbutton("apply")</script>';
		//$script = '<script type="text/javascript">setTimeout("loc = (window.dialogArguments || opener || parent || top).location; loc.reload()", 1000);</script>';
		//$script = '<script type="text/javascript">window.parent.document.getElementById("TB_closeWindowButton").click();//document.getElementById("TB_closeWindowButton").click()</script>';

		jexit( 'Saved' . $script );
	}
	function saveVideo()
	{
		$images = JFactory::getApplication()->input->get( 'files', array(), 'array' );
		$model = $this->getModel( 'file' );

		$video['provider'] = JFactory::getApplication()->input->get( 'videoProvider');
		$video['video_key'] = JFactory::getApplication()->input->get( 'video_id');
		$video['title'] = JFactory::getApplication()->input->get( 'video_title');
		$video['item_id'] = JFactory::getApplication()->input->get( 'item_id');
		$video['type'] = strtolower(JFactory::getApplication()->input->get( 'type'));
		$video['published'] = 1; 
		// $image['default'] = 0;item_id
		if($video['title'] == ''){
			$video['title'] = "Untitled";
		}
		if ( $id = $model->store( $video ) ) {
			$msg = JText::_( 'Successfully saved' );
		} else {
			$msg = JText::_( 'An error has occurred: ' . $model->getError() );
		}

		$script = '<script type="text/javascript">window.parent.document.getElementById("TB_closeWindowButton").click();</script>';
		$script .= '<script type="text/javascript">window.parent.Joomla.submitbutton("apply")</script>';

		jexit( 'Saved' . $script );
	}

	function save()
	{
		
		$model = $this->getModel( 'file' );

		if ( $id = $model->store() ) {
			$msg = JText::_( 'File successfully saved' );
		} else {
			$id	= JFactory::getApplication()->input->getInt( 'id' );
			$msg = JText::_( 'An error has occurred: ' . $model->getError() );
		}

		parent::display();
	}

	function remove()
	{

		$model  = $this->getModel( 'file' );
		$async  = JFactory::getApplication()->input->getInt( 'async', 0 );
		$silent = JFactory::getApplication()->input->getInt( 'silent', 1 );

		if ( $model->delete() ) {

			$cids = JFactory::getApplication()->input->get( 'cid', array(0), 'array' );

			$msg = JText::_( 'File successfully deleted' );
			if ( count( $cids ) > 1 ) {
				$msg = JText::_( 'File successfully deleted' );
			}

			if ( $async && $silent ) {
				$msg = 1;
			}
		} else {
			$id	 = JFactory::getApplication()->input->getInt('id');
			$msg = JText::_( 'An error has occurred: ' ) . $model->getError();
		}
		// If coming from ajax request
		if ( $async ) {
			echo $msg;
			jexit();
		}
		$this->setRedirect( 'index.php?option=com_pago&view=images&gallery=' .$this->_gallery,
			$msg
		);
	}

	function makeDefault()
	{
		$model = $this->getModel( 'file' );
		$async = JFactory::getApplication()->input->getInt( 'async', 0 );

		if ( $model->makeDefault() ) {
			if ( !$async ) {
				$msg = JText::_( 'Made image default for gallery' );
			} else {
				$msg = 1;
			}
		} else {
			$id	= JFactory::getApplication()->input->getInt( 'id' );
			$msg = JText::_( 'An error has occurred: '.$model->getError() );
		}

		// If coming from ajax request
		if ( $async ) {
			echo $msg;
			jexit();
		}

		$this->setRedirect( 'index.php?option=com_jphoto&view=images&gallery=' . $this->_gallery,
			$msg );
	}
	function removeDefault()
	{
		$model = $this->getModel( 'file' );
		$async = JFactory::getApplication()->input->getInt( 'async', 0 );

		if ( $model->removeDefault() ) {
			if ( !$async ) {
				$msg = JText::_( 'Remove image default for gallery' );
			} else {
				$msg = 1;
			}
		} else {
			$id	= JFactory::getApplication()->input->getInt( 'id' );
			$msg = JText::_( 'An error has occurred: '.$model->getError() );
		}

		if ( $async ) {
			echo $msg;
			jexit();
		}

		$this->setRedirect( 'index.php?option=com_jphoto&view=images&gallery=' . $this->_gallery,
			$msg );
	}

	function publish()
	{
		$db  = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->post->get( 'cid', array(0), 'array' );
		$type = JFactory::getApplication()->input->post->get( 'type');
		$async = JFactory::getApplication()->input->getInt( 'async', '0' );
		$table = '';
		$insTable = '';
		$idPrefix = 'id';
		$field = 'published';

		JArrayHelper::toInteger( $cid, array(0) );
		$publish = ( $this->getTask() == 'publish' ? 1 : 0 );

		if ( count( $cid ) < 1 ) {
			$action = $publish ? JText::_( 'publish' ) : JText::_( 'unpublish' );
			JFactory::getApplication()->enqueueMessage(JText::_('Unable to ' . $action), 'error');
		}

		$cids = implode( ',', $cid );

		switch ($type) {
			case 'file':
				$table = "#__pago_files";
				$insTable = "files";
				break;
			case 'items':
				$table = "#__pago_items";
				$insTable = "items";
				break;
			case 'categories':
				$table = "#__pago_categoriesi";
				$insTable = "categoriesi";
				break;
			case 'comments':
				$table = "#__pago_comments";
				$insTable = "comments";
				break;
			case 'coupons':
				$table = "#__pago_coupon";
				$insTable = "coupon";
				break;
			case 'shippingrules':
				$table = "#__pago_custom_shipping_rules";	
				$insTable = "custom_shipping_rules";
				$idPrefix = "rule_id";
				break;
			case 'tax_class':
				$table = "#__pago_tax_class";	
				$insTable = "tax_class";
				$idPrefix = "pgtax_class_id";
				$field = "pgtax_class_enable";
				break;
			case 'tax':
				$table = "#__pago_tax_rates";	
				$insTable = "tax_rates";
				$idPrefix = "pgtax_id";
				$field = "pgtax_enable";
				break;
			case 'template':
				$table = "#__pago_view_templates";	
				$insTable = "view_templates";
				$idPrefix = "pgtemplate_id";
				$field = "pgtemplate_enable";
				break;
			case 'locations':
				$table = "#__pago_country";	
				$insTable = "locations";
				$idPrefix = "country_id";
				$field = 'publish';
				break;
			case 'states':
				$table = "#__pago_country_state";	
				$insTable = "locations_state";
				$idPrefix = "state_id";
				$field = 'publish';
				break;
			case 'varation':
				$table = "#__pago_product_varation";	
				$insTable = "product_varation";
				break;
			case 'extension':
				$table = "#__extensions";	
				$insTable = "extensions";
				$idPrefix = "extension_id";
				$field = 'enabled';
				break;
			case 'currency':
				$table = "#__pago_currency";	
				$insTable = "currency";
				$idPrefix = "id";
				$field = 'published';
				break;
			default:
				$table = "#__pago_files";
				$insTable = "files";
				break;
		}
		$query = 'UPDATE '.$table.' SET '.$field.' = ' . (int) $publish
			. ' WHERE '.$idPrefix.' IN ( ' .$cids. ' )';
		$db->setQuery( $query );
		if ( !$db->query() ) {
			JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
		}

		if ( $async ) {
			// Instantiate an article table object
			// $row = JTable::getInstance( $table, 'Table' );
			// $row->load( (int) $cid[0] );
			
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM {$table} WHERE {$idPrefix} = '{$cid[0]}'");
			$row = $db->loadObject();
			
			if (!isset($row->published))
				$row->published = $row->{$field};

			echo PagoHelper::published( $row, 0, 'tick.png',  'publish_x.png',
				'', ' class="publish-buttons" type="'.$type.'" rel="' .$cid[0]. '"' );
			jexit();
		} else {
			$this->setRedirect( "index.php?option=pago&view=".$type, $msg );
		}
	}
}
?>
