'use strict';

/**
 * Общий функционал шаблона
 */
indi.app.blocks.common = function()
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

		if(typeof domElement == 'undefined') {
			domElement = $(document);
		}

		if ($.fn.placeholder) {
			domElement.find('input[placeholder], textarea[placeholder]').placeholder();
		}

		if ($.fn.fancybox) {
			domElement.find('.fancybox').fancybox();
		}

		if ($.fn.mask) {
			domElement.find('input[type="tel"]').mask('+7-999-9999999');
		}

		// К форме надо добавить id (произвольный) и класс js-sisyphus.
		// Также в header.php расскоментировать подключение плагина
		if ($.fn.sisyphus) {
			var $formsSisyphus = domElement.find('.js-sisyphus');
			$formsSisyphus.each(function() {
				var id = $(this).attr('id');
				if(id) {
					$('#' + id).sisyphus();
				}
			});
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
        var loading = new indi.ui.loading($reloadContainer);
        $.ajax({
            url: url,
            success: function (response) {
                var newContainerHtml = $(response).find(reloadContainerSelector).html();
                $reloadContainer.html(newContainerHtml);
                indi.app.blocks.common.initDOM($reloadContainer);
                indi.app.blocks.init();
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
	indi.ui.onInit(this.initDOM, this);
};


/**
 * XXX: Блок шаблона "Выбор города"
 */
indi.app.blocks.citySelector = function()
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
indi.app.blocks.citySelector.exists = function()
{
	return $('.city-selector').length > 0;
};



/**
 * XXX: Блок шаблона "Шапка"
 */
indi.app.blocks.header = function()
{
	/* Код блока */
};

/**
 * Проверяет наличие блока "Шапка"
 *
 * @return boolean
 */
indi.app.blocks.header.exists = function()
{
	return $('#header').length > 0;
};




/**
 * XXX: Контроллер "Наверх", появляющийся при прокрутке
 */
indi.app.blocks.goTop = function()
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
indi.app.blocks.pager = function () {

	var pager = this;
	/**
	 *
	 * Добавление блока с еще одной страницей по ajax
	 *
	 * @param url ссылка
	 * @param reloadContainerSelector селектор контейнера
	 */

	pager.reloadContainerShowMore = function (url, reloadContainerSelector) {
		var $reloadContainer = $(reloadContainerSelector);
		var loading = new indi.ui.loading($reloadContainer);
		$.ajax({
			url: url,
			success: function (response) {
				var newContainerHtml = $(response).find(reloadContainerSelector).html();
				var $newContainerHtml = $(newContainerHtml);
				$reloadContainer.find(".js-ajax-pagenation-more").remove();
				$newContainerHtml.appendTo($reloadContainer);
				indi.app.blocks.common.initDOM($reloadContainer);
				loading.hide();
			}
		});
	};

    // обычная постраничка
    $(document).on("click", ".js-ajax-pagenation a", function (e) {
        var reloadContainerId = $(this).closest(".js-ajax-container").attr("id");
        var reloadContainerSelector = "#" + reloadContainerId;
        var url = $(this).attr("href");
        indi.app.blocks.common.reloadContainer(url, reloadContainerSelector);
        e.stopPropagation();
        return false;
    });

	// показать еще
	$(document).on("click", ".js-ajax-pagenation-more a", function (e) {
		var reloadContainerId = $(this).closest(".js-ajax-container").attr("id");
		var reloadContainerSelector = "#" + reloadContainerId;
		var url = $(this).attr("href");
		pager.reloadContainerShowMore(url, reloadContainerSelector);
		e.stopPropagation();
		return false;
	});

};

/**
 * Проверяет наличие блока "Ajax pager"
 *
 * @return boolean
 */
indi.app.blocks.pager.exists = function () {
    return $('.js-ajax-pagenation, .js-ajax-pagenation-more').length > 0;
};

/**
 * XXX: Блок шаблона "Загрузка фото с ресайзом"
 */

indi.app.blocks.fileResize = function () {
	$(document).on('change', '.js-file-resize input[type="file"]', function () {
		//При загрузке фото сохраняем файл на сервере
		var fd = new FormData();
		fd.append('file', $(this)[0].files[0]);
		fd.append('folder',"stories");
		$.ajax({
			url: '/ajax/images/saveUploadImage',
			data: fd,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function (data) {
				//Проверяем, если фото подгружено не в первый раз, старое нужно удалить
				if ($('.js-imagerez').data('newimg') != $('.js-imagerez').data('oldimg')) {
					var delId = $('.js-imagerez').attr('newimg');
				}
				var result = jQuery.parseJSON(data.data);
				//подставляем новую картинку пользователю
				$('.js-image-display').attr('src', result.image);
				//$('.js-imagerez').attr('src', result.image);
				$('.js-imagerez').data('newimg', result.id);
				$('.cropper-container').find("img").attr('src', result.image);

				//Инициализируем кроппер
				var viewPort = indi.app.blocks.common.getViewport();
				var containerWidth = 330;
				var containerHeight = 330;
				if (viewPort.size == "s") {
					var containerWidth = 150;
					var containerHeight = 150;
				}
				//Инициализируем кроппер
				$('.js-image-display').cropper({
					aspectRatio: 4 / 4,
					cropBoxMovable: true,
					cropBoxResizable: true,
					minContainerWidth: containerWidth,
					minContainerHeight: containerHeight,
					crop: function (e) {
					}
				});
				$(".js-image-display").cropper("reset");
				$('.cropper-container').show();
				$('.js-crop').css("display", "inline-block");
				//$(".js-image-input").val(result.id);

				$('#avatar-modal').modal('show');
				//Удаляем старое фото если нужно
				if (delId) {
					var fdd = new FormData();
					fdd.append('imageid', delId);
					$.ajax({
						url: '/ajax/images/deleteImage',
						data: fdd,
						processData: false,
						contentType: false,
						type: 'POST',
						success: function (data) {
						}
					});
				}
			}
		});
	});
	//По клику на обрезать сохраняем новый обрезанный файл
	$(document).on('click', '.js-crop', function (e) {
		e.stopPropagation();
		var cropBoxData = $(".js-image-display").cropper("getData");
		var croppedCanvas = $(".js-image-display").cropper("getData");
		var imgSrc = $(".js-image-display").attr("src");
		var type = $(this).data("type");
		var imgID = $('.js-imagerez').data('newimg');
		var fd = new FormData();
		fd.append('width', cropBoxData.width);
		fd.append('height', cropBoxData.height);
		fd.append('x', cropBoxData.x);
		fd.append('y', cropBoxData.y);
		fd.append('img', imgSrc);
		fd.append('imgid', imgID);
		fd.append('type', type);

		$.ajax({
			url: '/ajax/images/cropImage/',
			data: fd,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function (data) {
				var result = jQuery.parseJSON(data.data);
				$('.cropper-container').hide();
				$('.js-crop').css("display", "none");
				$('.js-imagerez').attr('src', result.image);
				$('.js-imagerez').data('newimg', result.id);
				$(".js-image-input").val(result.id).trigger('change');
			}
		});
		$('#avatar-modal').modal('hide');
	});
};
/**
 * Проверяет наличие блока "Загрузка фото с ресайзом"
 *
 * @return boolean
 */
indi.app.blocks.fileResize.exists = function () {
	return $('.js-file-resize').length > 0;
};
/**
 * XXX: Блок шаблона "Профиль пользователя"
 */
indi.app.blocks.userProfile = function () {
	//Сохранение обрезанного фото
	$(document).on('change', '.js-user-file', function () {
		var newId = $(this).val();
		var userIdValue = $("#user-id").val();
		$.post(
			'/ajax/images/saveUserPhoto/',
			{photoId: newId, userId: userIdValue},
			function (reponse) {
				$.post(
					window.location.href,
					function (response) {
						$("#user-profile").find('.js-file-resize').replaceWith(
							$(response).find('.js-file-resize')
						);
						indi.ui.init($("#user-profile"));
					}
				);

			}
		);
	});
};
/**
 * Проверяет наличие блока "Профиль пользователя"
 *
 * @return boolean
 */
indi.app.blocks.userProfile.exists = function () {
	return $('#user-profile').length > 0;
};



/**
 * Блок шаблона "Стандартный индикатор ajax-битрикса"
 */
indi.app.blocks.bitrixDefaultIndicator = function () {
	var lastWait = [];
	/* non-xhr loadings */
	BX.showWait = function (node, msg) {
		node = BX(node) || document.body || document.documentElement;
		msg = msg || BX.message('JS_CORE_LOADING');
		var container_id = node.id || Math.random();
		var obMsg = node.bxmsg = document.body.appendChild(BX.create('DIV', {
			props: {
				id: 'wait_' + container_id,
				className: 'bx-core-waitwindow'
			},
			text: msg
		}));
		var loading = new indi.ui.loading($(node));
		window.loading = loading;
		lastWait[lastWait.length] = obMsg;
		return obMsg;
	};
	BX.closeWait = function (node, obMsg) {
		window.loading.hide();
		if (node && !obMsg)
			obMsg = node.bxmsg;
		if (node && !obMsg && BX.hasClass(node, 'bx-core-waitwindow'))
			obMsg = node;
		if (node && !obMsg)
			obMsg = BX('wait_' + node.id);
		if (!obMsg)
			obMsg = lastWait.pop();
		if (obMsg && obMsg.parentNode) {
			for (var i = 0, len = lastWait.length; i < len; i++) {
				if (obMsg == lastWait[i]) {
					lastWait = BX.util.deleteFromArray(lastWait, i);
					break;
				}
			}
			obMsg.parentNode.removeChild(obMsg);
			if (node)
				node.bxmsg = null;
			BX.cleanNode(obMsg, true);
		}
	};
	BX.addCustomEvent('onAjaxSuccess', function () {
		indi.ui.widgets.init();
		indi.app.blocks.init();
		indi.app.blocks.common.initDOM();
	});
};

indi.app.blocks.bitrixDefaultIndicator.exists = function () {
	return $('.js-custom-indicator').length > 0;
};
/**
 * "Цели Yandex"
 */
indi.app.blocks.yandex = function()
{

	//на клик
	$('body').on('click', '.js-ya-goal', function() {
		var goalVal;
		if(goalVal = $(this).data('goal')) {
			//yaCounter13809109.reachGoal(goalVal);
			console.log(goalVal);
		}
		return true;
	});

	//на загрузку



};

/**
 * Проверяет наличие страницы "Цели Yandex"
 *
 * @return boolean
 */
indi.app.blocks.yandex.exists = function()
{
	return $('.js-ya-goal').length > 0;
};





/**
 * "Цели Google"
 */
indi.app.blocks.google = function()
{

	//на клик
	$('body').on('click', '.js-ga-goal', function() {
		var category = $(this).data('category');
		var action = $(this).data('action');
		if(category && action) {
			//ga('send', 'event', category, action);

			//console.log(category);
			//console.log(action);
		}
		return true;
	});

	//на загрузку



};

/**
 * Проверяет наличие страницы "Цели Google"
 *
 * @return boolean
 */
indi.app.blocks.google.exists = function()
{
	return $('.js-ga-goal').length > 0;
};
/**
 * XXX: Блок шаблона "Ссылка, открывающая форму во всплывающем окне"
 */

indi.app.blocks.popupForm = function()
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

			indi.ui.init(modal);

			//Обработчик отправки формы
			modal.on('submit', 'form', function() {
				$(this).find(':input[type="submit"]').prop('disabled', true);

				var loading = new indi.ui.loading(this);

				var form = $(this);
				var formData = new FormData($(form)[0]);

				$.ajax({
					url: form.attr('action'),
					type: 'post',
					processData: false,
					contentType: false,
					data:  formData,

					success: function(response) {
						loading.hide();

						modal.find('.modal-body').replaceWith(
							$(response).find('.modal-body')
						);
						modal.find('.has-error:first :input:first').focus();

						indi.ui.init(modal);
					}
				});

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
		var loading = new indi.ui.loading('body');

		$.get(
			link.data('href'),
			function(response) {
				loading.hide();

				var id = link.data('modal-id');
				if (!id) {
					id = indi.utils.getId('id');
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
	indi.ui.onInit(this.initDOM, this);
};




/* Инициализация после готовности DOM */
$(function() {
	indi.init();
});