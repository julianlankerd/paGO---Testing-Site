<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Pago  Helper
 *
 * @package Joomla
 * @subpackage Pago
 * @since 1.5
 */
class PagoHelper
{
	/**
	 * Function to facilitate the getting of a product
	 *
	 * @since 1.0
	 *
	 * @param int The product ID
	 * @return object The prodct
	 **/
	static public function get_product( $id )
	{
		static $products = array();

		if ( !$id ) { return false; }

		if ( isset( $products[$id] ) ) {
			return $products[$id];
		}

		if ( empty( $products ) ) {
			jimport( 'joomla.database.table' );
			JTable::addIncludePath( JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_pago'
				.DIRECTORY_SEPARATOR. 'tables' );
		}

		$row = JTable::getInstance( 'item', 'Table' );
		$row->load( $id );

		$products[$id] = $row;

		return $products[$id];
	}

	static public function load_template( $folder, $file )
	{

		$app    = JFactory::getApplication();
		$config = Pago::get_instance( 'config' )->get();

		$paths = array(
			JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_pago/'
				. $config->get( 'template.pago_theme' ) . "/{$folder}/{$file}.php",
			JPATH_SITE . '/components/com_pago/templates/' . $config->get( 'template.pago_theme' )
				. "/{$folder}/{$file}.php",
			JPATH_SITE . '/components/com_pago/templates/default' . "/{$folder}/{$file}.php",
		);

		// Lets check to see if a template exist in custom theme location in components folder
		foreach ( $paths as $path ) {
			if ( file_exists( $path ) ) {
				return $path;
			}
		}
	}

	/**
	 * Load the absolute path to downloadable files stored on the server
	 */
	static public function get_files_base_path()
	{
		$cache = Pago::get_instance( 'cache' );
		$path  = $cache->get( 'files_abs_path', 'config' );

		if ( $path ) {
			return $path;
		}

		$config = Pago::get_instance( 'config' )->get();
		$path   = $config->get( 'files_file_path', 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'files' );

		// Check to see if path is absolute
		if ( !in_array( substr( $path, 0, 1 ), array( '/', '\\' ) ) ) {
			$path = JPATH_ROOT .DIRECTORY_SEPARATOR. str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
		}

		$path = rtrim( $path, '/\\' );

		// Store in cache
		$cache->add( 'files_abs_path', $path, 'config' );

		return $path;
	}

	/**
	 * Cut string to n symbols and add delim but do not break words
	 *
	 * @param string string we are operating with
	 * @param integer character count to cut to
	 * @param string|NULL delimiter. Default: '…'
	 * @return string processed string
	 **/
	static public function neat_trim( $str, $n, $delim = '…' )
	{
		$len = strlen( $str );

		if ( $len > $n ) {
			preg_match( '/(.{' .$n. '}.*?)\b/', $str, $matches );
			return rtrim( $matches[1] ) . $delim;
		} else {
			return $str;
		}
	}

