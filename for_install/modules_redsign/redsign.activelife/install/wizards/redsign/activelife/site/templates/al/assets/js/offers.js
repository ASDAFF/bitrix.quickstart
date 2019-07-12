appSLine.offerPropChangedTimeout = 0;

appSLine.catalogElementSetPrice = function ($product, price) {

  var $pricePDV = $product.find('.js-price_pdv-' + price.PRICE_ID);
  if ($pricePDV) {
    if (parseFloat(price.DISCOUNT_DIFF) > 0) {
      $pricePDV.closest('.price').addClass('price-disc');
      //$pricePDV.prev('.rs_price-name').show();
    } else {
      $pricePDV.closest('.price').removeClass('price-disc');
      //$pricePDV.prev('.rs_price-name').hide();
    }
    $pricePDV.html(price.PRINT_DISCOUNT_VALUE);
  }

  var $pricePDD = $product.find('.js-price_pdd-' + price.PRICE_ID);
  if ($pricePDD) {
    if (parseFloat(price.DISCOUNT_DIFF > 0)) {
      $pricePDD.html(price.PRINT_DISCOUNT);//.parent().show();
    } else {
      $pricePDD.html('');//.parent().hide();
    }
  }

  var $pricePV = $product.find('.js-price_pv-' + price.PRICE_ID);
  if ($pricePV) {
    if (parseFloat(price.DISCOUNT_DIFF) > 0) {
      $pricePV.html(price.PRINT_VALUE);//.parent().show();
    } else {
      $pricePV.html('');//.parent().hide();
    }
  }
}

appSLine.catalogElementSetPriceMatrix = function ($product, matrix) {

  var iQuantity = $product.find('.js-quantity').val();

  for (var row in matrix.ROWS) {

    if (
        (matrix.ROWS[row].QUANTITY_FROM == 0 || matrix.ROWS[row].QUANTITY_FROM <= iQuantity) &&
        (matrix.ROWS[row].QUANTITY_TO == 0 || matrix.ROWS[row].QUANTITY_TO >= iQuantity)
    ) {
      for (var col in matrix.COLS) {
        if (!!matrix.MATRIX[col][row]) {

          this.catalogElementSetPrice($product, {
            PRICE_ID: matrix.COLS[col].ID,
            PRINT_DISCOUNT_VALUE: matrix.MATRIX[col][row].PRINT_DISCOUNT_VALUE,
            PRINT_DISCOUNT: matrix.MATRIX[col][row].PRINT_DISCOUNT_DIFF,
            PRINT_VALUE: matrix.MATRIX[col][row].PRINT_VALUE,
            DISCOUNT_DIFF: matrix.MATRIX[col][row].DISCOUNT_DIFF,
          });
        }
      }
      break;
    }
  }
}

appSLine.offerPropChanged = function ($option) {

  var $product = $option.closest('.js-product'),
      iProductID = $product.data('product-id'),
      $offerProp = $option.closest('.js-offer_prop'),
      CURRENT_PROP_CODE = $offerProp.data('code'),
      value = $option.data('value');

  if (appSLine.offers[iProductID] && !$option.hasClass('disabled')) {
    // change styles
    //$offerProp.removeClass('opened').addClass('closed');

    $offerProp.find('.offer_prop__value').removeClass('checked');
    $option.addClass('checked');
    $offerProp.find('.offer_prop__checked').text($option.text());
    
    //$offerProp.find('.dropdown')
    

    var NEXT_PROP_CODE = '',
        PROP_CODE = '',
        arCurrentValues = new Object();

    for (var index in appSLine.offers[iProductID].SORT_PROPS) {
      PROP_CODE = appSLine.offers[iProductID].SORT_PROPS[index];
      arCurrentValues[PROP_CODE] = $product.find('.offer_prop[data-code="' + PROP_CODE + '"]').find('.offer_prop__value.checked').data('value');

      // save next prop_code
      if (PROP_CODE == CURRENT_PROP_CODE) {
        if (appSLine.offers[iProductID].SORT_PROPS[parseInt(index) + 1]) {
          NEXT_PROP_CODE = appSLine.offers[iProductID].SORT_PROPS[parseInt(index) + 1];
        } else {
          NEXT_PROP_CODE = false;
        }
        break;
      }
    }

    // get enabled values for next property
    if (NEXT_PROP_CODE) {

      var allPropTrue1 = true,
          arNextEnabledProps = new Array();

      for (var offer_id in appSLine.offers[iProductID].OFFERS) {
        allPropTrue1 = true;
        offer_loop:
        for (var sPropCode in arCurrentValues) {
          if (arCurrentValues[sPropCode] != appSLine.offers[iProductID].OFFERS[offer_id].PROPERTIES[sPropCode]) {
            allPropTrue1 = false;
            break offer_loop;
          }
        }

        if (allPropTrue1) {
          arNextEnabledProps.push(appSLine.offers[iProductID].OFFERS[offer_id].PROPERTIES[NEXT_PROP_CODE]);
        }
      }

      // disable and enable values for NEXT_PROP_CODE
      var $nextProp = $product.find('.offer_prop[data-code="' + NEXT_PROP_CODE + '"]');
      $nextProp.find('.offer_prop__value').each(function(i) {
        var $option = $(this),
            emptyInEnabled = true;

        for (var index in arNextEnabledProps) {
          if ($option.data('value') == arNextEnabledProps[index]) {
            emptyInEnabled = false;
            break;
          }
        }

        $option.addClass('disabled');
        if (!emptyInEnabled) {
          $option.removeClass('disabled');
        }
      });

      // call itself
      if (!$nextProp.find('.offer_prop__value.checked').hasClass('disabled')) {
        $nextOption = $nextProp.find('.offer_prop__value.checked');
      } else {
        $nextOption = $nextProp.find('.offer_prop__value:not(.disabled):first');
      }
      this.offerPropChanged($nextOption);
    } else {
      this.catalogElementChangeOffer($product);
    }
  }
}

