function drawPlacemark(arShopsItem, rsPlacemark) {
    arShopsItem.each(function() {
        if (!$(this).is(':visible')) {
            rsPlacemark[$(this).data('id')].options.set('visible', false);
        } else {
            rsPlacemark[$(this).data('id')].options.set('visible', true);
        }
    });
}

$(document).ready(function() {
    var arShopsItem = $('.js-shops_list').find('.js-item'),
        arMapCoord = [0, 0],
        rsPlacemark = {},
        rsYMapShops,
        classActive = "active";

    arShopsItem.each(function() {
        var arCoords = $(this).data('coords').split(',');
        arMapCoord[0] = arMapCoord[0] + parseFloat(arCoords[0]);
        arMapCoord[1] = arMapCoord[1] + parseFloat(arCoords[1]);
    });

    arMapCoord[0] = arMapCoord[0] / arShopsItem.length;
    arMapCoord[1] = arMapCoord[1] / arShopsItem.length;

    var rsPlacemark = {},
        rsYMapShops;

    ymaps.ready(function() {
        rsYMapShops = new ymaps.Map('rsYMapShops', {
            center: arMapCoord,
            zoom: 14,
            type: 'yandex#publicMap',
            behaviors: ['default', 'scrollZoom']
        });

        arShopsItem.each(function() {
            var arCoords = $(this).data('coords').split(','),
                id = $(this).data('id');
            arCoords[0] = parseFloat(arCoords[0]);
            arCoords[1] = parseFloat(arCoords[1]);
            rsPlacemark[id] = new ymaps.Placemark(
                arCoords, {
                    balloonContentHeader: $(this).find('.js-item__name').html(),
                    balloonContentBody: $(this).find('.js-item__descr').html()
                }
            );
            rsYMapShops.geoObjects.add(rsPlacemark[id]);
        });
        rsYMapShops.setBounds(rsYMapShops.geoObjects.getBounds(), {
            checkZoomRange: true
        });
    });

    arShopsItem.on('mouseenter', function() {
        rsPlacemark[$(this).data('id')].options.set('preset', 'twirl#redDotIcon');
    }).on('mouseleave', function() {
        rsPlacemark[$(this).data('id')].options.set('preset', 'twirl#blueIcon');
    });

    // city search
    $(document).on('keyup', '.js-search_city input', function() {
        var $element = $(this),
            searchValue = $element.val(),
			filter = $(".js-filter .js-btn.active").data('filter'),
            $shops,
            $foundShops = $([]),
            $shop,
            shopName,
            shopDescr;

		$shops = filter ? $(".js-shops_list .js-item[data-type=" + filter + "]") : $(".js-shops_list .js-item");

        if (!searchValue || searchValue.trim() == '') {
            $foundShops = $shops;
            $(".js-clear-shops-input").removeClass('active');
        } else {

            $(".js-clear-shops-input").addClass('active');

            $.each($shops, function(index, item) {
                $shop = $(item);
                shopName = $shop.find(".js-item__name").text();
                shopDescr = $shop.find(".js-item__descr").text();

                if (
                    (shopName && shopName.toLowerCase().indexOf(searchValue.toLowerCase()) !== -1) ||
                    (shopDescr && shopDescr.toLowerCase().indexOf(searchValue.toLowerCase()) !== -1)
                ) {
                    $foundShops = $foundShops.add($shop);
                }
            });
        }


        $shops.addClass("hidden");
        if ($foundShops.length > 0) {
            $foundShops.removeClass('hidden');
			$(".js-not-found:visible").hide();
        } else {
			$(".js-not-found:hidden").show();
        }

        highlightFoundText($foundShops, searchValue);
    });

	$(document).on('click', ".js-shops_list .js-item", function() {
		var coords = $(this).data('coords');

		if(!coords) return;

		coords = coords.split(',');
		coords = $.map(coords, function(coord) { return parseFloat(coord, 10); });
		rsYMapShops.setCenter(coords, 18);

        if(appSLine.getPageWidth() <= appSLine.grid.md) {
          $(window).scrollTop($("#rsYMapShops").offset().top);
        }
	});

    $(document).on('blur', '.js-search_city input', function() {
        var value = $(this).val();
        if (value.length < 1) {
            $('.js-shops_list').find('.js-item').show();
        } else {
            $('.js-search_city input').trigger('keyup');
        }
        drawPlacemark(arShopsItem, rsPlacemark);
    });


    // filter
    $(document).on('click', '.js-filter .js-btn', function() {
        $('.js-shops .js-filter').find('.js-btn').removeClass(classActive);
        $(this).addClass(classActive);

        var typeFilter = $(this).data('filter');
        if (typeFilter.length > 0) {
            $('.js-shops_list').find('.js-item').hide();
            $('.js-shops_list').find('.js-item[data-type="' + typeFilter + '"]').show();
        } else {
            $('.js-shops_list').find('.js-item').show();
        }

		$(".js-search_city input").trigger('keyup');
        drawPlacemark(arShopsItem, rsPlacemark);
    });

    $(document).on('click', ".js-clear-shops-input", function() {
        $(".js-search_city .shops-input").val('').keyup();
    });

    function highlightFoundText($shops, searchValue) {
        var highlightSubstring = function(substr, str) {return  str.replace(new RegExp('(' + substr + ')','gi'), '<span>$1</span>');}

        $shops.each(function(key, item) {
            var $shop = $(item);
            var shopName = $shop.find(".js-item__name").html();
            var shopDescr = $shop.find(".js-item__descr").html();

            shopName = shopName.replace(new RegExp('<span>(.*?)</span>', 'gi'), '$1');
            shopDescr = shopDescr.replace(new RegExp('<span>(.*?)</span>', 'gi'), '$1');

            if(!searchValue) {
                $shop.find(".js-item__name").html(shopName.replace(new RegExp('<span>(.*?)</span>', 'gi'), '$1'));
                $shop.find(".js-item__descr").html(shopDescr.replace(new RegExp('<span>(.*?)</span>', 'gi'), '$1'));
                return;
            }

            if(shopName) {
                shopName.replace(new RegExp('<span>(.*?)</span>', 'gi'), '$1');
                $shop.find(".js-item__name").html(highlightSubstring(searchValue, shopName));
            }


            if(shopDescr) {
                shopDescr.replace(new RegExp('<span>(.*?)</span>', 'gi'), '$1');
                $shop.find(".js-item__descr").html(highlightSubstring(searchValue, shopDescr));
            }
        });
    }
});
