<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class PagoViewDefault extends JViewLegacy
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 *
	 * @param	string template	Template file name
	 **/
	function display( $tpl = null )
	{

		$task = 'task_' . JFactory::getApplication()->input->getCmd('task');

		if( method_exists( $this, $task ) ){
			$msg = JText::_( $this->$task() );
			$link = 'index.php?option=com_pago';
			JFactory::getApplication()->controller->setRedirect( $link, $msg );
		}

		/*$cmp_xml = qp( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS .'pago.xml' );

		$version = $cmp_xml->find('install')->find('version')->text();	*/

		JHTML::_('behavior.tooltip', '.hasTip');
		jimport('joomla.html.pane');

		//$pane = JPane::getInstance('sliders');

		$cmp_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_pago' . DS;

		Pago::load_helpers( 'pagoparameter' );

		$params = new PagoParameter( 'store_config', $cmp_path . 'config.xml' );

		JForm::addfieldpath( array(
			$cmp_path . DS . 'elements'
			)
		);

		if( !$store_params = $params->render( 'general' ) ){
			$store_params = 'PAGOSTOREPARAMSNOTFOUND';
		}

		if( !$email_params = $params->render( 'email' ) ){
			$email_params = 'PAGOSTOREEMAILPARAMSNOTFOUND';
		}

		// Lets grab the user
		$user = JFactory::getUser();

		$dashboard               = $this->getModel( 'Dashboard','PagoModel' );
		$orders                  = $dashboard->get_recent_orders();
		$items                   = $dashboard->best_selling_items();
		$abandoned               = $this->ticker_format( $dashboard->abandoned_carts() );
		$abandoned_percent       = $dashboard->abandoned_carts_percent();
		$orders                  = $this->format_orders( $orders );
		$average_sale            = $this->ticker_format( Pago::get_instance( 'price' )->format( $dashboard->average_sale() ) );
		$average_percent         = $dashboard->average_percent();
		$new_customers           = $this->ticker_format( $dashboard->new_customers() );
		$new_customers_percent   = $dashboard->new_customers_percent();

		$total_sales = $this->ticker_format( Pago::get_instance( 'price' )->format( $dashboard->total_sales() ) );
		$total_sales_percent = $dashboard->total_sales_percent();

		$this->assignRef( 'total_sales',             $total_sales );
		$this->assignRef( 'total_sales_percent',     $total_sales_percent );
		$this->assignRef( 'average_sale',            $average_sale );
		$this->assignRef( 'average_percent',         $average_percent );
		$this->assignRef( 'abandoned',               $abandoned );
		$this->assignRef( 'abandoned_percent',       $abandoned_percent );
		$this->assignRef( 'new_customers',           $new_customers );
		$this->assignRef( 'new_customers_percent',   $new_customers_percent );
		$this->assignRef( 'items',                   $items );
		$this->assignRef( 'orders',                  $orders );
		//$this->assignRef( 'pane',                    $pane );
		$this->assignRef( 'store_params',            $store_params );
		$this->assignRef( 'email_params',            $email_params );
		$this->assignRef( 'user',                    $user );

		$dashboard->removeExpiredData();
		
		parent::display( $tpl );
	}
	private function ticker_format( $number ) {
		
		$numbers = str_split( $number );

		$formatted = '<div class="pg-ticker">';

		foreach($numbers as $tmp){
			if (preg_match('/^[a-z0-9]+$/i', $tmp)){
				$formatted .= '<span><span class="pg-bevel"></span>'.$tmp.'</span>';
			} else {
				$formatted .= $tmp;
			}
		}

		$formatted .= '</div>';

		return $formatted;
	}

	function format_orders($orders){
		$this->order_status_options = Pago::get_instance('config')->get_order_status_options();

		if( is_array( $orders ) ) {

			foreach( $orders as $k => $order ) {

				if( !$order->order_status ){
					$order->order_status = 'P';
				}

				$order->order_status = $this->order_status_options[ $order->order_status ];
			}
		}
		return $orders;
	}

	function task_store_params()
	{
		$component = 'com_pago';
		$post = JFactory::getApplication()->input->getArray($_POST);
		$table =& JTable::getInstance('component');

		if (!$table->loadByOption( $component ))
		{
			JError::raiseWarning( 500, 'Not a valid component' );
			return false;
		}

		$post = JFactory::getApplication()->input->getArray($_POST);
		$post['option'] = $component;
		$table->bind( $post );

		// pre-save checks
		if (!$table->check()) {
			JError::raiseWarning( 500, $table->getError() );
			return false;
		}

		// save the changes
		if (!$table->store()) {
			JError::raiseWarning( 500, $table->getError() );
			return false;
		}

		return 'PAGOSAVEPARAMSSUCCESS';
	}

	function setToolBar($version)
	{
		$title = JText::_( 'PAGO_NAME' ) . ' v' . $version;
		JToolBarHelper::title( $title, 'generic.png' );
	}

	function addIcon( $image , $url , $text , $newWindow = false )
	{
		$lang		=& JFactory::getLanguage();

		$newWindow	= ( $newWindow ) ? ' target="_blank"' : '';
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $url; ?>"<?php echo $newWindow; ?>>
					<?php echo JHTML::_('image',
						'administrator/templates/khepri/images/header/' . $image ,
						NULL,
						NULL,
						$text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}
}
?>