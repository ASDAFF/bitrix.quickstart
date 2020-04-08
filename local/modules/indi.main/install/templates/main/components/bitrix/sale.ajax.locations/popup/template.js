$(function() {
	//Проверяем, не проводили ли инициализацию ранее
	var domDocument = $(document);
	if (domDocument.data('sale-ajax-locations-popup-ready')) {
		return;
	}
	domDocument.data('sale-ajax-locations-popup-ready', true);
	
	//Таймаут на скрытие списка местоположений
	var dropDownHideTimeout = null;
	
	//Возвращает список местоположений
	var getDropDown = function(field) {
		return field.siblings('.dropdown-menu');
	};
	
	//Отображает список местоположений
	var showDropDown = function(field, checkCount) {
		var dropDown = getDropDown(field);
		
		if (checkCount && !dropDown.has('li').length) {
			return;
		}
		
		dropDownHideTimeout && clearTimeout(dropDownHideTimeout);
		dropDownHideTimeout = null;
		
		dropDown.addClass('dropdown-active');
	};
	
	//Скрывает список местоположений
	var hideDropDown = function(field) {
		dropDownHideTimeout = setTimeout(function() {
			var dropDown = getDropDown(field);
			dropDown.removeClass('dropdown-active');
			dropDownHideTimeout = null;
			
			//Если ни одно метоположение не было выбрано - сбрасываем на начальное состояние
			var nameField = getLocationNameField(field);
			var valueField = getLocationIdField(field);
			if (!valueField.val()) {
				nameField.val(nameField.data('initial-value') || '');
				valueField.val(valueField.data('initial-value') || '');
			}
		}, 300);
	};
	
	//Возвращает поле ввода наименования местоположения
	var getLocationNameField = function(trigger) {
		return $(trigger).closest('.sale-ajax-locations').find('.location-name');
	};
	
	//Возвращает поле идентификатора местоположения
	var getLocationIdField = function(trigger) {
		return $(trigger).closest('.sale-ajax-locations').find('.location-id');
	};
	
	//Ранее запущенный AJAX-запрос
	var ajaxRequest = null;
	
	//Показывает список местоположений при фокусе
	domDocument.on(
		'focus',
		'.sale-ajax-locations-popup .location-name, .sale-ajax-locations-popup .dropdown-menu a',
		function() {
			showDropDown(getLocationNameField(this), true);
		}
	);
	
	//Скрывает список местоположений при потере фокуса
	domDocument.on(
		'blur',
		'.sale-ajax-locations-popup .location-name, .sale-ajax-locations-popup .dropdown-menu a',
		function() {
			hideDropDown(getLocationNameField(this));
	});
	
	//Запоминает значение поля до начала поиска
	domDocument.on('focus', '.sale-ajax-locations-popup .location-name', function(event) {
		var field = $(this);
		var oldSearch = field.data('old-search');
		if (oldSearch === undefined) {
			field.data('old-search', field.val());
		}
	});
	
	//Обновляет список местоположений
	domDocument.on('keyup', '.sale-ajax-locations-popup .location-name', function(event) {
		var field = $(this);
		var valueField = getLocationIdField(this);
		var domElement = field.closest('.sale-ajax-locations');
		var url = domElement.data('ajax-gate');
		var params = domElement.data('params') || {};
		var search = field.val();
		var oldSearch = field.data('old-search');
		
		if (search != oldSearch) {
			if (ajaxRequest) {
				ajaxRequest.abort();
				ajaxRequest = null;
			}
			
			field.data('old-search', search);
			valueField.val('');
			
			ajaxRequest = $.getJSON(
				url, 
				$.extend(params, {
					search: search
				}),
				function(response) {
					ajaxRequest = null;
					
					var dropDown = getDropDown(field);
					dropDown.find('li').remove();
					var len = response.length;
					if (len > 0) {
						for (var i = 0; i < len; i++) {
							var name = [];
							if (response[i].NAME) {
								name.push(response[i].NAME);
							}
							if (response[i].REGION_NAME) {
								name.push(response[i].REGION_NAME);
							}
							if (response[i].COUNTRY_NAME) {
								name.push(response[i].COUNTRY_NAME);
							}
							dropDown.append('<li><a href="#" data-id="' + response[i].ID + '">' + name.join(', ') + '</a></li>');
						}
					}
					
					showDropDown(field, false);
				}
			);
		}
	});
	
	//Переводит фокус на список местоположений при нажатии стрелки вниз
	domDocument.on('keypress', '.sale-ajax-locations-popup .location-name', function(event) {
		if (event.key == 'Down') {
			event.stopPropagation();
			getDropDown($(this)).find('a:first').focus();
			return false;
		}
	});
	
	//Запоминает местоположение, выбранное из списка
	domDocument.on('click', '.sale-ajax-locations-popup .dropdown-menu a', function() {
		var link = $(this);
		
		var nameField = getLocationNameField(this);
		nameField.val(link.html());
		
		var valueField = getLocationIdField(this);
		valueField
			.val(link.data('id'))
			.trigger('change');
		
		return false;
	});
	
	//Перемещение между местоположениями в списке при нажатии стрелок вверх/вниз
	domDocument.on('keypress', '.sale-ajax-locations-popup .dropdown-menu a', function(event) {
		if (event.key == 'Up' || event.key == 'Down') {
			event.stopPropagation();
			
			var container = $(this).closest('.dropdown-menu');
			var active = container.find('li:has(a:focus)');
			var next = event.key == 'Up' ? active.prev('li') : active.next('li');
			next.find('a').focus();
			
			return false;
		}
	});
});