appSLine.catalogElementChangeOffer = function($product) {

  var iProductID = $product.data('product-id'),
      arProduct = appSLine.offers[iProductID];
  if (!!arProduct) {

    // get all checked values
    var arrFullChosed = {};
    $product.find('.offer_prop__value.checked').each(function(index1) {
      var $option = $(this),
          code = $option.closest('.js-offer_prop').data('code'),
          value = $option.data('value');
      arrFullChosed[code] = value;
    });

    // get offer_id (key=ID)
    var iOfferID = 0,
        all_prop_true2 = true;
    for (var offer_id in arProduct.OFFERS) {
      all_prop_true2 = true;
      for (var pCode in arrFullChosed) {
        if (arrFullChosed[pCode] != arProduct.OFFERS[offer_id].PROPERTIES[pCode]) {
          all_prop_true2 = false;
          break;
        }
      }
      if (all_prop_true2) {
        iOfferID = offer_id;
        break;
      }
    }

    if (arProduct.OFFERS[iOfferID] == undefined) {
        return false
    }

    var arOffer = arProduct.OFFERS[iOfferID];
    $product.data('offer-id', iOfferID);

    $product.find('.js-product_id').val(iOfferID);

    if (arOffer.DETAIL_PAGE_URL != undefined) {
      $product.find(
        '.js-product__name,\
        .catalog_item__detail,\
        .catalog_item__zoom,\
        .catalog_item__pic'
      ).attr('href', arOffer.DETAIL_PAGE_URL);
    }

    // set image
    if (arOffer.IMAGES != undefined) {
      var $slider = $product.find('.picbox__carousel'),
          $sliderAPI = $slider.data('owl.carousel');


      if ($slider.length > 0) {

        var smartSpeed = $sliderAPI.settings.smartSpeed,
            sSliderHTML = '';

        for (var i in arOffer.IMAGES) {

          if (i != 'p') {
            var sItemHTML = '<a class="picbox__canvas js_picture_glass" data-offer-id="'+ iOfferID +'" data-dot="<img class=\'owl-preview\' src=\''+ arOffer.IMAGES[i].smaller +'\'>">\
                <img class="picbox__img" src="'+ arOffer.IMAGES[i].big +'" data-large="'+ arOffer.IMAGES[i].original +'"/>\
              </a>';
            if (i == 'd') {
              sSliderHTML = sItemHTML + sSliderHTML;
            } else {
              sSliderHTML += sItemHTML;
            }
          }
        }

        for (var i in $sliderAPI._items) {
          var $slide = $sliderAPI._items[i].children('a'); // no noimg
          if ($slide.length == 1 && !$slide.attr('data-offer-id')) {
            sSliderHTML += $sliderAPI._items[i].html();
          }
        }

        $sliderAPI._plugins.navigation._templates = [];
        $sliderAPI._plugins.navigation._controls.$absolute.html('');
        $sliderAPI.settings.smartSpeed = 0;
        $slider.trigger('replace.owl.carousel', [sSliderHTML]);

        setTimeout(function() {
          $slider.trigger('refresh.owl.carousel').trigger('to.owl.carousel', [0]);
        }, 250);

        $sliderAPI.settings.smartSpeed = smartSpeed;

      } else {
        var key = ['p', 'd', 0];
        for (var i in key) {
          if (arOffer.IMAGES[key[i]] != undefined) {
            $product.find('.catalog_item__img').attr('src', arOffer.IMAGES[key[i]].small);
            break;
          }
        }
      }
    }


    // offers DISPLAY_PROPERTIES
    if (arOffer.DISPLAY_PROPERTIES != undefined) {
      for (var iPropId in arOffer.DISPLAY_PROPERTIES) {
        var $propVal = $product.find('.sku_prop__val_'+ iPropId);
        if ($propVal.length) {
          $propVal.html(arOffer.DISPLAY_PROPERTIES[iPropId].DISPLAY_VALUE);
          if (arOffer.DISPLAY_PROPERTIES[iPropId].DISPLAY_VALUE == '') {
            $propVal.prev('.sku_prop__name').hide();
            $propVal.parent('.sku_prop').hide();
          } else {
            $propVal.prev('.sku_prop__name').show();
            $propVal.parent('.sku_prop').show();
          }
        }
      }
    }

    // set ratio
    if (arOffer.CATALOG_MEASURE_NAME) {
      $product.find('.js-measure').html(arOffer.CATALOG_MEASURE_NAME);
    }

    // quantity
    if (arOffer.CATALOG_MEASURE_RATIO) {
      $product.find('.js-quantity')
        .data('ratio', arOffer.CATALOG_MEASURE_RATIO)
        .attr('step', arOffer.CATALOG_MEASURE_RATIO)
        .attr('min', arOffer.CATALOG_MEASURE_RATIO)
        .val(arOffer.CATALOG_MEASURE_RATIO);
    }

    // set price
    if (!!arOffer.PRICE_MATRIX) {
      this.catalogElementSetPriceMatrix($product, arOffer.PRICE_MATRIX);
    } else if (arOffer.PRICES) {
      for (var PRICE_ID in arOffer.PRICES) {
        this.catalogElementSetPrice($product, arOffer.PRICES[PRICE_ID]);
      }
    }

    // daysarticle & quickbuy
    $timers = $product.removeClass('qb da2').find('.js_timer, .rs_progress-wrap').hide();
    if (arProduct.ELEMENT.DAYSARTICLE2 || arOffer.DAYSARTICLE2) {
      $product.addClass('da2');
      if ($timers.filter('[data-offer-id="'+ iProductID +'"]').length > 0) {
        $timers.filter('[data-offer-id="'+ iProductID +'"]').show();
      } else if ($timers.filter('[data-offer-id="'+ iOfferID +'"]').length > 0) {
        $timers.filter('[data-offer-id="'+ iOfferID +'"]').show();
      }
    } else if (arProduct.ELEMENT.QUICKBUY || arOffer.QUICKBUY) {
      $product.addClass('qb');
      if ($timers.filter('[data-offer-id="'+ iProductID +'"]').length > 0) {
        $timers.filter('[data-offer-id="'+ iProductID +'"]').show();
      } else if ($timers.filter('[data-offer-id="'+ iOfferID +'"]').length > 0) {
        $timers.filter('[data-offer-id="'+ iOfferID +'"]').show();
      }
    }

    // set add2basket url & enable/disable
    if (arOffer.CAN_BUY) {
      $product.find('.js-add2cart, .js-buy1click').removeClass('disabled');
      
      if (arProduct.ELEMENT.CATALOG_SUBSCRIBE) {
        $product.find('.js-subscribe').hide();
      }

    } else {
      $product.find('.js-add2cart, .js-buy1click').addClass('disabled');
      
      if (arProduct.ELEMENT.CATALOG_SUBSCRIBE) {
        $product.find('.js-subscribe').attr('data-item', iOfferID).show();
      }
    }

    // stocks
    var $stocks = $product.find('.stocks');
    if ($stocks.length) {
      function getStringCount(num, iMinAmount, arVars) {
        num = parseInt(num);
        iMinAmount = parseInt(iMinAmount)
        if (num == 0) {
          return arVars[0];
        } else if (num >= iMinAmount) {
          return arVars[2];
        } else {
          return arVars[1];
        }
      }

      var arClasses = ['is-outofstock', 'is-limited', 'is-instock'];

      if (!this.offers[iProductID].PARAMS.USE_STORE || this.stocks[iProductID] == undefined) {

        var arMessages = [BX.message('RS_SLINE_STOCK_OUT_OF_STOCK'), BX.message('RS_SLINE_STOCK_LIMITED_AVAILABILITY'), BX.message('RS_SLINE_STOCK_IN_STOCK')];

        $stocks.find('.stocks__amount').text(
          !!arProduct.PARAMS.USE_MIN_AMOUNT
            ? getStringCount(arOffer.CATALOG_QUANTITY, arProduct.PARAMS.MIN_AMOUNT, arMessages)
            : arOffer.CATALOG_QUANTITY
        );

        var $scale = $stocks.find('.stocks__scale')
                      .removeClass('is-outofstock is-limited is-instock')
                      .addClass(getStringCount(arOffer.CATALOG_QUANTITY, arProduct.PARAMS.MIN_AMOUNT, arClasses));

        if (arOffer.CATALOG_QUANTITY == 0) {
          $scale.children('.scale__over').removeAttr('style');
        } else {
          $scale.find('.scale__over').css('width', '100%');
        }

      } else {

        var arStocks = this.stocks[iProductID],
            arMessages = [arStocks.MESSAGES.OUT_OF_STOCK, arStocks.MESSAGES.LIMITED_AVAILABILITY, arStocks.MESSAGES.IN_STOCK];

        $stocks.find('.stocks__amount').text(
          !!arStocks.USE_MIN_AMOUNT
            ? getStringCount(arStocks.SKU[iOfferID].TOTAL_AMOUNT, arStocks.MIN_AMOUNT, arMessages)
            : arStocks.SKU[iOfferID].TOTAL_AMOUNT
        );

        var $scale = $stocks.find('.stocks__scale')
          .removeClass('is-outofstock is-limited is-instock')
          .addClass(getStringCount(arStocks.SKU[iOfferID].TOTAL_AMOUNT, arStocks.MIN_AMOUNT, arClasses));

        if (arStocks.SKU[iOfferID].MAX_AMOUNT == 0) {
           $scale.children('.scale__over').removeAttr('style');
        } else {
           $scale.children('.scale__over').css('width', '100%');
        }

        for (i in arStocks.STORES) {

          var $stock = $stocks.find('#' + arStocks.ID + '_' + arStocks.STORES[i]);

          if (!arStocks.SHOW_EMPTY_STORE && arStocks.SKU[iOfferID].STORES[arStocks.STORES[i]] <= 0) {
            $stock.hide();
          } else {

            $stock.find('.stock__quantity').text(
              !!arStocks.USE_MIN_AMOUNT
                ? getStringCount(arStocks.SKU[iOfferID].STORES[arStocks.STORES[i]], arStocks.MIN_AMOUNT, arMessages)
                : arStocks.SKU[iOfferID].STORES[arStocks.STORES[i]]
            );

            var $scale = $stock.show().find('.stock__scale')
              .removeClass('is-outofstock is-limited is-instock')
              .addClass(getStringCount(arStocks.SKU[iOfferID].STORES[arStocks.STORES[i]], arStocks.MIN_AMOUNT, arClasses));

            if (arStocks.SKU[iOfferID].MAX_AMOUNT == 0) {
              $scale.children('.scale__over').removeAttr('style');
            } else {
              $scale.children('.scale__over').css('width', 100 * arStocks.SKU[iOfferID].STORES[arStocks.STORES[i]] / arStocks.SKU[iOfferID].MAX_AMOUNT+'%');
            }

          }
        }
      }
    }

    // set buttons "in basket" and "not in basket"
    this.setProductItems({
        items: $product
    });

    // fancybox reposition
    $.fancybox.toggle();
  }
};

