<?php defined( '_JEXEC' ) or die();

/**
 * @package paGO Commerce
 * @author 'corePHP', LLC
 * @copyright (C) 2013 - 'corePHP' LLC and paGO Commerce
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

print_r( $manifest ); 

/*
//below is the contents of the manifest, this can be used to create a html template that will be mailed to the user on an order status change

$manifest = array(
	[id] => 1
    [mode] => 1
    [order_id] => 958
    [order_status] => K PENDING
    [order_token] => 
    [description] => 
    [session_id] => 
    [customer_ip] => 
    [customer_id] => 42
    [currency] => USD
    [unit_of_measure] => LB
    [subtotal] => 5.99000
    [refund_total] => 0.00000
    [tax_total] => 0.60
    [total] => 16.59000
    [store_credits] => 0
    [store_credit] => 0
    [promotional_codes] => Array
        (
        )

    [promo_code] => 
    [promo_discount] => 0
    [continue_shopping_url] => http://localhost/pagodev/v2/?option=com_pago
    [edit_cart_url] => http://localhost/pagodev/v2/?option=com_pago&view=cart
    [merchant_calculations_url] => http://localhost/pagodev/v2/?option=com_pago&view=ipn&format=ipn&gateway=0
    [notify_url] => http://localhost/pagodev/v2/?option=com_pago&view=ipn&format=ipn&gateway=0
    [cc] => Array
        (
            [cardNumber] => 
            [expirationDate] => 1969-12
            [cv2] => 
        )

    [shipments] => Array
        (
            [0] => Array
                (
                    [id] => 1
                    [carrier] =>  - Ground
                    [method] =>  - Ground
                    [shipping_total] => 10.00
                    [items] => Array
                        (
                            [0] => Array
                                (
                                    [id] => 17286
                                    [name] => Pago Magazine Subscription
                                    [description] => <p>Monthly Subscription Pago Magazine</p>
                                    [price] => 5.99000
                                    [tax_exempt] => 0
                                    [quantity] => 1
                                    [weight] => 0.1000
                                    [height] => 0.0000
                                    [width] => 0.0000
                                    [length] => 0.0000
                                    [tangible] => 1
									
									//*****note that this will be empty if there is no subscription*****
                                    [subscription] => Array
                                        (
                                            [billing_period] => YEARLY
                                            [initial_price] => 5.99000
                                            [start_date] => 2012-11-15T00:00:00Z
                                            [shipping_cost] => 2.00000
                                            [price_total] => 0.00000
                                        )

                                )

                        )

                    [addresses] => Array
                        (
                            [ship_from] => Array
                                (
                                    [city] => Beverly Hills
                                    [region] => California
                                    [country_code] => US
                                    [postal_code] => 90210
                                )

                            [billing] => Array
                                (
                                    [first_name] => adam
                                    [middle_name] => 
                                    [last_name] => Docherty
                                    [company] => corephp
                                    [address] => 1 road st
                                    [address2] => 
                                    [city] => Beverly Hills
                                    [region] => California
                                    [region_2_code] => 
                                    [postal_code] => 90210
                                    [country] => US
                                    [country_2_code] => US
                                    [email] => 
                                    [phone] => (234) 235 5678
                                    [phone2] => 
                                )

                            [mailing] => Array
                                (
                                    [first_name] => adam
                                    [middle_name] => 
                                    [last_name] => Docherty
                                    [company] => corephp
                                    [address] => 1 road st
                                    [address2] => 
                                    [city] => Beverly Hills
                                    [region] => California
                                    [region_2_code] => 
                                    [postal_code] => 90210
                                    [country] => US
                                    [country_2_code] => US
                                    [email] => 
                                    [phone] => (234) 235 5678
                                    [phone2] => 
                                )

                        )

                )

        )*/
?>