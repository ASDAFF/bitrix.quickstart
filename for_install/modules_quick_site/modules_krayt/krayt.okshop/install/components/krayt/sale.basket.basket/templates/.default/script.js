BX.ready(function() {

	var sku_props = BX.findChildren(BX('basket_items'), {tagName: 'li', className: 'sku_prop'}, true);
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
				BX.closeWait();
				updateBasketTable(basketItemId, result);
			}
		});
	}
}

function updateBasketTable(basketItemId, res)
{
	var table = BX("basket_items");

	if (!table)
		return;

	var rows = table.rows,
		newBasketItemId = res['BASKET_ID'],
		arItem = res['BASKET_DATA']['GRID']['ROWS'][newBasketItemId],
		lastRow = rows[rows.length - 1],
		newRow = document.createElement('tr'),
		arColumns = res['COLUMNS'].split(','),
		bShowDeleteColumn = false,
		bShowDelayColumn = false,
		bShowPropsColumn = false,
		bShowPriceType = false,
		bUseFloatQuantity = (res['PARAMS']['QUANTITY_FLOAT'] == 'Y') ? true : false;

	// insert new row instead of original basket item row
	if (basketItemId !== null)
	{
		var origBasketItem = BX(basketItemId);

		newRow.setAttribute('id', res['BASKET_ID']);
		lastRow.parentNode.insertBefore(newRow, origBasketItem.nextSibling);

		if (res['DELETE_ORIGINAL'] == 'Y')
			origBasketItem.parentNode.removeChild(origBasketItem);

		// fill row with fields' values
		var oCellMargin = newRow.insertCell(-1);
			oCellMargin.setAttribute('class', 'margin');

		for (i = 0; i < arColumns.length; i++)
		{
			if (arColumns[i] == 'DELETE')
				bShowDeleteColumn = true;

			if (arColumns[i] == 'DELAY')
				bShowDelayColumn = true;

			if (arColumns[i] == 'PROPS')
				bShowPropsColumn = true;

			if (arColumns[i] == 'TYPE')
				bShowPriceType = true;
		}

		for (i = 0; i < arColumns.length; i++)
		{
			if (arColumns[i] == 'PROPS' || arColumns[i] == 'DELAY' || arColumns[i] == 'DELETE' || arColumns[i] == 'TYPE')
				continue;


			if (arColumns[i] == 'NAME')
			{
				// first <td> - image and brand
				var oCellName = newRow.insertCell(-1),
					imageURL = '',
					cellNameHTML = '';

				oCellName.setAttribute('class', 'itemphoto');

				if (arItem['PREVIEW_PICTURE_SRC'].length > 0)
					imageURL = arItem['PREVIEW_PICTURE_SRC'];
				else if (arItem['DETAIL_PICTURE_SRC'].length > 0)
					imageURL = arItem['DETAIL_PICTURE_SRC'];
				else
					imageURL = basketJSParams['TEMPLATE_FOLDER'] + '/images/no_photo.png';

				if (arItem['DETAIL_PAGE_URL'].length > 0)
				{
					cellNameHTML = '<div class="bx_ordercart_photo_container">\
						<a href="' + arItem['DETAIL_PAGE_URL'] + '">\
							<div class="bx_ordercart_photo" style="background-image:url(\'' + imageURL + '\')"></div>\
						</a>\
					</div>';
				}
				else
				{
					cellNameHTML = '<div class="bx_ordercart_photo_container">\
						<div class="bx_ordercart_photo" style="background-image:url(\'' + imageURL + '\')"></div>\
					</div>';
				}

				if (arItem['BRAND'] && arItem['BRAND'].length > 0)
				{
					cellNameHTML += '<div class="bx_ordercart_brand">\
						<img alt="" src="' + arItem['BRAND'] + '"/>\
					</div>';
				}

				oCellName.innerHTML = cellNameHTML;

				// second <td> - name, basket props, sku props
				var oCellItem = newRow.insertCell(-1),
					cellItemHTML = '';
				oCellItem.setAttribute('class', 'item');

				if (arItem['DETAIL_PAGE_URL'].length > 0)
					cellItemHTML += '<h2 class="bx_ordercart_itemtitle"><a href="' + arItem['DETAIL_PAGE_URL'] + '">' + arItem['NAME'] + '</a></h2>';
				else
					cellItemHTML += '<h2 class="bx_ordercart_itemtitle">' + arItem['NAME'] + '</h2>';

				cellItemHTML += '<div class="bx_ordercart_itemart">';

				if (bShowPropsColumn)
				{
					var bSkip;
					for (var j = 0; j < arItem['PROPS'].length; j++)
					{
						var val = arItem['PROPS'][j];

						if (arItem.SKU_DATA)
						{
							bSkip = false;
							for (var propId in arItem.SKU_DATA)
							{
								if (arItem.SKU_DATA.hasOwnProperty(propId))
								{
									var arProp = arItem.SKU_DATA[propId];

									if (arProp['CODE'] == val['CODE'])
									{
										bSkip = true;
										break;
									}
								}
							}
							if (bSkip)
								continue;
						}

						cellItemHTML += val['NAME'] + ':&nbsp;<span>' + val['VALUE'] + '</span><br/>';
					};
				}
				cellItemHTML += '</div>';

				if (arItem.SKU_DATA)
				{
					var arProp, bIsImageProperty, full, arVal;

					for (var propId in arItem.SKU_DATA)
					{
						if (arItem.SKU_DATA.hasOwnProperty(propId))
						{
							arProp = arItem.SKU_DATA[propId];
							bIsImageProperty = false;
							full = (Object.keys(arProp['VALUES']).length > 5) ? 'full' : '';

							for (var valId in arProp['VALUES'])
							{
								arVal = arProp['VALUES'][valId];

								if (arVal['PICT'] !== false)
								{
									bIsImageProperty = true;
									break;
								}
							}

							// sku property can contain list of images or values
							if (bIsImageProperty)
							{
								cellItemHTML += '<div class="bx_item_detail_scu_small_noadaptive ' + full + '">';
								cellItemHTML += '<span class="bx_item_section_name_gray">' + arProp['NAME'] + '</span>';
								cellItemHTML += '<div class="bx_scu_scroller_container">';
								cellItemHTML += '<div class="bx_scu">';

								cellItemHTML += '<ul id="prop_' + arProp['CODE'] + '_' + arItem['ID'] + '" style="width: 200%; margin-left:0%;" class="sku_prop_list">';

								var arSkuValue, selected;

								for (var valueId in arProp['VALUES'])
								{
									arSkuValue = arProp['VALUES'][valueId];
									selected = '';

									// get current selected item
									for (var k = 0; k < arItem['PROPS'].length; k++)
									{
										var arItemProp = arItem['PROPS'][k];

										if (arItemProp['CODE'] == arProp['CODE'])
										{
											if (arItemProp['VALUE'] == arSkuValue['NAME'] || arItemProp['VALUE'] == arSkuValue['XML_ID'])
												selected = 'bx_active';
										}
									}

									cellItemHTML += '<li style="width:10%;"\
														class="sku_prop ' + selected + '"\
														data-value-id="' + arSkuValue['XML_ID'] + '"\
														data-element="' + arItem['ID'] + '"\
														data-property="' + arProp['CODE'] + '"\
														>\
														<a href="javascript:void(0);">\
															<span style="background-image:url(' + arSkuValue['PICT']['SRC'] + ')"></span>\
														</a>\
													</li>';
								}

								cellItemHTML += '</ul>';
								cellItemHTML += '</div>';

								cellItemHTML += '<div class="bx_slide_left" onclick="leftScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ');"></div>';
								cellItemHTML += '<div class="bx_slide_right" onclick="rightScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ');"></div>';

								cellItemHTML += '</div>';
								cellItemHTML += '</div>';
							}
							else // not image
							{
								cellItemHTML += '<div class="bx_item_detail_size_small_noadaptive ' + full + '">';
								cellItemHTML += '<span class="bx_item_section_name_gray">' + arProp['NAME'] + '</span>';
								cellItemHTML += '<div class="bx_size_scroller_container">';
								cellItemHTML += '<div class="bx_size">';

								cellItemHTML += '<ul id="prop_' + arProp['CODE'] + '_' + arItem['ID'] + '" style="width: 200%; margin-left:0%;" class="sku_prop_list">';

								for (var valueId in arProp['VALUES'])
								{
									var arSkuValue = arProp['VALUES'][valueId],
										selected = '';

									// get current selected item
									for (var k = 0; k < arItem['PROPS'].length; k++)
									{
										var arItemProp = arItem['PROPS'][k];

										if (arItemProp['CODE'] == arProp['CODE'])
										{
											if (arItemProp['VALUE'] == arSkuValue['NAME'])
												selected = 'bx_active';
										}
									}

									cellItemHTML += '<li style="width:10%;"\
														class="sku_prop ' + selected + '"\
														data-value-id="' + arSkuValue['NAME'] + '"\
														data-element="' + arItem['ID'] + '"\
														data-property="' + arProp['CODE'] + '"\
														>\
														<a href="javascript:void(0);">' + arSkuValue['NAME'] + '</span></a>\
													</li>';
								}

								cellItemHTML += '</ul>';
								cellItemHTML += '</div>';

								cellItemHTML += '<div class="bx_slide_left" onclick="leftScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ');"></div>';
								cellItemHTML += '<div class="bx_slide_right" onclick="rightScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ');"></div>';

								cellItemHTML += '</div>';
								cellItemHTML += '</div>';
							}
						}
					}
				}

				oCellItem.innerHTML = cellItemHTML;
			}
			else if (arColumns[i] == 'QUANTITY')
			{
				var oCellQuantity = newRow.insertCell(-1),
					oCellQuantityHTML = '',
					ratio = (parseFloat(arItem['MEASURE_RATIO']) > 0) ? arItem['MEASURE_RATIO'] : 1,
					max = (parseFloat(arItem['AVAILABLE_QUANTITY']) > 0) ? 'max="' + arItem['AVAILABLE_QUANTITY'] + '"' : '';


				var isUpdateQuantity = false;

				if (ratio != 0 && ratio != '')
				{
					var oldQuantity = arItem['QUANTITY'];
					arItem['QUANTITY'] = getCorrectRatioQuantity(arItem['QUANTITY'], ratio, bUseFloatQuantity);

					if (oldQuantity != arItem['QUANTITY'])
					{
						isUpdateQuantity = true;
					}
				}

				oCellQuantity.setAttribute('class', 'custom');
				oCellQuantityHTML += '<span>' + getColumnName(res, arColumns[i]) + ':</span>';

				oCellQuantityHTML += '<div class="centered">';
				oCellQuantityHTML += '<table cellspacing="0" cellpadding="0" class="counter">';
				oCellQuantityHTML += '<tr>';
				oCellQuantityHTML += '<td>';

				oCellQuantityHTML += '<input\
										type="text"\
										size="3"\
										id="QUANTITY_INPUT_' + arItem['ID'] + '"\
										name="QUANTITY_INPUT_' + arItem['ID'] + '"\
										size="2"\
										maxlength="18"\
										min="0"\
										' + max + '\
										step=' + ratio + '\
										style="max-width: 50px"\
										value="' + arItem['QUANTITY'] + '"\
										onchange="updateQuantity(\'QUANTITY_INPUT_' + arItem['ID'] + '\',\'' + arItem['ID'] + '\', ' + ratio + ',' + bUseFloatQuantity + ')"\
					>';

				oCellQuantityHTML += '</td>';

				if (ratio != 0
					&& ratio != ''
					) // if not Set parent, show quantity control
				{
					oCellQuantityHTML += '<td id="basket_quantity_control">\
						<div class="basket_quantity_control">\
							<a href="javascript:void(0);" class="plus" onclick="setQuantity(' + arItem['ID'] + ', ' + ratio + ', \'up\', ' + bUseFloatQuantity + ');"></a>\
							<a href="javascript:void(0);" class="minus" onclick="setQuantity(' + arItem['ID'] + ', ' + ratio + ', \'down\', ' + bUseFloatQuantity + ');"></a>\
						</div>\
					</td>';
				}

				if (arItem.hasOwnProperty('MEASURE_TEXT') && arItem['MEASURE_TEXT'].length > 0)
					oCellQuantityHTML += '<td style="text-align: left">' + arItem['MEASURE_TEXT'] + '</td>';

				oCellQuantityHTML += '</tr>';
				oCellQuantityHTML += '</table>';
				oCellQuantityHTML += '</div>';

				oCellQuantityHTML += getMobileQuantityControl(
										"QUANTITY_SELECT_" + arItem['ID'],
										"QUANTITY_SELECT_" + arItem['ID'],
										arItem["QUANTITY"],
										arItem["AVAILABLE_QUANTITY"],
										arItem["MEASURE_RATIO"],
										arItem["MEASURE_TEXT"]
									);

				oCellQuantityHTML += '<input type="hidden" id="QUANTITY_' + arItem['ID'] + '" name="QUANTITY_' + arItem['ID'] + '" value="' + arItem['QUANTITY'] + '" />';

				oCellQuantity.innerHTML = oCellQuantityHTML;

				if (isUpdateQuantity)
				{
					updateQuantity('QUANTITY_INPUT_' + arItem['ID'], arItem['ID'], ratio, bUseFloatQuantity);
				}
			}
			else if (arColumns[i] == 'PRICE')
			{
				var oCellPrice = newRow.insertCell(-1),
					fullPrice = (arItem['FULL_PRICE_FORMATED'] != arItem['PRICE_FORMATED']) ? arItem['FULL_PRICE_FORMATED'] : '';

				oCellPrice.setAttribute('class', 'price');
				oCellPrice.innerHTML += '<div class="current_price" id="current_price_' + arItem['ID'] + '">' + arItem['PRICE_FORMATED'] + '</div>';
				oCellPrice.innerHTML += '<div class="old_price" id="old_price_' + arItem['ID'] + '">' + fullPrice + '</div>';

				if (bShowPriceType && arItem['NOTES'].length > 0)
				{
					oCellPrice.innerHTML += '<div class="type_price">' + basketJSParams['SALE_TYPE'] + '</div>';
					oCellPrice.innerHTML += '<div class="type_price_value">' + arItem['NOTES'] + '</div>';
				}
			}
			else if (arColumns[i] == 'DISCOUNT')
			{
				var oCellDiscount = newRow.insertCell(-1);
				oCellDiscount.setAttribute('class', 'custom');
				oCellDiscount.innerHTML = '<span>' + getColumnName(res, arColumns[i]) + ':</span>';
				oCellDiscount.innerHTML += '<div id="discount_value_' + arItem['ID'] + '">' + arItem['DISCOUNT_PRICE_PERCENT_FORMATED'] + '</div>';
			}
			else if (arColumns[i] == 'WEIGHT')
			{
				var oCellWeight = newRow.insertCell(-1);
				oCellWeight.setAttribute('class', 'custom');
				oCellWeight.innerHTML = '<span>' + getColumnName(res, arColumns[i]) + ':</span>';
				oCellWeight.innerHTML += arItem['WEIGHT_FORMATED'];
			}
			else
			{
				var oCellCustom = newRow.insertCell(-1),
					customColumnVal = '';

				oCellCustom.setAttribute('class', 'custom');
				oCellCustom.innerHTML = '<span>' + getColumnName(res, arColumns[i]) + ':</span>';

				if (arColumns[i] == 'SUM')
					customColumnVal += '<div id="sum_' + arItem['ID'] + '">';

				if (typeof(arItem[arColumns[i]]) != 'undefined' )
				{
					customColumnVal += arItem[arColumns[i]];
				}

				if (arColumns[i] == 'SUM')
					customColumnVal += '</div>';

				oCellCustom.innerHTML += customColumnVal;
			}
		}

		if (bShowDeleteColumn || bShowDelayColumn)
		{
			var oCellControl = newRow.insertCell(-1);
				oCellControl.setAttribute('class', 'control');

			if (bShowDeleteColumn)
				oCellControl.innerHTML = '<a href="' + basketJSParams['DELETE_URL'].replace('#ID#', arItem['ID']) +'">' + basketJSParams['SALE_DELETE'] + '</a><br />';

			if (bShowDelayColumn)
				oCellControl.innerHTML += '<a href="' + basketJSParams['DELAY_URL'].replace('#ID#', arItem['ID']) + '">' + basketJSParams['SALE_DELAY'] + '</a>';
		}

		var oCellMargin2 = newRow.insertCell(-1);
			oCellMargin2.setAttribute('class', 'margin');

		// set sku props click handler
		var sku_props = BX.findChildren(BX(newBasketItemId), {tagName: 'li', className: 'sku_prop'}, true);
		if (!!sku_props && sku_props.length > 0)
		{
			for (i = 0; sku_props.length > i; i++)
			{
				BX.bind(sku_props[i], 'click', BX.delegate(function(e){ skuPropClickHandler(e);}, this));
			}
		}
	}

	// update product params after recalculation
	for (var id in res.BASKET_DATA.GRID.ROWS)
	{
		if (res.BASKET_DATA.GRID.ROWS.hasOwnProperty(id))
		{
			var item = res.BASKET_DATA.GRID.ROWS[id];

			if (BX('discount_value_' + id))
				BX('discount_value_' + id).innerHTML = item.DISCOUNT_PRICE_PERCENT_FORMATED;

			if (BX('current_price_' + id))
				BX('current_price_' + id).innerHTML = item.PRICE_FORMATED;

			if (BX('old_price_' + id))
				BX('old_price_' + id).innerHTML = (item.FULL_PRICE_FORMATED != item.PRICE_FORMATED) ? item.FULL_PRICE_FORMATED : '';

			if (BX('sum_' + id))
				BX('sum_' + id).innerHTML = item.SUM;

			// if the quantity was set by user to 0 or was too much, we need to show corrected quantity value from ajax response
			if (BX('QUANTITY_' + id))
			{
				BX('QUANTITY_INPUT_' + id).value = item.QUANTITY;
				BX('QUANTITY_INPUT_' + id).defaultValue = item.QUANTITY;

				var option,
					options = BX('QUANTITY_SELECT_' + id).options,
					i = options.length;

				while (i--)
				{
					option = options[i];
					if (parseFloat(option.value).toFixed(2) == parseFloat(item.QUANTITY).toFixed(2))
						option.selected = true;
				}

				BX('QUANTITY_' + id).value = item.QUANTITY;
			}
		}
	}

	// update coupon info
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

	// update total basket values
	if (BX('allWeight_FORMATED'))
		BX('allWeight_FORMATED').innerHTML = res['BASKET_DATA']['allWeight_FORMATED'].replace(/\s/g, '&nbsp;');

	if (BX('allSum_wVAT_FORMATED'))
		BX('allSum_wVAT_FORMATED').innerHTML = res['BASKET_DATA']['allSum_wVAT_FORMATED'].replace(/\s/g, '&nbsp;');

	if (BX('allVATSum_FORMATED'))
		BX('allVATSum_FORMATED').innerHTML = res['BASKET_DATA']['allVATSum_FORMATED'].replace(/\s/g, '&nbsp;');

	if (BX('allSum_FORMATED'))
		BX('allSum_FORMATED').innerHTML = res['BASKET_DATA']['allSum_FORMATED'].replace(/\s/g, '&nbsp;');

	if (BX('PRICE_WITHOUT_DISCOUNT'))
		BX('PRICE_WITHOUT_DISCOUNT').innerHTML = (res['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'] != res['BASKET_DATA']['allSum_FORMATED']) ? res['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'].replace(/\s/g, '&nbsp;') : '';
}

