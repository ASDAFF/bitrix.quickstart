(function( $ ) {

    $.fn.citrusRealtyAddress = function(options) {
        var opts = $.extend( {}, $.fn.citrusRealtyAddress.defaults, options );

        ymaps.ready(function () {
            var $container = $('#' + opts.id);
            ymaps.geocode(opts.address, {
                results: 1
            }).then(function (res) {
                var firstGeoObject = res.geoObjects.get(0);
                if (!firstGeoObject) {
                    if (typeof(opts.onError) === 'function')
                        opts.onError();
                    else {
                        $('.content-map-scale').hide();
                        $container.hide();
                    }
                    return;
                }
                var map = new ymaps.Map(opts.id, {
                    center: firstGeoObject.geometry.getCoordinates(),
                    zoom: 11,
                    controls: opts.controls
                });

                if (opts.header)
                    firstGeoObject.properties.set('balloonContentHeader', opts.header);
                if (opts.body)
                    firstGeoObject.properties.set('balloonContentBody', opts.body);
                if (opts.footer)
                    firstGeoObject.properties.set('balloonContentFooter', opts.footer);

                map.geoObjects.add(firstGeoObject);

                var bounds = firstGeoObject.properties.get('boundedBy');
                map.setBounds(bounds, {/*checkZoomRange: true*/});

                if (opts.openBaloon)
                    firstGeoObject.balloon.open();

            }, function (err) {
                if (typeof(opts.onError) === 'function')
                    opts.onError();
                else {
                    $('.content-map-scale').hide();
                    $container.hide();
                }
            })
        });

        return this;
    };

    $.fn.citrusRealtyAddress.defaults = {
        id: '',
        address: '',
        header: false,
        body: false,
        footer: false,
        controls: ['smallMapDefaultSet'],
        openBallon: false
    };

}( jQuery ));

$(function () {
    // nook показывать ссылку только когда есть результат геокодирования
    $('.map-link').click(function () {
        var $this = $(this);
        var address = $this.data('address');
        if (address.length)
            $.fancybox({
                padding: 0,
                autoSize: false,
                scrolling: 'no',
                width: '95%',
                height: '95%',
                content: '<div id="map" style="width: 100%;height: 100%;"></div>',
                afterShow: function () {
                    $().citrusRealtyAddress({
                        id: 'map',
                        address: address,
                        header: '',
                        body: address,
                        footer: '',
                        controls: ['largeMapDefaultSet'],
                        onError: function () {
                            $('#map').height('auto').html('<h2 style="text-align: center">' + BX.message('CITRUS_REALTY_ADDRESS_NOT_FOUND') + '</h2>');
                            $('.fancybox-inner').css('display', 'table-cell').css('vertical-align', 'middle');
                            window.setTimeout($.fancybox.close, 2000);
                       }
                    })
                }
            });
    });
});
