BX.ready(function() {

	var sku_props = BX.findChildren(BX('basket_items_list'), {tagName: 'li', className: 'sku_prop'}, true);
	if (!!sku_props && sku_props.length > 0)
	{
		for (i = 0; sku_props.length > i; i++)
		{
			BX.bind(sku_props[i], 'click', BX.delegate(function(e){ skuPropClickHandler(e);}, this));
		}
	}
});

function skuPropClickHandler(e)
{
	if (!e) e = window.event;
	var target = BX.proxy_context;

	if (!!target && target.hasAttribute('data-value-id'))
	{
		BX.showWait();

		var basketItemId = target.getAttribute('data-element'),
			property = target.getAttribute('data-property'),
			property_values = {},
			postData = {},
			action_var = BX('action_var').value;

		property_values[property] = target.getAttribute('data-value-id');

		// if already selected element is clicked
		if (BX.hasClass(target, 'bx_active'))
		{
			BX.closeWait();
			return;
		}

		// get other basket item props to get full unique set of props of the new product
		var all_sku_props = BX.findChildren(BX(basketItemId), {tagName: 'ul', className: 'sku_prop_list'}, true);
		if (!!all_sku_props && all_sku_props.length > 0)
		{
			for (var i = 0; all_sku_props.length > i; i++)
			{
				if (all_sku_props[i].id == 'prop_' + property + '_' + basketItemId)
				{
					continue;
				}
				else
				{
					var sku_prop_value = BX.findChildren(BX(all_sku_props[i].id), {tagName: 'li', className: 'bx_active'}, true);
					if (!!sku_prop_value && sku_prop_value.length > 0)
					{
						for (var m = 0; sku_prop_value.length > m; m++)
						{
							if (sku_prop_value[m].hasAttribute('data-value-id'))
								property_values[sku_prop_value[m].getAttribute('data-property')] = sku_prop_value[m].getAttribute('data-value-id');
						}
					}
				}
			}
		}

		postData = {
			'basketItemId': basketItemId,
			'sessid': BX.bitrix_sessid(),
			'site_id': BX.message('SITE_ID'),
			'props': property_values,
			'action_var': action_var,
			'select_props': BX('column_headers').value,
			'offers_props': BX('offers_props').value,
			'quantity_float': BX('quantity_float').value,
			'count_discount_4_all_quantity': BX('count_discount_4_all_quantity').value,
			'price_vat_show_value': BX('price_vat_show_value').value,
			'hide_coupon': BX('hide_coupon').value,
			'use_prepayment': BX('use_prepayment').value
		};

		postData[action_var] = 'select_item';

		BX.ajax({
			url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
			method: 'POST',
			data: postData,
			dataType: 'json',
			onsuccess: function(result)
			{
				//BX.closeWait();
				updateBasketTable(result);
			}
		});
	}
}

function updateBasketTable(res)
{

	BX.ajax({
		url: location.pathname,
		method: 'POST',
		data: 'UPDATE_BIG_BASKET_AJAX=Y',
		onsuccess: function(html)
		{
			var coupon_number = BX('coupon').value;
			$("#update_big_basket_ajax").html(html);
			BX('coupon').value = coupon_number;
			updateBasketTable2(res);
			BX.closeWait();

			var sku_props = BX.findChildren(BX('basket_items_list'), {tagName: 'li', className: 'sku_prop'}, true);
			if (!!sku_props && sku_props.length > 0)
			{
				for (i = 0; sku_props.length > i; i++)
				{
					BX.bind(sku_props[i], 'click', BX.delegate(function(e){ skuPropClickHandler(e);}, this));
				}
			}

		}
	});
}

function updateBasketTable2(res) {
	if (BX('coupon'))
	{
		var couponClass = "";
		if (BX('coupon_approved') && BX('coupon').value.length == 0)
		{
			BX('coupon_approved').value = "N";
		}
		if (res.hasOwnProperty('VALID_COUPON'))
		{
			couponClass = (!!res['VALID_COUPON']) ? 'good' : 'bad';
			if (BX('coupon_approved'))
			{
				BX('coupon_approved').value = (!!res['VALID_COUPON']) ? 'Y' : 'N'
			}
		}
		if (BX('coupon_approved') && BX('coupon').value.length > 0)
		{
			couponClass = BX('coupon_approved').value == "Y" ? "good" : "bad";
		}else
		{
			couponClass = "";
		}
		BX('coupon').className = couponClass;
	}
	// update warnings if any
	if (res.hasOwnProperty('WARNING_MESSAGE'))
	{
		var warningText = '';
		for (var i = res['WARNING_MESSAGE'].length - 1; i >= 0; i--)
			warningText += res['WARNING_MESSAGE'][i] + '<br/>';
		BX('warning_message').innerHTML = warningText;
	}
}


function leftScroll(prop, id)
{
	var el = BX('prop_' + prop + '_' + id);

	if (el)
	{
		var curVal = parseInt(el.style.marginLeft);
		if (curVal < 0)
			el.style.marginLeft = curVal + 20 + '%';
	}
}

function rightScroll(prop, id)
{
	var el = BX('prop_' + prop + '_' + id);

	if (el)
	{
		var curVal = parseInt(el.style.marginLeft);
		if (curVal >= 0)
			el.style.marginLeft = curVal - 20 + '%';
	}
}

function checkOut()
{
	BX("basket_form").submit();
	return true;
}

function enterCoupon()
{
	recalcBasketAjax();
}


function recalcBasketAjax()
{
	BX.showWait();

	var property_values = {},
		action_var = BX('action_var').value,
		items = BX('basket_items_list'),
		delayedItems = BX('basket_items_delayed');

	var postData = {
		'sessid': BX.bitrix_sessid(),
		'site_id': BX.message('SITE_ID'),
		'props': property_values,
		'action_var': action_var,
		'select_props': BX('column_headers').value,
		'offers_props': BX('offers_props').value,
		'quantity_float': BX('quantity_float').value,
		'count_discount_4_all_quantity': BX('count_discount_4_all_quantity').value,
		'price_vat_show_value': BX('price_vat_show_value').value,
		'hide_coupon': BX('hide_coupon').value,
		'use_prepayment': BX('use_prepayment').value,
		'coupon': !!BX('coupon') ? BX('coupon').value : ""
	};

	postData[action_var] = 'recalculate';

	if (!!items && $("#basket_items_list").find(".item_quantity").length > 0) {
		$("#basket_items_list").find(".item_quantity").each(function () {
			postData[$(this).find("input").attr("name")] = $(this).find("input").val();
		});
	}
	if (!!delayedItems && $("#basket_items_delayed").find(".delay_input").length > 0) {
		$("#basket_items_delayed").find(".delay_input").each(function () {
			postData[$(this).attr("name")] = "Y";
		});
	}

	BX.ajax({
		url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
		method: 'POST',
		data: postData,
		dataType: 'json',
		onsuccess: function(result)
		{
			//BX.closeWait();
			updateBasketTable(result);
		}
	});
}

$(document).on("click", ".show_basket_sku_props", function () {
	$(this).closest(".basket_items_blocks_item").find(".text_sku_props").slideUp("fast");
	$(this).closest(".basket_items_blocks_item").find(".hidden_sku_props").slideDown("fast");
	$(this).closest(".itemName").find(".text_sku_props").slideUp("fast");
	$(this).closest(".itemName").find(".hidden_sku_props").slideDown("fast");
	
	return false;
});