	static public function saveContentPrep( &$row )
	{
		// Get metadata string
		$metadata = JFactory::getApplication()->input->post->get( 'meta', null, 'array' );

		// Clean metadata
		if ( is_array( $metadata ) && !empty( $metadata ) ) {
			// clean up keywords -- eliminate extra spaces between phrases
			// and cr (\r) and lf (\n) characters from string
			if ( !empty( $metadata['keywords'] ) ) { // only process if not empty
				// Array of characters to remove
				$bad_characters = array( "\n", "\r", "\"", "<", ">" );
				// Remove bad characters
				$after_clean = JString::str_ireplace( $bad_characters, '', $metadata['keywords'] );
				$keys = explode( ',', $after_clean ); // Create array using commas as delimiter
				$clean_keys = array();
				foreach ( $keys as $key ) {
					if ( trim( $key ) ) {  // ignore blank keywords
						$clean_keys[] = trim( $key );
					}
				}
				// Put array back together delimited by ', '
				$metadata['keywords'] = implode( ', ', $clean_keys );
			}

			// Clean up description -- eliminate quotes and <> brackets
			if( !empty( $metadata['description'] ) ) { // only process if not empty
				$bad_characters = array( "\"", "<", ">" );
				$metadata['description'] = JString::str_ireplace(
					$bad_characters, '', $metadata['description']
				);
			}

			$txt = array();
			foreach ( $metadata as $k => $v ) {
				$txt[] = "$k=$v";
			}

			$row->metadata = implode( "\n", $txt );
		}

		// Get submitted text from the request variables
		$text = JFactory::getApplication()->input->post->get( 'text', '');

		// Clean text for xhtml transitional compliance
		$text = str_replace( '<br>', '<br />', $text );

		// Search for the {readmore} tag and split the text up accordingly.
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos	= preg_match( $pattern, $text );

		if ( $tagPos == 0 ) {
			$row->introtext	= $text;
		} else {
			list( $row->introtext, $row->fulltext ) = preg_split( $pattern, $text, 2 );
		}

		// Do the filtering by what the user has set in com_content
		// Filter settings
		jimport( 'joomla.application.component.helper' );
		$config	= JComponentHelper::getParams( 'com_content' );
		$user   = JFactory::getUser();
		$gid    = $user->get( 'gid' );

		$filterGroups =  $config->get( 'filter_groups' );

		// convert to array if one group selected
		if ( ( !is_array( $filterGroups ) && (int) $filterGroups > 0 ) ) {
			$filterGroups = array( $filterGroups );
		}

		if (is_array( $filterGroups ) && in_array( $gid, $filterGroups ) ) {
			$filterType  = $config->get( 'filter_type' );
			$filterTags  = preg_split( '#[,\s]+#', trim( $config->get( 'filter_tags' ) ) );
			$filterAttrs = preg_split( '#[,\s]+#', trim( $config->get( 'filter_attritbutes' ) ) );

			switch ( $filterType ) {
				case 'NH':
					$filter	= new JFilterInput();
					break;
				case 'WL': // Turn off xss auto clean
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 0, 0 );
					break;
				case 'BL':
				default:
					$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1 );
					break;
			}
			$row->introtext	= $filter->clean( $row->introtext );
			$row->fulltext	= $filter->clean( $row->fulltext );
		} elseif( empty( $filterGroups) && $gid != '25' ) {
			// No default filtering for super admin (gid=25)
			$filter = new JFilterInput( array(), array(), 1, 1 );
			$row->introtext	= $filter->clean( $row->introtext );
			$row->fulltext	= $filter->clean( $row->fulltext );
		}

		return true;
	}

	/**
	 * Add a custom button to the toolbar
	 */
	static public function addCustomButton( $title,
											$doTask,
											$css_class,
											$url = '#',
											$class = 'toolbar',
											$rel = '',
											$toolbar_obj = null )
	{
		$i18n_text	= JText::_( $title );
		$html = "<a href=\"$url\" onclick=\"$doTask\" class=\"".
			"$css_class\" rel=\"$rel\">\n";
		$html .= "$i18n_text\n";
		$html .= "</a>\n";

		// if ( null == $toolbar_obj ) {
		// 	$toolbar_obj = & JToolBar::getInstance( 'toolbar' );
		// }
		// $toolbar_obj->appendButton( 'Custom', $html, $class );

		return $html;
	}

	static public function published(   &$row,
										$i,
										$imgY = 'tick.png',
										$imgX = 'publish_x.png',
										$prefix = '',
										$attribs = ''
									)
	{

		$type = explode('type="', $attribs);
		$type = explode('" rel', $type[1]);

		$type = $type[0];
		if($type == "locations"){
			$img    = $row->publish ? $imgY : $imgX;
			$task   = $row->publish ? 'unpublish' : 'publish';
			$alt    = $row->publish ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
			$action = $row->publish ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );
		}
		else{
			$img    = $row->published ? $imgY : $imgX;
			$task   = $row->published ? 'unpublish' : 'publish';
			$alt    = $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
			$action = $row->published ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );
		}

		if ( !$attribs ) {
			$attribs = 'onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')"';
		}

		$href = '
			<a href="javascript:void(0);" ' .$attribs. ' title="'. $action .'">
			<img src="components/com_pago/css/img-new/'. $img .'" border="0" alt="'. $alt .
			'" class="item-'.$task. '" /></a>';

		return $href;
	}
	
	public static function checkedout (&$row,
									$i, 
									$editorName, 
									$time, 
									$prefix = '', 
									$enabled = false, 
									$checkbox = 'cb')
	{
		JHtml::_( 'bootstrap.tooltip' );

		if (is_array($prefix))
		{
			$options = $prefix;
			$enabled = array_key_exists('enabled', $options) ? $options['enabled'] : $enabled;
			$checkbox = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}
	
		$text = $editorName . '<br />' . JHtml::_('date', $time, JText::_('DATE_FORMAT_LC')) . '<br />' . JHtml::_('date', $time, 'H:i');
		$active_title = JHtml::tooltipText(JText::_('JLIB_HTML_CHECKIN'), $text, 0);
		$inactive_title = JHtml::tooltipText(JText::_('JLIB_HTML_CHECKED_OUT'), $text, 0);
		
		return <<<HTML
		<button type="button" class="hasTooltip pg-btn pg-btn-checkin" onclick="return listItemTask('cb{$i}', 'checkin');" title="{$active_title}">
			<i class="fa fa-lock"></i>
		</button>
HTML;
	}
	
	static public function preselected(   &$row,
										$i,
										$imgY = 'publish.png',
										$imgX = 'unpublish.png',
										$prefix = '',
										$attribs = ''
									)
	{
		$img    = $row->preselected ? $imgY : $imgX;
		$task   = $row->preselected ? 'not_selected' : 'preselected';
		$alt    = $row->preselected ? JText::_( 'PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_PRESELECTED' ) : JText::_( 'PAGO_CUSTOM_ATTRIBUTES_PRODUCT_VARATION_NOT_SELECTED' );
		$action = $row->preselected ? 'not_selected' : 'preselected' ;
		

		if ( !$attribs ) {
			$attribs = 'onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')"';
		}

		$href = '
			<a href="javascript:void(0);" ' .$attribs. ' title="'. $action .'">
			<img src="components/com_pago/css/img-new/'. $img .'" border="0" alt="'. $alt .
			'" class="item-'.$task. '" /></a>';
			
		return $href;
	}

	static public function convert_hr_to_bytes( $size )
	{
		$size = strtolower( $size );
		$bytes = (int) $size;

		if ( strpos( $size, 'k' ) !== false ) {
			$bytes = intval( $size ) * 1024;
		} elseif ( strpos($size, 'm') !== false ) {
			$bytes = intval( $size ) * 1024 * 1024;
		} elseif ( strpos($size, 'g') !== false ) {
			$bytes = intval( $size ) * 1024 * 1024 * 1024;
		}

		return $bytes;
	}

	static public function convert_bytes_to_hr( $bytes )
	{
		$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		$log = log( $bytes, 1024 );
		$power = (int) $log;
		$size = pow( 1024, $log - $power );
		return $size . $units[$power];
	}

	static public function max_upload_size()
	{
		$u_bytes = PagoHelper::convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
		$p_bytes = PagoHelper::convert_hr_to_bytes( ini_get( 'post_max_size' ) );
		return min( $u_bytes, $p_bytes );
	}

	/**
	 * Will get the value of any tag in the a components XML file
	 *
	 * @since 1.0
	 *
	 * @param string The tag name that you want the value for
	 * @param string The component that we want to get the version of (Optional)
	 * @return string Value inside required tag
	 **/
	static public function get_xml_tag_value( $tag, $option = null )
	{
		if ( !$option ) {
			$option = JFactory::getApplication()->input->get( 'option' );
		}

		$admin_dir = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components';
		$site_dir = JPATH_SITE .DIRECTORY_SEPARATOR. 'components';

		/* Get the component folder and list of xml files in folder */
		$folder = $admin_dir .DIRECTORY_SEPARATOR. $option;

		if ( JFolder::exists( $folder ) ) {
			$xml_files_in_dir = JFolder::files( $folder, '.xml$' );
		} else {
			$folder = $site_dir .DIRECTORY_SEPARATOR. $option;
			if ( JFolder::exists( $folder ) ) {
				$xml_files_in_dir = JFolder::files( $folder, '.xml$' );
			} else {
				$xml_files_in_dir = null;
			}
		}

		$value = '';
		if ( count($xml_files_in_dir ) ) {
			foreach ( $xml_files_in_dir as $xmlfile ) {
				// Read the file to see if it's a valid component XML file
				$xml = JFactory::getXMLParser( 'Simple' );

				if ( !$xml->loadFile( $folder .DIRECTORY_SEPARATOR. $xmlfile ) ) {
					continue;
				}

				if ( !is_object( $xml->document ) || $xml->document->name() != 'install' ) {
					continue;
				}

				$value = '';
				$element = &$xml->document->{$tag}[0];
				$value = $element ? $element->data() : '';

				if ( $value ) {
					break;
				}
			}
		}

		if ( isset( $value ) ) {
			return $value;
		} else {
			return 0;
		}
	}

	/**
	 * Checks db current database version
	 *
	 * This is a database check function
	 * it will check the current database version and run any updates
	 * Calles methods of class PagoUpgrader located: '/helpers/upgrade.php'
	 *
	 * @since 1.0
	 *
	 * @return void
	 **/
	static public function db_check()
	{
		// $current_version = PagoHelper::get_xml_tag_value( 'dbversion' );
		//
		// if ( !$current_version ) {
		// 	return;
		// }

		jimport( 'joomla.database.table' );
		require_once JPATH_COMPONENT .DIRECTORY_SEPARATOR. 'tables' .DIRECTORY_SEPARATOR. 'config.php';

		$db  = JFactory::getDBO();
		$row = new JTableConfig( $db, 'name' );
		$row->load( 'dbversion' );
		$db_version = $row->params;

		// If equal, nothing to do
		// if ( $db_version == $current_version ) {
		// 	return;
		// }

		//CANNOT USE PAGO CLASS HERE BECAUSE INSTALLATION WILL NOT WORK!
		//Pago::load_helpers( 'upgrade' );
		require( JPATH_COMPONENT . '/helpers/upgrade.php' );

		if ( !$db_version ) {
			PagoUpgrader::create_config();
			$db_version = 1000;
		}

		// if ( $db_version < 1000 ) {
		// 	PagoUpgrader::upgrade_1000();
		// }

		$current_version = PagoUpgrader::find_upgrades( $db_version );

		// Finally store new version to DB
		$row->params = $current_version;
		$row->store();
	}

	/**
	 * Checks that all necessary plugins are available for pago to run smoothly
	 */
	static public function plugin_check()
	{
		jimport( 'joomla.plugin.helper' );

		// Check for system plugin
		if ( !JPluginHelper::isEnabled( 'system', 'pago' ) ) {
			die( 'Please install and enable the paGO system plugin. This is required.' );
		}
	}

	/**
	 * Returns a list of files in a directory
	 *
	 * @param string The absolute path to the directory
	 * @return array A list of files in directory
	 */
	static public function get_files_in_dir( $directory )
	{
		$files = array();

		if ( $handle = @opendir( $directory ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != '..' && $file != '.' && $file != '.ds_Store' ) {
					if ( is_dir( $directory .DIRECTORY_SEPARATOR. $file ) ) {
						continue;
					} else {
						$files[] = $file;
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @since 1.5
	 *
	 * @param string $original Maybe unserialized original, if is needed.
	 * @return mixed Unserialized data can be any type.
	 */
	static public function maybe_unserialize( $original ) {
		if ( PagoHelper::is_serialized( $original ) )
			// don't attempt to unserialize data that wasn't serialized going in
			return @unserialize( $original );
		return $original;
	}

	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 * @since 1.5
	 *
	 * @param mixed $data Value to check to see if was serialized.
	 * @return bool False if not serialized and true if it was.
	 */
	static public function is_serialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;
		switch ( $badions[1] ) {
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}

		return false;
	}

	/**
	 * Write general configuration settings
	 *
	 * @return bool return true/false
	 */
	static public function writeDefaultSettings()
	{
		$config = array();
		$db = JFactory::getDBO();

		$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'size'";
		$db->setQuery($sql);
		$sizeunit = $db->loadObject();
		$config['SIZEUNIT'] = $sizeunit->code;

		$sql = "SELECT `code` FROM #__pago_units where `default` = 1 and `type` = 'weight'";
		$db->setQuery($sql);
		$weightunit = $db->loadObject();
		$config['WEIGHTUNIT'] = $weightunit->code;

		$sql = "SELECT * FROM #__pago_currency where `default` = 1";
		$db->setQuery($sql);
		$currency = $db->loadObject();
		$config['CURRENCY_CODE'] = $currency->code;
		$config['CURRENCY_SYMBOL'] = $currency->symbol;

		$configSettings = "<?php\n";

		foreach ($config as $key => $value)
		{
			$configSettings .= "define('$key', '" . addslashes($value) . "');\n";
		}

		$configSettings .= "?>";
		$defaultSettingPath = JPATH_SITE . "/administrator/components/com_pago/helpers/pagoConfig.php";

		if ($fp = fopen($defaultSettingPath, "w"))
		{
			fputs($fp, $configSettings, strlen($configSettings));
			fclose($fp);

			return true;
		}
		else
		{
			return false;
		}
	}

	static public function getCountryName($country2code)
	{
		$config = array();
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__pago_country where `country_2_code` = '" . $country2code . "'";
		$db->setQuery($sql);
		$country = $db->loadObject();

		return $country;
	}

	static public function get_category_tree()
	{
		JTable::addIncludePath(JPATH_COMPONENT . '/tables');
		$key = 'id';
		$val = 'catname';
		$cat_table = JTable::getInstance('categoriesi', 'Table');
		$cats = $cat_table->getTree(1);

		foreach ($cats as $cat)
		{
			$cat_name = str_repeat('_', (($cat->level) * 2));

			$cat_name .= '[' .  $cat->level  . ']_ ' .  $cat->name;
			$options[] = array(
				'id' => $cat->id,
				'catname' => $cat_name
			);
		}

		echo $html = @JHTML::_('select.genericlist', $options, 'catname[]', 'class="inputbox" multiple="true"', $key, $val);

	}

	static public function get_all_plugins( $folder = 'pago_gateway' , $enabled = 1 )
	{
		$db = JFactory::getDBO();
		$where_enabled = "";

		if ($enabled == 1 || $enabled == 0)
		{
			$where_enabled = " and `enabled` = '" . $enabled . "'";
		}

		$sql = "SELECT * FROM #__extensions where `type` ='plugin' and `folder` = '" . $folder . "'" . $where_enabled;
		$db->setQuery($sql);
		$plugin_options = $db->loadObjectList();


		return $plugin_options;
	}
	static public function item_is_new($itemId){
		$db = JFactory::getDBO();
		$itemId = (int)$itemId;
		
		$sql = "SELECT `until_new_date` FROM #__pago_items where id =".$itemId ;
		$db->setQuery($sql);
		$until_new_date = $db->loadResult();

		if($until_new_date >= date( 'Y-m-d')){
			return true;
		}else{
			return false;
		}
	}

	public function uplaodDefaultImage($default_image_path)
	{
		$db = JFactory::getDBO();
		jimport('joomla.filesystem.file');
        Pago::load_helpers('imagehandler');
       // $dispatcher = KDispatcher::getInstance();
        $params = Pago::get_instance('config')->get();
		$title =  "no-image";
        $content = $title;
      	$src_folder = $default_image_path;
      	if(is_file($src_folder))
        {
        	if (!is_dir(JPATH_SITE . "/media/pago/default/"))
            {
            	mkdir(JPATH_SITE . "/media/pago/default", 0755);
            }
			 $uploads = PagoImageHandlerHelper::upload_dir( "/media/pago/default/" );
            $name_prefix = '';
            $filename = PagoImageHandlerHelper::unique_filename( $uploads['path'], $name_prefix."noimage.jpg" );
            $dest_folder = JPATH_SITE . "/media/pago/default/" . $filename;
           

            copy($src_folder, $dest_folder);
            $mimes = false;
            $mime_check = true;
            $filetype = PagoImageHandlerHelper::check_filetype( $filename, $mimes, $mime_check );
            extract( $filetype );

            $url = JURI::root()."/media/pago/default/" . $filename;
            $file = array( 'file' => $dest_folder, 'file_name' => $filename, 'url' => $url,'type' => $type );

            $url       = $file['url'];
            $type      = $file['type'];
            $file_name = $file['file_name'];
            $file1      = $file['file'];
            $title     = preg_replace( '/\.[^.]+$/', '', basename( $file1 ) );
            //$content   = '';
            $file_meta = array();

            if ( $file_meta = @PagoImageHandlerHelper::read_image_metadata( $file1 ) ) {
                if ( trim( $file_meta['title'] ) ) {
                    $title = $file_meta['title'];
                }
                if ( trim( $file_meta['caption'] ) ) {
                    $content = $file_meta['caption'];
                }
            }

            $data = array(
                'title'     => $title,
                'caption'   => $content,
                'alias'     => $title,
                'item_id'   => 0,
                'default'   => 0,
                'published' => 1,
                'file_name' => $file_name,
                'type'      => 'images',
                'mime_type' => $type,
                'file_meta' => array( 'file_meta' => $file_meta ),
                'created_time' => date( 'Y-m-d H:i:s', time() ),
                'modified_time' => date( 'Y-m-d H:i:s', time() )
            );

            PagoImageHandlerHelper::generate_image_metadata( $file, $data, $params );

            // Serialize meta data for storage
            $data['file_meta'] = serialize( $data['file_meta'] );
            JTable::addIncludePath(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_pago'.DS.'tables');
		
            $row = JTable::getInstance( 'files', 'Table' );
            
            if (!$row->bind($data))
            {
            	$this->setError($db->getErrorMsg());
                return false;
            }

            PagoHelper::saveContentPrep($row);

            // Quick and ugly fix
            if ($row->introtext && !$row->fulltext )
            {
                $row->fulltext = $row->introtext;
            }
            elseif ( $row->introtext && $row->fulltext )                             {
                $row->fulltext = $row->introtext . ' ' . $row->fulltext;
            }

            unset( $row->introtext );

            if (!$row->id)
            {
                $row->ordering = $row->getNextOrder("`item_id` = {$row->item_id} AND `type` = '$row->type'");
            }

            if ( !$row->store() )
            {
            	$this->setError($row->getError());
                return false;
            }

        }
	}
	static public function getAvatar($userId=false){
		$config = Pago::get_instance('config')->get();
		$pago_theme   = $config->get( 'template.pago_theme', 'default' );
		$user = JFactory::getUser();
		$userId = (int)$userId;
		if(!$userId){
			$userId = $user->id;	
		}
		$avatar = array();
		$avatar['havaAvatar'] = false;
		$searchAvatar = true;
		//if (!$user->guest) {
			$checkPath = JPATH_ROOT . '/media/pago/users/'.$userId.'.jpg';
			if(file_exists($checkPath)){
				$avatar['avatarPath'] = JURI::root( true ) .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'users' .DIRECTORY_SEPARATOR.$userId.'.jpg';	
				$searchAvatar = false;
				$avatar['havaAvatar'] = true;
			}
			$checkPath = JPATH_ROOT . '/media/pago/users/'.$userId.'.png';
			if($searchAvatar && file_exists($checkPath)){
				$avatar['avatarPath'] = JURI::root( true ) .DIRECTORY_SEPARATOR. 'media' .DIRECTORY_SEPARATOR. 'pago' .DIRECTORY_SEPARATOR. 'users' .DIRECTORY_SEPARATOR.$userId.'.png';	
				$avatar['havaAvatar'] = true;
				$searchAvatar = false;
			}
		//}
		
		$app = JFactory::getApplication(0);
		$joomla_theme = $app->getTemplate();
		
		$return_paths = array();
		$paths = array(
			'component' => array(
				'full' => JPATH_SITE .'/components/com_pago/templates/'.$pago_theme.'/',
				'url' => JURI::base(true) . '/components/com_pago/templates/'.$pago_theme.'/'
			),
			'joverride' => array(
				'full' => JPATH_SITE . '/templates/' . $joomla_theme . '/html/com_pago/'. $pago_theme . '/',
				'url' => JURI::base(true) . '/templates/' . $joomla_theme .'/html/com_pago/'. $pago_theme . '/'
			)

		);
		
		foreach ( $paths as $path ) 
		{
			$tpath = $path['full'] ;
			if ( file_exists( $tpath .'images/no-image.png') && is_dir( $tpath ) ) 
			{
				$full_path = $path['url'] .'images/no-image.png';
			}
		}
		
		if($searchAvatar){
			$avatar['avatarPath'] = $full_path;
		}
		return $avatar;
	}
	static public function getModuleById($moduleId){
		
		$moduleId = (int)$moduleId;

		$db = JFactory::getDBO();	
		$sql = "SELECT `params` FROM #__modules where `id` = ".$moduleId;
		$db->setQuery($sql);
		return $db->loadObject();
	}
	static public function getItemPrimaryCat($id){
		$id - (int)$id;
		$db = JFactory::getDBO();
		
		$sql = 'SELECT primary_category FROM #__pago_items WHERE id = ' . $id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	static public function getParentCategories($categoryId)
	{
		$db = JFactory::getDBO();
        
        $select = 'SELECT parent_id, level FROM #__pago_categoriesi WHERE id='.$categoryId;
        $db->setQuery($select);
        $result = $db->loadObjectList();
       	$parents = array();
        $i = 0;
        foreach ($result as $item) {
            $parents[$i]['parent_id'] = $item->parent_id;
            $parents[$i]['level'] = $item->level;
            if($parents[$i]['level']>1) {
                $parents[$i]['parent'] = PagoHelper::getParentCategories($item->parent_id);  
            }
            $i++;
            
        }
        return $parents;
	}

	static public function getParents($array)
	{
		$parents = "";
		$i=0;
		foreach ($array as $item) {
			while(PagoHelper::getParentArrays($item)==1){
		        	$parents .= " ".$item['parent_id'];
		        	$i++;
		        	$item = $item["parent"][0];
	        }
	
		}  
        return $parents;
	}
	static public function getParentArrays($array)
	{
		if(array_key_exists("parent",$array)){
			return 1;
		}else{
			return 2;
		}
		
	}
}

if ( !function_exists( 'myPrint' ) ) :
/**
 * Function for printing data
 * @return
 */
function myPrint( $var, $pre = true )
{
	if( $pre )
		echo "<pre>";
	print_r( $var );
	if( $pre )
		echo "</pre>";
}
endif;

?>