/*
 * ВТОРАЯ ЧАСТЬ ОСНОВНОГО БАЗОВОГО КОДА:
 * более безопасная - можно использовать обфускацию
 * http://www.jsobfuscate.com/index.php
 */

window["taxi"] = new function() {
    var self = this;
    // приватные переменные
    self.points = [null, null];
    self.city = '';

    //приватные функции
    self.geocoder = function() {
    };
    self.map = function() {
    };
    self.routeinfo = function() {
    };
    self.geocoders = new Collection();
    self.maps = new Collection();

    self.selectGeocoder = function(name) {
        //выбираем геокодер из списка добавленных
        self.geocoder = self.geocoders.get(name).fn;

        return self;
    };
    self.setCity = function(city) {
        /*
         задаем город по умолчанию
         используется только при инициализции карты чтоб спозиционировать в нужном городе
         */
        self.city = city;
        return self;
    };

    self.startInput = function(fn) {
        //привязываем функцию-обработчик вывода значения точки старта для вывода в форму
        if (typeof(fn) === 'function') {
            self.startInput = fn;
        }
        return self.startInput;
    };
    self.endInput = function(fn) {
        //привязываем функцию-обработчик вывода значения точки финиша для вывода в форму
        if (typeof(fn) === 'function') {
            self.endInput = fn;
        }
        return self.endInput;
    };


    return {
        maps: function() {
            return self.maps;
        },
        map: function() {
            return self.maps.first();
        },
        startpoint: function(point, skip) { //задаем точку старта
            /*
             point - точка полученая от геокодера
             skip - строка со списком полей, разделенных через зяпятую,  которые следует исключить при обработке вывода в форму
             */
            if (typeof(point) === 'object') {
                self.points[0] = point;
                self.map.setPoints(self.points); //задаем точки (внутри setPoints вызвыается рендеринг маршрута)
                skip = skip || "";
                self.startInput(point, skip);//вызываем обработчик вывода точки в форму и передаем точку и поля котоные не нужно изменять
            }
            return self.points[0];
        },
        findpoints: function() {
            return self.points;
        },
        endpoint: function(point, skip) {
            //аналогично предыдущей функции, только для точки финиша
            if (typeof(point) === 'object') {
                self.points[1] = point;
                self.map.setPoints(self.points);
                skip = skip || "";
                self.endInput(point, skip);
            }
            return self.points[1];
        },
        addGeocoder: function(name, fn) {
            //добавляем геокодер в коллекцию геокодеров
            self.geocoders.add({
                name: name,
                fn: fn
            });
            return self;
        },
        geocoder: function(find, limit, callback) {
            //публичный доступ к геокодеру для работы автокомплита
            return self.geocoder.find(find, limit, callback);
        },
        mypos: function(loc, callback) {
            //публичная функция, ставит точку старта по loc и передает callback
            return self.map.mypos(loc, callback);
        },
        addMap: function(name, fn) {
            //добавляем карту в коллекцию карт
            self.maps.add({
                name: name,
                fn: fn
            });
            return self;
        },
        selectMap: function(name) {
            /*
             выбираем карту
             в принципе можно сделать её приватной,
             но оставлю публичной на случай если кому-то из заказчиков
             захочется иметь возможность на лету её поменять
             
             для при уже после taxi.init({}) достаточно вызвать:
             taxi.selectMap('google')
             или
             taxi.selectMap('yandex')
             */
            self.map = self.maps.get(name).fn;
            //кешируем объект для передачи публичных методов
            var taxiObj = this;
            self.map.init({
                geocoder: self.geocoder.find,
                city: self.city,
                mapcontainer: self.mapcontainer,
                points: self.points,
                startpoint: function(res) {
                    taxiObj.startpoint(res);
                },
                endpoint: function(res) {
                    taxiObj.endpoint(res);
                },
                //т.к. следующие функции у нас приватные и недоступны извне, то явно передаем их
                startInput: function(res) {
                    self.startInput(res);
                },
                endInput: function(res) {
                    self.endInput(res);
                },
                routeinfo: function(res) {
                    self.routeinfo(res);
                }
            });
            return self;
        },
        init: function(param) {
            /*
             добавляем наши обработчики из init.js
             */
            self.routeinfo = param.routeinfo; //обработчик расстояния
            self.startInput = param.startInput; //обработчик инпутов точки старта
            self.endInput = param.endInput; //обработчик ипнутов точки финиша

            self.setCity(param.city); //задаем город поумолчанию для карты
            self.selectGeocoder(param.geocoder); //выбираем и инициализируем геокодер
            self.mapcontainer = param.mapcontainer; //задаем id контейнера карты
            this.selectMap(param.map);

        },
        wayPointsData: []
    };
};

// ? создаем рабочий основной объект
var taxi = window["taxi"];

