UPDATE `#__pago_mail_templates` SET `pgemail_body` = '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">INVOICE DETAILS</div>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Items</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{order_item_ship_method_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_item_ship_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" > </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Payment Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{payment_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_payment_msg_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Mailing Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_shipmethod_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">{order_customernote_lbl}</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>' WHERE `pgemail_id` = 1;
UPDATE `#__pago_mail_templates` SET `pgemail_body` = '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">INVOICE DETAILS</div>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Items</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{order_item_ship_method_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_item_ship_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" > </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Payment Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{payment_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_payment_msg_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Mailing Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_shipmethod_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">{order_customernote_lbl}</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>' WHERE `pgemail_id` = 2;
UPDATE `#__pago_mail_templates` SET `pgemail_body` = '<div style="font-family:arial; font-size: 20px; margin-bottom: 20px;">Order Status Info</div>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{orderid_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_status_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_cdate_lbl}</strong>:</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{ordertotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_tax_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_ship_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_detail_link_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_detail_link}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{tracking_number_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{tracking_number}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Customer Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_billing_add_lbl}</strong></div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="50%">\r\n<div><strong>{order_mailing_add_lbl}</strong></div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Order Items</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{item_name_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_quantity_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_sku_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{item_price_lbl}</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{item_name}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_quantity}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_sku}</td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{item_price}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{order_item_ship_method_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_item_ship_method}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"><strong>{tracking_number_lbl}</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{tracking_number}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_subtotal_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{order_ship_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"> </td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%"><strong>{ordertotal_lbl} :</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="23%">{ordertotal}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Payment Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{payment_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_payment_msg_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">Mailing Information</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;" width="20%"><strong>{order_shipmethod_lbl}:</strong></td>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div style="font-family:arial; background:#494646; color: #fff; font-size: 14px; margin: 0; padding: 12px 10px; font-weight:bold">{order_customernote_lbl}</div>\r\n<table border="0" cellspacing="0" width="100%" style="border:1px solid #a3a19c; border-collapse: collapse;margin-bottom:20px;">\r\n<tbody>\r\n<tr>\r\n<td style="border:1px solid #a3a19c; padding: 12px 10px;">{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>' WHERE `pgemail_id` = 4;
UPDATE `#__pago_view_templates` SET `pgtemplate_body` = '<p><h2><strong>{order_receipt_lbl}</strong></h2></p>\r\n<div id="order_receipt_header">{order_information_lbl}</div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td width="20%">{orderid_lbl}:</td>\r\n<td>{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{order_status_lbl}:</td>\r\n<td>{orderstatus}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{order_cdate_lbl}:</td>\r\n<td>{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{ordertotal_lbl}:</td>\r\n<td>{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{order_tax_lbl}:</td>\r\n<td>{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{order_ship_lbl}:</td>\r\n<td>{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{order_disc_lbl}:</td>\r\n<td>{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%"><strong>{order_coupon_disc_lbl}:</strong></td>\r\n<td>{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div id="order_receipt_header">{customer_information_lbl}</div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td width="50%">\r\n<div>{order_billing_add_lbl}</div>\r\n<div>{billingaddress}</div>\r\n</td>\r\n<td width="50%">\r\n<div>{order_mailing_add_lbl}</div>\r\n<div>{mailingaddress}</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div id="order_receipt_header">{order_items_lbl}</div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td>{item_name_lbl}</td>\r\n<td width="23%">{item_quantity_lbl}</td>\r\n<td width="23%">{item_sku_lbl}</td>\r\n<td width="23%">{item_price_lbl}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div><!-- {item_loop_start} -->\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td>{item_name}</td>\r\n<td width="23%">{item_quantity}</td>\r\n<td width="23%">{item_sku}</td>\r\n<td width="23%">{item_price}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- {item_loop_end} --></div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td> </td>\r\n<td width="23%"> </td>\r\n<td width="23%">{order_subtotal_lbl} :</td>\r\n<td width="23%">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td> </td>\r\n<td width="23%"> </td>\r\n<td width="23%">{order_ship_lbl} :</td>\r\n<td width="23%">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td> </td>\r\n<td width="23%"> </td>\r\n<td width="23%">{ordertotal_lbl} :</td>\r\n<td width="23%">{ordertotal}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div id="order_receipt_payment_header">{payment_information_lbl}</div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td width="20%">{payment_lbl}:</td>\r\n<td>{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td width="20%">{order_payment_msg_lbl}:</td>\r\n<td>{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div id="order_receipt_mailing_header">{mailing_information_lbl}</div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td width="20%">{order_shipmethod_lbl}:</td>\r\n<td>{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div id="order_receipt_customernote_header">{order_customernote_lbl}</div>\r\n<table width="100%" border="0px">\r\n<tbody>\r\n<tr>\r\n<td>{order_customernote}</td>\r\n</tr>\r\n</tbody>\r\n</table>' WHERE `pgtemplate_id` = 1
