/**
 * @param basketItemId
 * @param {{BASKET_ID : string, BASKET_DATA : { GRID : { ROWS : {} }}, COLUMNS: {}, PARAMS: {}, DELETE_ORIGINAL : string }} res
 */
function updateBasketTable(basketItemId, res)
{
	var table = BX("basket_items"),
		rows,
		newBasketItemId,
		arItem,
		lastRow,
		newRow,
		arColumns,
		bShowDeleteColumn = false,
		bShowDelayColumn = false,
		bShowPropsColumn = false,
		bShowPriceType = false,
		bUseFloatQuantity,
		origBasketItem,
		oCellMargin,
		i,
		oCellName,
		imageURL,
		cellNameHTML,
		oCellItem,
		cellItemHTML,
		bSkip,
		j,
		val,
		propId,
		arProp,
		bIsImageProperty,
		full,
		arVal,
		valId,
		arSkuValue,
		selected,
		valueId,
		k,
		arItemProp,
		oCellQuantity,
		oCellQuantityHTML,
		ratio,
		max,
		isUpdateQuantity,
		oldQuantity,
		oCellPrice,
		fullPrice,
		id,
		oCellDiscount,
		oCellWeight,
		oCellCustom,
		customColumnVal;

	if (!table || typeof res !== 'object')
	{
		return;
	}

	rows = table.rows;
	lastRow = rows[rows.length - 1];
	bUseFloatQuantity = (res.PARAMS.QUANTITY_FLOAT === 'Y');

	// insert new row instead of original basket item row
	if (basketItemId !== null && !!res.BASKET_DATA)
	{
		newBasketItemId = res.BASKET_ID;
		arItem = res.BASKET_DATA.GRID.ROWS[newBasketItemId];
		arColumns = res.COLUMNS.split(',');
		newRow = document.createElement('tr');

		origBasketItem = BX(basketItemId);

		newRow.setAttribute('id', res.BASKET_ID);
		lastRow.parentNode.insertBefore(newRow, origBasketItem.nextSibling);

		if (res.DELETE_ORIGINAL === 'Y')
		{
			origBasketItem.parentNode.removeChild(origBasketItem);
		}

		// fill row with fields' values
		oCellMargin = newRow.insertCell(-1);
		oCellMargin.setAttribute('class', 'margin');

		for (i = 0; i < arColumns.length; i++)
		{
			if (arColumns[i] === 'DELETE')
			{
				bShowDeleteColumn = true;
			}
			else if (arColumns[i] === 'DELAY')
			{
				bShowDelayColumn = true;
			}
			else if (arColumns[i] === 'PROPS')
			{
				bShowPropsColumn = true;
			}
			else if (arColumns[i] === 'TYPE')
			{
				bShowPriceType = true;
			}
		}

		for (i = 0; i < arColumns.length; i++)
		{
			switch (arColumns[i])
			{
				case 'PROPS':
				case 'DELAY':
				case 'DELETE':
				case 'TYPE':
					break;
				case 'NAME':
					// first <td> - image and brand
					oCellName = newRow.insertCell(-1);
					imageURL = '';
					cellNameHTML = '';

					oCellName.setAttribute('class', 'itemphoto');

					if (arItem.PREVIEW_PICTURE_SRC.length > 0)
					{
						imageURL = arItem.PREVIEW_PICTURE_SRC;
					}
					else if (arItem.DETAIL_PICTURE_SRC.length > 0)
					{
						imageURL = arItem.DETAIL_PICTURE_SRC;
					}
					else
					{
						imageURL = basketJSParams.TEMPLATE_FOLDER + '/images/no_photo.png';
					}

					if (arItem.DETAIL_PAGE_URL.length > 0)
					{
						cellNameHTML = '<div class="bx_ordercart_photo_container">\
							<a href="' + arItem.DETAIL_PAGE_URL + '">\
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

					if (arItem.BRAND && arItem.BRAND.length > 0)
					{
						cellNameHTML += '<div class="bx_ordercart_brand">\
							<img alt="" src="' + arItem.BRAND + '"/>\
						</div>';
					}

					oCellName.innerHTML = cellNameHTML;

					// second <td> - name, basket props, sku props
					oCellItem = newRow.insertCell(-1);
					cellItemHTML = '';
					oCellItem.setAttribute('class', 'item');

					if (arItem['DETAIL_PAGE_URL'].length > 0)
						cellItemHTML += '<h2 class="bx_ordercart_itemtitle"><a href="' + arItem['DETAIL_PAGE_URL'] + '">' + arItem['NAME'] + '</a></h2>';
					else
						cellItemHTML += '<h2 class="bx_ordercart_itemtitle">' + arItem['NAME'] + '</h2>';

					cellItemHTML += '<div class="bx_ordercart_itemart">';

					if (bShowPropsColumn)
					{
						for (j = 0; j < arItem['PROPS'].length; j++)
						{
							val = arItem['PROPS'][j];

							if (arItem.SKU_DATA)
							{
								bSkip = false;
								for (propId in arItem.SKU_DATA)
								{
									if (arItem.SKU_DATA.hasOwnProperty(propId))
									{
										arProp = arItem.SKU_DATA[propId];

										if (arProp['CODE'] === val['CODE'])
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
						}
					}
					cellItemHTML += '</div>';

					if (arItem.SKU_DATA)
					{
						for (propId in arItem.SKU_DATA)
						{
							if (arItem.SKU_DATA.hasOwnProperty(propId))
							{
								arProp = arItem.SKU_DATA[propId];
								bIsImageProperty = false;
								full = (BX.util.array_keys(arProp['VALUES']).length > 5) ? 'full' : '';

								for (valId in arProp['VALUES'])
								{
									arVal = arProp['VALUES'][valId];
									if (!!arVal && typeof arVal === 'object' && !!arVal['PICT'])
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

									for (valueId in arProp['VALUES'])
									{
										arSkuValue = arProp['VALUES'][valueId];
										selected = '';

										// get current selected item
										for (k = 0; k < arItem['PROPS'].length; k++)
										{
											arItemProp = arItem['PROPS'][k];

											if (arItemProp['CODE'] === arProp['CODE'])
											{
												if (arItemProp['VALUE'] === arSkuValue['NAME'] || arItemProp['VALUE'] === arSkuValue['XML_ID'])
													selected = ' bx_active';
											}
										}

										cellItemHTML += '<li style="width:10%;"\
															class="sku_prop' + selected + '"\
															data-value-id="' + arSkuValue['XML_ID'] + '"\
															data-element="' + arItem['ID'] + '"\
															data-property="' + arProp['CODE'] + '"\
															>\
															<a href="javascript:void(0)" class="cnt"><span class="cnt_item" style="background-image:url(' + arSkuValue['PICT']['SRC'] + '"></span></a>\
														</li>';
									}

									cellItemHTML += '</ul>';
									cellItemHTML += '</div>';

									cellItemHTML += '<div class="bx_slide_left" onclick="leftScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ', '+ BX.util.array_keys(arProp['VALUES']).length + ');"></div>';
									cellItemHTML += '<div class="bx_slide_right" onclick="rightScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ', '+ BX.util.array_keys(arProp['VALUES']).length + ');"></div>';

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

									for (valueId in arProp['VALUES'])
									{
										arSkuValue = arProp['VALUES'][valueId];
										selected = '';

										// get current selected item
										for (k = 0; k < arItem['PROPS'].length; k++)
										{
											arItemProp = arItem['PROPS'][k];

											if (arItemProp['CODE'] === arProp['CODE'])
											{
												if (arItemProp['VALUE'] === arSkuValue['NAME'])
													selected = 'bx_active';
											}
										}

										cellItemHTML += '<li style="width:10%;"\
															class="sku_prop ' + selected + '"\
															data-value-id="' + arSkuValue['NAME'] + '"\
															data-element="' + arItem['ID'] + '"\
															data-property="' + arProp['CODE'] + '"\
															>\
															<a href="javascript:void(0)" class="cnt">' + arSkuValue['NAME'] + '</a>\
														</li>';
									}

									cellItemHTML += '</ul>';
									cellItemHTML += '</div>';

									cellItemHTML += '<div class="bx_slide_left" onclick="leftScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ', '+ BX.util.array_keys(arProp['VALUES']).length + ');"></div>';
									cellItemHTML += '<div class="bx_slide_right" onclick="rightScroll(\'' + arProp['CODE'] + '\', ' + arItem['ID'] + ', '+ BX.util.array_keys(arProp['VALUES']).length + ');"></div>';

									cellItemHTML += '</div>';
									cellItemHTML += '</div>';
								}
							}
						}
					}

					oCellItem.innerHTML = cellItemHTML;
					break;
				case 'QUANTITY':
					oCellQuantity = newRow.insertCell(-1);
					oCellQuantityHTML = '';
					ratio = (parseFloat(arItem['MEASURE_RATIO']) > 0) ? arItem['MEASURE_RATIO'] : 1;
					max = (parseFloat(arItem['AVAILABLE_QUANTITY']) > 0) ? 'max="' + arItem['AVAILABLE_QUANTITY'] + '"' : '';

					isUpdateQuantity = false;

					if (ratio != 0 && ratio != '')
					{
						oldQuantity = arItem['QUANTITY'];
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

					oCellQuantityHTML += '<input type="text" size="3" id="QUANTITY_INPUT_' + arItem['ID'] + '"\
											name="QUANTITY_INPUT_' + arItem['ID'] + '"\
											size="2" maxlength="18" min="0" ' + max + 'step=' + ratio + '\
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

					oCellQuantityHTML += '<input type="hidden" id="QUANTITY_' + arItem['ID'] + '" name="QUANTITY_' + arItem['ID'] + '" value="' + arItem['QUANTITY'] + '" />';

					oCellQuantity.innerHTML = oCellQuantityHTML;

					if (isUpdateQuantity)
					{
						updateQuantity('QUANTITY_INPUT_' + arItem['ID'], arItem['ID'], ratio, bUseFloatQuantity);
					}
					break;
				case 'PRICE':
					oCellPrice = newRow.insertCell(-1);
					fullPrice = (arItem['FULL_PRICE_FORMATED'] != arItem['PRICE_FORMATED']) ? arItem['FULL_PRICE_FORMATED'] : '';

					oCellPrice.setAttribute('class', 'price');
					oCellPrice.innerHTML += '<div class="current_price" id="current_price_' + arItem['ID'] + '">' + arItem['PRICE_FORMATED'] + '</div>';
					oCellPrice.innerHTML += '<div class="old_price" id="old_price_' + arItem['ID'] + '">' + fullPrice + '</div>';

					if (bShowPriceType && arItem['NOTES'].length > 0)
					{
						oCellPrice.innerHTML += '<div class="type_price">' + basketJSParams['SALE_TYPE'] + '</div>';
						oCellPrice.innerHTML += '<div class="type_price_value">' + arItem['NOTES'] + '</div>';
					}
					break;
				case 'DISCOUNT':
					oCellDiscount = newRow.insertCell(-1);
					oCellDiscount.setAttribute('class', 'custom');
					oCellDiscount.innerHTML = '<span>' + getColumnName(res, arColumns[i]) + ':</span>';
					oCellDiscount.innerHTML += '<div id="discount_value_' + arItem['ID'] + '">' + arItem['DISCOUNT_PRICE_PERCENT_FORMATED'] + '</div>';
					break;
				case 'WEIGHT':
					oCellWeight = newRow.insertCell(-1);
					oCellWeight.setAttribute('class', 'custom');
					oCellWeight.innerHTML = '<span>' + getColumnName(res, arColumns[i]) + ':</span>';
					oCellWeight.innerHTML += arItem['WEIGHT_FORMATED'];
					break;
				default:
					oCellCustom = newRow.insertCell(-1);
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
					break;
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
	if (!!res.BASKET_DATA)
	{
		for (id in res.BASKET_DATA.GRID.ROWS)
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

					BX('QUANTITY_' + id).value = item.QUANTITY;
				}
			}
		}
	}

	// update coupon info
	if (!!res.BASKET_DATA)
		couponListUpdate(res.BASKET_DATA);

	// update warnings if any
	if (res.hasOwnProperty('WARNING_MESSAGE'))
	{
		var warningText = '';

		for (i = res['WARNING_MESSAGE'].length - 1; i >= 0; i--)
			warningText += res['WARNING_MESSAGE'][i] + '<br/>';

		BX('warning_message').innerHTML = warningText;
	}

	// update total basket values
	if (!!res.BASKET_DATA)
	{
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

		BX.onCustomEvent('OnBasketChange');
	}
}
/**
 * @param couponBlock
 * @param {COUPON: string, JS_STATUS: string} oneCoupon - new coupon.
 */
function couponCreate(couponBlock, oneCoupon)
{
	var couponClass = 'disabled';

	if (!BX.type.isElementNode(couponBlock))
		return;
	if (oneCoupon.JS_STATUS === 'BAD')
		couponClass = 'bad';
	else if (oneCoupon.JS_STATUS === 'APPLYED')
		couponClass = 'good';

	couponBlock.appendChild(BX.create(
		'div',
		{
			props: {
				className: 'bx_ordercart_coupon'
			},
			children: [
				BX.create(
					'input',
					{
						props: {
							className: couponClass,
							type: 'text',
							value: oneCoupon.COUPON,
							name: 'OLD_COUPON[]'
						},
						attrs: {
							disabled: true,
							readonly: true
						}
					}
				),
				BX.create(
					'span',
					{
						props: {
							className: couponClass
						},
						attrs: {
							'data-coupon': oneCoupon.COUPON
						}
					}
				),
				BX.create(
					'div',
					{
						props: {
							className: 'bx_ordercart_coupon_notes'
						},
						html: oneCoupon.JS_CHECK_CODE
					}
				)
			]
		}
	));
}

/**
 * @param {COUPON_LIST : []} res
 */
function couponListUpdate(res)
{
	var couponBlock,
		couponClass,
		fieldCoupon,
		couponsCollection,
		couponFound,
		i,
		j,
		key;

	if (!!res && typeof res !== 'object')
	{
		return;
	}

	couponBlock = BX('coupons_block');
	if (!!couponBlock)
	{
		if (!!res.COUPON_LIST && BX.type.isArray(res.COUPON_LIST))
		{
			fieldCoupon = BX('coupon');
			if (!!fieldCoupon)
			{
				fieldCoupon.value = '';
			}
			couponsCollection = BX.findChildren(couponBlock, { tagName: 'input', property: { name: 'OLD_COUPON[]' } }, true);

			if (!!couponsCollection)
			{
				if (BX.type.isElementNode(couponsCollection))
				{
					couponsCollection = [couponsCollection];
				}
				for (i = 0; i < res.COUPON_LIST.length; i++)
				{
					couponFound = false;
					key = -1;
					for (j = 0; j < couponsCollection.length; j++)
					{
						if (couponsCollection[j].value === res.COUPON_LIST[i].COUPON)
						{
							couponFound = true;
							key = j;
							couponsCollection[j].couponUpdate = true;
							break;
						}
					}
					if (couponFound)
					{
						couponClass = 'disabled';
						if (res.COUPON_LIST[i].JS_STATUS === 'BAD')
							couponClass = 'bad';
						else if (res.COUPON_LIST[i].JS_STATUS === 'APPLYED')
							couponClass = 'good';

						BX.adjust(couponsCollection[key], {props: {className: couponClass}});
						BX.adjust(couponsCollection[key].nextSibling, {props: {className: couponClass}});
						BX.adjust(couponsCollection[key].nextSibling.nextSibling, {html: res.COUPON_LIST[i].JS_CHECK_CODE});
					}
					else
					{
						couponCreate(couponBlock, res.COUPON_LIST[i]);
					}
				}
				for (j = 0; j < couponsCollection.length; j++)
				{
					if (typeof (couponsCollection[j].couponUpdate) === 'undefined' || !couponsCollection[j].couponUpdate)
					{
						BX.remove(couponsCollection[j].parentNode);
						couponsCollection[j] = null;
					}
					else
					{
						couponsCollection[j].couponUpdate = null;
					}
				}
			}
			else
			{
				for (i = 0; i < res.COUPON_LIST.length; i++)
				{
					couponCreate(couponBlock, res.COUPON_LIST[i]);
				}
			}
		}
	}
	couponBlock = null;
}

function skuPropClickHandler(e)
{
	if (!e)
	{
		e = window.event;
	}
	var target = BX.proxy_context,
		basketItemId,
		property,
		property_values = {},
		postData = {},
		action_var,
		all_sku_props,
		i,
		sku_prop_value,
		m;

	if (!!target && target.hasAttribute('data-value-id'))
	{
		BX.showWait();

		basketItemId = target.getAttribute('data-element');
		property = target.getAttribute('data-property');
		action_var = BX('action_var').value;

		property_values[property] = target.getAttribute('data-value-id');

		// if already selected element is clicked
		if (BX.hasClass(target, 'bx_active'))
		{
			BX.closeWait();
			return;
		}

		// get other basket item props to get full unique set of props of the new product
		all_sku_props = BX.findChildren(BX(basketItemId), {tagName: 'ul', className: 'sku_prop_list'}, true);
		if (!!all_sku_props && all_sku_props.length > 0)
		{
			for (i = 0; all_sku_props.length > i; i++)
			{
				if (all_sku_props[i].id !== 'prop_' + property + '_' + basketItemId)
				{
					sku_prop_value = BX.findChildren(BX(all_sku_props[i].id), {tagName: 'li', className: 'bx_active'}, true);
					if (!!sku_prop_value && sku_prop_value.length > 0)
					{
						for (m = 0; sku_prop_value.length > m; m++)
						{
							if (sku_prop_value[m].hasAttribute('data-value-id'))
							{
								property_values[sku_prop_value[m].getAttribute('data-property')] = sku_prop_value[m].getAttribute('data-value-id');
							}
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

function getColumnName(result, columnCode)
{
	if (BX('col_' + columnCode))
	{
		return BX.util.trim(BX('col_' + columnCode).innerHTML);
	}
	else
	{
		return '';
	}
}

function leftScroll(prop, id, count)
{
	count = parseInt(count, 10);
	var el = BX('prop_' + prop + '_' + id);

	if (el)
	{
		var curVal = parseInt(el.style.marginLeft, 10);
		if (curVal <= (6 - count)*20)
			el.style.marginLeft = curVal + 20 + '%';
	}
}

function rightScroll(prop, id, count)
{
	count = parseInt(count, 10);
	var el = BX('prop_' + prop + '_' + id);

	if (el)
	{
		var curVal = parseInt(el.style.marginLeft, 10);
		if (curVal > (5 - count)*20)
			el.style.marginLeft = curVal - 20 + '%';
	}
}

function checkOut()
{
	if (!!BX('coupon'))
		BX('coupon').disabled = true;
	BX("basket_form").submit();
	return true;
}

function enterCoupon()
{
	var newCoupon = BX('coupon');
	if (!!newCoupon && !!newCoupon.value)
		recalcBasketAjax({'coupon' : newCoupon.value});
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

		// set hidden real quantity value (will be used in actual calculation)
		BX("QUANTITY_" + basketId).value = newVal;

		recalcBasketAjax({});
	}
	else
	{
		newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);

		if (newVal != oldVal)
		{
			BX("QUANTITY_INPUT_" + basketId).value = newVal;
			BX("QUANTITY_" + basketId).value = newVal;
			recalcBasketAjax({});
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
		i;
	ratio = parseFloat(ratio);

	if (reminder === 0)
	{
		return result;
	}

	if (ratio !== 0 && ratio != 1)
	{
		for (i = ratio, max = parseFloat(quantity) + parseFloat(ratio); i <= max; i = parseFloat(parseFloat(i) + parseFloat(ratio)).toFixed(2))
		{
			result = i;
		}

	}else if (ratio === 1)
	{
		result = quantity | 0;
	}

	if (parseInt(result, 10) != parseFloat(result))
	{
		bIsQuantityFloat = true;
	}

	result = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(result, 10) : parseFloat(result).toFixed(2);

	return result;
}
/**
 *
 * @param {} params
 */
function recalcBasketAjax(params)
{
	BX.showWait();

	var property_values = {},
		action_var = BX('action_var').value,
		items = BX('basket_items'),
		delayedItems = BX('delayed_items'),
		postData,
		i;

	postData = {
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
	postData[action_var] = 'recalculate';
	if (!!params && typeof params === 'object')
	{
		for (i in params)
		{
			if (params.hasOwnProperty(i))
				postData[i] = params[i];
		}
	}

	if (!!items && items.rows.length > 0)
	{
		for (i = 1; items.rows.length > i; i++)
			postData['QUANTITY_' + items.rows[i].id] = BX('QUANTITY_' + items.rows[i].id).value;
	}

	if (!!delayedItems && delayedItems.rows.length > 0)
	{
		for (i = 1; delayedItems.rows.length > i; i++)
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

function deleteCoupon(e)
{
	var target = BX.proxy_context,
		value;

	if (!!target && target.hasAttribute('data-coupon'))
	{
		value = target.getAttribute('data-coupon');
		if (!!value && value.length > 0)
		{
			recalcBasketAjax({'delete_coupon' : value});
		}
	}
}

BX.ready(function() {
	var sku_props = BX.findChildren(BX('basket_items'), {tagName: 'li', className: 'sku_prop'}, true),
		i,
		couponBlock;
	if (!!sku_props && sku_props.length > 0)
	{
		for (i = 0; sku_props.length > i; i++)
		{
			BX.bind(sku_props[i], 'click', BX.delegate(function(e){ skuPropClickHandler(e);}, this));
		}
	}
	couponBlock = BX('coupons_block');
	if (!!couponBlock)
		BX.bindDelegate(couponBlock, 'click', { 'attribute': 'data-coupon' }, BX.delegate(function(e){deleteCoupon(e); }, this));
});