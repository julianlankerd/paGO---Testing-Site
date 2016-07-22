<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

@include 'development.php';
if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );
if ( !defined( 'PAGO_IN_DEVELOPMENT' ) ) {
	define( 'PAGO_IN_DEVELOPMENT', false );
}

if ( PAGO_IN_DEVELOPMENT ) {
	ini_set( 'display_errors', '1' );
	error_reporting( E_ALL );
}

class Pago
{
	static $_instances = array();

	public static function get_instance( $class )
	{
		$class = 'pago_' . $class;

		if ( isset( self::$_instances[$class] ) ) {
			return self::$_instances[$class];
		}

		$path = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago';

		$class_file = $path .DIRECTORY_SEPARATOR. 'instances' .DIRECTORY_SEPARATOR. $class . '.php';

		if ( file_exists( $class_file ) ) {
			require_once( $class_file );

			if ( class_exists( $class ) ) {
				self::$_instances[$class] = new $class();
				return self::$_instances[$class];
			} else {
				JError::raiseWarning( 500, 'PAGO_CLASS_NOT_EXISTS' . $class ); return;
			}
		} else {
			JError::raiseWarning( 500, 'PAGO_FILE_NOT_EXISTS' . $class_file ); return;
		}
	}

	/**
	 * Wrapper method for Pago errors, at the moment it calls JError, but could be used for better
	 * error handling or a better error page than the standard joomla error page
	 * ONLY TO BE USED FOR ERRORS WHERE PAGE DIES!
	 *
	 * @since    1.0
	 *
	 * @param    string    $code    The application-internal error code for this error
	 * @param    string    $msg    The error message, which may also be shown the user if need be.
	 * @param    mixed    $info    Optional: Additional error information (usually only developer-relevant information that the user should never see, like a database DIRECTORY_SEPARATORN).
	 * @return    object    $error    The configured JError object
	 */
	public static function error( $code, $msg, $info = null )
	{
		JError::raiseError( $code, $msg, $info );
	}

	/**
	 * This is a method to facilitate loading of helper files, this should be the only way that
	 * helper files are required. This way avoiding require_once();
	 *
	 */
	public static function load_helpers( $load = array() )
	{
		static $loaded = array();
		$path = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago' .DIRECTORY_SEPARATOR. 'helpers';

		foreach ( (array) $load as $file ) {
			if ( !in_array( $file, $loaded ) ) {
				require $path .DIRECTORY_SEPARATOR. $file . '.php';
				$loaded[] = $file;
			}
		}
	}

	/**
	 * This will display the requested view
	 * If the application is admin, it pulls admin views otherwise it pulls site views
	 */
	public static function display_view( $_view, $layout = 'default', $model = null )
	{
		$option = JFactory::getApplication()->input->get( 'option' );
		static $_models = array();
		static $_views = array();

		$app = JFactory::getApplication();

		$admin_path = '';
		if ( $app->isAdmin() ) {
			$admin_path = DIRECTORY_SEPARATOR . 'administrator';
		}

		// Avoid redeclared classes
		if ( !isset( $_views[$_view] ) ) {
			require JPATH_ROOT . $admin_path .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago' .DIRECTORY_SEPARATOR. 'views'
				.DIRECTORY_SEPARATOR. strtolower( $_view ) .DIRECTORY_SEPARATOR. 'view.html.php';
			$_views[$_view] = true;
		}

		// Store previous variables
		$prev_option = $option;
		$option = 'com_pago';
		$prev_layout = JFactory::getApplication()->input->get( 'layout' );
		JFactory::getApplication()->input->set( 'layout', $layout );

		// Call and display layout
		$class = "PagoView{$_view}";
		$view = new $class( array(
			'base_path' => JPATH_ROOT . $admin_path .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago'
			) );

		if ( $model ) {
			if ( !isset( $_models[$model] ) ) {
				require JPATH_ROOT . $admin_path .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago' .DIRECTORY_SEPARATOR. 'models'
					.DIRECTORY_SEPARATOR. $model . '.php';
				$class = "PagoModel{$model}";
				$_models[$model] = new $class;
			}

			$view->setModel( $_models[$model], true );
		}

		$view->setLayout( $layout );
		$view->display();

		// Reset variables
		$option = $prev_option;
		JFactory::getApplication()->input->set( 'layout', $prev_layout );
	}

}

