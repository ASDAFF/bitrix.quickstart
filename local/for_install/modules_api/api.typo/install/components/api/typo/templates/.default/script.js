(function ($) {

	// настройки со значением по умолчанию
	var defaults = {
		modalId: '#api_typo_modal',
		message: {}
	};

	// публичные методы
	var methods = {

		// инициализация плагина
		init: function (params) {

			// актуальные настройки, будут индивидуальными при каждом запуске
			var options = $.extend({}, defaults, params);

			// инициализируем лишь единожды
			if (!this.data('apiTypo')) {

				// закинем настройки в реестр data
				this.data('apiTypo', options);

				var modalId = options.modalId;

				//options.modalId = modalId;
				$(document).on('keydown', function (e) {
					if (e.keyCode == 13 && e.ctrlKey)
					{

						var selectedText = "";
						if (window.getSelection) {
							selectedText = window.getSelection().toString();
						} else if (document.getSelection) {
							selectedText = document.getSelection().toString();
						} else if (document.selection && document.selection.type != "Control") {
							selectedText = document.selection.createRange().text;
						}

						if(selectedText.length > options.MAX_LENGTH)
							$.fn.apiTypo('showMessage', options, {'status':'error', 'message':options.MESS_ALERT_TEXT_MAX});
						else if(selectedText.length == 0)
							$.fn.apiTypo('showMessage', options, {'status':'error', 'message':options.MESS_ALERT_TEXT_EMPTY});
						else
							$.fn.apiTypo('showModal', options, selectedText);
					}
				});


				$(document).on('click', '.api-typo-modal, .api-typo-modal-close', function (e) {
					e.preventDefault();

					$(modalId + ' .api-typo-modal-dialog').css({
						'transform': 'translateY(-200px)',
						'-webkit-transform': 'translateY(-200px)'
					});
					$(modalId).animate({opacity: 0}, 300, function () {
						$(this).hide();
						$.fn.apiTypo('hideModal', options);
					});

				});

				$(document).on('click', '.api-typo-modal-dialog', function (e) {
					e.preventDefault();
					e.stopPropagation();
				});

				$(document).on('click', modalId + ' .api-button-send', function () {

					var submitButton = this;

					$(modalId + ' input:text').attr('readonly', true);
					$(submitButton).prop('disabled', true);

					$.ajax({
						url: options.AJAX_URL,
						type: 'POST',
						dataType: 'json',
						data: {
							sessid: BX.bitrix_sessid(),
							SITE_ID: options.SITE_ID,
							URL: window.location.href,
							OPTIONS: options,
							FORM: $(modalId + ' form').serialize()
						},
						error: function(jqXHR, textStatus, errorThrown){
							console.log('textStatus: ' + textStatus);
							console.log('errorThrown: ' + errorThrown);
							/*$.each( jqXHR, function(k, v){
									console.log('key: ' + k + ', value: ' + v );
							});*/
						},
						success: function (data) {
							$.fn.apiTypo('showMessage', options, data);
						}
					});

				});
			}

			return this;
		},
		showModal: function (options, selectedText) {
			$('html').addClass('api-typo-modal-active');

			var modalId = options.modalId.replace('#','');

			if (!$(options.modalId).length)
				$('body').append('<div id="'+modalId+'" class="api-typo-modal fade"></div>');

			var modalWindow = '' +
				 '<div class="api-typo-modal-dialog">' +
					 '<div class="api-typo-modal-close api-typo-close"></div>' +
					 '<div class="api-header">' +
						 '<div class="api-title">'+options.MESS_MODAL_TITLE+'</div>' +
					 '</div>' +
					 '<div class="api-content">' +
						 '<form class="api-form">' +
							 '<div class="api-fields">' +
								 '<input type="hidden" name="TYPO[ERROR]" value="' + selectedText + '">' +
								 '<input type="text" name="TYPO[COMMENT]" tabindex="1" placeholder="'+options.MESS_MODAL_COMMENT+'">' +
							 '</div>' +
							 '<div class="api-buttons">' +
								 '<button type="button" class="api-button-send" tabindex="2">'+options.MESS_MODAL_SUBMIT+'</button>&nbsp;' +
								 '<button type="button" class="api-typo-modal-close" tabindex="3">'+options.MESS_MODAL_CLOSE+'</button>' +
							 '</div>' +
						 '</form>' +
					 '</div>' +
				 '</div>';

			$(options.modalId).html(modalWindow).show().animate({opacity: 1}, 0, function () {
				$(options.modalId).addClass('api-typo-open');
				$.fn.apiTypo('resize', options);
			});
		},
		hideModal: function (options) {
			$(options.modalId).remove().removeClass('api-typo-open');
			$('html').removeClass('api-typo-modal-active');
		},
		showMessage: function (options, data) {
			$('html').addClass('api-typo-modal-active');

			var modalId = options.modalId.replace('#','');
			var dialogStyle = $(options.modalId + ' .api-typo-modal-dialog').attr('style');

			if (!$(options.modalId).length)
				$('body').append('<div id="'+modalId+'" class="api-typo-modal fade"></div>');

			var modalMessage = '' +
				 '<div class="api-typo-modal-dialog" style="'+dialogStyle+'">' +
				 '<div class="api-typo-modal-close api-typo-close"></div>' +
				 '<div class="api-alert api-alert-success">' +
					 '<span></span>' +
					 '<div class="api-alert-title">'+data.message+'</div>' +
				 '</div>' +
				 '</div>';

			$(options.modalId).html(modalMessage).show().animate({opacity: 1}, 0, function () {
				$(options.modalId).addClass('api-typo-open');
				$.fn.apiTypo('resize', options);

				window.setTimeout(function(){
					$.fn.apiTypo('hideModal', options);
				},2000);
			});

			/*var alert = options.modalId + ' .api-alert';
			$(alert).animate({marginTop: -1*(parseInt($(alert).outerHeight())/2)}, 100, function () {
				window.setTimeout(function(){
					$.fn.apiTypo('hideModal', options);
				},2000);
			});*/
		},
		resize: function (options) {

			var modalId = options.modalId;
			var dialog = $(modalId + ' .api-typo-modal-dialog');

			$('.api-typo-modal.api-typo-open').each(function () {
				var dh  = dialog.outerHeight(),
				    pad = parseInt(dialog.css('margin-top'), 10) + parseInt(dialog.css('margin-bottom'), 10);

				if ((dh + pad) < window.innerHeight) {
					dialog.animate({top: (window.innerHeight - (dh + pad)) / 2}, 100);
				} else {
					dialog.animate({top: ''}, 100);
				}
			});
		}
	};

	$.fn.apiTypo = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiTypo');
		}
	};

})(jQuery);