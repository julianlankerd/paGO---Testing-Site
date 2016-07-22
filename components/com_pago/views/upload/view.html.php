<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
if ( !defined('_JEXEC') ) { die( 'Direct Access to this location is not allowed.' ); }
/**
 * @version		$Id: view.html.php 1 2009-10-27 20:56:04Z rafael $
 * @package		Pago
 * @copyright	Copyright (C) 2009 'corePHP' / corephp.com. All rights reserved.
 * @license		GNU/GPL, see LICENSE.txt
 */

jimport( 'joomla.application.component.view' );

class PagoViewUpload extends JViewLegacy
{
	function display( $tpl = null )
	{
		$db       = JFactory::getDBO();
		$row      = $this->get( 'Data' );
		$product  = $this->get( 'Product' );
		$category = $this->get( 'Category' );
		$params   = Pago::get_instance( 'config' )->get();

		// Set view from template switcher
		Pago::get_instance( 'template' )->set( $this, 'upload' );

		// If something went wrong with the upload
		if ( empty( $row ) ) {
			$this->assignRef( 'upload', JFactory::getApplication()->input->files->get( 'upload', array(), 'array' ) );
			parent::display( $tpl );
			return;
		}

		$this->assignRef( 'row', $row );
		$this->assignRef( 'params', $params );
		$this->assignRef( 'category', $category );
		$this->assignRef( 'upload', JFactory::getApplication()->input->files->get( 'upload', array(), 'array' ) );

		parent::display( $tpl );
	}
}
?>