//добавляем карты и геокодеры
taxi.addMap('yandex', new function() {
    if (typeof(ymaps) === "undefined" || ymaps === null) {
        //если обьект ymaps не инициализирован, то мы не сможем дальше строить карту
        return;
    }
    var yandexMap = this; //закешируем объект
    yandexMap.route = null;
    yandexMap.point_source = null;
    yandexMap.mypos = null;
    yandexMap.points = null;
    yandexMap.lastRoute = null;
    function ymapsinit() {
        yandexMap.geocoder(yandexMap.city, 1, function(res) {
            /*
             делаем запрос к геокодеру по названию нашего города по умолчанию
             и ставим карту по центу полученных координат
             */
            var pos = res[0].point;

            yandexMap.map = new ymaps.Map(yandexMap.mapcontainer, {//передаем id блока, куда вставлять карту
                center: [pos[0], pos[1]], //задаем центр
                zoom: 11, //ставим zoom = 11
                behaviors: ['default', 'scrollZoom', 'multiTouch'] //добаялем скролл колесом мыши и жесты мультитача для зума и перемещения карты
            });
            yandexMap.map.controls
                    .add('zoomControl')
                    .add('typeSelector');
        });

//---------------------------------------------------------------------------------------------------------
	var source = window.source + '';
	if (source.length > 0){
		ymaps.geocode(window.order_city +','+window.source, { results: 1 }).then(function (res) {
			var firstGeoObject = res.geoObjects.get(0);
			yandexMap.map.geoObjects.add(new ymaps.Placemark(firstGeoObject.geometry.getCoordinates(),{balloonContentBody:'Адрес посадки',hintContent:'Адрес посадки'},{}));
		});	
	}
		
	if(window.order && parseInt(window.crew, 10) > 0)
		var fp = window.crew;
	else
		var fp = false;
			
	setInterval(function(){AddMarks(yandexMap, fp)}, 12000);
//---------------------------------------------------------------------------------------------------------


    }

	function AddMarks(yaMap, fp){
	
		$.ajax({
		  url: '/include/ajax_cars_info.php',
		  type: 'GET',	
		  data: {crew_id: fp},
		  success: function(data){
			var points = [];

			for(var point in yandexMap.points){
				if (yandexMap.points[point] == null) continue;
				RemoveMarks(yandexMap.points[point]);
			}

			
			for(var key in data){
				var val = data[key];			

				if (val.gos_number == null) val.gos_number = '---';
				
				if (val.status == 'waiting'){
					points[val.crew_code] = new ymaps.Placemark([val.lat, val.lon], {
				        balloonContentBody: val.name_car + " <em>" + val.color_car.toLowerCase() + "</em>",
						balloonContentFooter: 'Позывной: '+val.crew_code+"<br />" + 'Гос.номер: ' + val.gos_number,
						hintContent: val.name_car + " <em>" + val.color_car.toLowerCase() + "</em>"		
				    },{iconImageHref:'/ico-taxi.png', iconImageSize: [41, 36]})
	
					points[val.crew_code].crew_code = val.crew_code;				
					AddEvents(points[val.crew_code], val.crew_id);
				}
				else{
					points[val.crew_code] = new ymaps.Placemark([val.lat, val.lon], {hintContent: val.name_car + " <em>" + val.color_car.toLowerCase() + "</em>"}, {iconImageHref:'/ico-taxi-red.png', iconImageSize: [41, 36]});
					points[val.crew_code].crew_code = val.crew_code;
				}				
				yaMap.map.geoObjects.add(points[val.crew_code]);

			}
			
			yandexMap.points = points;	
		  },
		  dataType: 'json'
		});
		
	}
	
	function RemoveMarks(mark){
		yandexMap.map.geoObjects.remove(mark);
	}	


	function AddEvents(mark, id){
		mark.events.add('click', function(e){				
			$('#FIELD_TYPE_AUTO').val('Хочу заказать машину с позывным: '+mark.crew_code);					
		})
	}
//---------------------------------------------------------------------------------------------------------	
	

    /*function resetroute() {
     }*/
    return {
        name: 'yandex', //возвращаем поле name для поиска по коллекции карт
        init: function(params) {
            jQuery.extend(yandexMap, params);
            ymaps.ready(ymapsinit);
        },
        //строим роутинг|добавлем точки
        createroute: function() {
            //если уже строили, то удаляем с карты
            if (yandexMap.route) {
                yandexMap.map.geoObjects.remove(yandexMap.route);
            }
             //если точка начального местоположения уже есть на карте, то удаляем её
            if (yandexMap.mypos) {
                yandexMap.map.geoObjects.remove(yandexMap.mypos);
            }
            yandexMap.mypos = null;
            yandexMap.route = null;
	    yandexMap.source_pos = null;	

            if (!yandexMap.points[0] || typeof(yandexMap.points[0].length) === "number") {
                //если не задана точка старта, то расходимся
                return;
            }
            if (!yandexMap.points[1] || typeof(yandexMap.points[1].length) === "number" && yandexMap.points[0]) {
                /*
                 если задана точка старта, но нет точки финиша,
                 то ставим точку и вешаем на неё событие переноса по карте
                 */
		
                yandexMap.mypos = new ymaps.Placemark(yandexMap.points[0].point, {//инициализируем точку
                    // iconContent: ""
                }, {
                    draggable: true
                });
                yandexMap.mypos.events.add('dragend', function() {//внешаем событие окончания переноса
                    yandexMap.geocoder(yandexMap.mypos.geometry.getCoordinates(), 1, function(res) {
                        //если по полученным резуальтатам есть ответ геокодера, то задаем новую точку старта из первого результата
                        if (res) {
                            // self.startInput(res[0]);
                            yandexMap.startpoint(res[0]);
                            // self.points[0].label= res[0]['label'];
                            yandexMap.points[0].point = yandexMap.mypos.geometry.getCoordinates();
                        }
                    });
                });
                yandexMap.map.geoObjects.add(yandexMap.mypos); // добавляем точку на карту
                yandexMap.map.setCenter(yandexMap.points[0].point); //центруем карту по точке
                return; //завешраем работу функции
            }

            //сюда мы попадаем только когда заданы обе точки
            var point = [];
            for (var i = 0, l = yandexMap.points.length; i < l; i++) {
                //получам массив только с координатами
                point[i] = yandexMap.points[i].point;
            }
            //скармливаем массив с координатами роутеру
            ymaps.route(point, {
                mapStateAutoApply: true
            }).then(function(router) {
                // Удаление старого пути, если он был

                //   myRoute && myMap.geoObjects.remove(myRoute);
                if (yandexMap.lastRoute) {
                    yandexMap.map.geoObjects.remove(yandexMap.lastRoute);
                }

                yandexMap.route = router;
                yandexMap.editor = yandexMap.route.editor;
                yandexMap.editor.start({addWayPoints: false}); //запрещаем добавлене точек кликом на карте
                var editpoint;
                yandexMap.editor.events
                        .add('routeupdate', function() {
                    /*
                     у карт яндекса есть действующая на нервы особенность:
                     после обновления маршрута он выставляет зум так, 
                     чтобы маршрут умещался в область видимости карты.
                     если с первого раза при драге точки не попасти в нужную,
                     то надо заново зумить и двигать
                     */
                    if (yandexMap.route) {
                        yandexMap.routeinfo({
                            length: yandexMap.route.getLength(), // Длина маршрута
                            time: yandexMap.route.getJamsTime()// Время маршрута с пробками
                        });
                    }
                })
                        .add('waypointdragstart', function(e) {
                    var waypoint = e.get('wayPoint');
                    //некрасивый костыль для определения какая сейчас точка двигается
                    editpoint = parseInt(waypoint.properties.get('iconContent'), 10) - 1;
                })
                        .add('waypointdragend', function(e) {
                    if (typeof(editpoint) === "number") {
                        var waypoint = e.get('wayPoint');
                        yandexMap.geocoder(waypoint.geometry.getCoordinates(), 1, function(res) {
                            // console.log(waypoint.geometry.getCoordinates());
                            if (res.length > 0) {
                                switch (editpoint) {
                                    case 0: //если двигали точку с индкесом 0, то меняем координаты точки старта
                                        yandexMap.startpoint(res[0]);
                                        break;
                                    case 1: //если индекс был 1, то меняем точку финиша
                                        yandexMap.endpoint(res[0]);
                                        break;
                                }
                                taxi.wayPointsData[editpoint] = res[0].label;
                            } else {
                                // Если yandexMap.geocoder ничего не нашел на наши координаты                                
                                taxi.wayPointsData[editpoint] = editpoint + 1 + '';
                                yandexMap.updateWayPointsText();
                            }
                        });
                    }
                });
                yandexMap.route.getPaths().options.set({
                    strokeColor: '0000ffee', //задаем синий цвет обводки пути в формате rgba, т.е. r = 00, g = 00, b = ff, alpha = ee
                    opacity: 0.9 //прозрачность пути
                });
                yandexMap.editor.events.fire('routeupdate');
                // сохраним ссылку на прошлый путь для его удаления
                yandexMap.lastRoute = yandexMap.route;
                yandexMap.map.geoObjects.add(yandexMap.route);

                /**
                 * Обновить Подписи к путевым точкам
                 * @returns {undefined}
                 */
                yandexMap.updateWayPointsText = function() {
                    yandexMap.route.getWayPoints().each(function(item, i) {
//                    console.log(item);
//                    console.log(item.properties.get('balloonContent'));
                        //console.log(taxi.wayPointsData);
                        if (taxi.wayPointsData.length > i) {
                            var address = taxi.wayPointsData[i];
                        } else {
                            var address = '';
                        }
                        var coordinates = item.geometry.getCoordinates();
                        // Координаты в неверном порядке выводились янексом по умолчанию, так что меняем/или не меняем их
                        var content = coordinates[0] + ", " + coordinates[1];
                        // console.log(address);
                        item.properties.set('balloonContentBody',
                                '<p>'
                                + address
                                + '</p>'
                                + '<span style="font-size: small">('
//                                + item.properties.get('balloonContent')
                                + content
                                + ')</span>');
                    });
                };
                yandexMap.updateWayPointsText();
//                console.log(yandexMap.map);
//                yandexMap.map.setZoom(yandexMap.map.getZoom() - 1);
                /*
                 var address = geocode.geoObjects.get(0) &&
                 geocode.geoObjects.get(0).properties.get('balloonContentBody') || '';
                 
                 var distance = Math.round(router.getLength() / 1000),
                 message = '<span>Расстояние: ' + distance + 'км.</span><br/>' +
                 '<span style="font-weight: bold; font-style: italic">Стоимость доставки: %sр.</span>';
                 
                 self._route = router.getPaths();
                 self._route.options.set({ strokeWidth: 5, strokeColor: '0000ffff', opacity: 0.5 });
                 self._map.geoObjects.add(self._route);
                 self._start.properties.set('balloonContentBody', address + message.replace('%s', self.calculate(distance)));
                 self._start.balloon.open();
                 */


            });

        },
        //адает точки и строит маршрут, используется при обновлени точек из инпутов
        setPoints: function(points) {
            yandexMap.points = points;
            this.createroute();
        },
        mypos: function(loc, callback) {
            // var mymap = this;
            var pos = [loc.coords.latitude, loc.coords.longitude];
            yandexMap.geocoder(pos, 1, function(res) {
                callback(); //вызываем колбек: убираем анимацию преловадера, возвщаем на место иконку "найти меня"
                if (!res || res.length === 0 || res[0].length === 0) {
                    return;
                } //если нет результата то выходим
                yandexMap.startpoint(res[0]); //задаем точку старта
                ymaps.getZoomRange('yandex#map', res[0].point).then(function(result) {
                    yandexMap.map.setZoom(result[1] - 1); //ставим зум на точку
                });
            });
        }
    };
});

