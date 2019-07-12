
BasketPoolQuantity = function()
{
	this.processing = false;
	this.poolQuantity = {};
	this.updateTimer = null;
	this.currentQuantity = {};

	this.updateQuantity();
};


BasketPoolQuantity.prototype.updateQuantity = function()
{
	var items = BX('basket_items');

	if (!!items && items.rows.length > 0)
	{
		for (var i = 0; items.rows.length > i; i++)
		{
			var itemId = items.rows[i].id;
			this.currentQuantity[itemId] = BX('QUANTITY_' + itemId).value;
		}
	}

};


BasketPoolQuantity.prototype.changeQuantity = function(itemId)
{
	var quantity = BX('QUANTITY_' + itemId).value;
	var isPoolEmpty = this.isPoolEmpty();

	if (this.currentQuantity[itemId] && this.currentQuantity[itemId] != quantity)
	{
		this.poolQuantity[itemId] = this.currentQuantity[itemId] = quantity;
	}

	if (!isPoolEmpty)
	{
		this.enableTimer(true);
	}
	else
	{
		this.trySendPool();
	}
};


BasketPoolQuantity.prototype.trySendPool = function()
{
	if (!this.isPoolEmpty() && !this.isProcessing())
	{
		this.enableTimer(false);
		recalcBasketAjax({});
	}
};

BasketPoolQuantity.prototype.isPoolEmpty = function()
{
	return ( Object.keys(this.poolQuantity).length == 0 );
};

BasketPoolQuantity.prototype.clearPool = function()
{
	this.poolQuantity = {};
};

BasketPoolQuantity.prototype.isProcessing = function()
{
	return (this.processing === true);
};

BasketPoolQuantity.prototype.setProcessing = function(value)
{
	this.processing = (value === true);
};

