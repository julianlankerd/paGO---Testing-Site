<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * Extend JView to provide seemless functionality for pago themes
 */
class PagoView extends JViewLegacy
{
	/**
	 * Holds pago config
	 */
	public $config = null;
	/**
	 * holds pago theme params
	 */
	public $tmpl_params = null;
	/**
	 * holds theme name
	 */
	protected $theme = null;
	/**
	 * holds base path to theme
	 */
	protected $theme_path = null;
	/**
	 * holds full path to theme css
	 */
	protected $theme_css_path = null;
	/**
	 * holds css url
	 */
	protected $theme_css_url = null;
	/**
	 * holds full path to theme functions.php
	 */
	protected $theme_functions_path = null;

	/*
	 * Override parent constuct and make sure config and tmpl_params is set for every view
	 *
	 */
	public function __construct( $config = array() )
	{
		parent::__construct($config);
		$kconfig = Pago::get_instance( 'config' );
		$this->config = $kconfig->get('global');
		$this->tmpl_params = $kconfig->get( $this->config->get('template.pago_theme', 'default') );
		unset($kconfig);
	}

	/**
	 * Override parent display and make sure the module helper gets set for every view
	 *
	 */
	public function display( $tpl = null )
	{

		Pago::load_helpers( 'module' );
		$modules = new PagoHelperModule;
		$this->assignRef( 'modules', $modules );

		parent::display( $tpl );
	}

	/**
	 * Set template path to current view
	 *
	 * Finds the template path and caches if cache enabled
	 *
	 * @params $view JView
	 * @params $tmpl string
	 */
	public function set( $tmpl, $value = NULL )
	{
		$cache        = JFactory::getCache( 'com_pago', '' );
		$config       = Pago::get_instance( 'config' )->get();
		$pago_theme   = $config->get( 'template.pago_theme', 'default' );
		$template_helper = Pago::get_instance( 'template' );
		$cache_theme = $cache->get('theme', 'template');

		if(file_exists(JPATH_SITE .'/components/com_pago/templates/'.$pago_theme."/".$tmpl))
		{
			$this->theme = null;
		}

		if( ($tmpl == 'item' || $tmpl =='category' || $tmpl =='search' || $tmpl =='cart') && $value != NULL)
		{
			$this->theme = $value;
		}
		
		if ( $this->theme == null ) {
			$this->theme = $pago_theme; // always just init to theme from global config
		}
		if ( $this->theme == $cache_theme ) {
			// pull from cache if something is there and nothing has changed
			$this->theme_path           = $cache->get( 'theme_path', 'template' );
			$this->theme_css_path       = $cache->get( 'theme_css_path', 'template' );
			$this->theme_css_url       = $cache->get( 'theme_css_url', 'template' );
			$this->theme_functions_path = $cache->get( 'theme_functions_path', 'template' );
			$this->full_path_before_theme       = $cache->get( 'full_path_before_theme', 'template' );
   			$this->url_path_before_theme = $cache->get( 'url_path_before_theme', 'template' );
   			$this->theme_path_url = $cache->get( 'theme_path_url', 'template' );
			$this->addTemplatePath( $this->theme_path .  $tmpl );
			$this->include_functions_file();
			$this->add_css_file();
			return true; // return once everything is set
		}
		//echo $this->theme_path . $tmpl; exit;
		// this should only run if no theme info is anywhere or it has changed
		list(
			$this->theme_path,
			$this->theme_css_path,
			$this->theme_css_url,
			$this->theme_functions_path,
			$this->full_path_before_theme,
			$this->url_path_before_theme,
			$this->theme_path_url
		) = $template_helper->find_paths( $this->theme, $tmpl );
		
		// Add the path we just found
		$this->addTemplatePath( $this->theme_path . $tmpl );
		$this->include_functions_file();
		$this->add_css_file();
		// store our new theme in cache
		$cache->store( $this->theme, 'theme', 'template' );
		$cache->store( $this->theme_path, 'theme_path', 'template' );
		$cache->store( $this->theme_functions_path, 'theme_functions_path', 'template' );
		$cache->store( $this->theme_css_path, 'theme_css_path', 'template' );
		$cache->store( $this->theme_css_url, 'theme_css_url', 'template' );
		$cache->store( $this->full_path_before_theme, 'full_path_before_theme', 'template' );
		$cache->store( $this->url_path_before_theme, 'url_path_before_theme', 'template' );
		$cache->store( $this->theme_path_url, 'theme_path_url', 'template' );
	}

