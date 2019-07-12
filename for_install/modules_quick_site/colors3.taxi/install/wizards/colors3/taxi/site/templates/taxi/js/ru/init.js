jQuery(document).ready(function($) {

var someFunction = function(part, param, callback) {
        var url = '/include/street_search.php';	
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            data: {
                term: part,
		city_from: $('#FIELD_CITY_OTKUDA').val(),
		city_to: $('#FIELD_CITY_KUDA').val(),
		type_field: param.inputField
            },
            success: function(response) {
                callback(response);
            }
        });  
    };

    // виджет автокомплита
    $.widget("ThreeColors.geo_autocomplete", {
        _init: function() {            
            this.options._cache = {};
            this.element.autocomplete(this.options)._renderItem = function(_ul, _item) {
                return $('<li></li>').data('item.autocomplete', _item).append(this.options.getItemHTML(_item)).appendTo(_ul);
            };
        },
        options: {
            minLength: 3,
            delay: 300,
            // Тип контрола, который запрашиват функцию source - улица, населенный пункт, только город - street, locality, city
            inputType: 'street',
            /**
             * Функция для прописовки автокомплита путем запросов к внешним геокодерам
             * и получением от них списка вариантов
             * @param {object} _request - объект запроса с .term = 'Пушкинск' - запрошенной строкой для автоподсказки
             * @param {function} _responseCallback - вызвать эту функцию с аргументов ответа от геокодера - это просто массив объектов должен быть: _responseCallback(geocoderAnswer)
             * @returns {undefined}
             */
            source: function(_request, _responseCallback) {
                var city = this.options.city.val() || window.city;
                var house = this.options.house.val() || "";
                var housing = this.options.housing.val() || "";

                // Образование поисковой строки: добави город в критерий поиска / или Россию / страну
                if (this.options.inputType === 'street' && city.length > 0) {
                    _request.term = city + ", " + _request.term;
                } else if (this.options.inputType === 'locality'){
                    _request.term = 'Россия, ' + _request.term;
                } else if (this.options.inputType === 'city'){
                    _request.term = 'Россия, город' + _request.term;
                }

                // кеширование результатов
                if (_request.term in this.options._cache) {
                    _responseCallback(this.options._cache[_request.term]);
                } else {
                    var self = this;
                    var _address = _request.term;
			if (window.autocomplete){
	                    taxi.suggestCaller.search(_address, function(responseObject) {
	
	                        if (!responseObject) {
	                            return;
	                        }
	                        self.options._cache[_request.term] = responseObject.variants;
	
	                        _responseCallback(responseObject.variants);
	                    });
			}else{
				someFunction(_request.term, this.options, function(vars) {
	
	                        self.options._cache[_request.term] = vars;
	                        _responseCallback(vars);
	                    });
			}
                }



            },
            /**
             * ? для более красивого отображения выпадающего списка найденных значений
             * @param {type} _item - исходный элменет списка
             * @returns {string} - ? полученный элемент списка в виде выпадающей разметки
             */
            getItemHTML: function(_item) {
//                console.log(item);
                return _item.label.replace(/,/gi, ',<br/>');
            }
        }
    });

    // Запретим сабмит формы при нажатии Enter внутри полей формы
    // При нажатии клавиш в контролах запускаем таймаут для автообновлений
    $('.control-group input').keypress(function(event) {
        var _this = $(this);
        // Время задержки перед автообновлением пути на карте после нажатия клавиш
        var lockTime = 1000;
        var isFromControl = _this.closest('.CITY_OTKUDA-group').length > 0 || _this.closest('.FROM_HOUSE-group').length > 0;
        // нажали явным образом enter внутри поля ввода
        if (event.keyCode === 13) {
            event.stopPropagation();
            event.preventDefault();
            taxi.updateRoute(isFromControl);
        } else {
            _this.data('locked', true);
            _this.data('pushed', true);
            // нажали любую другую клавишу - пробуем обновиться
            function runAutoUpdate(sender) {
                setTimeout(function() {
                    if (sender.data('pushed') && !sender.data('locked')) {
                        taxi.updateRoute(isFromControl);
                        sender.data('pushed', false);
                        sender.data('locked', false);
                    } else if (sender.data('pushed') && sender.data('locked')) {
                        sender.data('locked', false);
                        runAutoUpdate(sender);
                    }
                }, lockTime);
            }

            runAutoUpdate(_this);
        }
    });

    $("#map").each(function() {
        // Настойка входных инпутов с адресами откуда и куда
        var inputFromStreet = $("#FIELD_FROM"); //Улица
        var inputFromHouse = $("#FIELD_FROM_HOUSE"); //Дом
        var inputFromHousing = $("#FIELD_FROM_HOUSING"); //Корпус
        var inputFromCity = $('#FIELD_CITY_OTKUDA');
        var FROM_HOUSE = $(".FROM_HOUSE-group"); // ? Группа контролов

        var inputToStreet = $("#FIELD_TO"); //Улица
        var inputToHouse = $("#FIELD_TO_HOUSE"); //Дом
        var inputToHousing = $("#FIELD_TO_HOUSING"); //Корпус
        var inputToCity = $('#FIELD_CITY_KUDA');
        var TO_HOUSE = $(".TO_HOUSE-group"); // ? Группа контролов

        function getCityRow(res) {
            if (typeof(res.city) !== 'undefined' && res.city !== null && res.city !== window.city) {
                var city = res.city;
            } else {
                var city = window.city;
            }
            return (city) ? city : '';
        }
        function startInputSetValues(res, ignore) {
            if (!res)
                return;
            ignore = ignore || '';
            if (!res.changed) {
                res.value = res.street;
                res.changed = true;
            }
            if (ignore.indexOf('value') < 0) {
                inputFromStreet.val(res.value);
            }
            if (ignore.indexOf('city') < 0) {
                inputFromCity.val(res.city);
            }
            if (ignore.indexOf('house') < 0) {
                inputFromHouse.val(res.house);
            }
            if (ignore.indexOf('housing') < 0) {
                inputFromHousing.val(res.housing);
            }
            return res;
        }
        function endInputSetValues(res, ignore) {
            if (!res)
                return;
            ignore = ignore || '';
            if (!res.changed) {
                res.value = res.street;
                res.changed = true;
            }
            if (ignore.indexOf('value') < 0) {
                inputToStreet.val(res.value);
            }
            if (ignore.indexOf('city') < 0) {
                inputToCity.val(res.city);
            }
            if (ignore.indexOf('house') < 0) {
                inputToHouse.val(res.house);
            }
            if (ignore.indexOf('housing') < 0) {
                inputToHousing.val(res.housing);
            }
            return res;
        }
        /**
         * поставляем значение города по умолчанию
         * решение о сокрытии\показе доп. полей формы
         * @returns {undefined}
         */
        function writeDefaultCity() {
            if (!inputFromStreet.val()) {
                inputFromCity.val(window.city);
            } else {
                FROM_HOUSE.show();
            }
            if (!inputToStreet.val()) {
                inputToCity.val(window.city);
            } else {
                TO_HOUSE.show();
            }
        }
        /**
         * Инициализация объекта для расчета стоимостей и работы с картой, создание карт и т.д.
         * @returns {undefined}
         */
        function initTaxiMapObjects() {
            /**
             * Расчет стоимости поездки
             * @param {type} length - длина маршрута или 0
             * @returns {taxi.callCost.minpricecity}
             */
            taxi.callCost = function(length) {
                var mileage = $('#tariff_travel option:selected').data('mileage');
                var landing = $('#tariff_travel option:selected').data('landing');
                var included = $('#tariff_travel option:selected').data('included');
                var minpricecity = $('#tariff_travel option:selected').data('minpricecity');
                var cost = 0;
		
		var dop_cost = 0;	

		$('.dop_input:checked').each(function(){
			dop_cost += Math.round($(this).data('cost'));
		})

		
	

                if ((length / 1000) > included)
                {
                    cost = landing + (length / 1000 - included) * mileage;
                }
                else
                {
                    cost = landing;
                }

                if (minpricecity > cost)
                {
                    cost = minpricecity;
                }
                return cost + dop_cost;
            };

            taxi.init({
                mapcontainer: "map", // id элемента карты
                map: window.geoservice, //сервис карт yandex|google
                googlemaptoken: '',
                geocoder: window.geoservice, //геокодер сервиса карт yandex|google. на данный момент гекокодер яндекса возможно использовать только с картой яндекса
                city: window.city, //город по умолчанию
                startInput: startInputSetValues,
                endInput: endInputSetValues,
                region: 'ru',
                routeinfo: function(res) {
                    var length = res.length; // Длина маршрута
                    var time = res.time; //время маршрута

                    var cost = taxi.callCost(length);

                    $('#list').html(
                            '<div class="control-group rez"><label class="control-label"><span class="rect"></span></label><div class="controls"><p>Ехать '
                            + Math.round(length / 1000) + ' км</p><p>С учетом пробок '
                            + Math.round(time / 60) + ' мин.</p><p>Примерная стоимость <span id="cost_order" style="color: white;">'
                            + Math.round(cost) + '</span> руб.</p><span id="range_travel" style="display: none;">'
                            + Math.round(length / 1000) + '</span></div></div>'
                            );
                }
            });
            // ? Перерасчет стоимости при смене класса обслуживания такси
            $('#tariff_travel').on('change', function() {
                var cost = taxi.callCost(0);
                //$('#list p:eq(2)').text('Примерная стоимость <span id="cost_order" style="color: white;">' + (cost) + '</span> руб.');
		$('#cost_order').text(cost);
            });

		$('.dop_input').on('change', function(){
			var cost = parseInt($('#cost_order').text());
			if ($(this).is(':checked'))
				
				$('#cost_order').text(cost + Math.round($(this).data('cost')))
			else
				$('#cost_order').text(cost - Math.round($(this).data('cost')))
		})			



        }
        /**
         * Автозаполнение полей города при пустых значениях
         */
        function initCityAutoFill() {
            $.each([inputFromCity, inputToCity], function() {
                $(this).on('blur', function() {
                    var _this = $(this);
                    if ($.trim(_this.val()).length === 0) {
                        _this.val(window.city);
                    }
                });
            });
        }
        /**
         * Инициализация сервиса поиска меня на карте через IP
         * @returns {undefined}
         */
        function initFindMyGeoLocation() {
            if (geo_position_js.init()) {
                var findme = $('#find-me');
                peloader = $("<img>").attr("src", "/preloader.gif").css({"margin": "7px 5px 0 -24px", "float": "right"});
                findme.html('<img src="target.png" />')
                        .css({
                    "cursor": "pointer",
                    "color": "#08c"
                })
                        .before(peloader.hide())
                        .on('click', function(e) {
                    findme.hide();
                    e.preventDefault();
                    peloader.show();
                    FROM_HOUSE.show();
                    geo_position_js.getCurrentPosition(function(res) {
                        taxi.mypos(res, function() {
                            peloader.hide();
                            findme.show();
                        });
                    }, function() {

                    });
                });
            }
        }

        initTaxiMapObjects();
        writeDefaultCity();
        initCityAutoFill();
        initFindMyGeoLocation();

        // Включение автокомплита для улицы, событий при фокусе и потере фокуса входными инпутами

        /**
         * Инициализация автокомплита для группы контролов
         * @param {type} autoCompleteOptions
         * @returns {undefined}
         */
        function initAutoComplete(autoCompleteOptions) {
            // автокоплит для города
            autoCompleteOptions.cityInput.geo_autocomplete({
                city: autoCompleteOptions.cityInput,
                house: autoCompleteOptions.houseInput,
                housing: autoCompleteOptions.housingInput,
                // ищем именно насленный пункт
                inputType: 'locality',
                close: function(event, ui) {
                    // обрезка всего лишнего      
                    var isFromControl = (autoCompleteOptions.wayPointIndex == 0);
                    taxi.updateRoute(isFromControl);
                    var old = autoCompleteOptions.cityInput.val() + "";
                    old = old.replace(/,.*$/g, '');
                    autoCompleteOptions.cityInput.val(old);
                }
            });
            // автокомплит для улицы
            autoCompleteOptions.streetInput.geo_autocomplete({
//                geocoder: taxi.geocoder,
                city: autoCompleteOptions.cityInput,
                house: autoCompleteOptions.houseInput,
                housing: autoCompleteOptions.housingInput,
                inputType: 'street',
                close: function(event, ui) {
                    // обрезка всего лишнего      
                    var isFromControl = (autoCompleteOptions.wayPointIndex == 0);
                    taxi.updateRoute(isFromControl);
                    var old = autoCompleteOptions.streetInput.val() + "";
                    old = old.replace(/,.*$/g, '');
                    autoCompleteOptions.streetInput.val(old);

                    // Раньше устанавливалась точка начала или конца маршрута, сейчас же ничего не делаем просто обновляем улицу\город\дом и т.д.
                    // autoCompleteOptions.taxiSetPointFunction(ui.item);                    
                }
            }).on('focus', function() {
                autoCompleteOptions.hiddenControls.show();
            }).on('blur', function() {
                if (!$(this).val()) {
                    autoCompleteOptions.taxiSetPointFunction(null);
                    autoCompleteOptions.hiddenControls.hide().find('input').each(function() {
                        $(this).val('');
                    });
                } else {
                    autoCompleteOptions.hiddenControls.show();
                }
            });

            /**
             * Функция для обновления маршрута от или до точки
             * Согласно текущему адресу
             */
            var currentUpdateFunction = function() {
                var city = autoCompleteOptions.cityInput.val() || window.city;
                var _address =
                        city + ", "
                        + autoCompleteOptions.streetInput.val() + ", "
                        + autoCompleteOptions.houseInput.val()
                        + ((autoCompleteOptions.housingInput.val()) ? " к " + autoCompleteOptions.housingInput.val() : "");
                taxi.geocoder(_address, 1, function(res) {
                    if (res.length === 0 || (!res[0].street && !res[0].city)) {
                        // taxi.startpoint(null,'value,city, house');
                        return;
                    }
                    // autoCompleteOptions.taxiSetPointFunction(null);
                    taxi.wayPointsData[autoCompleteOptions.wayPointIndex] = res[0].label;

                    autoCompleteOptions.taxiSetPointFunction(res[0], 'value,city,house,housing');
                });
            };

            // Строим маршруты при изменении инпутов через некоторое время
            $.each([autoCompleteOptions.houseInput, autoCompleteOptions.housingInput], function() {
                $(this).on('focus', function() {
                    $(this).data('val', $(this).val());
                }).on('blur', function() {
                    // замена на русские буквы для букв копрусов зданий
                    autoCompleteOptions.houseInput.val(
                            autoCompleteOptions.houseInput.val().replace("a", "а").replace("b", "б")
                            );
                });
            });

            var res = {
                updateFunction: currentUpdateFunction
            };
            return res;
        }
        var tmp = initAutoComplete({
            hiddenControls: FROM_HOUSE,
            streetInput: inputFromStreet,
            cityInput: inputFromCity,
            houseInput: inputFromHouse,
            housingInput: inputFromHousing,
            taxiSetPointFunction: taxi.startpoint,
            wayPointIndex: 0
        });
        taxi.updateFromFunction = tmp.updateFunction;
        var tmp = initAutoComplete({
            hiddenControls: TO_HOUSE,
            streetInput: inputToStreet,
            cityInput: inputToCity,
            houseInput: inputToHouse,
            housingInput: inputToHousing,
            taxiSetPointFunction: taxi.endpoint,
            wayPointIndex: 1
        });
        taxi.updateToFunction = tmp.updateFunction;

        /**
         * Функция для обновления роута через введенные адреса
         * @returns {undefined}
         */
        taxi.updateRoute = function(isFromControl) {
            if (isFromControl) {
                taxi.updateFromFunction();
            } else {
                taxi.updateToFunction();
            }
        };
    });

});