/**
 * Создание и инициализации общего объекта - внутреннего (универсального геокодера) для карт яндекс
 * При этом описываем запрос по методу find(searchWords:string, answersLimit:integer, callback:function) после запроса на поиск автокомплита
 * или обработки геокодера
 * @param {string} - имя этого геокодера с коллекции геокодеров
 * @param {type} - конструктор объекта-геокодера, главная функция в котором - это find
 */
taxi.addGeocoder('yandex', new function() {
    //проверяем подгружен ли апи яндекс карт
    if (typeof(ymaps) === "undefined" || ymaps === null) {
        return;
    }
    return {
        // имя нашего внутреннеого геокодера в коллекции
        name: 'yandex',
        // переопределение основной функции, также тут и возможно ограничить территорию выдачи резальтатов по прямоугольной области поиска:
        // http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/geocode.xml
        find: function(find, limit, callback) {
            //Меняем местами координаты для обратного декодирования
//            if (typeof(find) === 'object' && find.length === 2){
//                var tmp = find;
//                find[0] = tmp[1];
//                find[1] = tmp[0];
//            }
            // find = 'Россия, ' + find;
            limit = limit || 1;
            var geocodes = []; //инициализируем пустой массив в результатми
            // Произвольные опции для: например, ограничения выборки геокодера
            // http://api.yandex.ru/maps/doc/jsapi/2.x/ref/reference/geocode.xml

            // ?? зададим некоторую область поиска вокруг главного города
//            var cityCoors = [53.211463, 56.852775]
//                    , boundedByRadius = 1
//                    ;
//            var from = [cityCoors[0] - boundedByRadius, cityCoors[1] - boundedByRadius]
//                    , to = [cityCoors[0] + boundedByRadius, cityCoors[1] + boundedByRadius];
            // var moscowBounds = [[55.94305795164068, 37.29843782760007], [55.544884368537986, 38.03452181197507]];

            ymapsGeocoderOptions = {
                // Ограничение области поиска: углы прямоугольной области
                // boundedBy: [from, to],
                // boundedBy: moscowBounds,
                // Искать только внутри области, заданной опцией boundedBy. Значение по умолчанию: false        
//                strictBounds: limit !== 1,        
                strictBounds: false,
                results: limit
            };
//            console.log(ymapsGeocoderOptions);
            //отправялем запрос в геокодер
            // http://geocode-maps.yandex.ru/1.x/?geocode=50.58792450886083,36.24359766992159
//            console.log(find);
            ymaps.geocode(find, ymapsGeocoderOptions).then(
                    function(res) {
//                        console.log(res);
                        //получаем результат и обрабатываем каждый элемент по отдельности
                        res.geoObjects.each(function(item) {

                            /**
                             * Т.к. не нашлось лучшего способа получить инфу о результатах ищем по возможным путям:
                             * смотреть пример из GET API геокодера:
                             * http://geocode-maps.yandex.ru/1.x/?geocode=Пермь
                             * Тут можно понять где именно лежит инфа 
                             * @returns {object|false} - false - если не будет найден объект геоинфы
                             */
                            function internalFindLocality(item) {
                                var locality = false;
                                var searchLocalityIn = [
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.Locality',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName.Locality',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AddressLine',
                                    'metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.SubAdministrativeAreaName'
                                ];
                                // Координаты в неверном порядке выводились янексом по умолчанию, так что меняем/или не меняем их
                                var coordinates = item.geometry.getCoordinates();
                                var coordinatesString = coordinates[0] + ", " + coordinates[1];
                                for (var searchIndex in searchLocalityIn) {
                                    locality = item.properties.get(searchLocalityIn[searchIndex]);
                                    if (locality) {
                                        if (typeof(locality) === 'string') {
                                            locality.LocalityName = locality;
                                            locality.PseudoStreet = coordinatesString;
                                        }
                                        return locality;
                                    }
                                }
                                locality = coordinatesString;
                                return locality;
                            }
                            // console.log(item);                            1                            
                            var Locality = internalFindLocality(item);

                            if (!Locality) {
                                return;
                            } //если в этой переменной пусто, то ниче не обрабатываем

                            var dependent = "";//район                            
                            var street = typeof(Locality) === 'string' ? Locality : ""; //улица или координаты \ 
                            var house, housing; //дом, корпус
                            house = housing = "";

                            var city = Locality.LocalityName || ""; //за значение города берем поле LocalityName

                            if (Locality.Thoroughfare) { //обычно тут прячется название улицы и дом
                                street = Locality.Thoroughfare.ThoroughfareName || Locality.Thoroughfare.Premise.PremiseName || Locality.PseudoStreet; //берем название улицы
                                if (Locality.Thoroughfare.Premise) { //смотрим есть ли массив в котором должен быть спрятан номер дома
                                    if (Locality.Thoroughfare.Premise.PremiseNumber) {//если домер дома есть — берем его
                                        house = Locality.Thoroughfare.Premise.PremiseNumber;
                                        housing = house.match(/[к][0-9]+$/i);
                                        if (housing !== null) { //герекспом разбиваем ответ и ищем совпадение в маской [к][0-9]+$
                                            housing = housing[0].match(/[+0-9]+$/i)[0]; //получаем номер корпуса
                                            house = house.match(/^[0-9\/]+/i)[0]; //оставляем только номер дома
                                        }
                                    }
                                }
                            } else if (Locality.DependentLocality) { //если нет массива с улицей, то ищем район
                                dependent = Locality.DependentLocality.DependentLocalityName; //берем название района
                                if (Locality.DependentLocality.Thoroughfare) { //ищем улицу в районе
                                    street = Locality.DependentLocality.Thoroughfare.ThoroughfareName; //заносим название улицы
                                }
                            } else if (Locality.Premise) { //хз что именно означает этот массив, но в нем тоже может оказаться улица
                                street = Locality.Premise.PremiseName; //берем улицу
                            }

                            //формируем поле ответа геокодера для вывода в автокомплите
                            var c = (city) ? city : "";
                            var d = (dependent) ? ", " + dependent : "";
                            var s = (street) ? ((city) ? ", " + street : street) : "";
                            var h = (house) ? " " + house : "";
                            var hs = (housing) ? " корпус " + housing : "";
                            var label = c + d + s + h + hs;
                            var value = street;
                            /*старый код, раньше вырезалось все регекспом, не совсем надежно*/
                            //var label item.properties.get('name');
                            // var value = item.properties.get('name');
                            /* var elements,remove;
                             elements = remove = null;
                             
                             elements = label.split(',');
                             for (var j = 0; j <= elements.length - 1; j++) {
                             
                             elements[j] = elements[j].replace(/^\s/g,'');
                             
                             if (remove==null){
                             var reg = elements[j].match(/^[0-9]+[\/а-яa-z]*[+0-9]*$/i);
                             if (reg!=null){
                             house = reg[0];
                             housing = house.match(/[к][0-9]+$/i);
                             if (housing!=null){
                             housing = housing[0].match(/[+0-9]+$/i)[0];
                             house = house.match(/^[0-9\/]+/i)[0];
                             }
                             remove = j;
                             
                             }
                             }
                             }
                             if (remove != null){
                             elements.splice(remove,1);
                             elements.splice(0,1);
                             value = elements.join();
                             }*/

                            //записываем результат ответа в массив
                            geocodes.push({
                                point: item.geometry.getCoordinates(),
                                street: street,
                                city: city,
                                label: label,
                                house: house,
                                housing: housing,
                                value: value
                            });
                        }); //end of each
                        //console.log(geocodes);
                        if (typeof(callback) === 'function') {
                            //вызываем колбек и передаем ему результаты обработки
                            callback(geocodes);
                        }
                    },
                    function(/*err*/) {
                    }
            );
        }
    };
});

