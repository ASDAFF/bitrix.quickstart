$(function() {
	/**
	* Шаг корзины
	*/
	(function(domElement) {
		if (!domElement.length) {
			return;
		}
		
		var ajaxRequest = null;
		var updateTimeout = null;
		
		//Обновляет корзину
		var updateForm = function(delayed) {
			if (updateTimeout) {
				clearTimeout(updateTimeout);
				updateTimeout = null;
			}
			if (delayed) {
				updateTimeout = setTimeout(updateForm, 500);
				return;
			}
			
			var form = domElement.find('form');
			if (ajaxRequest !== null) {
				ajaxRequest.abort();
			}
			
			var loading = new site.ui.loading(form.find('.basket-total:first'));
			
			ajaxRequest = $.post(
				form.attr('action'),
				form.serialize()
			).done(function(response) {
				domElement.html(
					$(response).find('.step-basket').html() || ''
				);
				site.ui.init(domElement);
			}).fail(function(xhr, textStatus) {
				if (xhr.status) {
					alert('Server error: ' + textStatus);
				}
			}).always(function(response, state, xhr) {
				ajaxRequest = null;
				loading.hide();
			});
		};
		
		//Выполняет действие с элементом корзины
		var doFormAction = function(action, id) {
			domElement.find('input[name="ACTION_TYPE"]').val(action);
			domElement.find('input[name="ACTION_ITEM"]').val(id);
			updateForm();
		};
		
		//Изменение кол-ва
		domElement.on('change', '.basket-qty-control input', function() {
			updateForm();
		});
		domElement.on('click', '.basket-qty-control .fast-control', function() {
			var step = $(this).hasClass('fast-control-dec') ? -1 : 1;
			var input = $(this).closest('.basket-qty-control').find('input');
			var oldVal = parseInt(input.val());
			var newVal = Math.max(1, oldVal + step);
			if (oldVal != newVal) {
				input.val(newVal);
				updateForm(true);
			}
			return false;
		});
		
		//Отложить
		domElement.on('click', '.tools-delay', function() {
			doFormAction('delay', $(this).data('id'));
			return false;
		});
		
		//Вернуть
		domElement.on('click', '.tools-revert', function() {
			doFormAction('revert', $(this).data('id'));
			return false;
		});
		
		//Удалить
		domElement.on('click', '.tools-delete', function() {
			if (confirm($(this).data('confirm') || 'Are you sure?')) {
				doFormAction('delete', $(this).data('id'));
			}
			return false;
		});
		
		//Отправка формы
		domElement.on('click', 'input[type="button"], button', function() {
			updateForm();
			return false;
		});
		domElement.on('submit', 'form', function() {
			updateForm();
			return false;
		});
	})($('.sale-order-ajax-default .step-basket'));
	
	
	
	
	/**
	* Шаг идентификации пользователя
	*/
	(function(domElement) {
		if (!domElement.length) {
			return;
		}
	})($('.sale-order-ajax-default .step-identity'));
	
	
	
	
	/**
	* Шаг заказа
	*/
	(function(domElement) {
		if (!domElement.length) {
			return;
		}
		
		//Обновляет форму заказа
		var updateForm = function() {
			var form = domElement.find('form');
			var loading = new site.ui.loading(form);
			
			$.post(
				form.attr('action'),
				form.serialize()
			).done(function(response) {
				if (!response) {
					return;
				}
				
				domElement.html(
					$(response).find('.step-order').html() || ''
				);
				site.ui.init(domElement);
				
				var errorElement = domElement.find('.has-error:first');
				if (!errorElement.length) {
					errorElement = domElement.find('.alert:first');
				}
				if (errorElement.length) {
					$('body, html').animate({
						'scrollTop': errorElement.offset().top
					}, function() {
						errorElement.find(':input').focus();
					});
				}
			}).fail(function(xhr, textStatus) {
				if (xhr.status) {
					alert('Server error: ' + textStatus);
				}
			}).always(function(response, state, xhr) {
				//Выполняем редирект, если пришел специальный заголовок
				var redirectLocation = xhr.getResponseHeader && xhr.getResponseHeader('X-Redirect-Location') || '';
				if (redirectLocation) {
					document.location.href = redirectLocation;
				} else {
					loading.hide();
				}
			});
		}
		
		//Изменение профиля покупателя
		domElement.on('change', 'form :input[name="PROFILE_ID"]', function() {
			$(this.form).find('input[name="PROFILE_CHANGE"]').val('Y');
		});
		
		//Изменение меcтоположения
		domElement.on('change', '.sale-ajax-locations .location-id', function() {
			if (this.value) {
				updateForm();
			}
		});
		
		//Поиск местоположения по почтовому индексу
		domElement.on('click', '.btn-zip-check', function() {
			var button = $(this);
			var zip = domElement.find('input.type-zip');
			var group = zip.closest('.form-group');
			
			var locationField = domElement.find('.sale-ajax-locations');
			if (!locationField.length) {
				return;
			}
			
			if (!zip.val()) {
				group.addClass('has-error');
				return;
			}
			
			var loading = new site.ui.loading(zip.closest('.prop'));
			$.post(
				zip.data('ajax-gate'), {
					'zip': zip.val()
				},
				function(response) {
					loading.hide();
					
					if (response.success) {
						group.addClass('has-success');
						
						var locationId = locationField.find('.location-id');
						$('<input/>').attr({
							'type': 'hidden',
							'name': locationId.attr('name'),
							'value': response.data.ID,
						}).appendTo(locationField);
						
						updateForm();
					} else {
						group
							.addClass('has-error')
							.after('<div class="form-group group-zip-error"><div class="alert alert-danger" role="alert">' + response.message + '</div></div>');
					}
				}
			);
		});
		
		//Изменение почтового индекса
		domElement.on('change', 'input.type-zip', function() {
			$(this).closest('.form-group').removeClass('has-error');
			domElement.find('.group-zip-error').remove();
		});
		
		//Оплата с ЛС пользователя
		domElement.on('change', ':input[name="PAY_CURRENT_ACCOUNT"]', function() {
			if ($(this).data('only-full') == 1) {
				if (this.checked) {
					$(this.form).find('input[name="PAY_SYSTEM_ID"]').prop('checked', false);
				} else {
					$(this.form).find('input[name="PAY_SYSTEM_ID"]:first').prop('checked', true);
				}
			}
		});
		
		//Поля, изменение значений которых ведет к обновлению формы
		domElement.on('change', 'form :input.observable', function() {
			updateForm();
		});
		
		//Отправка формы с подтверждением
		domElement.on('click', ':input[type="submit"]', function() {
			$(this.form).find('input[name="CONFIRM_ORDER"]').val('Y');
		});
		
		//Отправка формы общая
		domElement.on('submit', 'form', function(event) {
			updateForm();
			return false;
		});
		
		//Выбор пункта выдачи
		domElement.on('change', 'select[name="BUYER_STORE"]', function() {
			var id = $(this).find('option:selected').data('for') || '';
			$('#' + id)
				.removeClass('hidden')
				.siblings('article').addClass('hidden');
		});
	})($('.sale-order-ajax-default .step-order'));
	
	
	
	
	/**
	* Шаг успешного оформления заказа
	*/
	(function(domElement) {
		if (!domElement.length) {
			return;
		}
	})($('.sale-order-ajax-default .step-success'));
});