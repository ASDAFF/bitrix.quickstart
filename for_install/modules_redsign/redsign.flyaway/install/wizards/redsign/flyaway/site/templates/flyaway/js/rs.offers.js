function updateProductDataAfterOffersChanged($element) {
    'use strict';

    var elementId = $element.data('elementid');
    var ce = new CatalogElement(elementId);
    
    /*
     * Select Properties
     */
    var skuProperties = ce.getSkuProperties(),
        $sku = $element.find(".js-sku_props"),
        $skuProp,
        skuCode;

    for(skuCode in skuProperties) {
        if(!skuProperties.hasOwnProperty(skuCode)) {
            continue;
        }

        $skuProp = $sku.find(".js-sku_prop[data-code=" + skuCode + "]");
        $skuProp.find(".js-sku_option")
                .removeClass("checked")
                .filter("[data-value='" + skuProperties[skuCode] + "']")
                .addClass("checked");

        $skuProp.find(".js_select-val").html(skuProperties[skuCode]);
    }

    /*
     *  Disable properties
     */
    var enabledProperties = ce.getEnabledOfferValues(),
        propCode;
    $sku.find(".js-sku_option:not(.disabled)").addClass("disabled");

    for(propCode in enabledProperties) {
        $skuProp = $sku.find(".js-sku_prop[data-code=" + propCode + "]");
        enabledProperties[propCode].forEach(function(val) {
            $skuProp.find(".js-sku_option.disabled[data-value='" + val + "']").removeClass("disabled");
        });
    }

    /**
     * Update Name
    **/
    var name = ce.getName();
    if(name) {
        $element.find(".js-compare-name").html(name);
    }

    /**
     * Update link
    **/
    if (!$element.hasClass('js-detail')) {
        var link =  addUriParameter($element.find(".js-compare-name").attr('href'), 'offer_id', ce.getProductId());
        $element.find(".js-compare-name, .js-detail_page_url").attr('href', link);
    }
    
    /**
     * Update picture
    **/
    var picture = ce.getPictures(1);
    if(picture && picture.src) {
        $element.find(".js-preview").attr('src', picture.src);
    }

    /**
     * Render prices
    **/
    var prices = ce.getPrices(),
        isPageDetail =  $element.hasClass("js-detail"),
        $pricesBlock = $element.find(".products__prices:first").html(''),
        $_priceBlock = $("<div></div>").addClass('prices'),
        $_priceValues = $("<div></div>").addClass('prices__values');

    prices.forEach(function(price) {

        var $priceBlock = $_priceBlock.clone(),
            $priceValues = $_priceValues.clone();

        $priceBlock.append(
            $("<div></div>")
              .addClass((!isPageDetail ? 'hidden-xs ':'') + 'prices__title')
              .html(prices.length > 1 ? price.title : '')
        );

        if(price.hasDiscount) {

            $priceValues
                .append(
                    $("<div></div>")
                        .addClass((!isPageDetail ? 'hidden-xs ':'') + "prices__val prices__val_old")
                        .html(price.printValue)
                )
                .append(
                    $("<div></div>")
                        .addClass("prices__val prices__val_cool prices__val_new")
                        .html(price.printDiscountValue)
                );

        } else {

            $priceValues.append(
                $("<div></div>")
                    .addClass("prices__val prices__val_cool")
                    .html(price.printValue)
            );

        }
        $priceBlock.append($priceValues);

        if($element.closest(".products_list").length > 0) {
            $priceBlock = $priceBlock.wrap("<div class='products__prices-item'></div>").parent();
        }

        $pricesBlock.append($priceBlock);
    });


    /**
     *  Update Basket form
    **/
    var productId = ce.getProductId(),
        canBuy = ce.canBuy(),
        $basketForm = $element.find('.add2basketform'),
        isInBasket = false;

    if(canBuy) {
        $basketForm.removeClass("cantbuy");
    } else {
        $basketForm.addClass("cantbuy");
    }

    $.each(Basket.inbasket(), function(key, id) {
        if(productId == id) {
            isInBasket = true;
            return false;
        }
    });

    if(isInBasket) {
        $basketForm.addClass("checked");
    } else {
         $basketForm.removeClass("checked");
    }

    $basketForm.find(".js-add2basketpid").val(productId);


    /**
     * Update amount
    **/
   var quantity = ce.getQuantity(),
       $stores = $element.find(".stores");
   console.log(rsFlyaway.stocks[elementId]);

   if(quantity > 0 && quantity > rsFlyaway.stocks[elementId].LOW_QUANTITY) {

       $stores.find(".stores-icon:first").removeClass('stores-mal').addClass("stores-full");
       $stores.find(".genamount:first")
              .removeClass("empty").addClass("isset")
              .html(rsFlyaway.stocks[elementId].MESSAGES.MESSAGE_ISSET);

   } else if(quantity > 0) {
       $stores.find(".stores-icon:first").removeClass('stores-full').addClass("stores-mal");
       $stores.find(".genamount:first")
              .removeClass("empty isset")
              .html(rsFlyaway.stocks[elementId].MESSAGES.MESSAGE_LOW);
   } else {
       $stores.find(".stores-icon:first").removeClass("stores-full stores-mal");
       $stores.find(".genamount:first")
              .removeClass("isset").addClass("empty")
              .html(rsFlyaway.stocks[elementId].MESSAGES.MESSAGE_EMPTY);
   }
}

$(document).ready(function() {

    $(document).on("click", ".js-sku_option:not(.disabled)", function() {
        var $this = $(this),
            $element = $this.closest(".js-element"),
            $props = $this.closest(".js-sku_props"),
            offerValues = {};

        $this.closest(".js-sku_prop").find(".js-sku_option").removeClass("checked");
        $this.addClass("checked");

        $.each($props.find(".js-sku_prop"), function(key, skuProp) {
            var $skuProp = $(skuProp);
            offerValues[$skuProp.data('code')] = $skuProp.find(".js-sku_option.checked").data('value');
        });

        var catalogElement = new CatalogElement($element.data('elementid'));
        if(!catalogElement.isReady) {
            rsFlyaway.darken($element);
        }

        catalogElement.ready(function() {

            if($element.hasClass("area2darken")) {
                rsFlyaway.darken($element);
            }
            catalogElement.selectOfferByValues(offerValues);
            updateProductDataAfterOffersChanged($element);

            setTimeout(function() {
                $(document).trigger("changeOffer.rs.flyaway", [$element]);
            });
        });

    });

});
