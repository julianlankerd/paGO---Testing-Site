<?php
/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class TableItem extends JTable
{
	public $id                = null;
	public $name              = null;
	public $type              = null;
	public $created           = null;
	public $modified          = null;
	public $published         = 1;
	public $alias             = null;
	public $primary_category  = null;
	public $price             = 0;
	public $price_type        = null;
	public $subscr_start_num  = null;
	public $subscr_start_type = null;
	public $subscr_price      = null;
	public $subscr_shipping   = null;
	public $sub_recur		  = null;
	public $featured          = 0;
	public $description       = null;
	public $sku               = null;
	public $currency          = null;
	public $qty               = null;
	public $qty_limit         = null;
	public $tax_exempt        = 0;
	public $content           = null;
	public $height            = 0;
	public $width             = 0;
	public $length            = 0;
	public $weight            = 0;
	public $visibility        = 0;
	public $expiry_date       = null;
	public $availibility_date = null;
	public $jump_to_checkout  = null;
	public $free_shipping = 0;
	public $pgtax_class_id = 0;
	public $availibility_options = 0;
	public $disc_start_date  = null;
	public $disc_end_date  = null;
	public $discount_amount  = null;
	public $apply_discount            = 0;
	public $discount_type            = 0;
	public $item_custom_layout = null;
	public $related_category = null;
	public $show_new = null;
	public $until_new_date = null;
	public $view_settings_product_title;
	public $view_settings_product_image;
	public $view_settings_featured_badge;
	public $view_settings_quantity_in_stock;
	public $view_settings_short_desc;
	public $view_settings_short_desc_limit;
	public $view_settings_desc;
	public $view_settings_desc_limit;
	public $view_settings_sku;
	public $view_settings_price;
	public $view_settings_discounted_price;
	public $view_settings_attribute;
	public $view_settings_media;
	public $view_settings_downloads;
	public $view_settings_rating;
	public $view_settings_category;
	public $view_settings_add_to_cart;
	public $view_settings_add_to_cart_qty;
	public $view_view_settings_product_review;
	public $view_view_settings_related_products;
	public $view_settings_related_num_of_products;
	public $view_settings_related_title;
	public $view_settings_related_category;
	public $view_settings_related_image;
	public $view_settings_related_short_text;
	public $view_settings_image_settings_show;
	public $view_settings_image_settings;
	public $view_settings_fb;
	public $view_settings_tw;
	public $view_settings_pinterest;
	public $view_settings_google_plus;
	public $item_custom_layout_inherit;
	public $view_settings_title_limit_inherit;
	public $view_settings_title_limit;
	public $view_settings_product_image_zoom;

	/**
	* Constructor
	*
	* @param object Database connector object
	*/
	function __construct( $db )
	{
		parent::__construct( '#__pago_items', 'id', $db );
	}

	/**
	 * Overloaded check function
	 */
	function check()
	{
		if ( trim( $this->name ) == '' ) {
			$this->setError( JText::_('PAGO_ITEM_ERROR_NO_NAME') );
			return false;
		}

		if ( empty( $this->alias ) ) {
			$this->alias = $this->name;
		}

		$this->alias = JFilterOutput::stringURLSafe( $this->alias );

		if ( trim( str_replace( '-', '', $this->alias ) ) == '' ) {
			$datenow = JFactory::getDate();
			$this->alias = $datenow->Format( "Y-m-d-H-i-s" );
		}

		return true;
	}
}
