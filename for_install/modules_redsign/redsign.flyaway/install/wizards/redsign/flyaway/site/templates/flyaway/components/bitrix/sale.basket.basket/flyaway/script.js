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

  if (basketItemId !== null && !!res.BASKET_DATA) {
    newBasketItemId = res.BASKET_ID;
    arItem = res.BASKET_DATA.GRID.ROWS[newBasketItemId];
    var $item = $("tr[id=" + arItem['ID'] + "]");

    /* SKU PROPS  */
    if(arItem.SKU_DATA) {
        $.each(arItem['PROPS'], function(itemPropKey, itemProp) {

            var selectedItemSku = undefined;
            $.each(arItem.SKU_DATA, function(skuPropKey, skuProp) {

                if(skuProp.C0DE = itemProp.CODE) {
                    $.each(skuProp.VALUES, function(skuPropValKey, skuPropVal) {
                        //console.log(skuPropVal, itemProp);
                      if(skuPropVal['NAME'] == itemProp['VALUE']) {
                            selectedItemSku = skuPropVal;
                        }

                    });
                }

            });

            if(selectedItemSku) {
                var $prop = $("tr[id=" + arItem['ID'] + "] .js-sku-prop__" + itemProp.CODE);

                if($prop.data('sku-prop-type') == 'image') {
                    $prop.find(".js-sku_prop").removeClass('active');
                    $prop
                        .find(".js-sku_prop[data-value-id=" + selectedItemSku['XML_ID'] + "]")
                        .addClass('active');
                } else {
                    $prop.find(".js-sku_prop-value").html(selectedItemSku['NAME']);
                }

            }

        });
    }

    /* Name */
    $item.find(".js-item_name").html(arItem['NAME']);;

    /* Picture */
    var imageURL = undefined;
    if (arItem.PREVIEW_PICTURE_SRC.length > 0)
    {
      imageURL = arItem.PREVIEW_PICTURE_SRC;
    }
    else if (arItem.DETAIL_PICTURE_SRC.length > 0)
    {
      imageURL = arItem.DETAIL_PICTURE_SRC;
    }

    if(imageURL) {
      $item.find(".js-item_picture").css('background-image', 'url(' + imageURL + ')');
    }

    /* Price */
    //$item.find(".js-item-price").html(arItem['PRICE_FORMATED']);
  }

	// update product params after recalculation
	if (!!res.BASKET_DATA)
	{
		for (id in res.BASKET_DATA.GRID.ROWS)
		{
			if (res.BASKET_DATA.GRID.ROWS.hasOwnProperty(id))
			{
				var item = res.BASKET_DATA.GRID.ROWS[id];
        var $element = $("tr.js-element[id=" + id + "]");


        $element.find(".js-item-price").html(item.PRICE_FORMATED);

        $element.find(".js-item-sum").html(item.SUM);

				// if the quantity was set by user to 0 or was too much, we need to show corrected quantity value from ajax response
				if (BX('QUANTITY_' + id))
				{
					BX('QUANTITY_INPUT_' + id).value = item.QUANTITY;
					BX('QUANTITY_INPUT_' + id).defaultValue = item.QUANTITY;
					BX('QUANTITY_' + id).value = item.QUANTITY;

          $element.find('.js-quantity-select').each(function(key, value) {
            var $item = $(value);
            if($item.data('redsign.select') && $item.data('redsign.select').getValue() != item.QUANTITY) {
              console.log(id);
              $item.data('redsign.select')
                      .setValue(item.QUANTITY, item.QUANTITY + " " + item.MEASURE_TEXT);
            }
          });

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
  var couponColor = '';

	if (!BX.type.isElementNode(couponBlock))
		return;

	if (oneCoupon.JS_STATUS === 'BAD')
		couponColor = 'red';
	else if (oneCoupon.JS_STATUS === 'APPLYED')
		couponColor = 'green';

  var $couponBlock = $(couponBlock);
  $couponBlock.find("tbody").append(
      $("<tr></tr>")
          .append(
              $("<td></td>")
                  .addClass("personal-panel__coupons-icon")
                  .html('<i class="fa fa-tags"></i')
          )
          .append(
              $("<td></td>")
                  .addClass("personal-panel__coupons-value")
                  .append(
                      $('<input>')
                          .attr('type', 'hidden')
                          .attr('name', 'OLD_COUPON[]')
                          .attr('value', oneCoupon.COUPON)
                  )
                  .append(oneCoupon.COUPON)
           )
           .append(
                $("<td></td>")
                    .addClass("personal-panel__coupons-text")
                    .append(
                        $("<span></span>")
                          .css('color', couponColor)
                          .html(oneCoupon.JS_CHECK_CODE)
                    )
            )
            .append(
                $("<td></td>")
                    .addClass("personal-panel__coupons-remove")
                    .append(
                        $("<a></a>")
                          .attr("href", "javascript:void(0)")
                          .attr('data-coupon', oneCoupon.COUPON)
                          .html('<i class="fa fa-times"></i>')
                    )
            )
  );
}

/**
 * @param {COUPON_LIST : []} res
 */
function couponListUpdate(res)
{
	var couponBlock,
		couponClass,
    couponColor,
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
						couponColor = '';
						if (res.COUPON_LIST[i].JS_STATUS === 'BAD')
							couponClass = 'red';
						else if (res.COUPON_LIST[i].JS_STATUS === 'APPLYED')
							couponClass = 'green';

						BX.adjust(couponsCollection[key], {props: {className: couponClass}});
						BX.adjust(couponsCollection[key].parentNode.nextElementSibling.children, {props: {style: 'color: ' + couponColor}});
						BX.adjust(couponsCollection[key].parentNode.nextElementSibling.children, {html: res.COUPON_LIST[i].JS_CHECK_CODE});
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
						BX.remove(couponsCollection[j].parentNode.parentNode);
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

  var $darkenBlock = $("#basket_items");

  if(params.coupon || params.delete_coupon) {
     $darkenBlock = $(".js-personal-panel");
  }

	rsFlyaway.darken($darkenBlock);

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
		for (i = 0; items.rows.length > i; i++) {
        if(!items.rows[i].id) {
          continue;
        }
        postData['QUANTITY_' + items.rows[i].id] = BX('QUANTITY_' + items.rows[i].id).value;
    }
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
      		rsFlyaway.darken($darkenBlock);
			updateBasketTable(null, result);
		}
	});
}

