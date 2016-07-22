<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/kview.php';
/**
 * HTML View class for the Pago  component
 */
class PagoViewSearch extends PagoView
{
	function display( $tpl = null )
    {
        Pago::load_helpers( array( 'imagehandler' ) );
        //Pago::load_helpers( array( 'categories', 'module', 'imagehandler' ) );

        $document = JFactory::getDocument();
        $app      = JFactory::getApplication();
        $pathway  = $app->getPathWay();
        $user = JFactory::getUser();
        $config = Pago::get_instance( 'config' )->get();

        //Pago::load_helpers( array( 'imagehandler','attributes' ) );

        // Set view from template switcher
        $layout = $config->get( 'search_product_settings.search_custom_layout' );
        if($layout != "")
        {
            $this->set_theme($layout);
        }
        $this->set( 'search' , $layout );
	  $searchQuery = JFactory::getApplication()->input->get( 'search_query', array(), 'get', 'array' );
        $document->setTitle( JText::_( 'PAGO_SEARCH_NAV' )." - ".$searchQuery );
        $pathway->addItem( JText::_( 'PAGO_SEARCH_NAV' ) , false);

        
        if(!empty($searchQuery)){
            if(JFactory::getDBO()->name == 'mysql'){
                $searchQuery = mysql_real_escape_string($searchQuery);
            }
            else{
                $searchQuery = mysqli_real_escape_string(JFactory::getDBO()->getConnection(), $searchQuery);
            }
        }
        
        
        $items = false;
       // if($searchQuery){
            $searchModel = JModelLegacy::getInstance( 'Search', 'PagoModel' );
   
            $items = $searchModel->search( $searchQuery );
            $pagination = $searchModel->getPagination();
      //  }

        if(($config->get('search_product_settings.product_settings_short_desc') == 1) || ($config->get('search_product_settings.product_settings_desc') == 1) || ($config->get('search_product_settings.product_settings_product_title') == 1)){
            if($items){
                foreach ($items as $key => $item) {
                    if($config->get('search_product_settings.product_settings_short_desc') == 1){
                        $item->description = TruncateHTML::truncateWords( $item->description, $config->get('search_product_settings.product_settings_short_desc_limit'), '...');
                    }
                    if($config->get('search_product_settings.product_settings_desc') == 1){
                        $item->content = TruncateHTML::truncateWords( $item->content, $config->get('search_product_settings.product_settings_desc_limit'), '...');
                    }
                    if($config->get('search_product_settings.product_settings_product_title') == 1){
                        $item->name = TruncateHTML::truncateWords( $item->name, $config->get('search_product_settings.product_settings_product_title_limit'), '...');
                    }
                }
            }
        }

        $dispatcher = KDispatcher::getInstance();
        JPluginHelper::importPlugin( 'pago_gateway' );

        JLoader::register('NavigationHelper', JPATH_COMPONENT . '/helpers/navigation.php');
        $nav = new NavigationHelper;

        $this->assignRef( 'pagination',  $pagination );
        $this->assignRef('searchQuery', $searchQuery);
        $this->assignRef('nav', $nav);
        $this->assignRef( 'items', $items );
        $this->assignRef( 'user', $user );
        $this->assignRef( 'config', $config );

        parent::display($tpl);
    }
}
?>
