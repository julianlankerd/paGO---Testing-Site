<?php defined('_JEXEC') or die('Restricted access');
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// NOTE: IS THIS FILE NEEDED ANYMORE?
class pago_helper_installer
{
	var $_php_min_version = '5.2.6';
	var $_joomla_min_version = '2.5.0';
	var $_errors = array();
	var $_exceptions = array();
	var $_fatal_error;
	var $_base_extensions;
	var $_module_positions = array();

	function __construct( $module_positions )
	{
		$this->_module_positions = $module_positions;
	}

	public function install(){

		$this->check_php_version();
		$this->check_joomla_version();
		$this->check_categories();
		$this->check_items();
		$this->install_core_extensions();
		$this->place_module_positions();

		$e =& JError::getErrors();

		$this->fatal_error_check( $e );

		if( $this->_fatal_error ){
			return false;
		}

		return true;
	}

	public function check_categories(){
		$db  = JFactory::getDBO();

		$sql = "SELECT `id` FROM #__pago_categories";
		$db->setQuery( $sql );
		$cats = $db->loadObjectList();
		if( count($cats) <= 1){
			JError::raiseNotice( 100, 'Notice: There are no categories, create some in the Categories Manager' );
		}
	}

	public function check_items(){
		$db  = JFactory::getDBO();

		$sql = "SELECT `id` FROM #__pago_items";
		$db->setQuery( $sql );
		$rows = $db->loadObjectList();
		if( count($rows) < 1){
			JError::raiseNotice( 100, 'Notice: There are no items, create some in the Items Manager' );
		}
	}

	public function place_module_positions(){

		$db  = JFactory::getDBO();

		$mod_install_list = array();

		foreach( $this->_module_positions as $position ){
			$sql = "SELECT `position`
						FROM #__modules
							WHERE `position` = '$position'";
			$db->setQuery( $sql );
			$row = $db->loadObject();

			if( !isset($row->position) ){
				//JError::raiseNotice( 100, 'Notice: Module Position (' . $position . ') was not found and has been installed.' );
				$mod_install_list[] = $position;
			}
		}

		foreach($mod_install_list as $position){
			$this->install_module_position( $position );
		}

	}

	public function install_module_position( $position ){

		$db  = JFactory::getDBO();

		switch( $position ){
			case 'pago_checkout_login':
				$sql = "INSERT INTO `#__modules` (
							`title`,
							`content`,
							`ordering`,
							`position`,
							`checked_out`,
							`checked_out_time`,
							`published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`)
						VALUES(
							'Pago Login', '', 1,
							'pago_checkout_login', 0, '0000-00-00 00:00:00',
							1, 'mod_login', 0, 0, 1,
							'cache=0\nmoduleclass_sfx=\npretext=\nposttext=\nlogin=\nlogout=\ngreeting=1\nname=0\nusesecure=0\n\n', 0, 0, '');";
				$db->setQuery( $sql );

				if( !$db->query() ){
					JError::raiseWarning( 200, 'Failed to install mod_login to position pago_checkout_login. You must install manually!' );
				} else {
					$id = $db->insertid();
					$sql = "INSERT INTO `#__modules_menu` VALUES ($id,0) ";
					//(name, age) VALUES('Timmy Mellowman', '23' ) ")

					$db->setQuery( $sql );
					$db->query();
				}

			break;
			case 'pago_checkout_register':
				$sql = "INSERT INTO `#__modules` (
							`title`, `content`, `ordering`,
							`position`, `checked_out`, `checked_out_time`,
							`published`, `module`, `numnews`, `access`,
							`showtitle`, `params`, `iscore`, `client_id`, `control`)
						VALUES(
							'Register', '', 0, 'pago_checkout_register',
							62, '2010-08-18 02:41:07', 1, 'mod_joom_register',
							0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, '');";
				$db->setQuery( $sql );

				if( !$db->query() ){
					JError::raiseWarning( 200, 'Failed to install mod_joom_register to position pago_checkout_register. You must install manually!' );
				} else {
					$id = $db->insertid();
					$sql = "INSERT INTO `#__modules_menu` VALUES ($id,0) ";

					$db->setQuery( $sql );
					$db->query();
				}
			break;

