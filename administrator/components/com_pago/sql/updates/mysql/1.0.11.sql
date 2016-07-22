UPDATE `#__pago_view_templates` SET `pgtemplate_body` = '<p style="padding-bottom:15px;border-bottom:1px solid #a3a19c;font-weight:bold;margin-top:50px;">{order_receipt_lbl}</p>\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-collapse: separate ; background-color: #ffffff"><thead>\r\n<tr>\r\n<th colspan="2" style="color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{order_information_lbl}\r\n</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{orderid_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{orderid}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_status_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_cadte}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{ordertotal_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{ordertotal}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_tax_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{ordertax}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_ship_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_shipping}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_disc_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_discount}{ordercurrency}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_coupon_disc_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_coupon_disc}{ordercurrency}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n<div class="table-responsive"  style="margin-top:50px;">\r\n<table class="table" style="border-collapse: separate ;max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-spacing: 0px; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style="line-height:46px;font-size: 20px;color:white;background-color: #494646;border-bottom: 0;">{customer_information_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td>\r\n<table class="table" style="border-collapse: separate;">\r\n<tbody>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{order_billing_add_lbl}</td>\r\n</tr>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{billingaddress}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td style="border-left: 1px solid #a3a19c;">\r\n<table class="table" style="border-collapse: separate ;border:none;">\r\n<tbody>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{order_mailing_add_lbl}</td>\r\n</tr>\r\n<tr colspan="2" align="center">\r\n<td style="border: 0;">{mailingaddress}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="border-collapse: separate ;max-width: 90%;margin:0 auto;border: 1px solid #a3a19c; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style="	color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{order_items_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;	width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;border-left:none;">{item_name_lbl}</td>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;	width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;">{item_quantity_lbl}</td>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;	width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;">{item_sku_lbl}</td>\r\n<td style="line-height:46px;border-bottom:1px solid #a3a19c;	width:25%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;background-color: #f3f3f3;">{item_price_lbl}</td>\r\n</tr>{items_det}\r\n<tr>\r\n<td style="border-left:0px;padding: 0px;">\r\n<table class="table" style="margin-left:50%;width:50%; border-collapse: separate ;margin-bottom: 0px;">\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;">{order_subtotal_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;border-left: 1px solid #a3a19c;">{order_subtotal}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;">{order_ship_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;border-left: 1px solid #a3a19c;">{order_shipping}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;">{ordertotal_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border:none;background-color: white;border-left: 1px solid #a3a19c;">{ordertotal}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-collapse: separate ; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style="color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{payment_information_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{payment_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{paymentmethod}</td>\r\n</tr>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_payment_msg_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_payment_msg}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n<div class="table-responsive" style="margin-top:50px;">\r\n<table class="table" style="max-width: 90%;margin:0 auto;border: 1px solid #a3a19c;border-collapse: separate ; background-color: #ffffff">\r\n<thead>\r\n<tr>\r\n<th colspan="2" style="color:white;background-color: #494646;border-bottom: 0;line-height:46px;font-size: 20px;">{mailing_information_lbl}</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;background-color: #f3f3f3;">{order_shipmethod_lbl}</td>\r\n<td style="width:50%;display:inline-block;box-sizing :border-box;border-top: 1px solid #a3a19c;border-left: 1px solid #a3a19c;">{order_shipping_method}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>' WHERE `pgtemplate_id` = 1;