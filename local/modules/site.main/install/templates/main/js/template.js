'use strict';

/**
 * Общий функционал шаблона
 */
site.app.blocks.common = function()
{
    var common = this;
	/**
	 * XXX: Признак первичной инициализации (при загрузке документа)
	 *
	 * @var boolean
	 */
	var firstInit = true;
	
	/**
	 * XXX: Параметры viewport
	 *
	 * @var object
	 */
	var viewport = {
		width: 0,
		height: 0,
		size: '',
		sizeChanged: false
	};
	
	/**
	 * XXX: Обработчик изменения размеров окна браузера
	 *
	 * @return void
	 */
	var onWindowResize = function()
	{
		viewport.width = $(window).width();
		viewport.height = $(window).height();
		
		var prevSize = viewport.size;
		
		//Check breakpoints
		if (viewport.width <= 660) {
			viewport.size = 's';
		} else if (viewport.width <= 960) {
			viewport.size = 'm';
		} else {
			viewport.size = 'l';
		}
		
		viewport.sizeChanged = viewport.size != prevSize;
	};
	
	/**
	 * Возвращает параметры viewport
	 *
	 * @return object
	 */
	this.getViewport = function()
	{
		return viewport;
	};
	
	/**
	 * XXX: Инициализирует UI в заданном элементе DOM
	 *
	 * @param jQuery domElement DOM element
	 * @return void
	 */
	this.initDOM = function(domElement)
	{
		if ($.fn.placeholder) {
			domElement.find('input[placeholder], textarea[placeholder]').placeholder();
		}
		
		if ($.fn.fancybox) {
			domElement.find('.fancybox').fancybox();
		}
		
		if ($.fn.mask) {
			domElement.find('input[type="tel"]').mask('+7-999-9999999');
		}
		
		//Заставляем selectivizr заново обработать DOM при повторных инициализациях
		if (!firstInit && typeof Selectivizr != 'undefined') {
			Selectivizr.init();
		}
		
		// Показываем/скрываем пароль в полях форм
		domElement.find('.form .glyphicon-eye-open, .form .glyphicon-eye-close').click(function() {
			var icon = $(this);
			var field = icon.closest('.form-group').find('input');
			
			if (!icon.data('show-title')) {
				icon.data('show-title', icon.attr('title') || '');
			}
			
			if (icon.hasClass('glyphicon-eye-open')){
				field.attr('type', 'text');
				icon
					.removeClass('glyphicon-eye-open')
					.addClass('glyphicon-eye-close')
					.attr('title', icon.data('hide-title'));
			} else {
				field.attr('type', 'password');
				icon
					.removeClass('glyphicon-eye-close')
					.addClass('glyphicon-eye-open')
					.attr('title', icon.data('show-title'));
			}
			
			return false;
		});
		/**
		 * AJAX веб-форма
		 * Обязательно нужно указать action, id и enctype="multipart/formadata"
		 * Есть возможность делать редирект и перезагружать всю или часть страницы
		 */
		domElement.find(".js-ajax-form").each(function(indx){
			$(this).submit(function(event) {
				event.preventDefault(event);
				var fullReload = $(this).data("full-reload-container");
				var form = $(this);
				var id = $(this).attr("id");
				var formData = new FormData($(form)[0]);
				var loading = new site.ui.loading('body');
				$.ajax({
					url: form.attr('action'),
					type: 'post',
					processData: false,
					contentType: false,
					data:  formData,

					success: function(response) {
						//Если нужно, делаем редирект на указанную страницу
						if($(response).find(".js-redirect-url").length > 0){
							var redirect = $(response).find(".js-redirect-url").val();
							window.location.href = redirect;
						}
						//Если нужно, перезагружаем ajax всю страницу
						if(fullReload){
							response = $("<div>"+response+"</div>").find(fullReload).html();
							$(fullReload).html(response);
							site.ui.widgets.init(form);
							common.initDOM($(fullReload));
						}else{
							form.html($('<div>' + response + '</div>').find('#'+id).html());
							site.ui.widgets.init(form);
							common.initDOM(form);
						}
						//Если форма во всплывающем окне
						site.app.blocks.popupForm.initDOM($(".popup-form").parent());
						loading.hide();
						return false;
					}
				});
			});
		});
		
        // для ie - отдельное приглашение
        $(function() {$('[autofocus]').focus()});
		
		firstInit = false;
	};

    /**
     *
     * Обновление блока по ajax
     *
     * @param url ссылка
     * @param reloadContainerSelector селектор контейнера
     * @param setIdContainerInHash добавлять ли в url браузера id контейнера
     */

    common.reloadContainer = function (url, reloadContainerSelector, setIdContainerInHash) {
        var $reloadContainer = $(reloadContainerSelector);
        var loading = new site.ui.loading($reloadContainer);
        $.ajax({
            url: url,
            success: function (response) {
                var newContainerHtml = $(response).find(reloadContainerSelector).html();
                $reloadContainer.html(newContainerHtml);
                site.app.blocks.common.initDOM($reloadContainer);
                site.app.blocks.init();
                if (setIdContainerInHash == true) {
                    url += "#" + $reloadContainer.attr("id");
                }
                window.history.pushState("", "", url);
                loading.hide();
            }
        });
    };
	
	
	//Обработчик ресайза окна
	$(window).resize(onWindowResize);
	onWindowResize();
	
	//Обработчик инициализиации UI
	site.ui.onInit(this.initDOM, this);
};


/**
 * XXX: Блок шаблона "Выбор города"
 */