$(document).on('click', '.js-product .js-offer_prop .offer_prop__value', function(e){

  var $option = $(this);

  if (!$option.hasClass('disabled') && (!$option.hasClass('checked'))) {

    var $product = $option.closest('.js-product'),
        iProductID = parseInt($product.data('product-id')),
        $offerProp = $option.closest('.js-offer_prop');
        // $offerPropChecked = $offerProp.find('.offer_prop__checked');

    // if (!!$offerPropChecked) {
      // $offerPropChecked.text($option.text());
    // }

    if (iProductID > 0) {

      clearTimeout(appSLine.offerPropChangedTimeout);
      // comment if use bootstrap dropdown
      //e.stopPropagation();

      if (appSLine.offers[iProductID]) {
        appSLine.offerPropChanged($option);
      } else {
        var sAjaxUrl = $product.find('.js-product__name').attr('href'),
            $darkArea = $product.children('.catalog_item__inner');

        if (sAjaxUrl == undefined) {
          sAjaxUrl = $product.data('detail') == undefined
            ? window.location.href
            : $product.data('detail');
        }

        if ($darkArea.length < 1) {
          $darkArea = $product;
        }

        $darkArea.rsToggleDark({progress: true});
        appSLine.offerPropChangedTimeout = setTimeout(function(){
          $.ajax({
            type: 'POST',
            url: sAjaxUrl,
            dataType: 'json',
            data: {
              AJAX_CALL: 'Y',
              action: 'get_element_json',
            },
            success: function(json) {
              appSLine.offers[iProductID] = json;
              appSLine.offerPropChanged($option);
            },
            error: function (xhr, ajaxOptions, thrownError) {
              console.error(xhr.status);
              console.error(thrownError);
            },
            complete:function() {
              $darkArea.rsToggleDark();
            }
          });
        }, appSLine.ajaxTimeoutTime);
      }
    }
  }
  //e.stopPropagation();
  //return false;
});