			case 'pago_menu':
				$sql = "INSERT INTO `jos_modules` (
						`title`, `content`, `ordering`, `position`, `checked_out`,
						`checked_out_time`, `published`, `module`, `numnews`, `access`,
						`showtitle`, `params`, `iscore`, `client_id`, `control`)
					VALUES(
						'pago menu', '', 0, 'pago_menu', 0, '0000-00-00 00:00:00', 1,
						'mod_pago_menu', 0, 0, 0,
						'root=1\ndepth=0\nplugin=superfish\norientation=navbar\nminWidth=12\nmaxWidth=50\nextraWidth=5\ndelay=1200\ncache=1\ntag_id=\nmoduleclass_sfx=\n\n', 0, 0, '');";
				$db->setQuery( $sql );

				if( !$db->query() ){
					//JError::raiseWarning( 200, 'Failed to install mod_joom_register to position pago_checkout_register. You must install manually!' );
				} else {
					$id = $db->insertid();
					$sql = "INSERT INTO `#__modules_menu` VALUES ($id,0) ";

					$db->setQuery( $sql );
					$db->query();
				}
			break;

			case 'pago_toolbar':
				$sql = "INSERT INTO `jos_modules` (
					`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`,
					`published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`,
					`client_id`, `control`)
					VALUES('pago_toolbar', '', 15, 'pago_toolbar', 62, '2010-08-21 03:59:45', 1, 'mod_pago_toolbar', 0, 0, 1, '', 0, 0, '');";
				$db->setQuery( $sql );

				if( !$db->query() ){
					JError::raiseWarning( 200, 'Failed to install mod_joom_register to position pago_checkout_register. You must install manually!' );
				} else {
					$id = $db->insertid();
					$sql = "INSERT INTO `#__modules_menu` VALUES ($id,0) ";

					$db->setQuery( $sql );
					$db->query();
				}
			break;

			default:
				$sql = "INSERT INTO `#__modules` (
					`title`, `content`, `ordering`, `position`,
					`checked_out`, `checked_out_time`, `published`, `module`,
					`numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`)
					VALUES('$position',
					'This is a Pago Commerce module position. You can add any module you want here -
					disable if you do not want a module here - you should not delete it as you will
					then need to type the position into the module position param. If you do delete,
					access installation in Pago admin and it will be automatically redone. If you want to see the
					module positions on a page add &amp;sp=1 to the url in the browser address bar.', 1,
					'$position',
					0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, '');";
				$db->setQuery( $sql );
				if(!$db->query()){
					//JError::raiseWarning( 200, 'Failed to install mod_joom_register to position pago_checkout_register. You must install manually!' );
				} else {
					$id = $db->insertid();
					$sql = "INSERT INTO `#__modules_menu` VALUES ($id,0) ";

					$db->setQuery( $sql );
					$db->query();
				}
		}
	}

	public function fatal_error_check( $e ){

		foreach( $e as $error ){
			if( $error->code ==  200 ){
				$this->_fatal_error = true;
			}
		}
	}

	public function install_extension( $name ){
		$auto_install_dir = JPATH_ADMINISTRATOR . DS .'components' . DS . 'com_pago' . DS . 'autoinstall' . DS;

		$db  = JFactory::getDBO();

		jimport('joomla.installer.installer');
		$installer = & JInstaller::getInstance();

		$install_dir = $auto_install_dir . $name . DS;

		if( !$installer->install( $install_dir ) ){
			JError::raiseNotice( 100, 'Notice: Failed to install: ' . $name . '-' . $install_dir );
			return false;
		} else {

			$sql = "UPDATE #__plugins
						SET published = 1
							WHERE element = '$name'";

			$db->setQuery( $sql );
			$db->query();
		}

		return true;
	}

	public function install_core_extensions(){

		$dir = JPATH_SITE . DS .'components' . DS . 'com_pago' . DS . 'plugins' . DS;

		$groups = scandir( $dir );

		foreach( $groups as $group ){
			if(!strstr($group,'.')){
				Installer::plugins_install( $dir, $group );
			}
		}
		//die();
		$dir = JPATH_ADMINISTRATOR . DS .'components' . DS . 'com_pago' . DS . 'modules' . DS;

		Installer::modules_install( $dir );


	}

	public function check_php_version(){

		if ( strnatcmp( phpversion(), $this->_php_min_version ) >= 0 ){}else{
			JError::raiseWarning( 200, 'Server PHP version out of date - must be upgraded > than ' . $this->_php_min_version );
		}

		return true;
	}

	public function check_joomla_version(){
		jimport('joomla.version');
		$version = new JVersion();
		if ( strnatcmp( $version->getShortVersion(), $this->_joomla_min_version ) >= 0 ){}else{
			JError::raiseWarning( 200, 'Joomla! version out of date - must be upgraded > than ' . $this->_joomla_min_version );
		}

		return true;
	}
	//getShortVersion
}

