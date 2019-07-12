$(document).ready(function() {
    var $selectedItems = $(".js-sets-selected-items"),
        $allItems = $(".js-sets-all-items"),
        margin = 15,
        dUpdatePrices = debounce(updatePrices, 1000),
        ce = new CatalogElement($(".js-detail.js-element").data('elementid'));

    updateSlider();

    $(window).resize(debounce(updateSlider, 250));

    $(document).on('click', '.js-set-constructor .js-remove', removeItem);
    $(document).on('change', '.js-set-constructor .js-set-toggle', toggleItem);
    $(document).on('click', '.js-set-constructor .js-set-add2basket', add2basket);
    $(document).on('click', '.js-set-constructor .js-set-buy1click', buy1click);
    $(document).on('click', ".js-set-constructor .js-myset", function() {
        $(this).toggle();
        $(this).siblings(".js-myset").toggle();
        $(this).closest(".js-set-constructor").find(".js-sets-all-items").slideToggle();
    });
    $(document).on("changeOffer.rs.flyaway", changeSetAfterChangeOffer);


    function toggleItem(e) {
        if($(this).is(":checked")) {
            addItem.apply(this, [e]);
        } else {
            removeItem.apply(this, [e]);
        }
    }

    function addItem(e) {
        var $item = $(this).parents(".js-set-item"),
            $emptyElement = $selectedItems.filter(":visible").find(".js-set-item:not([data-elementid]):eq(0)"),
            $clonedElement = $item.clone();

        if($emptyElement.length > 0) {
            $clonedElement.find(".js-set-toggle").closest(".gui-box").replaceWith('<a class="set-item__icon js-remove" href="javascript:;"><i class="fa fa-close"></i> </a>');
            $emptyElement.replaceWith($clonedElement);
        } else {
            $(this).prop("checked", false);
        }

        dUpdatePrices();
    }

    function removeItem(e) {
        e.preventDefault();

        var $item = $(this).closest(".js-set-item"),
            elementId = $item.data('elementid');

        $selectedItems.filter(":visible").find("[data-elementid=" + elementId + "]").each(function(key, el) {
            $(el).replaceWith("<div class=\"set-item js-set-item hidden-xs\"><div class=\"set-item__cart\"></div></div>");
        });
        $selectedItems.filter(":visible").closest(".js-set-constructor").find(".js-sets-all-items [data-elementid=" + elementId + "] .js-set-toggle").prop('checked', false);
        dUpdatePrices();
    }

    function updatePrices() {
        var sumNewprice = 0,
            sumOldprice = 0,
            sumDiscount = 0,
            $jsConstructor = $(".js-set-constructor:visible"),
            url = $jsConstructor.data('ajaxpath'),
            data = {};


        $selectedItems.filter(":visible").find(".js-set-item[data-elementid]").each(function(key, item) {
            sumNewprice += parseInt($(item).data("price"));
            sumOldprice += parseInt($(item).data("oldprice"));
            sumDiscount += parseInt($(item).data("discount"));
        });

        data = {
          sessid: BX.bitrix_sessid(),
          action: "ajax_recount_prices",
          sumPrice: sumNewprice,
          sumOldPrice: sumOldprice,
          sumDiffDiscountPrice: sumDiscount,
          currency: $jsConstructor.data('currency')
        };

        return $.post(url, data)
            .then(function (result) {
                return BX ? BX.parseJSON(result) : $.parseJSON(result);
            })
            .then(function(resultJSON) {
                var pricesHtml = '';
                if(sumDiscount > 0) {

                    pricesHtml += '<div class="prices__val prices__val_cool prices__val_new big">' + resultJSON.formatSum + '</div>';
                    pricesHtml += '<div class="prices__val prices__val_old">' + resultJSON.formatOldSum + '</div>';
                    pricesHtml += '<div class="set-buyblock__profit">' + BX.message('YOUR_PROFIT') + '</div>';
                    pricesHtml += '<div class="prices__val prices__val_cool prices__val_new">' + resultJSON.formatDiscDiffSum + '</div>';

                } else {
                    pricesHtml += '<div class="prices__val prices__val_cool">' + resultJSON.formatSum + '</div>';
                }

                $jsConstructor.find(".js-buyblock-prices").html(pricesHtml);

            });
    }

    function buy1click(e) {
        var $jsConstructor = $(this).closest(".js-set-constructor"),
            $setItems = $jsConstructor.find(".js-sets-selected-items .js-set-item[data-elementid]"),
            productsString = '';

        if($(window).width() >= rsFlyaway.breakpoints.sm) {
            $(document).one("RSFLYAWAY_fancyBeforeShow", function() {
                $(".fancybox-wrap [name=RS_EXT_FIELD_0]").val(productsString);
            });
        } else {
            window.localStorage.setItem('insertdata', $.parseJSON({"RS_EXT_FIELD_0": productsString}));
        }

        $setItems.each(function(key, item) {
            productsString += "[" + $(item).data('elementid') + "] " + $(item).find(".set-item__name").text().trim() + ", ";
        });
    }

    function add2basket(e) {
        var $jsConstructor = $(this).closest(".js-set-constructor"),
            $setItems = $jsConstructor.find(".js-sets-selected-items .js-set-item[data-elementid]"),
            productIds = [],
            data = {};

        $setItems.each(function(key, item) {
            if($.inArray($(item).data('elementid'), productIds) === -1) {
                productIds.push($(item).data('elementid'));
            }
        });

        data = {
            sessid: BX.bitrix_sessid(),
            action: 'catalogSetAdd2Basket',
            set_ids: productIds,
            lid: $jsConstructor.data('lid'),
            iblockId: $jsConstructor.data('iblockid'),
            setOffersCartProps: $jsConstructor.data('setOffersCartProps')
        };

        rsFlyaway.darken($jsConstructor);
        $.post($jsConstructor.data('ajaxpath'), data, function (result) {
            BX.onCustomEvent('OnBasketChange');
            rsFlyaway.darken($jsConstructor);
        }, "json");
    }

    function changeSetAfterChangeOffer(event) {
        var  $set = $("#set_" + ce.getProductId());

        if($set.length > 0) {
            $("[id^=set]").hide();
            $set.show();
        }
    }

    function updateSlider() {
        if($(window).width() >= rsFlyaway.breakpoints.sm - 1) {

            $selectedItems.each(function(key, selectedItem) {

                var $selectedItem = $(selectedItem);
                if(!$selectedItem.data('owl.carousel')) {
                    $selectedItems.addClass('owl-theme owl-carousel');
                    $selectedItems.owlCarousel({
                        loop: false,
                        items: 4,
                        margin: margin,
                        responsive: {
                          0: {
                            items: 2
                          },
                          740: {
                            items: 3
                          },
                          1080: {
                            items: 4
                          }
                        }
                    });
                }

            });

            $allItems.each(function(key, allItem) {
                var $allItem = $(allItem);
                if(!$allItem.data('owl.carousel')) {
                    $allItem.addClass('owl-theme owl-carousel');

                    $allItem.owlCarousel({
                      loop: false,
                      items: 4,
                      margin: margin,
                      responsive: {
                        0: {
                          items: 3
                        },
                        740: {
                          items: 4
                        },
                        1080: {
                          items: 5
                        }
                      }
                  });
                }


            });

        } else {
            $selectedItems.each(function(key, selectedItem) {

                if($(selectedItem).data('owl.carousel')) {
                    $(selectedItem).data('owl.carousel').destroy();
                    $(selectedItem).removeClass('owl-theme owl-hidden owl-carousel');
                }

            });
            $allItems.each(function(key, allItem) {
                if($(allItem).data('owl.carousel')) {
                    $(allItem).data('owl.carousel').destroy();
                    $(allItem).removeClass('owl-theme owl-hidden owl-carousel');
                }
            });

        }
    }
});