function getColumnName(result, columnCode)
{
	return BX('col_' + columnCode).innerHTML.trim();
}

function getMobileQuantityControl(id, name, curVal, maxQuantity, ratio, measureText)
{
	var maxQuantity = parseFloat(maxQuantity),
		ratio = (parseFloat(ratio) != 0 && !isNaN(parseFloat(ratio))) ? parseFloat(ratio) : 1,
		startValue = (ratio > 0 && ratio != 1) ? ratio : 1,
		basketId = id.replace('QUANTITY_SELECT_', ''),
		disabled = '', // if not available for buying
		res = '';

	if (maxQuantity === 0)
	{
		disabled = "disabled";
		maxQuantity = 1;
	}

	res = '<div class="some-class">';
	res += '<select id="' + id + '" name="' + name + '" onchange="updateQuantity(\'' + id + '\', ' + basketId + ', ' + ratio + ');" ' + disabled +'>';

	for (var j = startValue; j <= maxQuantity; j = j + ratio)
	{
		var bIsQuantityFloat = false;

		if (parseInt(j) != parseFloat(j))
		{
			bIsQuantityFloat = true;
		}
		selected = (j == curVal) ? 'selected' : '';
		res += '<option value=' + j + ' ' + selected + '>' + (bIsQuantityFloat? parseFloat(j).toFixed(2) : parseInt(j) ) + '</option>';
	}

	res += '</select>';

	if (measureText !== undefined)
		res += measureText;

	res += '</div>';

	return res;
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

// check if quantity is valid
// and update values of both controls (text input field for PC and mobile quantity select) simultaneously
function updateQuantity(controlId, basketId, ratio, bUseFloatQuantity)
{
	var oldVal = BX(controlId).defaultValue,
		newVal = parseFloat(BX(controlId).value) || 0,
		bIsCorrectQuantityForRatio = false;

	if (ratio === 0 || ratio == 1)
	{
		bIsCorrectQuantityForRatio = true;
	}
	else
	{

		var newValInt = newVal * 10000,
			ratioInt = ratio * 10000,
			reminder = newValInt % ratioInt,
			newValRound = parseInt(newVal);

		if (reminder === 0)
		{
			bIsCorrectQuantityForRatio = true;
		}
	}

	var bIsQuantityFloat = false;

	if (parseInt(newVal) != parseFloat(newVal))
	{
		bIsQuantityFloat = true;
	}

	newVal = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(newVal) : parseFloat(newVal).toFixed(2);

	if (bIsCorrectQuantityForRatio)
	{
		BX(controlId).defaultValue = newVal;

		BX("QUANTITY_INPUT_" + basketId).value = newVal;

		var option,
			options = BX("QUANTITY_SELECT_" + basketId).options,
			i = options.length;

		while (i--)
		{
			option = options[i];
			if (parseFloat(option.value).toFixed(2) == parseFloat(newVal).toFixed(2))
				option.selected = true;
		}

		// set hidden real quantity value (will be used in actual calculation)
		BX("QUANTITY_" + basketId).value = newVal;

		recalcBasketAjax();
	}
	else
	{
		newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);

		if (newVal != oldVal)
		{
			BX("QUANTITY_INPUT_" + basketId).value = newVal;
			BX("QUANTITY_" + basketId).value = newVal;
			recalcBasketAjax();
		}else
		{
			BX(controlId).value = oldVal;
		}
	}
}