function clearBasket() {
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

    if (!!items && items.rows.length > 0)
    {
      for (i = 0; items.rows.length > i; i++) {
          if(!items.rows[i].id) {
            continue;
          }
          postData['DELETE_' + items.rows[i].id] = 'Y';
      }
    }

    BX.ajax({
      url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
      method: 'POST',
      data: postData,
      dataType: 'json',
      onsuccess: function(result)
      {
        $("[name=BasketRefresh]").click()
		BX.onCustomEvent('OnBasketChange');
      }
    });
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
		rsFlyaway.darken($("#basket_items"));

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
				rsFlyaway.darken($("#basket_items"));
				updateBasketTable(basketItemId, result);
			}
		});
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


if(BX) {

    BX.oldShowWait = BX.showWait;
    BX.showWait = function(node) {
        if(!node) {
            BX.oldShowWait(node);
            return;
        }


        var $darkenArea = node.ownerDocument ? $(node) :  $("#" + node);
        if($darkenArea.length) {
            rsFlyaway.darken($darkenArea);
        } else {
            BX.oldShowWait(node);
        }
    };

    BX.oldCloseWait = BX.closeWait;
    BX.closeWait = function(node) {

        if(!node) {
            rsFlyaway.darken($(".area2darken"));
            initSelect();
            BX.oldCloseWait();
            return;
        }

        var $darkenArea = node.ownerDocument ? $(node) :  $("#" + node);

        if($darkenArea.length) {
            rsFlyaway.darken($darkenArea);
            initSelect();
        } else {
            BX.oldCloseWait(node);
        }
		BX.onCustomEvent('OnBasketChange');
    };

}

$(document).ready(function() {

    $(document).on("change", ".js-quantity-basket-mobile", function() {
        var $element = $(this);
        $element.parents("tr").find("[id^=QUANTITY_INPUT_]").val($element.val()).change();
    });

    BX.addCustomEvent('onAjaxSuccess', function () {

    });

});