// Load Pago Plugins
JPluginHelper::importPlugin( 'pago' );

/**
 * KDispatcher is used to trigger events in paGO
 * This is the hook api for pago
 * Modeled of the WordPress Plugin API with modifications
 *
 * There are two types of events filters and actions
 * Filters are meant to filter any of the passed parameters
 * Actions are meant to perform an action at that point of the script
 */
class KDispatcher
{
	public static $filter = array();
	public static $merged_filters = array();
	public static $current_filter = '';
	public static $actions = array();

	/**
	 * Returns a reference to the global Event KDispatcher object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $dispatcher = &KDispatcher::getInstance();</pre>
	 *
	 * @access	public
	 * @return	KDispatcher	The EventDispatcher object.
	 * @since	1.0
	 */
	static public function &getInstance()
	{
		static $instance;

		if ( !is_object( $instance ) ) {
			$instance = new KDispatcher();
		}

		return $instance;
	}

	/**
	 * Triggers KDispatcher::apply_filters
	 * Method is here just to keep consistency with Joomla when using $dispatcher->trigger();
	 *
	 * @param string The name for the filter
	 * @param array An array of arguments
	 * @return void
	 */
	static public function trigger( $filter, $args = null )
	{
		/*
		 * If no arguments were passed, we still need to pass an empty array to
		 * the call_user_func_array function.
		 */
		if ( $args === null ) {
			$args = array();
		}

		return KDispatcher::apply_filters( $filter, $args );
	}

	public static function add_filter( $tag, $function_to_add, $priority = 10 )
	{
		$idx = KDispatcher::_filter_build_unique_id( $tag, $function_to_add, $priority );
		KDispatcher::$filter[$tag][$priority][$idx] =
			array( 'function' => $function_to_add );
		unset( KDispatcher::$merged_filters[$tag] );

		return true;
	}

	static public function has_filter( $tag, $function_to_check = false )
	{
		$has = !empty( KDispatcher::$filter[$tag] );
		if ( false === $function_to_check || false == $has ) {
			return $has;
		}

		if ( !$idx = KDispatcher::_filter_build_unique_id( $tag, $function_to_check, false ) ) {
			return false;
		}

		foreach ( (array) array_keys( KDispatcher::$filter[$tag] ) as $priority ) {
			if ( isset( KDispatcher::$filter[$tag][$priority][$idx] ) ) {
				return $priority;
			}
		}

		return false;
	}

	static public function apply_filters( $tag, $args )
	{
		$result = array();
		KDispatcher::$current_filter[] = $tag;

		// Do 'all' actions first
		if ( isset( KDispatcher::$filter['all'] ) ) {
			KDispatcher::_call_all_hook( $args );
		}

		if ( !isset( KDispatcher::$filter[$tag] ) ) {
			array_pop( KDispatcher::$current_filter );
			return $result;
		}

		// Sort
		if ( !isset( KDispatcher::$merged_filters[$tag] ) ) {
			ksort( KDispatcher::$filter[$tag] );
			KDispatcher::$merged_filters[$tag] = true;
		}

		reset( KDispatcher::$filter[$tag] );

		do {
			foreach( (array) current( KDispatcher::$filter[$tag] ) as $the_ ) {
				if ( !is_null( $the_['function'] ) ) {
					$result[] = call_user_func_array( $the_['function'], $args );
				}
			}
		} while ( next( KDispatcher::$filter[$tag] ) !== false );

		array_pop( KDispatcher::$current_filter );

		return $result;
	}

	static public function remove_filter( $tag, $function_to_remove, $priority = 10 )
	{
		$function_to_remove = KDispatcher::_filter_build_unique_id( $tag, $function_to_remove,
			$priority );

		$r = isset( KDispatcher::$filter[$tag][$priority][$function_to_remove] );

		if ( true === $r ) {
			unset( KDispatcher::$filter[$tag][$priority][$function_to_remove] );

			if ( empty( KDispatcher::$filter[$tag][$priority] ) ) {
				unset( KDispatcher::$filter[$tag][$priority] );
			}

			unset( KDispatcher::$merged_filters[$tag] );
		}

		return $r;
	}