// used when quantity is changed by clicking on arrows
function setQuantity(basketId, ratio, sign, bUseFloatQuantity)
{
	var curVal = parseFloat(BX("QUANTITY_INPUT_" + basketId).value),
		newVal;

	newVal = (sign == 'up') ? curVal + ratio : curVal - ratio;

	if (newVal < 0)
		newVal = 0;

	if (bUseFloatQuantity)
	{
		newVal = newVal.toFixed(2);
	}

	if (ratio > 0 && newVal < ratio)
	{
		newVal = ratio;
	}

	if (!bUseFloatQuantity && newVal != newVal.toFixed(2))
	{
		newVal = newVal.toFixed(2);
	}

	newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);

	BX("QUANTITY_INPUT_" + basketId).value = newVal;
	BX("QUANTITY_INPUT_" + basketId).defaultValue = newVal;

	updateQuantity('QUANTITY_INPUT_' + basketId, basketId, ratio, bUseFloatQuantity);
}

function getCorrectRatioQuantity(quantity, ratio, bUseFloatQuantity)
{
	var newValInt = quantity * 10000,
		ratioInt = ratio * 10000,
		reminder = newValInt % ratioInt,
		result = quantity,
		bIsQuantityFloat = false,
		ratio = parseFloat(ratio);


	if (reminder === 0)
	{
		return result;
	}

	if (ratio !== 0 && ratio != 1)
	{
		for (var i = ratio, max = parseFloat(quantity) + parseFloat(ratio); i <= max; i = parseFloat(parseFloat(i) + parseFloat(ratio)).toFixed(2))
		{
			result = i;
		}

	}else if (ratio === 1)
	{
		result = quantity | 0;
	}

	if (parseInt(result) != parseFloat(result))
	{
		bIsQuantityFloat = true;
	}

	result = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(result) : parseFloat(result).toFixed(2);

	return result;
}

