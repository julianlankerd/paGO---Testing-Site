<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
?>


<link href='//fonts.googleapis.com/css?family=PT+Sans+Caption:400,700' rel='stylesheet' type='text/css'>

<?php
	defined('_JEXEC') or die('Restricted access');

	$pago_manifest = JFactory::getXML(JPATH_SITE .'/administrator/components/com_pago/pago.xml');

	$app = JFactory::getApplication(0);
		$pago_theme   = $this->config->get( 'template.pago_theme', 'default' );
		if($this->theme == null)
		{
			$this->theme = $pago_theme;
		}
		if ( $app->isAdmin() ) {
			$db = JFactory::getDBO();
			$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
			$db->setQuery($query);
			$joomla_theme = $db->loadResult();
		} else {
			$joomla_theme = $app->getTemplate();
		}
		$return_paths = array();
		$paths = array(
			'component' => array(
				'full' => JPATH_SITE .'/components/com_pago/templates/'.$pago_theme.'/',
				'url' => JURI::base(true) . '/components/com_pago/templates/'.$pago_theme.'/'
			),
			'joverride' => array(
				'full' => JPATH_SITE . '/templates/' . $joomla_theme . '/html/com_pago/'. $this->theme . '/',
				'url' => JURI::base(true) . '/templates/' . $joomla_theme .'/html/com_pago/'. $this->theme . '/'
			)

		);
		if(file_exists( $this->theme_path ."css") && is_dir( $this->theme_path ."css" ))
		{
			if ( file_exists( $this->theme_path .'css/colorbox.css') && is_dir( $this->theme_path ) ) {
				$return_paths['colorbox'] = $this->theme_path_url .'css/colorbox.css';
			}
			if(file_exists( $this->theme_path .'css/jquery.qtip.min.css') && is_dir( $this->theme_path ))
			{
				$return_paths['qtip'] = $this->theme_path_url .'css/jquery.qtip.min.css';
			}
			if(file_exists( $this->theme_path .'css/jquery-ui-1.8.7.custom.css') && is_dir( $this->theme_path ))
			{
				$return_paths['custom'] = $this->theme_path_url .'css/jquery-ui-1.8.7.custom.css';
			}
			if(file_exists( $this->theme_path .'css/idangerous.swiper.css') && is_dir( $this->theme_path ))
			{
				$return_paths['idangerous'] = $this->theme_path_url  .'css/idangerous.swiper.css';
			}
			
		}
		else
		{
			if ( file_exists( $this->full_path_before_theme .'default/css/colorbox.css') && is_dir( $this->full_path_before_theme ) ) {
				$return_paths['colorbox'] = $this->url_path_before_theme .'default/css/colorbox.css';
			}
			if(file_exists( $this->full_path_before_theme .'default/css/jquery.qtip.min.css') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['qtip'] = $this->url_path_before_theme .'default/css/jquery.qtip.min.css';
			}
			if(file_exists( $this->full_path_before_theme .'default/css/jquery-ui-1.8.7.custom.css') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['custom'] = $this->url_path_before_theme .'default/css/jquery-ui-1.8.7.custom.css';
			}
			if(file_exists( $this->full_path_before_theme .'default/css/idangerous.swiper.css') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['idangerous'] = $this->url_path_before_theme  .'default/css/idangerous.swiper.css';
			}
		}
		

		if(file_exists( $this->theme_path ."js") && is_dir( $this->theme_path ."js" ))
		{
			if ( file_exists( $this->theme_path .'js/jquery.validate.min.js') && is_dir( $this->theme_path ) ) {
				$return_paths['validate'] = $this->theme_path_url .'js/jquery.validate.min.js';
			}
			if(file_exists( $this->theme_path .'js/additional-methods.min.js') && is_dir( $this->theme_path ))
			{
				$return_paths['additional'] = $this->theme_path_url  .'js/additional-methods.min.js';
			}
			if(file_exists( $this->theme_path .'js/jquery.qtip.pack.js') && is_dir( $this->theme_path ))
			{
				$return_paths['qtipjs'] = $this->theme_path_url  .'js/jquery.qtip.pack.js';
			}
			if(file_exists( $this->theme_path .'js/idangerous.swiper.js') && is_dir( $this->theme_path ))
			{
				$return_paths['idangerousjs'] = $this->theme_path_url .'js/idangerous.swiper.js';
			}
			if(file_exists( $this->theme_path .'js/idangerous.swiper.js') && is_dir( $this->theme_path ))
			{
				$return_paths['autocomplete'] = $this->theme_path_url .'js/jquery-ui-1.8.16.autocomplete.min.js';
			}
			if(file_exists( $this->theme_path .'js/pago.js') && is_dir( $this->theme_path ))
			{
				$return_paths['pagojs'] = $this->theme_path_url .'js/pago.js';
			}
			
		}
		else
		{
			if ( file_exists( $this->full_path_before_theme .'default/js/jquery.validate.min.js') && is_dir( $this->full_path_before_theme ) ) {
				$return_paths['validate'] = $this->url_path_before_theme .'default/js/jquery.validate.min.js';
			}
			if(file_exists( $this->full_path_before_theme .'default/js/additional-methods.min.js') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['additional'] = $this->url_path_before_theme  .'default/js/additional-methods.min.js';
			}
			if(file_exists( $this->full_path_before_theme .'default/js/jquery.qtip.pack.js') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['qtipjs'] = $this->url_path_before_theme  .'default/js/jquery.qtip.pack.js';
			}
			if(file_exists( $this->full_path_before_theme .'default/js/idangerous.swiper.js') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['idangerousjs'] = $this->url_path_before_theme .'default/js/idangerous.swiper.js';
			}
			if(file_exists( $this->full_path_before_theme .'default/js/idangerous.swiper.js') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['autocomplete'] = $this->url_path_before_theme .'default/js/jquery-ui-1.8.16.autocomplete.min.js';
			}
			if(file_exists( $this->full_path_before_theme .'default/js/pago.js') && is_dir( $this->full_path_before_theme ))
			{
				$return_paths['pagojs'] = $this->url_path_before_theme .'default/js/pago.js';
			}
		}
		$_root = JURI::root( true );
		$view = JFactory::getApplication()->input->get('view');
		$tmpl = JFactory::getApplication()->input->get('tmpl');
		$doc = JFactory::getDocument();
		$version = new JVersion();
		if($tmpl!='component'){
			PagoHtml::add_css( $return_paths['colorbox'] );
			PagoHtml::add_css( $return_paths['qtip'], true );
			PagoHtml::add_css( $return_paths['custom'] );
		}

		PagoHtml::loadBootstrapCss();
		//PagoHtml::add_css( $_root . '/components/com_pago/templates/'.$pago_theme.'/css/bootstrap.min.css' );
		PagoHtml::add_css( $return_paths['idangerous'] );

		PagoHtml::loadJquery();
		if($tmpl!='component'){
			PagoHtml::add_js( $return_paths['validate'], true );
			PagoHtml::add_js( $return_paths['additional'], true );
			PagoHtml::add_js( $return_paths['qtipjs'], true );
			PagoHtml::add_js( $return_paths['idangerousjs'], true );

			// if($this->config->get( 'general.pago_capture_app_data', true)){
			// 	PagoHtml::add_js( $_root . '/components/com_pago/javascript/ga.js', true );
			// }
		}


		if($version->RELEASE < 3){
			PagoHtml::loadBootstrapJs();
			//PagoHtml::add_js( $_root . '/components/com_pago/templates/'.$pago_theme.'/js/bootstrap.min.js', true );
			PagoHtml::add_js( $return_paths['autocomplete'], true );
		}
		//PagoHtml::add_js( $_root . '/components/com_pago/templates/'.$pago_theme.'/js/jquery.colorbox-min.js', true );
		PagoHtml::add_js( $return_paths['pagojs'] );

		$doc->addScriptDeclaration("
				jQuery.noConflict();
				var JPATH_COMPONENT = '" .JURI::base( true ). "/components/com_pago/';
				var JPATH_ROOT_JS = '" . JURI::root(true) . "';
				var JPATH_ROOT = '" .JURI::root( true ). "';
		");

		if($this->config->get( 'general.pago_capture_app_data', true)){
			$doc->addScriptDeclaration("
var PAGO_APP_INSTALL_ID = '" .juri::base(). "';
var PAGO_APP_VERSION = '" .(string)$pago_manifest->version. "';
var PAGO_APP_NAME = '" .(string)$pago_manifest->name. "';
var PAGO_APP_ID = '" .(string)$pago_manifest->name. "';
var PAGO_APP_UAID = '" .(string)$pago_manifest->uaid. "';

(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create',PAGO_APP_UAID,'none',{'name':'cp_tracker'});

ga('cp_tracker.send','screenview',{
	'appName':'paGO Commerce',
	'appId':PAGO_APP_ID,
	'appVersion':PAGO_APP_VERSION,
	'appInstallerId':PAGO_APP_INSTALL_ID
});

ga('cp_tracker.set','dimension1',PAGO_APP_ID);
ga('cp_tracker.set','dimension2',PAGO_APP_INSTALL_ID);
ga('cp_tracker.set','dimension3','paGO Commerce');
ga('cp_tracker.set','dimension4',PAGO_APP_VERSION);

ga('cp_tracker.send','pageview');
");
		}

		PagoHtml::loadFonts();
		PagoHtml::loadChosen();
		PagoHtml::add_css("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");
?>
<div id="pago" class="clearfix pg-main-container">
	<div id="pg-header" class="clearfix">
    	<?php

		// Change store title to h1 on homepage for better SEO, then depreciate to p tag on inner pages.
		if ($view == 'frontpage') {
			if ( $this->config->get( 'general.show_store_title', 0 ) ) : ?>
				<div class="pg-store-title">
                	<h1><?php echo $this->config->get( 'general.pago_store_name' );?></h1>
					<?php if( $this->tmpl_params->get( 'show_tagline', 1 ) ) : ?>
					   <span class="pg-store-slogan"><?php echo $this->config->get( 'general.pago_store_slogan' ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif;
		} else {
			if ( $this->config->get( 'general.show_store_title', 0 ) ) : ?>
				<div class="pg-store-title">
                	<p><?php echo $this->config->get( 'general.pago_store_name' ); ?></p>
					<?php if( $this->tmpl_params->get( 'show_tagline', 1 ) ) : ?>
					   <span class="pg-store-slogan"><?php echo $this->config->get( 'general.pago_store_slogan' ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif;
		} ?>
        <div id="pg-menu">
        	<div class="menu-wrapper clearfix">
		        <?php if( count($this->modules->get_position( 'pago_account_options' ) ) ) : ?>
		        	<div class="pg-account-options pg-menu">
						<?php $this->modules->render_position( 'pago_account_options' ) ?>
		            </div>
		        <?php endif; ?>
		        	<!-- <div class="pg-help">
		            	<a href="#replace-with-help-link"><?php echo JText::_('PAGO_MENU_HELP_LINK'); ?></a>
		            </div> -->
	       	</div>

        <?php if( count( $this->modules->get_position( 'pago_cart' ) ) ) : ?>
        	<div class="pg-mini-cart">
				<?php echo $this->modules->render_position( 'pago_cart' ) ?>
            </div>
        <?php endif; ?>
        </div><!-- end pg-menu -->
        <?php if( count( $this->modules->get_position( 'pago_search' ) ) ) : ?>
        	<div class="pg-search">
				<?php echo $this->modules->render_position( 'pago_search' ) ?>
            </div>
        <?php endif; ?>
        <?php if( count( $this->modules->get_position( 'pago_nav' ) ) ) : ?>
        	<div class="pg-nav clearfix">
    		    <?php echo $this->modules->render_position( 'pago_nav' ) ?>
            </div>
        <?php endif; ?>
	</div><!-- end pg-header -->
	<a name="top-of-store"></a>
    <div id="pg-content" class="clearfix">
        <?php if( count( $this->modules->get_position( 'pago_breadcrumbs' ) ) ) : ?>
        	<div class="pg-breadcrumbs">
				<?php echo $this->modules->render_position( 'pago_breadcrumbs' ) ?>
            </div>
        <?php endif; ?>