taxi.addMap('google', new function() {
    if (typeof(google) === "undefined" || google === null) {
        return;
    }
    var googleMap = this;
    googleMap.directionsService = null;
    googleMap.route = null;
    googleMap.mypos = null;
    // googleMap.points = [];
    return {
        name: 'google',
        init: function(params) {
            jQuery.extend(googleMap, params);
            //инициализируем карту по координатам города по умолчанию
            googleMap.geocoder(googleMap.city, 1, function(res) {
                //ставим точку по первому результату поиска
                var pos = res[0].point;
                googleMap.map = new google.maps.Map(document.getElementById(googleMap.mapcontainer), {//рисуем в блоке mapcontainer
                    zoom: 11,
                    center: new google.maps.LatLng(pos[0], pos[1]), //ставим точку центра по полученным координатам предворительно преобразовав их в нужный гуглу формат
                    mapTypeId: google.maps.MapTypeId.ROADMAP //выбираем  тип карты
                            /*
                             ROADMAP – стандартные двухмерные фрагменты Google Карт.
                             SATELLITE – фрагменты, представленные сделанными со спутника фотографиями.
                             HYBRID – фотографические фрагменты с наложенным слоем, содержащим наиболее важные объекты (дороги, названия городов).
                             TERRAIN – фрагменты топографической карты с рельефом местности, высотами и гидрографическими объектами (горы, реки и т. д.).
                             */
                });
            });
            //инициализируем объекты
            googleMap.directionsService = new google.maps.DirectionsService(); //возвращает параметры маршрура, т.е. длину пути, время, промежучные точки и т.п.
            googleMap.route = new google.maps.DirectionsRenderer({draggable: false}); //рисует маршрут
            googleMap.mypos = new google.maps.Marker(); //точка "моего" местоположения
        },
        createroute: function() {

            googleMap.route.setMap(null); //удаляем маршрут с карты (не совсем удаляем, переопределяем местоположения рендеринга в null)
            googleMap.mypos.setMap(null); //удаляем точку местоположения
            if (!googleMap.points[0] || typeof(googleMap.points[0].length) === "number") {
                //если у нас нет точки старта, то выходим
                return;
            }
            if (!googleMap.points[1] || typeof(googleMap.points[1].length) === "number" && googleMap.points[0]) {
                //если есть точка старта, но нет точки финиша, то рисуем точку с "моим местоположением" котрую можно двигать мышью
                var pos = googleMap.points[0].point;
                pos = new google.maps.LatLng(pos[0], pos[1]);
                //задаем точу с "моим местоположением"
                googleMap.mypos = new google.maps.Marker({
                    map: googleMap.map,
                    draggable: true,
                    position: pos
                });
                googleMap.mypos.setMap(googleMap.map);//добавляем её на карту
                googleMap.map.setCenter(pos); //центруем карту по точке

                //вешаем событие dragend для изменения точки и данных в форме
                google.maps.event.addListener(googleMap.mypos, "dragend", function() {
                    var point = googleMap.mypos.getPosition(); //получаем координаты где остановился драг точки
                    point = [point.lat(), point.lng()]; //преобразуем координаты в простой массив
                    googleMap.geocoder(point, 1, function(res) {
                        //если геокодер что-то знает об этой точке то задаем её как точку старта и изменяем данные в форме
                        if (res) {
                            googleMap.startpoint(res[0]);
                        }
                    });

                });
                return;
            }
            //если у нас есть все необходимые данные то задаем место рендеринга нашего маршрута в карту
            googleMap.route.setMap(googleMap.map);

            //получаем точку старта
            var start = googleMap.points[0].point;
            start = new google.maps.LatLng(start[0], start[1]);
            //получаем точку финиша
            var end = googleMap.points[1].point;
            end = new google.maps.LatLng(end[0], end[1]);
            //формируем запрос к сервису построения маршрута
            var request = {
                origin: start, //точка старта
                destination: end, //точка финиша
                travelMode: google.maps.TravelMode.DRIVING //тип маhршрута
                        /*
                         BICYCLING   маршрут для велосипеда
                         DRIVING     маршрут для автомобиля
                         TRANSIT     маршрут для общественного транспорта
                         WALKING     маршрут для пешехода
                         */
            };
            //строим маршрут
            googleMap.directionsService.route(request, function(result, status) {
                //если есть результат, то передаем его в рисовальщик маршрута
                if (status === google.maps.DirectionsStatus.OK) {
                    googleMap.route.setDirections(result);
                }
            });
            //вешаем событие отвечающее за изменение машртура
            google.maps.event.addListener(googleMap.route, 'directions_changed', function() {
                //получаем легенду маршрута
                var leg = googleMap.route.directions.routes[0].legs[0];
                googleMap.routeinfo({
                    length: leg.distance.value, // Длина маршрута
                    time: leg.duration.value// Время маршрута с пробками
                });
                return;

            });
        },
        setPoints: function(points) {
            googleMap.points = points;
            this.createroute();
        },
        mypos: function(loc, callback) {
            var pos = [loc.coords.latitude, loc.coords.longitude];
            // var mymap = this;
            googleMap.geocoder(pos, 1, function(res) {
                callback(); //вызываем колбек скрывающий иконку поиска местоположения и возвращающий кнопку поиска
                if (!res) {
                    return;
                } //если нет результата то выходим
                googleMap.startpoint(res[0]);//задаем точку по полученным координатам
                var pos = res[0].point;
                var latlng = new google.maps.LatLng(pos[0], pos[1]);
                //задаем зум к нашей точке по принципу: максимально возможный зум для этой области минус 3
                var maxZoomService = new google.maps.MaxZoomService();
                maxZoomService.getMaxZoomAtLatLng(latlng, function(response) {
                    if (response.status === google.maps.MaxZoomStatus.OK) {
                        googleMap.map.setZoom(response.zoom - 3);
                    }
                });
                //центруем карту
                googleMap.map.setCenter(latlng);
            });
        }
    };
});