site.app.blocks.citySelector = function()
{
	// выбираем город
	$('#cities-modal .item a').click(function(e){

		// получаем соответсвующий элемент инфоблока и записываем в куки
		var city_id = parseInt($(this).data('id'));
		$.get('/local/templates/main/ajax/set-city.php', { 'ID': city_id}, function(data){
			location.href = '/';
		});
		
		e.preventDefault();

		var cityNow = $('.current-city'),
			cityNew = $(this).text();

		$('#cities-modal .item a').removeClass('active');
		$(this).addClass('active');
		cityNow.text(cityNew);
	});			
	
};

/**
 * Проверяет наличие блока "Выбор города"
 *
 * @return boolean
 */
site.app.blocks.citySelector.exists = function()
{
	return $('.city-selector').length > 0;
};



/**
 * XXX: Блок шаблона "Шапка"
 */
site.app.blocks.header = function()
{
	/* Код блока */
};

/**
 * Проверяет наличие блока "Шапка"
 *
 * @return boolean
 */
site.app.blocks.header.exists = function()
{
	return $('#header').length > 0;
};




/**
 * XXX: Контроллер "Наверх", появляющийся при прокрутке
 */
site.app.blocks.goTop = function()
{
	/**
	 * Базовый DOM элемент
	 *
	 * @var jQuery
	 */
	var block = $('#go-top');
	
	/**
	 * Обработчик прокрутки окна браузера
	 *
	 * @return void
	 */
	var onScroll = function()
	{
		if ($(document).scrollTop() > $(window).height()) {
			block.addClass('enabled');
		} else {
			block.removeClass('enabled');
		}
	};
	
	//Обработчик клика по ссылке
	block.find('a').click(function() {
		$('html, body').animate({
			scrollTop: 0
		}, 'fast');
		
		return false;
	});
	
	/* Инициализация */
	$(document).scroll(onScroll);
	onScroll();
};


/**
 * Блок шаблона "Ajax pager"
 */
site.app.blocks.pager = function () {
    // обычная постраничка
    $(document).on("click", ".js-ajax-pagenation a", function (e) {
        var reloadContainerId = $(this).closest(".js-ajax-container").attr("id");
        var reloadContainerSelector = "#" + reloadContainerId;
        var url = $(this).attr("href");
        site.app.blocks.common.reloadContainer(url, reloadContainerSelector);
        e.stopPropagation();
        return false;
    });
};

/**
 * Проверяет наличие блока "Ajax pager"
 *
 * @return boolean
 */
site.app.blocks.pager.exists = function () {
    return $('.js-ajax-pagenation, .js-ajax-pagenation-more').length > 0;
};




/**
 * XXX: Блок шаблона "Ссылка, открывающая форму во всплывающем окне"
 */
site.app.blocks.popupForm = function()
{
    /**
    * Фокус на первое поле открытой формы
    * 
    */
    var setFocus = function(modal) {
        setTimeout(function(){
            modal.find('input.form-control:first').focus();    
        }, 500);
    }	
	
	var showModal = function(link) {
		var id = link.data('modal-id');
		var modal = id ? $('#' + id) : null;
		
		if (modal === null) {
			return false;
		}
		modal = modal.find('.modal');
		
		if(!modal.data('modal-ready')) {
			modal.data('modal-ready', true);
			
			site.ui.init(modal);
			
			//Обработчик отправки формы
			modal.on('submit', 'form', function() {
				$(this).find(':input[type="submit"]').prop('disabled', true);
				
				var loading = new site.ui.loading(this);
				
				$.post(
					$(this).attr('action'),
					$(this).serialize(),
					function(reponse) {
						loading.hide();
						
						modal.find('.modal-body').replaceWith(
							$(reponse).find('.modal-body')
						);
						modal.find('.has-error:first :input:first').focus();
						
						site.ui.init(modal);
					}
				);
				
				return false;
			});
			
			//Интеграция с HTML5-валидацией
			modal.on('click', 'form :input[type="submit"]', function() {
				$(this).closest('form').addClass('invalid');
			});
			
			//Закрытие по кнопке
			modal.on('click', '.btn-close', function() {
				modal.modal('hide');
			});
		}
		
		modal.modal({
			//backdrop: 'static'
		});
		
		setFocus(modal);
		
		return true;
	};
	
	var loadModal = function(link, callback) {
		var loading = new site.ui.loading('body');
		
		$.get(
			link.data('href'),
			function(response) {
				loading.hide();
				
				var id = link.data('modal-id');
				if (!id) {
					id = site.utils.getId('id');
					link.data('modal-id', id);
				}
				
				response = $('<div/>')
					.attr('id', id)
					.html(response);
				
				if (response.find('.modal').length == 0) {
					response.wrapInner(
						'<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-hidden="true">' +
						'	<div class="modal-dialog modal-md">' +
						'		<div class="modal-content">' +
						'			<div class="modal-body">' +
						'			</div>' +
						'		</div>' +
						'	</div>' +
						'</div>'
					);
				}
				
				response.appendTo('body');
				
				callback();
			}
		);
	};
	
	/**
	 * Инициализирует UI в заданном элементе DOM
	 *
	 * @param jQuery domElement DOM element
	 * @return void
	 */
	this.initDOM = function(domElement)
	{
		domElement.find('a.popup-form').each(function() {
			var link = $(this);
			
			link
				//Заменяем href, чтобы такие ссылки не открывались через контекстное меню "Открыть в новом окне"
				.data('href', link.attr('href'))
				.attr('href', 'javascript:;')
				//По click загружаем форму
				.click(function() {
					if (!showModal(link)) {
						loadModal(link, function() {
							showModal(link);
						});
					}
					
					return false;
				});
		});
	};
	
	//Обработчик инициализиации UI
	site.ui.onInit(this.initDOM, this);
};




/* Инициализация после готовности DOM */
$(function() {
	site.init();
});