	public function set_theme( $theme )
	{
		$this->theme = $theme;
		return true;
	}

	/**
	 * Include the theme functions.php file if exists
	 */
	private function include_functions_file()
	{
		// if not found then they don't have one

		if ( file_exists( $this->theme_functions_path ) ) {
			include_once( $this->theme_functions_path );
		}
	}

	/**
	 * Add the theme pago.css file if exists
	 */
	private function add_css_file()
	{
		// if css file exists then add otherwise they aren't using one in the template
		if ( file_exists( $this->theme_css_path ) ) {
			PagoHtml::add_css( $this->theme_css_url, false, 'text/css', 'screen' );
		}
	}

	/**
	 * Get theme name
	 *
	 * @return theme
	 */
	public function get_theme()
	{
		return $this->theme;
	}

	/**
	 * Get theme path
	 *
	 * @return theme_path
	 */
	public function get_theme_path()
	{
		return $this->theme_path;
	}


	/**
	 * Load a header template for the current theme
	 *
	 * Looks for default.php unless you pass in a different tmpl name without .php
	 *
	 * @param tmpl string
	 */
	public function load_header( $tmpl = 'default' )
	{
		// return if async param is pass thru
		if ( JFactory::getApplication()->input->getInt( 'async', 0) === 2 ) {
			return;
		}

		// if exists includ or else do nothing
		if ( file_exists( $this->theme_path . '/header/' . $tmpl . '.php' ) ) {
			include $this->theme_path . '/header/' . $tmpl . '.php';
		}
		else
		{
			include $this->full_path_before_theme . 'default/header/' . $tmpl . '.php';
		}
	}

	/**
	 * Load a footer template for the current theme
	 *
	 * Looks for default.php unless you pass in a different tmpl name without .php
	 *
	 * @param tmpl string
	 */
	public function load_footer( $tmpl = 'default' )
	{
		// return if async param is pass thru
		if ( JFactory::getApplication()->input->getInt( 'async', 0) === 2 ) {
			return;
		}
		// if exists includ or else do nothing
		if ( file_exists( $this->theme_path . '/footer/' . $tmpl . '.php' ) ) {
			include $this->theme_path . '/footer/' . $tmpl . '.php';
		}
		else
		{
			include $this->full_path_before_theme . 'default/footer/' . $tmpl . '.php';
		}
	}

	/**
	 * Set page metadata for categories or items
	 *
	 * Giving either category/item and an id will set the page metadata with teh saved meta data
	 * for category or item
	 *
	 * @param string category || item
	 * @param int Id of category or item
	 * @param string default title if none
	 */
	public function set_metadata( $type, $id, $title = '' )
	{
		$meta     = Pago::get_instance( 'meta' );
		$document = JFactory::getDocument();

		$html_title = $meta->get( $type, $id, 'html_title', true );
		if ( $html_title ) {
			$document->setTitle( $html_title );
		} else {
			$document->setTitle( $title );
		}

		// Set Meta Tag Title
		$tag_title = $meta->get( $type, $id, 'title', true );
		if ( $tag_title ) {
			$document->setMetaData( 'title', $tag_title );
		}

		// Set Meta Tag Descriptions
		$desc = $meta->get( $type, $id, 'description', true );
		if ( $desc ) {
			$document->setMetaData( 'description', $desc );
		}

		// Set Meta Tag Author
		$author = $meta->get( $type, $id, 'author', true );
		if ( $author ) {
			$document->setMetaData( 'author', $author );
		}

		// Set Meta Tag Robots
		$robots = $meta->get( $type, $id, 'robots', true );
		if ( $robots ) {
			$document->setMetaData( 'robots', $robots );
		}
	}

}
