<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die();

class PagoHtml{
	public static $loadFrontendChosen = false;
	public static $loadFont = false;
	public static $loadJqueryFiles = false;
	public static $loadBootstrapCssFile = false;
	public static $loadBootstrapJsFile = false;
	public static $loadUploadifiveFile = false;
	protected static $loaded = array();


	public static function sortable($tableId, $formId, $sortDir = 'asc', $saveOrderingUrl, $proceedSaveOrderButton = true, $nestedList = false)
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__]))
		{
			return;
		}

		// Depends on jQuery UI
		PagoHtml::add_js( JURI::root( true ) . '/administrator/components/com_pago/javascript/jquery-ui/js/sortablelist.js', true );
		PagoHtml::add_css( JURI::root( true ) . '/administrator/components/com_pago/javascript/jquery-ui/css/sortablelist.css', true );

		// Attach sortable to document
		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
					var sortableList = new $.JSortableList('#" . $tableId . " tbody','" . $formId . "','" . $sortDir . "' , '" . $saveOrderingUrl . "','','" . $nestedList . "');
				});
			})(jQuery);
			"
		);

		if ($proceedSaveOrderButton)
		{
			static::_proceedSaveOrderButton();
		}

		// Set static array
		static::$loaded[__METHOD__] = true;

		return;
	}


	public static function _proceedSaveOrderButton()
	{
		JFactory::getDocument()->addScriptDeclaration(
			"(function ($){
				$(document).ready(function (){
					var saveOrderButton = $('.saveorder');
					saveOrderButton.css({'opacity':'0.2', 'cursor':'default'}).attr('onclick','return false;');
					var oldOrderingValue = '';
					$('.text-area-order').focus(function ()
					{
						oldOrderingValue = $(this).attr('value');
					})
					.keyup(function (){
						var newOrderingValue = $(this).attr('value');
						if (oldOrderingValue != newOrderingValue)
						{
							saveOrderButton.css({'opacity':'1', 'cursor':'pointer'}).removeAttr('onclick')
						}
					});
				});
			})(jQuery);"
		);

		return;
	}
	static public function apply_table_style( $selector ){
		JFactory::getDocument()->addScriptDeclaration("
			jQuery.fn.styleTable = function (options) {
				var defaults = {
					css: 'styleTable'
				};
				options = jQuery.extend(defaults, options);

				return this.each(function () {

					input = jQuery(this);
					input.addClass(options.css);

					input.find('tr').live('mouseover mouseout', function (event) {
						if (event.type == 'mouseover') {
							jQuery(this).children('td').addClass('ui-state-hover');
						} else {
							jQuery(this).children('td').removeClass('ui-state-hover');
						}
					});

					input.find('th').addClass('ui-state-default');
					input.find('td').addClass('ui-widget-content');

					input.find('tr').each(function () {
						jQuery(this).children('td:not(:first)').addClass('first');
						jQuery(this).children('th:not(:first)').addClass('first');
					});
				});
			};

			jQuery(document).ready(function($) {
				$('{$selector}').addClass('styleTable');
				$('{$selector}').styleTable();
			});
		");

		JFactory::getDocument()->addStyleDeclaration("
			.styleTable { border-collapse: separate; }
			.styleTable TD { font-weight: normal !important; padding: .4em; border-top-width: 0px !important; }
			.styleTable TH { text-align: center; padding: .8em .4em; }
			.styleTable TD.first, .styleTable TH.first { border-left-width: 0px !important; }
			.styleTable tr.alt td{background:#ff0000}
		");
	}

	static public function deploy_tabpanel( $id = 'tabs' ){
		$doc = JFactory::getDocument();

		$doc->addScriptDeclaration("
			jQuery(document).ready(function(jQuery) {
				tab = 0;

				/*DEPRECIATED
				if ( ( new_tab = gup( 'tab', window.location.hash ) ) ) {
					tab = new_tab;
				}*/

				jQuery( '#$id' ).tabs({
					select:function(event,ui){
						winLoc = window.location.toString();
						winLoc = winLoc.split('#');
						winLoc = winLoc[0]+jQuery(ui.tab).attr('href');
						window.location = winLoc;
					}
				});

				jQuery('ul.ui-corner-all').removeClass('ui-corner-all');
			});
		");

		$doc->addStyleDeclaration("
			.ui-widget-content { border: none;}
			.ui-tabs { padding: 0;}
			.ui-widget-header { border-left: 0px; border-right: 0px; }
		");
	}

	static public function behaviour_jquery(){
		self::loadJquery();

		static $used;

		if ( $used ) { return; }

		$version = new JVersion();
		$doc = JFactory::getDocument();
		if($version->RELEASE <= 3){
			//PagoHtml::add_js( JURI::root( true ) . '/components/com_pago/javascript/jquery.js', true );
			PagoHtml::add_js( JURI::root( true ) . '/components/com_pago/javascript/jquery-migrate-1.2.1.min.js', true );
		}


		$doc->addScriptDeclaration("jQuery.noConflict();
			var JPATH_COMPONENT = '" .JURI::base( true ). "/components/com_pago/';
			var JPATH_ROOT_JS = '" . JURI::root(true) . "';
			var JPATH_ROOT = '" .JURI::root( true ). "';");

		$used = true;
	}

	static public function behaviour_jquery_validator(){
		PagoHtml::behaviour_jquery();

		PagoHtml::add_js( JURI::root( true )
			. '/components/com_pago/javascript/jquery.validate.min.js', true );

		PagoHtml::add_js( JURI::root( true )
			. '/components/com_pago/javascript/additional-methods.min.js', true );
	}

	static public function behaviour_jquery_qtip(){
		PagoHtml::behaviour_jquery();

		PagoHtml::add_js( JURI::root( true )
			. '/components/com_pago/javascript/jquery.qtip.pack.js', true );

		PagoHtml::add_css( JURI::root( true )
			. '/components/com_pago/css/jquery.qtip.min.css', true );
	}

	// static public function behavior_jqueryui_autocomplete()
	// {
	// 	PagoHtml::behaviour_jquery();

	// 	PagoHtml::add_js( JURI::root( true )
	// 		.'/components/com_pago/javascript/jquery-ui-1.8.16.autocomplete.min.js', true );

	// 	PagoHtml::add_css( JURI::root( true )
	// 		. '/components/com_pago/javascript/jqueryui/css/custom-theme'
	// 		. '/jquery-ui-1.8.7.custom.css' );
	// }

	static public function behaviour_jqueryui( $jquery_ui = false ){

		// PagoHtml::add_js( JURI::root( true )
		// 	.'/administrator/components/com_pago/javascript/jquery-ui/js/'
		// 	.'jquery-ui-1.8.16.custom.min.js', true );
		PagoHtml::add_js( JURI::root( true )
		 	.'/administrator/components/com_pago/javascript/jquery-ui/js/'
		 	.'jquery-ui-1.10.4.custom.min.js', true );

		PagoHtml::add_js( JURI::root( true )
			.'/administrator/components/com_pago/javascript/jquery-cook.js', true );

	}

	static public function tooltip(){
		static $used;

		if ( $used ) { return; }

		PagoHtml::behaviour_jquery();
		PagoHtml::behaviour_jqueryui();

		PagoHtml::add_js( JURI::root( true )
			. '/administrator/components/com_pago/javascript/jquery-ui/js/jquery.ui.tooltip.js',
			true );

		PagoHtml::add_css( JURI::root( true )
			. '/administrator/components/com_pago/javascript/jquery-ui/css/jquery-ui.tooltip.css',
			true );

		$used = true;
	}

	static public function multiselect(){

		PagoHtml::behaviour_jquery();
		PagoHtml::behaviour_jqueryui();

		PagoHtml::add_js( JURI::root( true )
			. '/administrator/components/com_pago/javascript/jquery-ui/js/'
			. 'jquery.multiselect.min.js', true );

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration( 'jQuery(document).ready( function() { '
			. 'jQuery("select[multiple=true]").multiselect(); jQuery("select[multiple=true]").trigger("chosen:updated"); });' );

	}

	static public function jstree( $plugins = array() ){
		PagoHtml::behaviour_jquery();

		foreach ( $plugins as $file ) {
			if($file == 'cookie'){
				PagoHtml::add_js( JURI::root(true)
				. "/components/com_pago/javascript/jsTree/_lib/jquery-cook.js" );
			}else{
				PagoHtml::add_js( JURI::root(true)
					. "/components/com_pago/javascript/jsTree/_lib/jquery.{$file}.js" );
			}
		}
		PagoHtml::add_js( JURI::root(true)
			. '/components/com_pago/javascript/jsTree/jquery.jstree.js' );
	}

	static public function thickbox(){
		PagoHtml::loadJquery();

		PagoHtml::add_css( JURI::root( true ) . '/components/com_pago/css/thickbox.css' );
		PagoHtml::add_js( JURI::root( true )
			. '/components/com_pago/javascript/jquery.thickbox-3.1.js' );
	}

	static public function uniform(){
		static $used;

		if ( $used ) { return; }


		PagoHtml::add_css( JURI::root( true )
			. '/administrator/components/com_pago/css/chosen.css' );
		PagoHtml::add_css( JURI::root( true )
			. '/administrator/components/com_pago/css/perfect-scrollbar.css' );

		PagoHtml::add_css("//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css");

		PagoHtml::behaviour_jquery();
		PagoHtml::add_js(  JURI::root( true ) .
			'/administrator/components/com_pago/javascript/chosen.jquery.js' );
		PagoHtml::add_js(  JURI::root( true ) .
			'/administrator/components/com_pago/javascript/perfect-scrollbar.js' );
		PagoHtml::add_js( JURI::root( true )
			. '/administrator/components/com_pago/javascript/com_pago.js' );

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration( '
			jQuery(document).ready( function() {
				// jQuery("select:not([multiple=multiple]), input:checkbox, input:radio, input:file").uniform();

				jQuery("div.uploader span").click(function() {
					jQuery(this).parents("div.uploader").find("input").trigger("click");
				});
		jQuery("div.uploader span.action").wrap("<div class=\"pg-uploader-button-wrapper\" />");
	});
		jQuery(window).on("load", function(){
			jQuery("select").chosen({"disable_search_threshold": 6});
		})

		' );

		$used = true;

	}

	static public function add_js( $path, $is_minified = false, $type = 'text/javascript' ){
		$doc = JFactory::getDocument();

		// if ( !$is_minified && constant( 'PAGO_IN_DEVELOPMENT' ) ) {

		// 	$path2 = str_replace( '.js', '-dev.js', $path );

		// 	if( PagoHTML::url_exists( $path2 )  ) {
		// 		// The file is found so we set it to path
		// 		$path = $path2;
		// 	}
		// }

		$doc->addScript( $path . '?' . time(), $type );
	}

	static public function add_css( $path,
									$is_minified = false,
									$type = 'text/css',
									$media = null,
									$attribs = array()
									)
	{
		$doc = JFactory::getDocument();

  //       if ( constant( 'PAGO_IN_DEVELOPMENT' ) ){
  //           $path_less = str_replace( '.css', '-dev.less', $path );
  //           $path_css = str_replace( '.css', '-dev.css', $path );
  //           if( PagoHTML::url_exists( $path_less )  ) {
  //               require_once("lessc.inc.php");
  //               $less2css = new lessc;
  //               $less2css->setFormatter("compressed");
  //               $less2css->compileFile($_SERVER['DOCUMENT_ROOT'].$path_less, $_SERVER['DOCUMENT_ROOT'].$path_css);
  //           }
  //       }

		// if ( !$is_minified && constant( 'PAGO_IN_DEVELOPMENT' ) ) {
		// 	$path2 = str_replace( '.css', '-dev.css', $path );

		// 	if( PagoHTML::url_exists( $path2 )  ) {
		// 		// The file is found so we set it to path
		// 		$path = $path2;
		// 	}
		// }

		$doc->addStyleSheet( $path . '?' . time(), $type, $media, $attribs );
	}

	static public function apply_layout_fixes(){
		$doc = JFactory::getDocument();

		if( !JFactory::getApplication()->input->get('sp') ) {

			$sticky_bar = '';

			$version = new JVersion();
				PagoHtml::add_js( JURI::root( true )
					. '/administrator/components/com_pago/javascript/layout_fixes.js' );

				if ( JFactory::getApplication()->input->get('view') != 'default' ) {
					PagoHtml::add_js( JURI::root( true )
						. '/components/com_pago/javascript/jquery.stickyBar.js' );
					if($version->RELEASE < 3){
						$sticky_bar = "jQuery.stickyBar(jQuery('#toolbar'),0);";
					}else{
						$sticky_bar = "jQuery.stickyBar(jQuery('.pg-header #toolbar'),22);";
					}
				}

			$confirm_msg = JText::_( 'PAGO_ARE_YOU_SURE' );

			$doc->addScriptDeclaration("
				jQuery(document).ready(function($) {
					$sticky_bar

					jQuery( 'button[type=task]' ).click( function(){

						var btn = jQuery(this);
						var answer = true;

						if( btn.attr( 'confirm' ) ){
							answer = confirm( '{$confirm_msg}' );
						}

						if ( answer ){
							jQuery( 'input[name=rel]' ).val( btn.attr( 'rel' ) );
							jQuery( 'input[name=task]' ).val( btn.attr( 'task' ) );
							jQuery( 'form[name=adminForm]' ).submit();
						}

						return false;
					});
				});
			");
		}
	}

	static public function module_top(  $content,
										$item_title = null,
										$buttons = null,
										$title_options = null,
										$special_class = null,
										$col_span = null,
										$order_icon = null,
										$table = true
									)
	{
		if ($table){
			$html = '<div class="pg-table-wrap"><table class="pg-table ' . $special_class
				. '"><thead><tr class="pg-main-heading"><td colspan="' . $col_span . '">';
			$html .= '<div class="pg-background-color">';
			if ( $order_icon ) {
				$order_class = strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $order_icon ) );
				$html .= '<div class="pg-order-status-icon"><span class="pg-icon pg-order-status-' .
					$order_class . '">' . $order_icon . '</span></div>';
			}
			$html .= $content;

			if ( $item_title ) {
				$html .= ' <span>' . $item_title . '</span>';
			}

			if ( $title_options ) {
				$html .= '<div class="pg-title-options">' . $title_options . '</div>';
			}

			if ( $buttons ) {
				foreach ( $buttons as $button ) {
					$attributes = '';

					if ( isset( $button['class'] ) ) {
						$attributes .= ' class="pg-title-button ' . $button['class'] . '"';
					} else {
						$attributes .= ' class="pg-title-button"';
					}

					if ( isset( $button['href'] ) ) {
						$attributes .= ' href="' . $button['href'] . '"';
					}

					if ( isset( $button['type'] ) ) {
						$attributes .= ' type="' . $button['type'] . '"';
					}

					if ( isset( $button['task'] ) ) {
						$attributes .= ' task="' . $button['task'] . '"';
					}

					if ( isset( $button['confirm'] ) ) {
						$attributes .= ' confirm="' . $button['confirm'] . '"';
					}

					if ( isset( $button['rel'] ) ) {
						$attributes .= ' rel="' . $button['rel'] . '"';
					}

					$html .= '<div class="pg-title-button-wrap">';
					$html .= '<' . $button['element_type'] . ' ' . $attributes . '><span></span>'
						. $button['text'] . '</' . $button['element_type'] . '>';
					$html .= '</div>';
				}
			}

			$html .= '</div></td></tr></thead><tbody>';

			return $html;
		}
		else{
			$html = '<div class=" ' . $special_class. '">';
			$html .= '<div class="pg-container-header">';

			if ( $order_icon ) {
				$order_class = strtolower( preg_replace( "/[^A-Za-z0-9]/", "-", $order_icon ) );
				$html .= '<div class="pg-order-status-icon"><span class="pg-icon pg-order-status-' .
					$order_class . '">' . $order_icon . '</span></div>';
			}
			$html .= $content;

			if ( $item_title ) {
				$html .= ' (' . $item_title . ')';
			}

			if ( $title_options ) {
				$html .= '<div class="pg-title-options">' . $title_options . '</div>';
			}

			if ( $buttons ) {
				foreach ( $buttons as $button ) {
					$attributes = '';

					if ( isset( $button['class'] ) ) {
						$attributes .= ' class="pg-title-button ' . $button['class'] . '"';
					} else {
						$attributes .= ' class="pg-title-button"';
					}

					if ( isset( $button['href'] ) ) {
						$attributes .= ' href="' . $button['href'] . '"';
					}

					if ( isset( $button['type'] ) ) {
						$attributes .= ' type="' . $button['type'] . '"';
					}

					if ( isset( $button['task'] ) ) {
						$attributes .= ' task="' . $button['task'] . '"';
					}

					if ( isset( $button['confirm'] ) ) {
						$attributes .= ' confirm="' . $button['confirm'] . '"';
					}

					if ( isset( $button['rel'] ) ) {
						$attributes .= ' rel="' . $button['rel'] . '"';
					}

					$html .= '<div class="pg-title-button-wrap">';
					$html .= '<' . $button['element_type'] . ' ' . $attributes . '><span></span>'
						. $button['text'] . '</' . $button['element_type'] . '>';
					$html .= '</div>';
				}
			}

			$html .= '</div>';

			return $html;
		}
	}

	static public function module_bottom($table = true)	{
		if ($table){
			$html ='</tbody></table></div>';
		}
		else{
			$html ='</div>';
		}
		return $html;
	}

	static public function side_menu( $menu_items ){
		$view = JFactory::getApplication()->input->get( 'view', '' );

		$filter_folder = '';
		$ch = JFactory::getApplication()->input->get( 'filter_folder', '');
		if (!empty($ch)){
			$filter_folder = JFactory::getApplication()->input->get( 'filter_folder', '' );
		}
		echo '<div class="pg-sidebar"><a href = "javascript:void(0)" class = "pg-sidebar-show-hide opened"></a>';

		$menu = '<ul class="pg-menu">';
		foreach ( $menu_items as $item ) {
			$hasChildren = false;
			$hasChevron = '';
			if (!empty( $item['children'])){
				$hasChildren = 'true';
				$hasChevron = '<span class = "chevron"></span>';
			}

			if ( $view == $item['view'] || ( $view == "default" && $item['view'] == "" ) ) {
				$menu .= '<li class="pg-menu-' . strtolower( $item['name'] ) . ' '. ' current">'.$hasChevron;
			} else {
				$menu .= '<li class="pg-menu-' . strtolower( $item['name'] ) . ' '. '">'. $hasChevron;
			}
			$menu .= '<a href="' . $item['link'] . '">';
			$menu .= $item['name'];
			$menu .= '</a>';
			if ($hasChildren != '') {
				$menu .= '<div class="pg-submenu-wrap"><ul>';
				$num_child = count( $item['children'] );
				$i = 0;
				foreach ( $item['children'] as $child ) {
					$cur = '';

					if ( $view == $child['view']){
						if (!empty($child['filter_folder'])){
							if ($filter_folder != '' && $child['filter_folder'] == $filter_folder){
								$cur = 'class = "current"';
							}
						}
						else{
							$cur = 'class = "current"';
						}
					}

					$menu .= '<li '.$cur.'>';
					$menu .= '<a href="' . $child['link'] . '">'
						. $child['name'] . '</a>';
					$menu .= '</li>';
					$i++;
				}
				$menu .= '</ul></div>';
			}
			$menu .= '</li>';
		}
		$menu .= '</ul>';
		echo $menu;
		echo '</div>';

	}

	static public function pago_top( $menu_items, $class = null, $top_menu = false ){
		$version = new JVersion();
		$joomlaVersion = '';
		$top_menu = PagoHtml::pago_top_menu($top_menu);
		$layout = JFactory::getApplication()->input->getCmd('layout');

		PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/styles.css' );
		PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/pago.css' );
		if($version->RELEASE >= 3){
			PagoHtml::add_css( JURI::base( true ) . '/components/com_pago/css/additional.css' );
			$joomlaVersion = 'joomla3';
		}

		if($layout === "modal")
		{
			echo '<div id="pago" class="pg-wrapper '.$joomlaVersion.'">';
			echo '<div class = "pg-modal-container clearfix"><div class="pg-main">';
		}
		else
		{
			echo '<div id="pago" class="pg-wrapper '.$joomlaVersion.'">';
			echo '<div class="pg-header ' . $class . '"><div id="pg-logo">'.
				'<a href="index.php?option=com_pago" title="paGO Commerce">paGO Commerce</a><span id="updates"><span id="HW_badge" class="HW_visible custom">New Updates</span></span></div>';
			echo '<div id="pago_toolbar" class = "pg-right">'.$top_menu.'</div>';
			echo '<div class="clear"></div></div>';
			echo PagoHtml::side_menu( $menu_items );
			echo '<div class = "pg-main-container clearfix"><div class="pg-main">';
		}
	}

	static private function pago_top_menu($top_menu){
		$result = '';
		if($top_menu){
			foreach ($top_menu as $v) {
				$result .= '<button onclick="Joomla.submitbutton(\''.$v['task'].'\')" class="'.$v['class'].'">'.$v['text'].'</button>';
			}
		}
		return $result;
	}

	static public function pago_pagination($pager) {
		
		if(!is_object($pager)) return;
			
		$pager = get_object_vars($pager);

		$version = new JVersion();

		if($version->RELEASE >= 3){
			$limitstart = $pager['limitstart'];
			$limit = $pager['limit'];
			$total = $pager['total'];
			$prefix = $pager['prefix'];
			$pagesStart = $pager['pagesStart'];
			$pagesStop = $pager['pagesStop'];
			$pagesCurrent = $pager['pagesCurrent'];
			$pagesTotal = $pager['pagesTotal'];
		}
		else{
			$limitstart = $pager['limitstart'];
			$limit = $pager['limit'];
			$total = $pager['total'];
			$prefix = $pager['prefix'];
			$pagesStart = $pager['pages.start'];
			$pagesStop = $pager['pages.stop'];
			$pagesCurrent = $pager['pages.current'];
			$pagesTotal = $pager['pages.total'];
		}

		$nextValue = intval($pagesCurrent)*$limit;
		$backValue = (intval($pagesCurrent)-2)*$limit;
		$lastValue = intval($pagesTotal-1)*$limit;

		$html = '<div class="pg-pagination">';
		$html .= '<ul class="pagination-list">';
		if($pagesCurrent == 1){
			$html .= '<li class="disabled"><a><i class="fa fa-angle-double-left"></i></a></li>';
			$html .= '<li class="prev disabled"><a></i>'.JText::_('PAGO_PAGINATION_PREV').'</a></li>';
		}
		else{
			$html .= '<li><a class="hasTooltip" title="" href="#" onclick="document.adminForm.limitstart.value=1; Joomla.submitform();return false;" data-original-title="Start"><i class="fa fa-angle-double-left"></i></a></li>';
			$html .= '<li class = "prev"><a class="hasTooltip" title="" href="#" onclick="document.adminForm.limitstart.value='.$backValue.'; Joomla.submitform();return false;" data-original-title="Previous">'.JText::_('PAGO_PAGINATION_PREV').'</a></li>';
		}
		$count = $pagesCurrent == 1 ? $pagesCurrent : $pagesCurrent-2;
		$count = $pagesCurrent == 2 ? $pagesCurrent - 1 : $count;

		$end = $count + 4;

		if($pagesCurrent + 2 >= $pagesTotal){
			$count = $pagesTotal-4;
			$end = $pagesTotal;
		}

		if($pagesTotal <= 5){
			$count = 1;
		}

		if($count >= 2){
			$v = ($count-2)*$limit;
			$html .= '<li class="points"><a class="fa fa-ellipsis-h" href="#" onclick="document.adminForm.limitstart.value='.$v.'; Joomla.submitform();return false;"></a></li>';
		}
		for($i=$count; $i<=$end; $i++){
			if($i == $pagesCurrent){
				$html .= '<li class="active hidden-phone"><a>'.$i.'</a></li>';
			}
			elseif ($i==1) {
				$html .= '<li class="hidden-phone"><a href="#" onclick="document.adminForm.limitstart.value=1; Joomla.submitform();return false;">'.$i.'</a></li>';
			}
			else{
				$v = ($i-1)*$limit;
				$html .= '<li class="hidden-phone"><a href="#" onclick="document.adminForm.limitstart.value='.$v.'; Joomla.submitform();return false;">'.$i.'</a></li>';
			}
		}

		if($pagesTotal > $count+4){
			$v = ($count+4)*$limit;
			$html .= '<li class="points"><a class="fa fa-ellipsis-h" href="#" onclick="document.adminForm.limitstart.value='.$v.'; Joomla.submitform();return false;"></a></li>';
		}


		if($pagesCurrent == $pagesStop){
			$html .= '<li class="disabled next"><a>'.JText::_('PAGO_PAGINATION_NEXT').'</a></li>';
			$html .= '<li class="disabled"><a><i class="fa fa-angle-double-right"></i></a></li>';
		}
		else{
			$html .= '<li class = "next"><a class="hasTooltip" title="" href="#" onclick="document.adminForm.limitstart.value='.$nextValue.'; Joomla.submitform();return false;" data-original-title="Next">'.JText::_('PAGO_PAGINATION_NEXT').'</a></li>';
			$html .= '<li><a class="hasTooltip" title="" href="#" onclick="document.adminForm.limitstart.value='.$lastValue.'; Joomla.submitform();return false;" data-original-title="End"><i class="fa fa-angle-double-right"></i></a></li>';
		}
		$html .= '</ul>';
		$html .= '<input type="hidden" name="limitstart" value="'.$limitstart.'">';
		$html .= '</div>';
		if($pagesTotal > 1){
			echo $html;
		}
		else{
			echo "";
		}
	}

	static public function pago_limitBox($pager){
		
		if(!is_object($pager)) return;
		
		$app = JFactory::getApplication();
		$limits = array();
		$total = 0;
		$prefix = '';

		// Make the option list.
		for ($i = 5; $i <= 30; $i += 5)
		{
			$limits[] = JHtml::_('select.option', "$i");
		}

		$limits[] = JHtml::_('select.option', '50', JText::_('J50'));
		$limits[] = JHtml::_('select.option', '100', JText::_('J100'));
		$limits[] = JHtml::_('select.option', '50000', JText::_('JALL'));

		$selected = $pager->limit;

		$version = new JVersion();

		if($version->RELEASE >= 3){
			$total = $pager->pagesTotal;
			$prefix = $pager->prefix;
		}
		else{
			$pager = get_object_vars($pager);
			$total = $pager['pages.total'];
			$prefix = $pager['prefix'];
		}


		// Build the select list.
		if ($app->isAdmin())
		{
			$html = JHtml::_(
				'select.genericlist',
				$limits,
				$prefix . 'limit',
				'class="inputbox input-mini" size="1" onchange="Joomla.submitform();"',
				'value',
				'text',
				$selected
			);
		}
		else
		{
			$html = JHtml::_(
				'select.genericlist',
				$limits,
				$prefix . 'limit',
				'class="inputbox input-mini" size="1" onchange="this.form.submit()"',
				'value',
				'text',
				$selected
			);
		}

		return $html;
	}

	static public function pago_bottom(){
		echo '</div></div>
		<div class="clear"></div>
		</div>';

		echo "<script type='text/javascript' data-cfasync='false'>window.purechatApi = { l: [], t: [], on: function () { this.l.push(arguments); } }; (function () { var done = false; var script = document.createElement('script'); script.async = true; script.type = 'text/javascript'; script.src = 'https://app.purechat.com/VisitorWidget/WidgetScript'; document.getElementsByTagName('HEAD').item(0).appendChild(script); script.onreadystatechange = script.onload = function (e) { if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) { var w = new PCWidget({c: 'ae418531-683c-4403-8b5e-cc8727f92340', f: true }); done = true; } }; })();</script>";
		echo '<script>
				  var HW_config = {
				    selector: "#updates", // where to inject the badge
				    account: "LJ4X2J" // your account ID
				  };
				  
				  window.setInterval( function () {
				  	
				  	var wrapper = document.getElementById( "HW_badge_cont" );
				  	
				  	if ( !wrapper )
				  		return;
				  	
				  	var n = wrapper.children[0].innerText;
				  	
				  	document.getElementById( "HW_badge" ).style.display = ( n != "" ) ? "block" : "none";
				  	
				  }, 100);
				  
				</script>
				<script async src="//cdn.headwayapp.co/widget.js"></script>';
		/*
		*/
	}

	static public function pago_truncate_description(){
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration("
			jQuery(document).ready(function() {
				jQuery('#pago td.pg-description .pg-description-read-more').click(function() {
					jQuery(this).parents('td.pg-description').find('.pg-long-description, .pg-short-description-ellipsis').toggle();
				});
			});
		");
	}

	static public function url_exists( $url ){
		 $url = "http://" . $_SERVER['HTTP_HOST'] . $url;
		 $file_headers = @get_headers($url);
		 if($file_headers[0] == 'HTTP/1.1 404 Not Found')
		 	return 0;
		 else
		 	return 1;
	}

	static public function getMailTemplateHints($template_type){
		$emailVar = '';

		if ($template_type == 'email_invoice'){
			$emailVar = JText::_('PAGO_EMAIL_INVOICE_HINTS');
		}

		if ($template_type == 'fraud_order_email'){
			$emailVar = JText::_('PAGO_EMAIL_FRAUD_NOTIFACTION_MAIL_HINTS');
		}

		if ($template_type == 'email_update_order_status'){
			$emailVar = JText::_('PAGO_EMAIL_UPDATE_ORDER_STATUS_HINTS');
		}

		return $emailVar;
	}

	static public function getCustomTemplateHints($template_type){
		$templatelVar = '';

		if ($template_type == 'order_receipt'){
			$templatelVar = JText::_('PAGO_ORDER_RECEIPT_TEMPLATE_HINTS');
		}

		return $templatelVar;
	}

	static function addGlobalConfigVariablesInJs(){
		$doc = JFactory::getDocument();
		$config = Pago::get_instance('config')->get('global');
		$currency_sym_position = $config->get('general.currency_symbol_display');
		$script ="
			window.CURRENCY_SYMBOL = '".CURRENCY_SYMBOL."';
			window.CURRENCY_CODE = '".CURRENCY_CODE."';
			window.CURRENCY_SYMBOL_POSITION = '".$currency_sym_position."';
		";
		$doc->addScriptDeclaration($script);
	}

	static function truncateByWord($text, $length = 0, $ending = '...', $exact = false, $considerHtml = true) {
		if($length == 0){
			return $text;
		}

		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
						unset($open_tags[$pos]);
						}
					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}

	public static function loadChosen($css = true){
		if(self::$loadFrontendChosen == false){
			$config = Pago::get_instance('config')->get();
			$app = JFactory::getApplication(0);
			$pago_theme   = $config->get( 'template.pago_theme', 'default' );
			$theme = $pago_theme;
			if ( $app->isAdmin() ) {
				$db = JFactory::getDBO();
				$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
				$db->setQuery($query);
				$joomla_theme = $db->loadResult();
			} else {
				$joomla_theme = $app->getTemplate();
			}

			$paths = array(
				'component' => array(
					'full' => JPATH_SITE .'/components/com_pago/templates/',
					'url' => JURI::base(true) . '/components/com_pago/templates/'
				),
				'joverride' => array(
					'full' => JPATH_SITE . '/templates/' . $joomla_theme . '/html/com_pago/',
					'url' => JURI::base(true) . '/templates/' . $joomla_theme .'/html/com_pago/'
				)
			);

			foreach ( $paths as $path ) {
				$tpath = $path['full'] . $theme . '/';
				$include_path = $path['url'] . $theme . '/';
				if ( file_exists( $tpath ) && is_dir( $tpath ) )
				{
					if ( file_exists( $tpath .'/js/chosen/chosen.jquery.js') && is_dir( $tpath ) ) {
						$return_paths['chosenjs'] = $include_path .'js/chosen/chosen.jquery.js';
					}
					else
					{
						$return_paths['chosenjs'] = $path['url'] .'default/js/chosen/chosen.jquery.js';
					}

					if(file_exists( $tpath .'/js/chosen/chosen.css') && is_dir( $tpath ))
					{
						$return_paths['chosencss'] = $include_path .'js/chosen/chosen.css';
					}
					else
					{
						$return_paths['chosencss'] = $path['url'] .'default/js/chosen/chosen.css';
					}

					if(file_exists( $tpath .'/js/chosen/chosen-dark.css') && is_dir( $tpath ))
					{
						$return_paths['chosendarkcss'] = $include_path .'js/chosen/chosen-dark.css';
					}
					else
					{
						$return_paths['chosendarkcss'] = $path['url'] .'default/js/chosen/chosen-dark.css';
					}
				}
			}
			$_root = JURI::root( true );
			PagoHtml::add_js( $return_paths['chosenjs']);
			if($css = true){
				$temStyle = $config->get( 'template.pago_theme_style', 0 );
				if($temStyle == 0){
					PagoHtml::add_css( $return_paths['chosencss'] );
				}elseif($temStyle == 1){
					PagoHtml::add_css($return_paths['chosendarkcss']);
				}

			}
		}
		self::$loadFrontendChosen = true;
	}

	public static function loadFonts(){
		if(self::$loadFont == false){
			$doc = JFactory::getDocument();
			$config = Pago::get_instance('config')->get();
			if ($config->get( 'template.pago_fonts')){
				$doc->addStyleSheet( '//fonts.googleapis.com/css?family=PT+Sans+Caption:400,700' );
				$font_settings = '.pg-main-container *:not(.fa) {
					font-family: "PT Sans Caption", sans-serif !important;
				}';
				$doc->addStyleDeclaration($font_settings);
			}
		}
		self::$loadFont = true;
	}

	public static function loadJquery(){
		if(self::$loadJqueryFiles == false){
			$version = new JVersion();
			if($version->RELEASE <= 3){
				$doc = JFactory::getDocument();
				$config = Pago::get_instance('config')->get();
				$pago_theme   = $config->get( 'template.pago_theme', 'default' );
				$tpath = JURI::base(true) . '/components/com_pago/templates/' . $pago_theme . '/';
    				if ( file_exists( $tpath ) && is_dir( $tpath ) )
    				{
     					$doc->addScript( JURI::root( true ) . '/components/com_pago/templates/'.$pago_theme.'/js/jquery.js' );
    				}
    				else
   				{
     					$doc->addScript( JURI::root( true ) . '/components/com_pago/templates/default/js/jquery.js' );
    				}
			}else{
				JHtml::_('jquery.framework');
			}
		}
		self::$loadJqueryFiles = true;
	}

	public static function loadBootstrapCss(){

		if(self::$loadBootstrapCssFile == false){

			$app = JFactory::getApplication(0);
			$config = Pago::get_instance('config')->get();
			$pago_theme   = $config->get( 'template.pago_theme', 'default' );
			$theme = $pago_theme;

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
					'full' => JPATH_SITE .'/components/com_pago/templates/',
					'url' => JURI::base(true) . '/components/com_pago/templates/'
				),
				'joverride' => array(
					'full' => JPATH_SITE . '/templates/' . $joomla_theme . '/html/com_pago/',
					'url' => JURI::base(true) . '/templates/' . $joomla_theme .'/html/com_pago/'
				)

			);
			foreach ( $paths as $path ) {
				$tpath = $path['full'] . $theme . '/';
				$include_path = $path['url'] . $theme . '/';
				if ( file_exists( $tpath ) && is_dir( $tpath ) )
				{
					if ( file_exists( $tpath .'/css/bootstrap.min.css') && is_dir( $tpath ) ) {
						$return_paths['bootstrapcss'] = $include_path .'css/bootstrap.min.css';
					}
					else
					{
						$return_paths['bootstrapcss'] = $path['url'].'default/css/bootstrap.min.css';
					}
				}
			}
			$doc = JFactory::getDocument();

			PagoHtml::add_css( $return_paths['bootstrapcss'] );
		}

		self::$loadBootstrapCssFile = true;

	}

	public static function loadBootstrapJs(){
		if(self::$loadBootstrapJsFile == false){

			$config = Pago::get_instance('config')->get();
			$doc = JFactory::getDocument();

			$app = JFactory::getApplication(0);
			$pago_theme   = $config->get( 'template.pago_theme', 'default' );
			$theme = $pago_theme;

			if ( $app->isAdmin() ) {
				$db = JFactory::getDBO();
				$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
				$db->setQuery($query);
				$joomla_theme = $db->loadResult();
			} else {
				$joomla_theme = $app->getTemplate();
			}

			$paths = array(
			'component' => array(
			 'full' => JPATH_SITE .'/components/com_pago/templates/',
			 'url' => JURI::base(true) . '/components/com_pago/templates/'
			),
			'joverride' => array(
			 'full' => JPATH_SITE . '/templates/' . $joomla_theme . '/html/com_pago/',
			 'url' => JURI::base(true) . '/templates/' . $joomla_theme .'/html/com_pago/'
			)

		   );

			foreach ( $paths as $path )
			{
				$tpath = $path['full'] . $theme . '/';
				$include_path = $path['url'] . $theme . '/';
				if ( file_exists( $tpath ) && is_dir( $tpath ) )
				{
					if ( file_exists( $tpath .'/js/bootstrap.min.js') && is_dir( $tpath ) )
					{
						$return_paths['bootstrpjs'] = $include_path .'js/bootstrap.min.js';
					}
					else
					{
						$return_paths['bootstrpjs'] = $path['url'].'default/js/bootstrap.min.js';
					}
				}
			}
			PagoHtml::add_js( $return_paths['bootstrpjs'] );
		}
		self::$loadBootstrapJsFile = true;
	}

	public static function loadUploadifive(){
		if(self::$loadUploadifiveFile == false){

			PagoHtml::add_js( JURI::root( true ) .  '/administrator/components/com_pago/javascript/uploadifive/jquery.uploadifive.js' );
			PagoHtml::add_css( JURI::root( true ) .  '/administrator/components/com_pago/javascript/uploadifive/uploadifive.css' );
		}
		self::$loadUploadifiveFile = true;
	}

	public static function loadBootstrapModalForBackend(){
		$version = new JVersion();
		if($version->RELEASE <= 3){
			PagoHtml::add_css( JURI::root( true )
				. '/administrator/components/com_pago/css/bootstrap.css', true );
			PagoHtml::add_css( JURI::root( true )
				. '/administrator/components/com_pago/css/bootstrap-theme.css', true );
			PagoHtml::add_js( JURI::root( true )
			 	.'/administrator/components/com_pago/javascript/'
			 	.'bootstrap.js', true );
		}
	}
}
class TruncateHTML {

    public static $charCount = 0;
    public static $wordCount = 0;
    public static $limit;
    public static $startNode;
    public static $ellipsis;
    public static $foundBreakpoint = false;

    public static function init($html, $limit, $ellipsis) {

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        self::$startNode = $dom->getElementsByTagName("body")->item(0); //the body tag node, our html fragment is automatically wrapped in a <html><body> etc... skeleton which we will strip later
        self::$limit = $limit;
        self::$ellipsis = $ellipsis;
        self::$charCount = 0;
        self::$wordCount = 0;
        self::$foundBreakpoint = false;

        return $dom;
    }

    public function truncateChars($html, $limit, $ellipsis = '...') {

        if($limit <= 0 || $limit >= strlen(strip_tags($html)))
            return $html;

        $dom = self::init($html, $limit, $ellipsis);

        self::domNodeTruncateChars(self::$startNode); //pass the body node on to be processed

        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML()); //hack to remove the html skeleton that is added, unfortunately this can't be avoided unless php > 5.3
    }

    public static function truncateWords($html, $limit, $ellipsis = '...') {


        if($limit <= 0 || $limit >= self::countWords(strip_tags($html))){
            return $html;
        }
        $html = '<?xml encoding="UTF-8">'.$html;
        $dom = self::init($html, $limit, $ellipsis);

        self::domNodeTruncateWords(self::$startNode); //pass the body node on to be processed
        $new_html = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML()); //hack to remove the html skeleton that is added, unfortunately this can't be avoided unless php > 5.3
        $new_html = str_replace('<?xml encoding="UTF-8">', '', $new_html);

        return $new_html;
    }

    private static function domNodeTruncateChars(DOMNode $domNode) {

        foreach ($domNode->childNodes as $node) {

            if(self::$foundBreakpoint == true) return;

            if($node->hasChildNodes()) {
                self::domNodeTruncateChars($node);
            } else {
                if((self::$charCount + strlen($node->nodeValue)) >= self::$limit) {
                    //we have found our end point
                    $node->nodeValue = substr($node->nodeValue, 0, self::$limit - self::$charCount);
                    self::removeProceedingNodes($node);
                    self::insertEllipsis($node);
                    self::$foundBreakpoint = true;
                    return;
                } else {
                    self::$charCount += strlen($node->nodeValue);
                }
            }
        }
    }

    private static function domNodeTruncateWords(DOMNode $domNode) {

        foreach ($domNode->childNodes as $node) {

            if(self::$foundBreakpoint == true) return;

            if($node->hasChildNodes()) {
                self::domNodeTruncateWords($node);
            } else {
                $curWordCount = self::countWords($node->nodeValue);

                if((self::$wordCount + $curWordCount) >= self::$limit) {
                    //we have found our end point
                    if($curWordCount > 1 && (self::$limit - self::$wordCount) < $curWordCount) {
                        $words = preg_split("/[\n\r\t ]+/", $node->nodeValue, (self::$limit - self::$wordCount) + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
                        end($words);
                        $last_word = prev($words);
                        $node->nodeValue = substr($node->nodeValue, 0, $last_word[1] + strlen($last_word[0]));
                    }

                    self::removeProceedingNodes($node);
                    self::insertEllipsis($node);
                    self::$foundBreakpoint = true;
                    return;
                } else {
                    self::$wordCount += $curWordCount;
                }
            }
        }
    }

    private static function removeProceedingNodes(DOMNode $domNode) {
        $nextNode = $domNode->nextSibling;

        if($nextNode !== NULL) {
            self::removeProceedingNodes($nextNode);
            $domNode->parentNode->removeChild($nextNode);
        } else {
            //scan upwards till we find a sibling
            $curNode = $domNode->parentNode;
            while($curNode !== self::$startNode) {
                if($curNode->nextSibling !== NULL) {
                    $curNode = $curNode->nextSibling;
                    self::removeProceedingNodes($curNode);
                    $curNode->parentNode->removeChild($curNode);
                    break;
                }
                $curNode = $curNode->parentNode;
            }
        }
    }

    private static function insertEllipsis(DOMNode $domNode) {
        $avoid = array('a', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5'); //html tags to avoid appending the ellipsis to

        if( in_array($domNode->parentNode->nodeName, $avoid) && ($domNode->parentNode->parentNode !== NULL || $domNode->parentNode->parentNode !== self::$startNode)) {
            // Append as text node to parent instead
            $textNode = new DOMText(self::$ellipsis);

            if($domNode->parentNode->parentNode->nextSibling)
                $domNode->parentNode->parentNode->insertBefore($textNode, $domNode->parentNode->parentNode->nextSibling);
            else
                $domNode->parentNode->parentNode->appendChild($textNode);
        } else {
            // Append to current node
            $domNode->nodeValue = rtrim($domNode->nodeValue).self::$ellipsis;
        }
    }

    static private function countWords($text) {
        $words = preg_split("/[\n\r\t ]+/", $text, -1, PREG_SPLIT_NO_EMPTY);
        return count($words);
    }
}