taxi.addGeocoder('google', new function() {
    //если не подгружжено апи гугла, то расходимся
    if (typeof(google) === "undefined" || google === null) {
        return;
    }
    //инициализируем функцию геокодера
    var geocoder = new google.maps.Geocoder();
    return {
        name: 'google',
        find: function(find, limit, callback) {
            limit = limit || 1;
            //если входные данные в find -- массив координат, то преобразуем в массив координат понятных геокодеру
            if (typeof(find) === 'object') {
                find = {'latLng': new google.maps.LatLng(find[0], find[1])};
            } else if (typeof(find) === 'string') { //если входные данные строка, то формируем запрос для обратного геокодирования
                find = {'address': find};
            }

            //вспомогательная функция для выборки их масства только значения поля long_name при вхождении type в types
            function getType(arr, type) {
                for (var i = arr.length - 1; i >= 0; i--) {
                    if (jQuery.inArray(type, arr[i].types) !== -1) {
                        return arr[i].long_name;
                    }
                }
                return "";
            }
            var geocodes = [];
            var param = {
                //фильтр по региору ru
                'region': 'ru'
            };
            jQuery.extend(param, find);
            geocoder.geocode(param, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    jQuery.each(results, function(i, result) {
                        var city, street, dependent;
                        city = street = dependent = "";
                        var house, housing;
                        house = housing = "";

                        city = getType(result.address_components, 'locality');//получаем город
                        street = getType(result.address_components, 'route'); //получаем улицу
                        if (!street) { //если нет улицы, то берем результат с типом point_of_interest, в нем могу быть например: автовокзал или жд вокзал
                            street = getType(result.address_components, 'point_of_interest');
                        }
                        if (!street) { //если предыдущий поиск ничего не дал, то ищем любое общественное здание
                            street = getType(result.address_components, 'establishment');
                        }
                        if (!street) { //если совсем ничего нет, то берем район
                            dependent = getType(result.address_components, 'administrative_area_level_2');
                        }
                        house = getType(result.address_components, 'street_number'); //номер дома
                        housing = house.match(/[к][0-9]+$/i);
                        if (housing !== null) {//ищем корпус, но пока гугл не умеет делить адреса по корпусам
                            housing = housing[0].match(/[+0-9]+$/i)[0];
                            house = house.match(/^[0-9\/]+/i)[0];
                        }

                        var c = (city) ? city : "";
                        var d = (dependent) ? ", " + dependent : "";
                        var s = (street) ? ((city) ? ", " + street : street) : "";
                        var h = (house) ? " " + house : "";
                        var hs = (housing) ? " корпус " + housing : "";
                        var label = c + d + s + h + hs;
                        var value = street;
                        //старый вариант поиска по регекспу, не особо надежен, но работает
                        // var label = item.properties.get('name');
                        // var value = item.properties.get('name');
                        /* var elements,remove;
                         elements = remove = null;
                         
                         elements = label.split(',');
                         for (var j = 0; j <= elements.length - 1; j++) {
                         elements[j] = elements[j].replace(/^\s/g,'');
                         if (remove==null){
                         var reg = elements[j].match(/^[0-9]+[\/а-яa-z]*[+0-9]*$/i);
                         if (reg!=null&&reg[0].length<6){
                         house = reg[0];
                         housing = house.match(/[к][0-9]+$/i);
                         if (housing!=null){
                         housing = housing[0].match(/[+0-9]+$/i)[0];
                         house = house.match(/^[0-9\/]+/i)[0];
                         }
                         remove = j;
                         }
                         }
                         }
                         if (remove != null){
                         elements.splice(remove,1);
                         }
                         var city = elements[1];
                         elements.splice(1,10); //сносим все что после города
                         elements.reverse(); // разворачиваем
                         value = elements.join(", ");*/

                        var loc = result.geometry.location;
                        //записываем результат в массив
                        geocodes.push({
                            point: [
                                loc.lat(),
                                loc.lng()
                            ],
                            city: city,
                            street: street,
                            label: label,
                            house: house,
                            housing: housing,
                            value: value
                        });
                    });
                    if (typeof(callback) === 'function') {
                        //передаем колбеку обработанный результат поиска
                        callback(geocodes);
                    }
                }
            });
        }
    };
});

