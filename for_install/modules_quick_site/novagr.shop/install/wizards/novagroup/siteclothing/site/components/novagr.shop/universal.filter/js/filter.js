var objFilter = {
	SESS_INCLUDE_AREAS	: 0,	// включен ли режим редактирования
	FIRST_TIME			: 1,
	arFilterResponseData: {},
	arElementsSearch	: {},
	arScale				: {},	// шкалы для слайдеров
	USE_AJAX			: 1,	//
	FILTER_RESET		: 1,	// reload filter:	1 - Y, 0 - N
	CATALOG_RESET		: 1,	// reload catalog:	1 - Y, 0 - N
	CHANGE_WORKAREA		: 1,
	CATALOG_IBLOCK_ID	: "",	//
	CATALOG_IBLOCK_CODE	: "",	//
	TRADEOF_IBLOCK_ID	: "",	//
	TRADEOF_IBLOCK_CODE	: "",	//
	PATH				: "",	//
	CURRENT_SECTION_ID	: "",
	CURRENT_SECTION_CODE: "",
	SECTION_ID			: "",
	QUERY				: "",
	ROOT_PATH			: "",
	BRAND_ROOT			: "",
	nPageSize			: "",
	FASHON_ROOT			: "",
	FASHON_MODE			: "",
	CURRENCY_FORMAT		: {},
	orderRow            : "",
	noGET		: 1,
	firstTime	: 1,
	changeSlider: 0,
	SEARCH_WHERE: "",
	/**
	*
	*/
	showAjaxLoader	: function() {
		var height = $(".page-container").height();

		var waitHtml = '<div class="centerbg1" id="preloaderbg" style="display: block;height: '+height+'px"> \
		  <div class="centerbg2"> \
			<div id="preloader"></div> \
		  </div> \
		</div>';
		$(waitHtml).prependTo('body');
	},
	/**
	*
	*/
	hideAjaxLoader	: function() {

		$("#preloaderbg").remove();

	},

	/**
	* функция проверки находятся ли риски слайдера цены на краевых позициях
	*/
	checkPriceSliderState	: function()
	{
		if( $('#slider-CATALOG_PRICE_1').length )
		{
			var val = $('#slider-CATALOG_PRICE_1').data('slider').getValue();
			if( (val[0] == 0) && (val[1] == 9) )
				return true;
			else return false;
		}else return true;
	},

	/**
	* функция прячем/покажем кнопки очистить
	*/
	setClearBtnStatus	: function() {
		$('.accordion').each(function(i){
			if( $(this).find('.arFilter.selected').length > 0)
			{
				$(this).find('.my_clear').removeClass('hidden');
			} else{
				$(this).find('.my_clear').addClass('hidden');
			}
		});

		if ( $('#slider-CATALOG_PRICE_1').length )
		{
			var val = $('#slider-CATALOG_PRICE_1').data('slider').getValue();
			if(
				( $('.arFilter.selected').length == 0 )
				&&
				( val[0] == 0 )
				&&
				( val[1] == 9 )
			)
			{
				$('#my_clear').addClass('hidden');
			} else {
				$('#my_clear').removeClass('hidden');
			}
		}else{
			if( $('.arFilter.selected').length == 0 )
			{
				$('#my_clear').addClass('hidden');
			} else {
				$('#my_clear').removeClass('hidden');
			}
		}
	},
    setOrder: function(orderRow) {
        objFilter.orderRow = orderRow;
    },
	/**
	* посылаем запрос в каталог и обрабатываем ответ
	*/
	getElements	: function(iNumPage, changeURL) {
		if (iNumPage < 1) iNumPage = 1;
		var arFilter = {};
		var arOffer = {};
		var addParams = "?iNumPage=" + iNumPage+"&nPageSize="+objFilter.nPageSize+"&orderRow="+objFilter.orderRow;
		if(objFilter.QUERY != "")
			addParams+= "&q="+objFilter.QUERY+"&SEARCH_WHERE="+objFilter.SEARCH_WHERE;
		var tmp = {};
		var j = -1;
		$('.arFilter.selected[data-offer="N"]').each(function(i){
			var tmp = {};
			tmp[$(this).attr('data-key')+""] = $(this).attr('data-value');
			arFilter[i] = tmp;
			addParams+="&arFilter["+(i++)+"]["+$(this).attr('data-key')+"]="+$(this).attr('data-value');
		});
		//var arFilterCtr = 0;
		//objTools.forEach(arFilter, function(key, val){ arFilterCtr++; });

		if(objFilter.changeSlider)
		//if( !objFilter.checkPriceSliderState() )
		{
			var arVal = {};
			objTools.forEach(objFilter.arScale, function(key, val){
				arVal = $('#slider-'+key).data('slider').getValue();
				addParams+="&arFilter["+(j--)+"][min"+key+"]=" + objFilter.arScale[key].DATA[ arVal[0] ];
				addParams+="&arFilter["+(j--)+"][max"+key+"]=" + objFilter.arScale[key].DATA[ arVal[1] ];
				tmp = {};
				tmp['min'+key] = objFilter.arScale[key].DATA[ arVal[0] ];
				arFilter[(j--)] = tmp;
				tmp = {};
				tmp['max'+key] = objFilter.arScale[key].DATA[ arVal[1] ];
				arFilter[(j--)] = tmp;
			});
		}
		$('.arFilter.selected[data-offer="Y"]').each(function(i){
			var tmp = {};
			tmp[$(this).attr('data-key')+""] = $(this).attr('data-value');
			arOffer[i] = tmp;
			addParams+="&arOffer["+(i++)+"]["+$(this).attr('data-key')+"]="+$(this).attr('data-value');
		});
		
		if(objFilter.SESS_INCLUDE_AREAS == 1)
			window.location.href = "./"+addParams;
		else{
			//var arOfferCtr = 0;
			//objTools.forEach(arOffer, function(key, val){ arOfferCtr++; });
			var arState = {}
			$('.arFilter').each(function(index, element) {
				arState[index] =  $(this).attr('data-value');
			});
			
			$.ajax({
				type		: "POST",
				url			: objFilter.PATH + "/ajax.php",
				data		: {
					'arElementsSearch'		: objFilter.arElementsSearch,
					'CATALOG_IBLOCK_ID'		: objFilter.CATALOG_IBLOCK_ID,
					'CATALOG_IBLOCK_CODE'	: objFilter.CATALOG_IBLOCK_CODE,
					'OFFERS_IBLOCK_ID'		: objFilter.TRADEOF_IBLOCK_ID,
					'OFFERS_IBLOCK_CODE'	: objFilter.TRADEOF_IBLOCK_CODE,
					'CUR_SECTION_ID'		: objFilter.CURRENT_SECTION_ID,
					'CUR_SECTION_CODE'		: objFilter.CURRENT_SECTION_CODE,
					'nPageSize'				: objFilter.nPageSize,
					'iNumPage'				: iNumPage,
					'arFilter'				: arFilter,
					'arOffer'				: arOffer,
					'arFilterValue'			: arState,
					'FILTER_RESET'			: objFilter.FILTER_RESET,
					'CATALOG_RESET'			: objFilter.CATALOG_RESET,
					'BRAND_ROOT'			: objFilter.BRAND_ROOT,
					'FASHION_ROOT'			: objFilter.FASHION_ROOT,
					'FASHION_MODE'			: objFilter.FASHION_MODE,
					'secid'					: objFilter.SECTION_ID,
					'q'						: objFilter.QUERY,
					'orderRow'              : objFilter.orderRow,
					'SEARCH_WHERE'			: objFilter.SEARCH_WHERE
				},
				dataType	: "JSON",
				beforeSend	: function() {
					objFilter.showAjaxLoader();
				},
				success		: function(data) {
	
					objFilter.arFilterResponseData = data;
	
					if(objFilter.FILTER_RESET == 1)
					{
						// re-set slider data
						//if(!objFilter.changeSlider)
						if(!objFilter.changeSlider)
						//if( objFilter.checkPriceSliderState() )
						{
							var formatString = objFilter.CURRENCY_FORMAT.FORMAT_STRING;
							var minValue = objTools.number_format(
								data.minPrice,
								objFilter.CURRENCY_FORMAT.DECIMALS,
								objFilter.CURRENCY_FORMAT.DEC_POINT,
								objFilter.CURRENCY_FORMAT.THOUSANDS_SEP
							);
							var maxValue = objTools.number_format(
								data.maxPrice,
								objFilter.CURRENCY_FORMAT.DECIMALS,
								objFilter.CURRENCY_FORMAT.DEC_POINT,
								objFilter.CURRENCY_FORMAT.THOUSANDS_SEP
							);
							$('#minAmount-CATALOG_PRICE_1').text( formatString.replace("#", minValue) );
							$('#maxAmount-CATALOG_PRICE_1').text( formatString.replace("#", maxValue) );
	
							var diff = Math.round((data.maxPrice - data.minPrice)/10);
							if(window.objFilter.arScale &&  window.objFilter.arScale.CATALOG_PRICE_1 )
							{
								objFilter.arScale.CATALOG_PRICE_1.DATA = {};
								objFilter.firstTime = 0;
								objFilter.arScale.CATALOG_PRICE_1.DATA[0] = data.minPrice;
								objFilter.arScale.CATALOG_PRICE_1.DATA[9] = data.maxPrice;
								var tmp = "";
								for(i = 1; i <= 8; i++ )
									objFilter.arScale.CATALOG_PRICE_1.DATA[i] = (data.minPrice - 0) + (i * diff);
	
								$('#slider-CATALOG_PRICE_1').slider('setValue', [ 0, 9 ]);
							}
						}
						objFilter.changeSlider = 0;
						// re-set checkbox items
						$('.removeonly').removeClass('removeonly');
	
	
						$('.arFilter').each(function(index, element){
	
							if( (objFilter.FIRST_TIME) )
							{
								if( (data.arFilterState2[index] == 0 ) )
								{
									$(element).addClass('enabled');
									$(element).parent().addClass('hide');
									$(element).parent().removeClass('show');
								}else{
									$(element).addClass('enabled');
									$(element).parent().addClass('show');
								}
							}
	
							if( !$(element).hasClass('exception') )
							{
								if(
									(data.arFilterState2[index] == 0 )
								){
									$(element).removeClass('enabled');
								}else{
									$(element).addClass('enabled');
									$(element).parent().addClass('show');
								}
							}
	
							// set remove only checkbox filter items
							if(
								(
									(
									data.arFilterState2[index] == 0
									)
									&&
									( $(element).hasClass('selected') )
								)
							){
								//$(element).removeClass('selected');
								//$(element).removeClass('enabled');
								$(element).removeClass('exception');
								$(element).addClass('removeonly');
							}
						});
	
						$('ul.attribute-items').each(function(index, element) {
							if( $(element).find('.show').length == 0)
								$(element).parent().parent().parent().parent().parent().parent().hide();
							else
								$(element).parent().parent().parent().parent().parent().parent().show();
						});
	
						$('.accordion-inner').each(function(index, element) {
							if( ($(element).find('.show').length < 8) )
								$(element).addClass('sel');
							else
								$(element).removeClass('sel');
						});
	
						//if (data.workarea && changeURL && objFilter.CATALOG_RESET) {
						if (data.workarea && objFilter.CHANGE_WORKAREA && objFilter.CATALOG_RESET) {
							$('#workarea').html(data.workarea);
						}
						objFilter.setClearBtnStatus();
	
						// если выбран один итем сделаем активными доступне для выбора элементы в блоке с выбранным итемом
						if( $('.arFilter.selected').length == 1 )
							$('.arFilter.selected').eq(-1).parent().parent().find('li a').addClass('enabled');
					} else {
	
						$('#workarea').html(data.workarea);
	
					}
	
					if (data.isAuthorized && data.isAuthorized == 1 ) {
	
						// заполняем поле с имэйлом
						//$("#userEmailAddr").attr("data-value", json.userEmail);
						// меняем класс у кнопок для подписки
						$('.bottom-balloon button').each(function() {
							if ($(this).hasClass('notify')) {
								$(this).removeClass('notify').addClass("authNotify");
							}
						});
						//alert(dump(data.offersSubsribed));
						for (i in data.offersSubsribed) {
							// меняем кнопку
							$("#btn_"+data.offersSubsribed[i]).removeClass("authNotify").html("Подписан");
						}
					}
				},
				complete: function(){
					objFilter.FIRST_TIME = 0;
					objFilter.FILTER_RESET = 1;
					objFilter.CATALOG_RESET = 1;
					objFilter.CHANGE_WORKAREA = 1;
					objFilter.hideAjaxLoader();
				}
			});
			if (changeURL) {
				window.history.pushState( null, null, "./"+addParams );
			}
		
			//$('a[rel=mypopover]').live('mouseover', function(){$(this).popover('show');});
			//$('a[rel=mypopover]').live('mouseout', function(){$(this).popover('hide');});
		}
	},
	init		: function(arScale, arElementsSearch, CURRENCY_FORMAT, CURRENT_SECTION_ID, CURRENT_SECTION_CODE, PATH, CATALOG_IBLOCK_ID, CATALOG_IBLOCK_CODE, TRADEOF_IBLOCK_ID, TRADEOF_IBLOCK_CODE, nPageSize, secid, QUERY, ROOT_PATH, BRAND_ROOT, FASHION_ROOT, FASHION_MODE, SESS_INCLUDE_AREAS, SEARCH_WHERE) {
		// зададим первичные значения
		objFilter.SESS_INCLUDE_AREAS = SESS_INCLUDE_AREAS;
		objFilter.CURRENCY_FORMAT = CURRENCY_FORMAT;
		objFilter.arElementsSearch  = arElementsSearch;
		objFilter.arScale = arScale;
		objFilter.CURRENT_SECTION_ID = CURRENT_SECTION_ID;
		objFilter.CURRENT_SECTION_CODE = CURRENT_SECTION_CODE;
		objFilter.CATALOG_IBLOCK_ID = CATALOG_IBLOCK_ID;
		objFilter.CATALOG_IBLOCK_CODE = CATALOG_IBLOCK_CODE;
		objFilter.TRADEOF_IBLOCK_ID = TRADEOF_IBLOCK_ID;
		objFilter.TRADEOF_IBLOCK_CODE = TRADEOF_IBLOCK_CODE;
		objFilter.PATH = PATH;
		objFilter.SEARCH_WHERE = SEARCH_WHERE;
		
		if( (arElementsSearch != null) && (arElementsSearch[0] != -1 ) )
			objFilter.QUERY = QUERY;
		else
		{
			$("#title-search-input").attr('value','');
			objFilter.QUERY = "";
		}
		
		objFilter.ROOT_PATH = ROOT_PATH;
		objFilter.BRAND_ROOT = BRAND_ROOT;
		objFilter.FASHION_ROOT = FASHION_ROOT;
		objFilter.FASHION_MODE = FASHION_MODE;
		$('.arFilter.selected[offer="N"]').each(function(i){
			objFilter.noGET = 0;
		});
		$('.arFilter.selected[offer="Y"]').each(function(i){
			objFilter.noGET = 0;
		});

		if(nPageSize == 0)
			objFilter.nPageSize = 16;
		else
			objFilter.nPageSize = nPageSize;
		objFilter.FILTER_RESET = 1;
		objFilter.USE_AJAX = 0;
		objFilter.SECTION_ID = secid;
		// обработчик кнопки очистить внутри блока фильтра
		$('.my_clear').live('click', function(){

            if ($(this).attr("id") != "my_clear") {

                var hiddenClearLinks = 0;
                var allClearLinks = 0;
                $('.my_clear').each(function (index) {
                    allClearLinks = allClearLinks + 1;
                    if ($(this).css('display') == 'none') hiddenClearLinks = hiddenClearLinks + 1;
                });
				/*
                if ((allClearLinks - hiddenClearLinks) < 3) {
					$('#my_clear').click();
                } else {
                    */
					$(this).parent().parent().find('.arFilter').removeClass('selected');

					objFilter.changeSlider = !objFilter.checkPriceSliderState();

					$('.exception').removeClass('exception');	// удалим все исключения из постобработки фильтра
					$('.removeonly').removeClass('removeonly');
					objFilter.getElements(1, true);
                //}
            }
			return false;
		});
		// переключалка "выводить по X"
		$('.npagesize').live('click', function(){
			$('.npagesize').removeClass('active');
			$(this).addClass('active');

			if( $('.npagesize.active').attr('data-value') == undefined)
				objFilter.nPageSize = 16;
			else objFilter.nPageSize = $('.npagesize.active').attr('data-value');
			objFilter.changeSlider = objFilter.checkPriceSliderState();
			objFilter.FILTER_RESET = 0;
			objFilter.getElements(1, true);
            //$("body,html").animate({"scrollTop":0},800);
			return false;
		});
		// обработчик переключателя сортировки
        $('#workarea .selectpicker').live('change', function(){
            objFilter.setOrder($(this).val());
			objFilter.changeSlider = !objFilter.checkPriceSliderState();
			objFilter.FILTER_RESET = 0;
            objFilter.getElements(1, true);
            return false;
        });
		// обработчик кнопки очистить все
		$('#my_clear').live('click', function(){
			$('.arFilter').removeClass('selected');
			$('.exception').removeClass('exception');	// удалим все исключения из постобработки фильтра
			$('.removeonly').removeClass('removeonly');
			objFilter.USE_AJAX = false;
            if(window.objFilter.arScale &&  window.objFilter.arScale.CATALOG_PRICE_1 )
            {
                for(i=0; i<10; i++)
                    objFilter.arScale.CATALOG_PRICE_1.DATA[i] = 0;
            }

			objTools.forEach(arScale, function(key, val){
				$('#minAmount-' + key).text( "" );
				$('#maxAmount-' + key).text( "" );
				$( '#slider-' + key ).slider('setValue', [ 0, val.DATA.length - 0 ]);
			});
			objFilter.changeSlider = 0;
			objFilter.USE_AJAX = true;
			objFilter.getElements(1, true);

			return false;
		});
		// проинициализируем слайдеры
		objTools.forEach(arScale, function(key, val){
			//alert(val.MIN + "/" + val.MAX);
			$( '#slider-' + key ).slider({
				min		: 0,
				max		: val.DATA.length - 1,
				value	: [ val.MIN,  val.MAX],
				step	: 1,
				tooltip	: false
			}).on('slide', function( obj ) {
				if(key == "CATALOG_PRICE_1")
				{
					var formatString = objFilter.CURRENCY_FORMAT.FORMAT_STRING;
					var minValue = objTools.number_format(
						val.DATA[ obj.value[0] ],
						objFilter.CURRENCY_FORMAT.DECIMALS,
						objFilter.CURRENCY_FORMAT.DEC_POINT,
						objFilter.CURRENCY_FORMAT.THOUSANDS_SEP
					);
					var maxValue = objTools.number_format(
						val.DATA[ obj.value[1] ],
						objFilter.CURRENCY_FORMAT.DECIMALS,
						objFilter.CURRENCY_FORMAT.DEC_POINT,
						objFilter.CURRENCY_FORMAT.THOUSANDS_SEP
					);
					$( "#minAmount-" + key ).text( formatString.replace("#", minValue) );
					$( "#maxAmount-" + key ).text( formatString.replace("#", maxValue) );
				}else{
					$( "#minAmount-" + key ).text( val.DATA[ obj.value[0] ] );
-					$( "#maxAmount-" + key ).text( val.DATA[ obj.value[1] ] );
				}
			}).on('slideStop', function( obj ) {
				//if(objFilter.USE_AJAX)
				$('.exception').removeClass('exception');
				objFilter.changeSlider = 1;
				objFilter.getElements(1, true);
				$('#my_clear').removeClass('hidden');
			});
			//set default price in formatt string
			if(key == "CATALOG_PRICE_1")
			{
				var formatString = objFilter.CURRENCY_FORMAT.FORMAT_STRING;
				var minValue = objTools.number_format(
					val.DATA[0],
					objFilter.CURRENCY_FORMAT.DECIMALS,
					objFilter.CURRENCY_FORMAT.DEC_POINT,
					objFilter.CURRENCY_FORMAT.THOUSANDS_SEP
				);
				var maxValue = objTools.number_format(
					val.DATA[ val.DATA.length-1 ],
					objFilter.CURRENCY_FORMAT.DECIMALS,
					objFilter.CURRENCY_FORMAT.DEC_POINT,
					objFilter.CURRENCY_FORMAT.THOUSANDS_SEP
				);
				$( "#minAmount-" + key ).text( formatString.replace("#", minValue) );
				$( "#maxAmount-" + key ).text( formatString.replace("#", maxValue) );
			}else{
				$('#minAmount-'+key).text( val.DATA[ val.MIN ] );
				$('#maxAmount-'+key).text( val.DATA[ val.MAX ] );
			}
		});
		// обработчик навигации
		$('.navig li a').live('click', function(){
			$("body,html").animate({"scrollTop":0},800);
			objFilter.FILTER_RESET = 0;
			objFilter.changeSlider = objFilter.checkPriceSliderState();
			objFilter.QUERY = $(this).attr('data-q');
			objFilter.SEARCH_WHERE = $(this).attr('data-where');
			objFilter.getElements($(this).attr('data-inumpage'), true);
			return false;
		});
		// обработчик фильтра
		$('.arFilter').live('click', function(){
			$('.exception').removeClass('exception');	// удалим все исключения из постобработки фильтра

			objFilter.changeSlider = !objFilter.checkPriceSliderState();

			if($(this).hasClass('enabled'))
			{
				if ($(this).hasClass('selected'))
				{
					$(this).removeClass('selected');

					if( $(this).parent().parent().find('.arFilter.selected').length > 0  )
						$(this).parent().parent().find('li a').addClass('exception');

					objFilter.getElements(1, true);
				} else if(!$(this).hasClass('disabled')){
					$(this).addClass('selected');

					if( $(this).parent().parent().find('.arFilter.selected').length > 0  )
						$(this).parent().parent().find('li a').addClass('exception');

					objFilter.getElements(1, true);
				}
			}
			return false;
		});

		$('.removeonly').live('mouseover', function(){
			$(this).addClass('enabled');
			$(this).addClass('selected');
		});

		$('.removeonly').live('mouseout', function(){
			$(this).removeClass('enabled');
		});
		objFilter.changeSlider = 1;
		objFilter.CATALOG_RESET = 0;
		
		if(objFilter.SESS_INCLUDE_AREAS != 1)
			objFilter.getElements(1, false);
	}
}

extend(objFilter, objTools);