$(document).on('change', '.js-product .js-quantity.js-use_count', function(e){

  var $option = $(this);

  if (!$option.hasClass('disabled')) {

    var $product = $option.closest('.js-product'),
        iProductID = parseInt($product.data('product-id'));

    if (iProductID > 0) {

      clearTimeout(appSLine.offerPropChangedTimeout);
      e.stopPropagation();

      if (appSLine.offers[iProductID]) {

        var iOfferId = $product.find('.js-product_id').val();

        if (
            appSLine.offers[iProductID].OFFERS[iOfferId] != undefined &&
            appSLine.offers[iProductID].OFFERS[iOfferId].PRICE_MATRIX != undefined
        ) {
          appSLine.catalogElementSetPriceMatrix($product, appSLine.offers[iProductID].OFFERS[iOfferId].PRICE_MATRIX);
        } else if (appSLine.offers[iProductID].ELEMENT.PRICE_MATRIX != undefined) {
          appSLine.catalogElementSetPriceMatrix($product, appSLine.offers[iProductID].ELEMENT.PRICE_MATRIX);
        }

      } else {

        var sAjaxUrl = $product.find('.js-product__name').attr('href'),
            $darkArea = $product.children('.catalog_item__inner');

        if (sAjaxUrl == undefined) {
          sAjaxUrl = $product.data('detail') == undefined
            ? window.location.href
            : $product.data('detail');
        }

        if ($darkArea.length < 1) {
          $darkArea = $product;
        }

        $darkArea.rsToggleDark({progress: true});
        appSLine.offerPropChangedTimeout = setTimeout(function(){
          $.ajax({
            type: 'POST',
            url: sAjaxUrl,
            dataType: 'json',
            data: {
              AJAX_CALL: 'Y',
              action: 'get_element_json',
            },
            success: function(json) {

              appSLine.offers[iProductID] = json;

              var iOfferId = $product.find('.js-product_id').val();

              if (appSLine.offers[iProductID]) {

                if (
                    appSLine.offers[iProductID].OFFERS[iOfferId] != undefined &&
                    appSLine.offers[iProductID].OFFERS[iOfferId].PRICE_MATRIX != undefined
                ) {
                  appSLine.catalogElementSetPriceMatrix($product, appSLine.offers[iProductID].OFFERS[iOfferId].PRICE_MATRIX);
                } else if (appSLine.offers[iProductID].ELEMENT.PRICE_MATRIX != undefined) {
                  appSLine.catalogElementSetPriceMatrix($product, appSLine.offers[iProductID].ELEMENT.PRICE_MATRIX);
                }
              }
            },
            error: function() {
              console.error(xhr.status);
              console.error(thrownError);
            },
            complete: function() {
              $darkArea.rsToggleDark();
            }
          });
        }, appSLine.ajaxTimeoutTime);
      }
    } else {
      console.error( 'Get element JSON -> iProductID is empty' );
    }
  }

  return false;
});