/**
 * Класс для выполнения спецзапроса к яндексу
 */
var SuggestCaller = function() {
    var self = this;
    self._lastResponse = null;
    self.callback = function() {
    };

    /**
     * Создание из пришедсшего "Сырого" массива данных объекта-ответа
     * @param {type} data
     */
    self.createResponseObject = function(data) {
        if (data.length < 3) {
            console.log("Error in parsing suggest response!");
            return false;
        }
        var variants = [];
        for (var index in data[1]) {
            /*
             * ["geo", 
             "улица Баранова, Ижевск, республика Удмуртская", 
             "Россия, республика Удму...Ижевск, улица Баранова ",
             Object { hl=[3]}
             ]
             */
            var current = data[1][index];
            variants.push({
                type: current[0],
                label: current[1],
                fullLabel: current[2],
                systemData: current[3]
            });
        }
        var response = {
            part: data[0],
            variants: variants
        };
        return response;
    };

    /**
     * Глобальная функция для обработки ответа Yandex Suggest 
     * скроем её имя
     * @param {type} data
     * @returns {undefined}
     */
    window.function_Sj2dk83xZi450_callback = function(data) {
        // обработка "сырого" массива-ответа
        var responseObject = self.createResponseObject(data);
        self._lastResponse = responseObject;
        // вызов текущего переданного каллбека
        self.callback(responseObject);
    };

    /**
     * part — часть слова, Параметр 
     * ll задаёт долготу и широту центра области (в градусах), через кодированный пробел
     * spn — её протяженность (в градусах).
     * Протяженность области задается двумя числами, первое из которых есть разница между максимальной и минимальной долготой, 
     * а второе — между максимальной и минимальной широтой данной области.
     * lang=ru-RU
     * search_type=all
     * fullpath =1 выдает только полные названия, 
     * v=5, видимо, версия API, с др.цифрами не работает
     */
    var SuggestCallerOptions = function() {
        return {
            // Это должна быть объявленная выше глобальная уникальная
            callback: 'function_Sj2dk83xZi450_callback',
            part: '',
            lang: 'ru-RU',
            search_type: 'all',
            // Поиск по области всей России идет по умолчанию
            // Центр области
//                ll: '105.71206938476564,63.056744844350014',
            ll: '64.22769438476561,62.20712366243136',
            // Границы области
//                spn: '165.23437499985945,67.98129092248304',
            spn: '82.61718750000001,32.68027802373878',
            fullpath: '1',
            v: '5'
        };
    };
    self.optionsObject = new SuggestCallerOptions();

    /**
     * Внутрений вызов запрос
     */
    self.internalGetRequest = function() {
        var optionsObject = self.optionsObject;
        if (optionsObject.part && optionsObject.part.length > 1) {
            var url = 'http://suggest-maps.yandex.ru/suggest-geo';

            $.ajax({
                url: url,
                type: 'get',
                data: optionsObject,
                // установим кросс-доменность и специальный вид ответа - запрос будет обработан как код
                dataType: 'jsonp',
                jsonp: "jsonp",
                crossDomain: true,
                success: function(response) {
                    // сюда на самом деле упраление не придет, а будет выполнен код в ответе - т.к.
                    // запрос был кроссдоменным и AJAX не работает как обычно
                    console.log(response);
                }
            });
        } else {
            return false;
        }
    };
    return {
        part: '',
        callback: function(responseObject) {
        },
        // Тип поиска: возможно, что принимает значения: 'all' , 
        search_type: 'all',
        // Поиск по области всей России идет по умолчанию
        // Центр области
        ll: '64.22769438476561,62.20712366243136',
        // Границы области: Протяженность области задается двумя числами, первое из которых есть разница между максимальной и минимальной долготой, 
        // а второе — между максимальной и минимальной широтой данной области. 
        spn: '82.61718750000001,32.68027802373878',
        /**
         * Выполнить запрос к автоподсказчику по части поискового запроса используя текущие гео настройки поиска
         * @param {string} part - поисковый запроса - 'Россия, город Моск'
         * @param {function} callback - выполнить этот каллбек при успешном ответе
         * @returns {@exp;self@call;internalGetRequest}
         */
        search: function(part, callback) {
            if (typeof(callback) !== 'function') {
                console.log("Bad suggest function callback type! This type must be function(responseObject)!");
            }
            // Инициализируем себя == адаптер запросов и посылаем запрос    
            this.part = part;
            this.callback = callback;

            // передаем публичные поля в приватные поля
            self.callback = callback;
            self.optionsObject.part = this.part;
            self.optionsObject.search_type = this.search_type;
            self.optionsObject.ll = this.ll;
            self.optionsObject.spn = this.spn;

            return self.internalGetRequest();
        },
        getLastResponse: function() {
            return self._lastResponse;
        }
    };
};

// Инициализируем адаптер запросов и посылаем запрос
taxi.suggestCaller = new SuggestCaller();