	static public function remove_all_filters( $tag, $priority = false )
	{
		if ( isset( KDispatcher::$filter[$tag] ) ) {
			if ( false !== $priority && isset( KDispatcher::$filter[$tag][$priority] ) ) {
				unset( KDispatcher::$filter[$tag][$priority] );
			} else {
				unset( KDispatcher::$filter[$tag] );
			}
		}

		if ( isset( KDispatcher::$merged_filters[$tag] ) ) {
			unset( KDispatcher::$merged_filters[$tag] );
		}

		return true;
	}

	static public function current_filter()
	{
		return end( KDispatcher::$current_filter );
	}

	/**
	 * ACTIONS START HERE
	 */

	static public function add_action( $tag, $function_to_add, $priority = 10 )
	{
		return KDispatcher::add_filter( $tag, $function_to_add, $priority );
	}

	static public function do_action( $tag, $args = array() )
	{
		// Count amount of timesaction has been called
		if ( !isset( KDispatcher::$actions[$tag] ) ) {
			KDispatcher::$actions[$tag] = 1;
		} else {
			++KDispatcher::$actions[$tag];
		}

		KDispatcher::$current_filter[] = $tag;

		// Unset reference to objects, if any other variables are passed by reference then oh well..
		foreach ( $args as $_key => $_value ) {
			if ( is_object( $_value ) ) {
				$args[$_key] = clone $_value;
			} else {
				$args[$_key] = $_value;
			}
		}

		// Do 'all' actions first
		if ( isset( KDispatcher::$filter['all'] ) ) {
			KDispatcher::_call_all_hook( $args );
		}

		if ( !isset( KDispatcher::$filter[$tag] ) ) {
			array_pop( KDispatcher::$current_filter );
			return;
		}

		// Sort
		if ( !isset( KDispatcher::$merged_filters[$tag] ) ) {
			ksort( KDispatcher::$filter[$tag] );
			KDispatcher::$merged_filters[$tag] = true;
		}

		reset( KDispatcher::$filter[$tag] );

		do {
			foreach ( (array) current( KDispatcher::$filter[$tag] ) as $the_ ) {
				if ( !is_null( $the_['function'] ) ) {
					call_user_func_array( $the_['function'], $args );
				}
			}
		} while ( next( KDispatcher::$filter[$tag] ) !== false );

		array_pop( KDispatcher::$current_filter );
	}

	static public function did_action( $tag )
	{
		if ( empty( KDispatcher::$actions ) || !isset( KDispatcher::$actions[$tag] ) ) {
			return 0;
		}

		return KDispatcher::$actions[$tag];
	}

	static public function has_action( $tag, $function_to_check = false )
	{
		return KDispatcher::has_filter( $tag, $function_to_check );
	}

	static public function remove_action( $tag, $function_to_remove, $priority = 10 )
	{
		return KDispatcher::remove_filter( $tag, $function_to_remove, $priority );
	}

	static public function remove_all_actions( $tag, $priority = false )
	{
		return KDispatcher::remove_all_filters( $tag, $priority );
	}

	/**
	 * FUNCTIONS FOR HANDLING HOOKS
	 */

	function _call_all_hook( $args )
	{
		reset( KDispatcher::$filter['all'] );

		do {
			foreach( (array) current( KDispatcher::$filter['all'] ) as $the_ ) {
				if ( !is_null( $the_['function'] ) ) {
					call_user_func_array( $the_['function'], $args );
				}
			}
		} while ( next( KDispatcher::$filter['all'] ) !== false );
	}

	static public function _filter_build_unique_id( $tag, $function, $priority )
	{
		static $filter_id_count = 0;

		if ( is_string( $function ) ) {
			return $function;
		}

		if ( is_object( $function ) ) {
			// Closures are currently implemented as objects
			$function = array( $function, '' );
		} else {
			$function = (array) $function;
		}

		if ( is_object( $function[0] ) ) {
			// Object Class Calling
			if ( function_exists( 'spl_object_hash' ) ) {
				return spl_object_hash( $function[0] ) . $function[1];
			} else {
				$obj_idx = get_class( $function[0] ) . $function[1];
				if ( !isset( $function[0]->filter_id ) ) {
					if ( false === $priority ) {
						return false;
					}

					$obj_idx .= isset( KDispatcher::$filter[$tag][$priority] )
						? count( (array) KDispatcher::$filter[$tag][$priority] )
						: $filter_id_count;

					$function[0]->filter_id = $filter_id_count;
					++$filter_id_count;
				} else {
					$obj_idx .= $function[0]->filter_id;
				}

				return $obj_idx;
			}
		} else if ( is_string( $function[0] ) ) {
			// Static Calling
			return $function[0] . $function[1];
		}
	}
}

