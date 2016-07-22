<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

class pago_config
{
	/**
	 * Get config params
	 *
	 * will get config params for giving namespace global is default
	 *
	 * @params $namespace string default global
	 * @params $group string default null
	 * @params $force_reload boolean default false
	 * @return $config JRegistry object
	 */
	public function get( $namespace = 'global', $group = null, $force_reload = false )
	{
		JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_pago/models', 'PagoModel' );
		$config = JModelLegacy::getInstance( 'config', 'PagoModel' );

		$config_data = new JRegistry();
		$config_data->loadObject( $config->get_config( $namespace, $group, $force_reload ) );

		$dispatcher = KDispatcher::getInstance();
		$dispatcher->trigger( 'pago_configuration', array( &$config_data, $namespace ) );

		return $config_data;
	}

	public function get_order_status_options()
	{
		return array(
			'P' => JTEXT::_( 'Pending' ),
			'C' => JTEXT::_( 'Confirmed' ),
			'X' => JTEXT::_( 'Cancelled' ),
			'R' => JTEXT::_( 'Refunded' ),
			'S' => JTEXT::_( 'Shipped' ),
			'D' => JTEXT::_( 'Denied' ),
			'PA' => JTEXT::_('Authorized'),
			'E' => JTEXT::_('Expired'),
			'U' => JTEXT::_('Unsubscribed')
		);
	}

	/*
	 * REMOVE - dustin
	 */
	public function ini_encode( $item )
	{
		throw new Exception( 'ini_encode - deprecated function' );
	}

	//still used for a few things so I flipped
	//the method name around to avoid confusion
	//don't remove this!
	public function encode_ini( $item )
	{
		$ini = false;

		if ( is_object($item) || is_array( $item ) ) {
			foreach ( $item as $key => $val ) {
				if ( preg_match( '/^_/', $key ) ) {
					continue;
				}
				if ( is_array( $val ) ) {
					$list_items = false;

					foreach ( (array)$val as $l_item ) {
						$list_items .= $l_item.'|';
					}

					$list_items = substr( $list_items, 0, -1 );
					$ini .= "$key=$list_items\n";
				} else {
					$ini .= "$key=$val\n";
				}
			}
		}

		return $ini;
	}
	public function getSizeByNumber($sizeNumber){

		$defaults = array(
			'thumbnail' => array(
				'width' => 150,
				'height' => 150
			),
			'small' => array(
				'width' => 100,
				'height' => 100
			),
			'medium' => array(
				'width' => 250,
				'height' => 250
			),
			'large' => array(
				'width' => 706,
				'height' => 598
			)
		);

		$params      = Pago::get_instance( 'config' )->get();
		$image_sizes = $params->get( 'media.image_sizes', $defaults );

		$isi = 1;
		foreach ($image_sizes as $ik => $iv) {
			if($sizeNumber == $isi){
				$sizeName = $ik;
			}
			$isi++; 			
		}
		return $sizeName;
	}
}
