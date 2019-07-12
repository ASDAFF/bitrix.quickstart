"use strict";



/**
 * Класс для геокодирования списка адресов или координат.
 * @class
 * @name MultiGeocoder
 * @param {Object} [options={}] Дефолтные опции мультигеокодера.
 */
function MultiGeocoder(options) {
    this._options = options || {};
}

/**
 * Функция множественнеого геокодирования.
 * @function
 * @requires ymaps.util.extend
 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/util.extend.xml
 * @requires ymaps.util.Promise
 * @see http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/util.Promise.xml
 * @name MultiGeocoder.geocode
 * @param {Array} requests Массив строк-имен топонимов и/или геометрий точек (обратное геокодирование)
 * @returns {Object} Как и в обычном геокодере, вернем объект-обещание.
 */
MultiGeocoder.prototype.geocode = function (requests, options) {
    var self = this,
        size = requests.length,
        geoObjects = new ymaps.GeoObjectCollection({}),
        promise = new ymaps.vow.Promise(function (resolve, reject) {

            requests.forEach(function (request, index) {
                ymaps.geocode(request, ymaps.util.extend({}, self._options, options))
                    .then(
                    function (response) {
                        var geoObject = response.geoObjects.get(0);
                        geoObject._idx = index;

                        geoObject && geoObjects.add(geoObject);
                        --size || resolve({ geoObjects : geoObjects });
                    },
                    function (err) {
                        reject(err);
                    }
                );
            });

        });

    return promise;
};

(function( $ ) {

    $.fn.citrusRealtyOfficeMapCheckHash = function() {
        var hash = window.location.hash.substr(1);
        if (window.geoObjects) {
            for (var i = 0; i < window.geoObjects.length; ++i) {
                if (window.geoObjects[i]._info.code == hash)
                    window.geoObjects[i].balloon.open();
            }
        }
    }
    $.fn.citrusRealtyOfficeMap = function(options) {
        var opts = $.extend( {}, $.fn.citrusRealtyOfficeMap.defaults, options );

        ymaps.ready(function() {
            var map = new ymaps.Map(opts.id, {
                center: [55.734046, 37.588628],
                zoom: 9,
                controls: opts.controls
            });

            var objects = [],
                info = [];

            for (var index = 0; index < opts.items.length; ++index) {
                var item = opts.items[index];
                objects.push(item.address);
                info.push(item);
            }

            if (!window.geoObjects)
                window.geoObjects = [];

            var geoCoder = new MultiGeocoder({});
            geoCoder.geocode(objects)
                .then(function (res) {
                    res.geoObjects.each(function (obj) {
                        obj._info = info[obj._idx];
                        window.geoObjects[obj._idx] = obj;

                        if (obj._info.header)
                            obj.properties.set('balloonContentHeader', obj._info.header);
                        if (obj._info.body)
                            obj.properties.set('balloonContentBody', obj._info.body);
                        if (obj._info.footer)
                            obj.properties.set('balloonContentFooter', obj._info.footer);

                    });
                    map.geoObjects.add(res.geoObjects);
                    map.setBounds(res.geoObjects.getBounds(), {checkZoomRange: true});
                    window.setTimeout(function() {
                        $().citrusRealtyOfficeMapCheckHash();
                    }, 500);
                },
                function(err) {
                    console.log(err);
                });
        });

        return this;
    };

    $.fn.citrusRealtyOfficeMap.defaults = {
        id: '',
        address: '',
        items: false,
        controls: ['smallMapDefaultSet'],
    };

    $(window).on('hashchange', function () {
        $().citrusRealtyOfficeMapCheckHash();
    });

}( jQuery ));