BasketPoolQuantity.prototype.enableTimer = function(value)
{
	clearTimeout(this.updateTimer);
	if (value === false)
		return;

	this.updateTimer = setTimeout(function(){ basketPoolQuantity.trySendPool(); }, 1500);
};

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
		iArticleColumn = false,
		bShowDiscountColumn = false,
		bShowWeightColumn = false,
		bShowPriceColumn = false,
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
		customColumnVal,
		basketForm = BX('basket_form');

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
		newRow.setAttribute('class', 'table_item product');
		lastRow.parentNode.insertBefore(newRow, origBasketItem.nextSibling);

		if (res.DELETE_ORIGINAL === 'Y')
		{
			origBasketItem.parentNode.removeChild(origBasketItem);
		}

		// fill row with fields' values
		var arColumnHide = ['TYPE', 'PROPS', 'DELAY', 'DELETE', 'DISCOUNT', 'WEIGHT'];

		for (i = 0; i < arColumns.length; i++)
		{
			if (arColumns[i] === 'TYPE') {
				bShowPriceType = true;
			} else if (arColumns[i] === 'PROPS') {
				bShowPropsColumn = true;
			} else if (arColumns[i] === 'DELAY') {
				bShowDelayColumn = true;
			} else if (arColumns[i] === 'DELETE') {
				bShowDeleteColumn = true;
			} else if (arColumns[i] === 'DISCOUNT') {
				bShowDiscountColumn = true;
			} else if (arColumns[i] === 'WEIGHT') {
				bShowWeightColumn = true;
			} else if (arColumns[i].indexOf('PROPERTY_') != -1) {
				if (!!basketJSParams.ARTICLE_PROP && Object.keys(basketJSParams.ARTICLE_PROP).length> 0) { // IE9+
					for (var key in basketJSParams.ARTICLE_PROP) {
						if (arColumns[i] == 'PROPERTY_'+ basketJSParams.ARTICLE_PROP[key]) {
							arColumnHide.push(arColumns[i]);
							iArticleColumn = i;
							break;
						}
					}
				}
			}
		}

		columnLoop: for (i = 0; i < arColumns.length; i++)
		{
      for (key in arColumnHide) {
				if(arColumnHide[key] == arColumns[i]) {
					continue columnLoop;
				}
			}

			switch (arColumns[i])
			{
				case 'TYPE':
				case 'PROPS':
				case 'DELAY':
				case 'DELETE':
				case 'DISCOUNT':
				case 'WEIGHT':
					break;
				case 'NAME':
/*
          oCellName = newRow.insertCell(-1);
          imageURL = '';
          cellNameHTML = '';

          oCellName.setAttribute('class', 'table_item__pic');

          if (arItem.PREVIEW_PICTURE_SRC.length > 0)
          {
            imageURL = arItem.PREVIEW_PICTURE_SRC;
          }
          else if (arItem.DETAIL_PICTURE_SRC.length > 0)
          {
            imageURL = arItem.DETAIL_PICTURE_SRC;
          } else {
            imageURL = basketJSParams.TEMPLATE_FOLDER + '/assets/img/noimg.png';
          }

          if (arItem.DETAIL_PAGE_URL.length > 0) {
            cellNameHTML = '<a href="' + arItem.DETAIL_PAGE_URL + '">\
              <img class="table_item__img" src="' + imageURL + '" alt="' + arItem['NAME'] + '" title="' + arItem['NAME'] + '">\
            </a>';
          } else {
            cellNameHTML = '<img class="table_item__img" src="' + imageURL + '" alt="' + arItem['NAME'] + '" title="' + arItem['NAME'] + '">';
          }

          // first <td> - image and brand

          if (arItem.BRAND && arItem.BRAND.length > 0)
          {
            cellNameHTML += '<div class="bx_ordercart_brand">\
              <img alt="" src="' + arItem.BRAND + '"/>\
            </div>';
          }

          oCellName.innerHTML = cellNameHTML;
*/
          oCellItem = newRow.insertCell(-1);
          oCellItem.setAttribute('class', 'table_item__item');
          cellItemHTML = '';

          if (arItem.PREVIEW_PICTURE_SRC.length > 0) {
            imageURL = arItem.PREVIEW_PICTURE_SRC;
          } else if (arItem.DETAIL_PICTURE_SRC.length > 0) {
            imageURL = arItem.DETAIL_PICTURE_SRC;
          } else {
            imageURL = basketJSParams.TEMPLATE_FOLDER + '/assets/img/noimg.png';
          }

          cellItemHTML += '<div class="table_item__pic">';

          if (arItem.DETAIL_PAGE_URL.length > 0) {
            cellItemHTML += '<a href="' + arItem.DETAIL_PAGE_URL + '">\
              <img class="table_item__img" src="' + imageURL + '" alt="' + arItem['NAME'] + '" title="' + arItem['NAME'] + '">\
            </a>';
          } else {
            cellItemHTML += '<img class="table_item__img" src="' + imageURL + '" alt="' + arItem['NAME'] + '" title="' + arItem['NAME'] + '">';
          }

          cellItemHTML += '</div>';

					cellItemHTML += '<div class="table_item__head">\
            <h4 class="table_item__name">';

					if (arItem['DETAIL_PAGE_URL'].length > 0) {
						cellItemHTML += '<a href="' + arItem['DETAIL_PAGE_URL'] + '">' + arItem['NAME'] + '</a>';
					} else {
						cellItemHTML += arItem['NAME'];
					}
          cellItemHTML += '</h4>';

					if (iArticleColumn !== false) {
						for (var key in basketJSParams.ARTICLE_PROP) {
							if (basketJSParams.ARTICLE_PROP[key] != '') {
								cellItemHTML += '<div class="product__article">' +
									res.BASKET_DATA.GRID.HEADERS[iArticleColumn]['name'] + ': ' +
									arItem[res.BASKET_DATA.GRID.HEADERS[iArticleColumn]['id']] +
							  '</div>';
								break;
							}
						}
					}

          cellItemHTML += '</div>\
            <dl class="table_item__props dl-list">';

          columnLoop2: for (j = 0; j < arColumns.length; j++) {
            for (key in arColumnHide) {
              if(arColumnHide[key] == arColumns[j]) {
                continue columnLoop2;
              }
            }

            switch (arColumns[j])
            {
              case 'TYPE':
              case 'PROPS':
              case 'DELAY':
              case 'DELETE':
              case 'DISCOUNT':
              case 'WEIGHT':
              case 'NAME':
              case 'QUANTITY':
              case 'PRICE':
              case 'SUM':
                break;
              default:
                var sColumnVal;
                if (arColumns[j].indexOf('PROPERTY_') != -1) {
                  sColumnVal = arItem[arColumns[j] + '_VALUE'];
                } else {
                  sColumnVal = arItem[arColumns[j]];
                }

                if (sColumnVal != undefined) {
                  cellItemHTML += '<dt>' + basketJSParams.HEADERS[j] + ':</dt> \
                    <dd>'+ sColumnVal +'</dd>';
                }
                break;
            }
          }

          if (bShowWeightColumn && arItem['WEIGHT_FORMATED'] > 0) {
            cellTotalHTML += '<dt>' + basketJSParams['SALE_WEIGHT'] + ':</dt> <dd>' + arItem['WEIGHT_FORMATED'] + '</dd>';
          }

          if (bShowPropsColumn) {
            for (var i in arItem['PROPS']) {
              var bSkip = false;
              for (var j in arItem['SKU_DATA']) {
                if (arItem['SKU_DATA'][j]['CODE'] == arItem['PROPS'][i]['CODE']) {
                  bSkip = true;
                  break;
                }
              }
              if (bSkip) {
                continue;
              }
              cellItemHTML += '<dt>'+ arItem['PROPS'][i]['NAME'] +':</dt>\
              <dd>'+ arItem['PROPS'][i]['VALUE'] +'</dd>';
            }
          }

          cellItemHTML += '</dl>';

          if (arItem.SKU_DATA)
          {
            cellItemHTML += '<div class="table_item__offer_props">';
            for (propId in arItem.SKU_DATA)
            {
              if (arItem.SKU_DATA.hasOwnProperty(propId))
              {
                arProp = arItem.SKU_DATA[propId];
                var bIsColor = false,
                  bIsBtn = false,
                  sOfferPropClass= 'offer_prop';

                if (BX.util.in_array(arProp['CODE'], basketJSParams['OFFER_TREE_COLOR_PROPS']))
                {
                  bIsColor = true;
                  sOfferPropClass += ' offer_prop-color';
                }
                else if (BX.util.in_array(arProp['CODE'], basketJSParams['OFFER_TREE_BTN_PROPS']))
                {
                  bIsBtn = true;
                  sOfferPropClass += ' offer_prop-btn';
                }
                else
                {
                  for (valId in arProp['VALUES'])
                  {
                    arVal = arProp['VALUES'][valId];
                    if (arVal['PICT'] !== false)
                    {
                      bIsColor = true;
                      break;
                    }
                  }
                }

                if (bIsColor || bIsBtn) {

                  cellItemHTML += '<div class="'+ sOfferPropClass +' js-offer_prop" data-code="' + arProp['CODE'] + '">\
                    <div class="offer_prop-name">' + arProp['NAME'] + ':</div>\
                  <ul class="offer_prop__values clearfix" id="prop_' + arProp['CODE'] + '_' + arItem['ID'] + '">';

                  var firstVal = false;
                  for (valueId in arProp['VALUES'])
                  {
                      arSkuValue = arProp['VALUES'][valueId];
                      var sOfferPropValueClass = 'offer_prop__value';
                      // get current selected item
                      for (k = 0; k < arItem['PROPS'].length; k++)
                      {
                          arItemProp = arItem['PROPS'][k];

                          if (arItemProp['CODE'] === arProp['CODE'])
                          {
                              if (arItemProp['VALUE'] === arSkuValue['NAME'] || arItemProp['VALUE'] === arSkuValue['XML_ID'])
                              {
                                  sOfferPropValueClass += ' checked';
                                  firstVal = arSkuValue;
                              }
                          }
                      }

                      cellItemHTML += '<li class="'+ sOfferPropValueClass +'"\
                        data-value-id="' + (arProp['TYPE'] == 'S' && arProp['USER_TYPE'] == 'directory' ? arSkuValue['XML_ID'] : arSkuValue['NAME']) +'"\
                        data-element="'+ arItem['ID'] + '"\
                        data-property="'+ arProp['CODE'] + '"\
                      >';

                      if (bIsColor) {
                        var sOfferPropIcon = '';
                        if (!!arSkuValue['PICT']) {
                          sOfferPropIcon = 'background-image:url(' + arSkuValue['PICT']['SRC'] + ')';
                        } else if(!!basketJSParams['COLORS_TABLE'][arSkuValue['NAME'].toLowerCase()]) {
                          sOfferPropIcon = 'background-color:'+ basketJSParams['COLORS_TABLE'][arSkuValue['NAME'].toLowerCase()]['RGB'];
                        }

                        cellItemHTML += '<span class="offer_prop__icon">\
                          <span class="offer_prop__img" title="' + arSkuValue['NAME'] + '" style="'+ sOfferPropIcon +'"></span>\
                        </span>';

                      } else {
                          cellItemHTML += arSkuValue['NAME'];
                      }

                      cellItemHTML += '</li>';
                  }

                  cellItemHTML += '</ul>\
                    </div>';
                } else {
                  var dropdownId = 'cart_item_'+ arProp['CODE'] + '_' + arItem['ID'];
                  cellItemHTML += '<div class="offer_prop js-offer_prop" data-code="' + arProp['CODE'] + '">\
                      <div class="offer_prop-name">' + arProp['NAME'] + ':</div>\
                      <div class="dropdown select">\
                          <ul class="offer_prop__values dropdown-menu" aria-labelledby="' + dropdownId +'" id="prop_' + arProp['CODE'] + '_' + arItem['ID'] + '">';
                          var firstVal = false;
                          for (valueId in arProp['VALUES'])
                          {
                              arSkuValue = arProp['VALUES'][valueId];
                              var sOfferPropValueClass = 'offer_prop__value';
                              // get current selected item
                              for (k = 0; k < arItem['PROPS'].length; k++)
                              {
                                  arItemProp = arItem['PROPS'][k];

                                  if (arItemProp['CODE'] === arProp['CODE'])
                                  {
                                      if (arItemProp['VALUE'] === arSkuValue['NAME'] || arItemProp['VALUE'] === arSkuValue['XML_ID'])
                                      {
                                          sOfferPropValueClass += ' checked';
                                          firstVal = arSkuValue;
                                      }
                                  }
                              }
                              cellItemHTML += '<li class="'+ sOfferPropValueClass +'"\
                                   data-value-id="' + (arProp['TYPE'] == 'S' && arProp['USER_TYPE'] == 'directory' ? arSkuValue['XML_ID'] : arSkuValue['NAME']) +'"\
                                    data-element="'+ arItem['ID'] + '"\
                                    data-property="'+ arProp['CODE'] + '"\
                                  >\
                                <a>'+ arSkuValue['NAME'] +'</a>\
                              </li>';

                              cellItemHTML += '</li>';
                          }
                  cellItemHTML += '</ul>\
                      <div class="dropdown-toggle select__btn" id="'+ dropdownId + '" data-toggle="dropdown" aria-expanded="true" aria-haspopup="true" role="button">\
                        <svg class="select__icon icon-svg"><use xlink:href="#svg-down-round"></use></svg>' + firstVal['NAME'] +
                      '</div>\
                    </div>\
                  </div>';
                }
              }
            }
            cellItemHTML += '</div>';
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

          oCellQuantity.setAttribute('class', 'table_item__quantity');
					//oCellQuantityHTML += '<dt>' + basketJSParams.HEADERS[i] + '</dt>';

					oCellQuantityHTML += '<span class="quantity">';

					if (ratio != 0 && ratio != '') // if not Set parent, show quantity control
					{
						oCellQuantityHTML += '<i class="quantity__minus js-basket-minus"></i>';
					}

					oCellQuantityHTML += '<input type="number" size="3"\
						class="quantity__input js-quantity"\
						id="QUANTITY_INPUT_' + arItem['ID'] + '"\
						name="QUANTITY_INPUT_' + arItem['ID'] + '"\
						size="2" min="0" ' + max + 'step=' + ratio + '\
						value="' + arItem['QUANTITY'] + '"\
						onchange="updateQuantity(\'QUANTITY_INPUT_' + arItem['ID'] + '\',\'' + arItem['ID'] + '\', ' + ratio + ',' + bUseFloatQuantity + ')"\
					/>';

					if (ratio != 0 && ratio != '') // if not Set parent, show quantity control
					{
						oCellQuantityHTML += '<i class="quantity__plus js-basket-plus"></i>';
					}

					oCellQuantityHTML += '<input type="hidden" id="QUANTITY_' + arItem['ID'] + '" name="QUANTITY_' + arItem['ID'] + '" value="' + arItem['QUANTITY'] + '" />\
						</span>';

          if (arItem.hasOwnProperty('MEASURE_TEXT') && arItem['MEASURE_TEXT'].length > 0)
					{
						oCellQuantityHTML += ' <span class="js-measure">' + arItem['MEASURE_TEXT'] + '</span>';
					}

					oCellQuantity.innerHTML = oCellQuantityHTML;

					if (isUpdateQuantity)
					{
						updateQuantity('QUANTITY_INPUT_' + arItem['ID'], arItem['ID'], ratio, bUseFloatQuantity);
					}
					break;

				case 'PRICE':
					oCellPrice = newRow.insertCell(-1);
					oCellPriceHTML = '';

					oCellPrice.setAttribute('class', 'table_item__price price');
					fullPrice = (arItem['FULL_PRICE_FORMATED'] != arItem['PRICE_FORMATED']) ? arItem['FULL_PRICE_FORMATED'] : '';
/*
					if(bShowPriceType && arItem['NOTES'].length > 0)
					{
						oCellPriceHTML += '<span class="type_price_value">' + arItem['NOTES'] + '</span>:';
					}

					else
					{
						oCellPriceHTML += '<span>' + basketJSParams.HEADERS[i] + '</span>:';
					}
*/
					oCellPriceHTML += ' <div class="price__pdv' + (parseFloat(arItem['DISCOUNT_PRICE_PERCENT'] > 0) ? ' disc' : '') + '" id="current_price_' + arItem['ID'] + '">' + arItem['PRICE_FORMATED'] + '</div>\
						<div class="price__pv" id="old_price_' + arItem['ID'] + '">' + fullPrice + '</div>';
					if (bShowDiscountColumn && 0 < parseFloat(arItem['DISCOUNT_PRICE_PERCENT']))
					{
						oCellPriceHTML += '<div class="price__pdd">' + basketJSParams['SALE_PRICE_DIFF'] + ': \
							<span id="discount_value_' + arItem['ID'] + '">' + arItem['DISCOUNT_PRICE_PERCENT_FORMATED'] + '</span>\
						</div>';
					}

          oCellPrice.innerHTML = oCellPriceHTML;
					break;
/*
				case 'DISCOUNT':
					oCellDiscount = newRow.insertCell(-1);
					oCellDiscount.innerHTML = '<span>' + getColumnName(res, arColumns[i]) + ':</span>';
					oCellDiscount.innerHTML += '<div id="discount_value_' + arItem['ID'] + '">' + arItem['DISCOUNT_PRICE_PERCENT_FORMATED'] + '</div>';
					break;
				case 'WEIGHT':
					oCellWeight = newRow.insertCell(-1);
					oCellWeight.innerHTML = '<span class="rs_cart-cn">' + getColumnName(res, arColumns[i]) + ':</span>';
					oCellWeight.innerHTML += arItem['WEIGHT_FORMATED'];
					break;
*/
				case 'SUM':
					oCellCustom = newRow.insertCell(-1);
					customColumnVal = '';

          oCellPrice.setAttribute('class', 'table_item__sum price');
					customColumnVal += '<div class="price__pdv" id="sum_' + arItem['ID'] + '">';
          if (typeof(arItem[arColumns[i]]) != undefined)
					{
						customColumnVal += arItem[arColumns[i]];
					}
          customColumnVal += '</div>';

					oCellCustom.innerHTML += customColumnVal;
					break;
				default:
					break;
			}
		}

		oCellControl = newRow.insertCell(-1);
		oCellControl.setAttribute('class', '');
		oCellControlHTML = '';

		if (bShowDeleteColumn)
		{
			oCellControlHTML += '<a href="' + basketJSParams['DELETE_URL'].replace('#ID#', arItem['ID']) +'">\
        <svg class="btn__icon icon-close icon-svg"><use xlink:href="#svg-close"></use></svg>' + basketJSParams['SALE_DELETE'] +
      '</a><br/>';
		}
		if (bShowDelayColumn)
		{
			oCellControlHTML += '<a href="' + basketJSParams['DELAY_URL'].replace('#ID#', arItem['ID']) + '">\
        <svg class="btn__icon icon-lock icon-svg"><use xlink:href="#svg-lock"></use></svg>' + basketJSParams['SALE_DELAY'] +
      '</a>';
		}

		oCellControl.innerHTML = oCellControlHTML;

		// set sku props click handler
		var sku_props = BX.findChildren(newRow, {className: 'offer_prop__value'}, true);
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
					BX('discount_value_' + id).innerHTML = basketJSParams['SALE_DISCOUNT'] + ': ' + item.DISCOUNT_PRICE_PERCENT_FORMATED;

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
			warningText += '<font class="errortext">' + res['WARNING_MESSAGE'][i] + '</font><br/>';

    var oWarning = BX('warning_message');
    if (warningText == '')
    {
      BX.hide(oWarning);
    }
    else
    {
      BX.show(oWarning);
    }
    oWarning.innerHTML = warningText;
	}

	// update total basket values
	if (!!res.BASKET_DATA)
	{
		var allWeight_FORMATED = BX.findChildren(basketForm, { class: 'allWeight_FORMATED'}, true);
		if (!!allWeight_FORMATED && allWeight_FORMATED.length > 0)
		{
			for (var i in allWeight_FORMATED)
			{
				allWeight_FORMATED[i].innerHTML = res['BASKET_DATA']['allWeight_FORMATED'].replace(/\s/g, '&nbsp;');
			}
		}

		var allSum_wVAT_FORMATED = BX.findChildren(basketForm, { class: 'allSum_wVAT_FORMATED'}, true);
		if (!!allSum_wVAT_FORMATED && allSum_wVAT_FORMATED.length > 0)
		{
			for (var i in allSum_wVAT_FORMATED)
			{
				allSum_wVAT_FORMATED[i].innerHTML = res['BASKET_DATA']['allSum_wVAT_FORMATED'].replace(/\s/g, '&nbsp;');
			}
		}

		var allVATSum_FORMATED = BX.findChildren(basketForm, { class: 'allVATSum_FORMATED'}, true);
		if (!!allVATSum_FORMATED && allVATSum_FORMATED.length > 0)
		{
			for (var i in allVATSum_FORMATED)
			{
				allVATSum_FORMATED[i].innerHTML = res['BASKET_DATA']['allVATSum_FORMATED'].replace(/\s/g, '&nbsp;');
			}
		}

		var allSum_FORMATED = BX.findChildren(basketForm, { class: 'allSum_FORMATED'}, true);
		if (!!allSum_FORMATED && allSum_FORMATED.length > 0)
		{
			for (var i in allSum_FORMATED)
			{
				allSum_FORMATED[i].innerHTML = res['BASKET_DATA']['allSum_FORMATED'].replace(/\s/g, '&nbsp;');
			}
		}

		var PRICE_WITHOUT_DISCOUNT = BX.findChildren(basketForm, { class: 'PRICE_WITHOUT_DISCOUNT'}, true);
		if (!!PRICE_WITHOUT_DISCOUNT && PRICE_WITHOUT_DISCOUNT.length > 0)
		{
			for (var i in PRICE_WITHOUT_DISCOUNT)
			{
				PRICE_WITHOUT_DISCOUNT[i].innerHTML = (res['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'] != res['BASKET_DATA']['allSum_FORMATED']) ? res['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'].replace(/\s/g, '&nbsp;') : '';
			}
		}



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
				className: 'coupon'
			},
			children: [
				BX.create(
					'span',
					{
						props: {
							className:  'coupon__del ' + couponClass
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
							className: 'l-context'
						},
						children: [
							BX.create(
								'input',
								{
									props: {
                    className:  'form-control coupon__input ' + couponClass,
										type: 'text',
										value: oneCoupon.COUPON,
										name: 'OLD_COUPON[]'
									},
									attrs: {
										disabled: true,
										readonly: true
									}
								}
							)
						]
					}
				),
				BX.create(
					'div',
					{
						props: {
							className: 'coupon__note'
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
		key,
		basketForm = BX('basket_form');

	if (!!res && typeof res !== 'object')
	{
		return;
	}


	couponBlock = BX.findChildren(basketForm, { class: 'cart__coupons'}, true);

	if (!!couponBlock && couponBlock.length > 0)
	{
		if (!!res.COUPON_LIST && BX.type.isArray(res.COUPON_LIST))
		{
			for (var iCouponBlock in couponBlock)
			{
				fieldCoupon = BX.findChildren(couponBlock[iCouponBlock], { class: 'coupon__input'}, true);

                if (!!fieldCoupon)
                {
                    fieldCoupon.value = '';
                }
                couponsCollection = BX.findChildren(couponBlock[iCouponBlock], { tagName: 'input', property: { name: 'OLD_COUPON[]' } }, true);

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
                            BX.adjust(couponsCollection[key], {props: {className: 'form-control coupon__input ' +couponClass}});
                            BX.adjust(couponsCollection[key].parentNode.previousSibling, {props: {className: 'coupon__del ' + couponClass}});
                            BX.adjust(couponsCollection[key].parentNode.nextSibling, {html: res.COUPON_LIST[i].JS_CHECK_CODE});
                        }
                        else
                        {
                            couponCreate(couponBlock[iCouponBlock], res.COUPON_LIST[i]);
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
                        couponCreate(couponBlock[iCouponBlock], res.COUPON_LIST[i]);
                    }
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
		if (BX.hasClass(target, 'checked'))
		{
			BX.closeWait();
			return;
		}

		// get other basket item props to get full unique set of props of the new product
		all_sku_props = BX.findChildren(BX(basketItemId), {tagName: 'ul', className: 'offer_prop__values'}, true);
		if (!!all_sku_props && all_sku_props.length > 0)
		{
			for (i = 0; all_sku_props.length > i; i++)
			{
				if (all_sku_props[i].id !== 'prop_' + property + '_' + basketItemId)
				{
					sku_prop_value = BX.findChildren(BX(all_sku_props[i].id), {tagName: 'li', className: 'checked'}, true);
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

function updateBasket()
{
	recalcBasketAjax({});
}

function enterCoupon(control)
{
	//var newCoupon = BX('coupon');
	var newCoupon = control;
	if (!!newCoupon && !!newCoupon.value)
		recalcBasketAjax({'coupon' : newCoupon.value});
}

// check if quantity is valid
// and update values of both controls (text input field for PC and mobile quantity select) simultaneously
function updateQuantity(controlId, basketId, ratio, bUseFloatQuantity)
{
	var oldVal = BX(controlId).defaultValue,
		newVal = parseFloat(BX(controlId).value) || 0,
		bIsCorrectQuantityForRatio = false,
		autoCalculate = ((BX("auto_calculation") && BX("auto_calculation").value == "Y") || !BX("auto_calculation"));

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

	newVal = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(newVal) : parseFloat(newVal).toFixed(4);
	newVal = correctQuantity(newVal);

	if (bIsCorrectQuantityForRatio)
	{
		BX(controlId).defaultValue = newVal;

		BX("QUANTITY_INPUT_" + basketId).value = newVal;

		// set hidden real quantity value (will be used in actual calculation)
		BX("QUANTITY_" + basketId).value = newVal;

		if (autoCalculate)
		{
			basketPoolQuantity.changeQuantity(basketId);
		}
	}
	else
	{
		newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);
		newVal = correctQuantity(newVal);

		if (newVal != oldVal)
		{
			BX("QUANTITY_INPUT_" + basketId).value = newVal;
			BX("QUANTITY_" + basketId).value = newVal;

			if (autoCalculate)
			{
				basketPoolQuantity.changeQuantity(basketId);
			}
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
		newVal = parseFloat(newVal).toFixed(4);
	}
	newVal = correctQuantity(newVal);

	if (ratio > 0 && newVal < ratio)
	{
		newVal = ratio;
	}

	if (!bUseFloatQuantity && newVal != newVal.toFixed(4))
	{
		newVal = parseFloat(newVal).toFixed(4);
	}

	newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);
	newVal = correctQuantity(newVal);

	BX("QUANTITY_INPUT_" + basketId).value = newVal;
	BX("QUANTITY_INPUT_" + basketId).defaultValue = newVal;

	updateQuantity('QUANTITY_INPUT_' + basketId, basketId, ratio, bUseFloatQuantity);
}

function getCorrectRatioQuantity(quantity, ratio, bUseFloatQuantity)
{
	var newValInt = quantity * 10000,
		ratioInt = ratio * 10000,
		reminder = (quantity / ratio - ((quantity / ratio).toFixed(0))).toFixed(6),
		result = quantity,
		bIsQuantityFloat = false,
		i;
	ratio = parseFloat(ratio);

	if (reminder == 0)
	{
		return result;
	}

	if (ratio !== 0 && ratio != 1)
	{
		for (i = ratio, max = parseFloat(quantity) + parseFloat(ratio); i <= max; i = parseFloat(parseFloat(i) + parseFloat(ratio)).toFixed(4))
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

	result = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(result, 10) : parseFloat(result).toFixed(4);
	result = correctQuantity(result);
	return result;
}

function correctQuantity(quantity)
{
	return parseFloat((quantity * 1).toString());
}


/**
 *
 * @param {} params
 */
function recalcBasketAjax(params)
{
	if (basketPoolQuantity.isProcessing())
	{
		return false;
	}

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
		for (i = 0; items.rows.length > i; i++)
			postData['QUANTITY_' + items.rows[i].id] = BX('QUANTITY_' + items.rows[i].id).value;
	}

	if (!!delayedItems && delayedItems.rows.length > 0)
	{
		for (i = 0; delayedItems.rows.length > i; i++)
			postData['DELAY_' + delayedItems.rows[i].id] = 'Y';
	}

	basketPoolQuantity.setProcessing(true);
	basketPoolQuantity.clearPool();

	BX.ajax({
		url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
		method: 'POST',
		data: postData,
		dataType: 'json',
		onsuccess: function(result)
		{
			BX.closeWait();
			basketPoolQuantity.setProcessing(false);

			if(params.coupon)
			{
				//hello, gifts!
				if(!!result && !!result.BASKET_DATA && !!result.BASKET_DATA.NEED_TO_RELOAD_FOR_GETTING_GIFTS)
				{
					BX.reload();
				}
			}

			if (basketPoolQuantity.isPoolEmpty())
			{
				updateBasketTable(null, result);
				basketPoolQuantity.updateQuantity();
			}
			else
			{
				basketPoolQuantity.enableTimer(true);
			}
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

	basketPoolQuantity = new BasketPoolQuantity();

	var sku_props = BX.findChildren(BX('basket_items'), {className: 'offer_prop__value'}, true),
		i,
		couponBlock,
		basketForm = BX('basket_form');

	if (!!sku_props && sku_props.length > 0)
	{
		for (i = 0; sku_props.length > i; i++)
		{
			BX.bind(sku_props[i], 'click', BX.delegate(function(e){ skuPropClickHandler(e);}, this));
		}
	}
	couponBlock = BX.findChildren(basketForm, { class: 'cart__coupons'}, true);

	if (!!couponBlock)
		for (var iCouponBlock in couponBlock)
			BX.bindDelegate(couponBlock[iCouponBlock], 'click', { 'attribute': 'data-coupon' }, BX.delegate(function(e){deleteCoupon(e); }, this));

	if (basketJSParams['EVENT_ONCHANGE_ON_START'] && basketJSParams['EVENT_ONCHANGE_ON_START'] == "Y")
	{
		BX.onCustomEvent('OnBasketChange');
	}
});