require JPATH_PLUGINS . '/system/pago/default-filters.php';

class plgSystemPago extends JPlugin
{
	// If you really need parameters then uncomment, but as of right now,
	// the parameters are not being used
	function __construct( $subject, $config )
	{
		KDispatcher::add_filter('onCron', array($this, 'onCron'));
		parent::__construct( $subject, $config );
	}

	/**
	 * Create and manage an index of category to menu_ids. The index is used mostly just for
	 * sef urls and is only used by the router.php.
	 */
	function onAfterInitialise()
	{
		$input  = JFactory::getApplication()->input;
		$option = $input->get( 'option' );

		if ( $option != 'com_menus' ) {
			return;
		}

		Pago::load_helpers( 'menu_ref' );
		$menu = new menu_ref();

		// try to set any empty index because we can't always get a menu_id on a new menu
		$menu->set_empty_index();

		$valid_tasks = array(
			'item.apply',
			'item.save',
			'item.save2new',
			'item.save2copy',
			'items.unpublish',
			'items.publish',
			'items.trash',
			'items.delete'
		);

		$task =  $input->get( 'task' );

		if ( !in_array( $task, $valid_tasks ) ) {
			return;
		}

		$cid =  $input->get( 'cid' );

		if ( $task == 'item.apply' || $task == 'item.save' ||
			$task == 'item.save2new' || $task == 'item.save2copy' ) {

			$jform =  $input->get( 'jform', '', 'ARRAY' );
			$id    =  $input->get( 'id' );

			if( empty( $id ) ) {
				// We have a new menu item created - since it is in the database and it is the newest ID lets get it.
				$db = JFactory::getDBO();

				$query = $db->getQuery( true );
				$query
					->select( 'id' )
					->from( $db->quoteName( '#__menu' ) )
					->order( 'id DESC' );
				$db->setQuery( $query );
				$result = $db->loadResult();

				$id = $result;
			}

			if ( $menu->check_cids( $cid ) ) {
				$published = $jform['published'];
				if ( $published == -2 ) {
					$published = -1;
				}
				$menu->update_menu_index( $cid, $published );
				return;
			}

			if ( !preg_match( '/option=com_pago\&view=category/', $jform['link'] ) ) {
				return;
			}

			if ( $jform['published'] == 1 ) {
				$menu->add_menu_index( (int) $jform['request']['cid'], (int) $id, 1);
			} else {
				$menu->add_menu_index( (int) $jform['request']['cid'], (int) $id, 0);
			}
			return;
		}

		// only try to do these operations if one or more ids exists already in the menu index
		if ( $menu->check_cids( $cid ) ) {
			if ( $task == 'items.trash' ) {
				$menu->update_menu_index( $cid, -1 );
			} else if ( $task == 'items.delete' ) {
				$menu->delete_menu_index( $cid );
			} else if ( $task == 'items.unpublish' ) {
				$menu->update_menu_index( $cid, 0 );
			} else if ( $task == 'items.publish' ) {
				$menu->update_menu_index( $cid, 1 );
			}
			return;
		}
		return;
	}

	function onCron()
	{
		$config = Pago::get_instance('config')->get();
		$db = JFactory::getDbo();
		$age = '30 MINUTE';

		//Garbage collection on failed / pending orders
		//will set the order to A which will not show in
		//the admin backend
		if($config->get('general.remove_pending_orders', 1)){

			$db->setQuery("
				UPDATE #__pago_orders
					SET order_status = 'A'
						WHERE (order_status = 'P' OR order_status = '')
							AND mdate < DATE_SUB(NOW(),INTERVAL {$age})
			");

			$db->query();
		}

		$db->setQuery("DELETE from #__pago_items WHERE name = '' AND created < DATE_SUB(NOW(),INTERVAL {$age})");
		$db->query();

		$db->setQuery("DELETE from #__pago_categoriesi WHERE name = '' AND created_time < DATE_SUB(NOW(),INTERVAL {$age})");
		$db->query();
	}
}