function recalcBasketAjax()
{
	BX.showWait();

	var property_values = {},
		action_var = BX('action_var').value,
		items = BX('basket_items'),
		delayedItems = BX('delayed_items');

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

	if (!!items && items.rows.length > 0)
	{
		for (var i = 1; items.rows.length > i; i++)
			postData['QUANTITY_' + items.rows[i].id] = BX('QUANTITY_' + items.rows[i].id).value;
	}

	if (!!delayedItems && delayedItems.rows.length > 0)
	{
		for (var i = 1; delayedItems.rows.length > i; i++)
			postData['DELAY_' + delayedItems.rows[i].id] = 'Y';
	}

	BX.ajax({
		url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
		method: 'POST',
		data: postData,
		dataType: 'json',
		onsuccess: function(result)
		{
			BX.closeWait();
			updateBasketTable(null, result);
		}
	});
}

function showBasketItemsList(val)
{
	BX.removeClass(BX("basket_toolbar_button"), "current");
	BX.removeClass(BX("basket_toolbar_button_delayed"), "current");
	BX.removeClass(BX("basket_toolbar_button_subscribed"), "current");
	BX.removeClass(BX("basket_toolbar_button_not_available"), "current");

	BX("normal_count").style.display = 'inline-block';
	BX("delay_count").style.display = 'inline-block';
	BX("subscribe_count").style.display = 'inline-block';
	BX("not_available_count").style.display = 'inline-block';

	if (val == 2)
	{
		if (BX("basket_items_list"))
			BX("basket_items_list").style.display = 'none';
		if (BX("basket_items_delayed"))
		{
			BX("basket_items_delayed").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button_delayed"), "current");
			BX("delay_count").style.display = 'none';
		}
		if (BX("basket_items_subscribed"))
			BX("basket_items_subscribed").style.display = 'none';
		if (BX("basket_items_not_available"))
			BX("basket_items_not_available").style.display = 'none';
	}
	else if(val == 3)
	{
		if (BX("basket_items_list"))
			BX("basket_items_list").style.display = 'none';
		if (BX("basket_items_delayed"))
			BX("basket_items_delayed").style.display = 'none';
		if (BX("basket_items_subscribed"))
		{
			BX("basket_items_subscribed").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button_subscribed"), "current");
			BX("subscribe_count").style.display = 'none';
		}
		if (BX("basket_items_not_available"))
			BX("basket_items_not_available").style.display = 'none';
	}
	else if (val == 4)
	{
		if (BX("basket_items_list"))
			BX("basket_items_list").style.display = 'none';
		if (BX("basket_items_delayed"))
			BX("basket_items_delayed").style.display = 'none';
		if (BX("basket_items_subscribed"))
			BX("basket_items_subscribed").style.display = 'none';
		if (BX("basket_items_not_available"))
		{
			BX("basket_items_not_available").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button_not_available"), "current");
			BX("not_available_count").style.display = 'none';
		}
	}
	else
	{
		if (BX("basket_items_list"))
		{
			BX("basket_items_list").style.display = 'block';
			BX.addClass(BX("basket_toolbar_button"), "current");
			BX("normal_count").style.display = 'none';
		}
		if (BX("basket_items_delayed"))
			BX("basket_items_delayed").style.display = 'none';
		if (BX("basket_items_subscribed"))
			BX("basket_items_subscribed").style.display = 'none';
		if (BX("basket_items_not_available"))
			BX("basket_items_not_available").style.display = 'none';
	}
}