jimport( 'joomla.installer.installer' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.archive' );
jimport( 'joomla.filesystem.archive.zip' );

class Installer{

	static function modules_install( $dir ){

		$positions = array(
			'mod_pago_snapshot' => 'pago_left',
			'mod_pago_top_customers' => 'pago_right',
			'mod_pago_best_selling' => 'pago_left',
			'mod_pago_recent_orders' => 'pago_right',
			'mod_pago_recent_products' => 'pago_left'
		);

		$db = JFactory::getDBO();

		$installer = JInstaller::getInstance();
		$extns = scandir( $dir );

		if( is_array( $extns ) ){
			foreach( $extns as $extn ){
				if( strstr( $extn, 'mod_' ) ){
					if( !$installer->install( $dir . $extn ) ){
						 JError::raiseWarning( '', JText::_( 'installation failed_' ) . $extn );
					} else {

						$published = false;
						$position = false;

						if(isset($positions[$extn])){
							$position = $positions[$extn];
							$published = 1;
						}

						$sql = "UPDATE #__modules
								SET published = $published, position = '$position'
									WHERE module = '$extn'";

						$db->setQuery( $sql );
						$db->query();
					}
				}
			}
		}
	}

	static function plugins_install( $dir, $group ){

		$dir = $dir . $group . '/';

		$installer = JInstaller::getInstance();
		$extns = scandir( $dir );

		if( !is_array( $extns ) ) return false;

		$db = JFactory::getDBO();

		foreach( $extns as $extn ){
			if( strstr( $extn, '.xml' ) ){

				$name = str_replace( '.xml', '', $extn );
				$dest = $dir . $name . '_install/';

				if(is_dir($dir . $name)){
				JFolder::copy( $dir . $name , $dest . $name , false, true );
				} else {
					JFolder::create( $dest . $name );
				}
				JFile::copy( $dir . $name . '.xml', $dest . $name . '.xml' );
				JFile::copy( $dir . $name . '.php', $dest . $name . '.php' );

				if( $installer->install( $dest ) ){
					$sql = "UPDATE #__plugins
								SET published = 1
									WHERE element = '$name'";

					$db->setQuery( $sql );
					$db->query();
				} else {
					JError::raiseWarning( 200, 'PLUGIN INSTALLATION FAILED: ' . $dest );
				}

				JFolder::delete( $dest );
			}
		}


	}

	static function modules_uninstall( $dir ){

		$extns = scandir( $dir );

		if( !is_array( $extns ) ) return false;

		$installer = JInstaller::getInstance();
		$where = false;

		$db = JFactory::getDBO();

		foreach( $extns as $extn ){
			if( strstr( $extn, 'mod_' ) ){
				$where .= ' module=' . $db->quote($extn) . ' OR';
			}
		}

		if( !$where ) return false;

		$where = substr_replace($where ,'' ,-3);

		$db->setQuery("
			SELECT id FROM #__modules WHERE $where
		");

		$results = $db->loadObjectList();

		if( !$results ) return false;

		foreach( $results as $result ){
			$installer->uninstall( 'module', $result->id, false );
		}

		return true;
	}

	static function plugins_uninstall( $dir, $group ){
		$dir = $dir . $group . '/';

		$extns = scandir( $dir );

		if( !is_array( $extns ) ) return false;

		$installer = JInstaller::getInstance();
		$where = false;

		$db = JFactory::getDBO();

		foreach( $extns as $extn ){
			if( strstr( $extn, '.xml' ) ){
				$name = str_replace( '.xml', '', $extn );
				$where .= ' element=' . $db->quote($name) . ' OR';
			}
		}

		if( !$where ) return false;

		$where = substr_replace($where ,'' ,-3);

		$db->setQuery("
			SELECT id FROM #__plugins WHERE $where
		");

		$results = $db->loadObjectList();

		if( !$results ) return false;

		foreach( $results as $result ){
			$installer->uninstall( 'plugin', $result->id, false );
		}

		return true;
	}
}


