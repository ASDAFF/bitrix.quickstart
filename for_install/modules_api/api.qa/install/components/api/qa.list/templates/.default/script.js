/**
 * $.fn.apiQaList
 */
(function ($) {

	// настройки со значением по умолчанию
	var defaults = {};
	var options = {};

	var qa = '.api-qa';
	var qa_form = '.api-qa .api-qa-form';
	var qa_form_answer = '.api-qa .api-qa-form-unswer';
	var qa_items = '.api-qa .api-items';

	// публичные методы
	var methods = {

		// инициализация плагина
		init: function (params) {

			// актуальные настройки, будут индивидуальными при каждом запуске
			options = $.extend({}, defaults, params);

			// инициализируем лишь единожды
			if (!this.data('apiQaList')) {

				// закинем настройки в реестр data
				this.data('apiQaList', options);

				// код плагина

				$.fn.apiQaList('replace');

				qa_form_answer = $(qa_form_answer).detach();

				//autoresize textarea
				$(qa).find('[data-autoresize]').each(function () {
					var offset = this.offsetHeight - this.clientHeight;
					var resizeTextarea = function (el) {
						$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
					};
					$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
					resizeTextarea(this);
				});

				$('.api-qa-count').each(function () {
					$(this).text('(' + options.COUNT + ')');
				});

			}

			return this;
		},
		answerForm: function (_this, id) {

			if (options.ALLOW === 'USER') {
				if (!options.USER.IS_AUTHORIZED) {
					$.fn.apiAlert({
						content: options.MESS_ALLOW_USER,
					});

					return false;
				}
			}
			else if (options.ALLOW === 'EDITOR') {
				if (!options.USER.IS_EDITOR) {
					$.fn.apiAlert({
						content: options.MESS_ALLOW_EDITOR,
					});

					return false;
				}
			}

			var qa_form_answer_id = '#api_qa_form_answer_' + id;
			var api_qa_item_id = '#api_qa_item_' + id;

			$(qa_form_answer_id).html(qa_form_answer);
			$(qa_form_answer_id).find('input[name=PARENT_ID]').val(id);
			$(qa_form_answer_id).find('input[name=LEVEL]').val($(api_qa_item_id).data('level') + 1);
			$(qa_form_answer_id).show();
			$(api_qa_item_id).addClass('api-active').siblings('.api-item').removeClass('api-active');
			$(qa_form_answer_id).find('textarea').focus();

			//autoresize textarea
			$(qa_form_answer_id).find('[data-autoresize]').each(function () {
				var offset = this.offsetHeight - this.clientHeight;
				var resizeTextarea = function (el) {
					$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
				};
				$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
				resizeTextarea(this);
			});

			//console.log(options);
			//console.log('answerForm');
		},

		answer: function (_this) {

			var qa_submit = _this;
			var qa_form_answer = $(_this).parents('.api-form-answer');
			var qa_item = $(_this).parents('.api-item');

			if (options.USE_PRIVACY) {
				var privacy_accepted = $(qa_form_answer).find('input[name=PRIVACY_ACCEPTED]').prop('checked');
				if (!privacy_accepted) {
					$.fn.apiAlert({
						content: options.MESS_PRIVACY_CONFIRM,
					});
					return false;
				}
			}

			//block fields before ajax
			$(qa_submit).prop('disabled', true);
			$(qa_form_answer).find('.api-field').attr('readonly', true);

			var postData = {
				sessid: BX.bitrix_sessid(),
				API_QA_LIST_AJAX: 'Y',
				FORM: {}
			};

			$(qa_form_answer).find('.api-field').each(function () {
				var name = $(this).attr('name');

				if (name == 'NOTIFY')
					postData.FORM[name] = ($(this).is(':checked') ? 'Y' : 'N');
				else
					postData.FORM[name] = $(this).val();
			});

			$.ajax({
				type: 'POST',
				data: $.extend(postData, options),
				url: options.AJAX_URL,
				dataType: 'json',
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('textStatus: ' + textStatus);
					console.log('errorThrown: ' + errorThrown);
					alert(textStatus);
				},
				success: function (data) {

					//unblock fields after ajax
					$(qa_submit).prop('disabled', false);
					$(qa_form_answer).find('.api-field').attr('readonly', false);

					if (data.status == 'error') {

						for (var key in postData.FORM) {
							var field = data.errors[key];
							if (field) {
								$(qa_form_answer).find('[name=' + key + ']').addClass('api-field-error');
							}
							else
								$(qa_form_answer).find('[name=' + key + ']').removeClass('api-field-error');
						}

						$(qa_form_answer).find('.api-field-error').each(function () {
							$(this).on('keyup change', function () {
								if ($(this).val().length)
									$(this).removeClass('api-field-error');
							});
						});

					}
					else if (data.status == 'moderation') {
						$(qa_form_answer).find('input:text, textarea').val('');
						$(qa_form_answer).hide();
						alert(data.html);
					}
					else {

						if (data.type == 'Q') {
							$(data.html).appendTo(qa_items).slideDown(200);
						}
						else {
							var qa_last_item = $(qa_items + ' .api-item[data-parent-id=' + data.parentId + ']').filter(':last');

							if (qa_last_item.length) {
								qa_last_item.after(data.html);
							}
							else {
								qa_item.after(data.html);
							}
						}

						$.fn.apiQaList('replace', data.id);

						var qa_new_item = $(qa_items + ' #api_qa_item_' + data.id);
						$('html, body').animate({
							scrollTop: $(document).find(qa_new_item).offset().top - 200
						}, 400, function () {
							qa_new_item.addClass('api-active');
						});

						$(qa_form_answer).find('input:text, textarea').val('');
						$(qa_form_answer).hide();
					}
				}
			});

			return false;
		},
		question: function (_this) {

			var qa_submit = _this;

			if (options.USE_PRIVACY) {
				var privacy_accepted = $(qa_form).find('input[name=PRIVACY_ACCEPTED]').prop('checked');
				if (!privacy_accepted) {
					$.fn.apiAlert({
						content: options.MESS_PRIVACY_CONFIRM,
					});
					return false;
				}
			}

			//block fields before ajax
			$(qa_submit).prop('disabled', true);
			$(qa_form).find('.api-field').attr('readonly', true);

			var postData = {
				sessid: BX.bitrix_sessid(),
				API_QA_LIST_AJAX: 'Y',
				FORM: {}
			};

			$(qa_form).find('.api-field').each(function () {
				var name = $(this).attr('name');

				if (name == 'NOTIFY')
					postData.FORM[name] = ($(this).is(':checked') ? 'Y' : 'N');
				else
					postData.FORM[name] = $(this).val();
			});

			$.ajax({
				type: 'POST',
				data: $.extend(true, postData, options),
				url: options.AJAX_URL,
				dataType: 'json',
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('textStatus: ' + textStatus);
					console.log('errorThrown: ' + errorThrown);
					alert(textStatus);
				},
				success: function (data) {

					//unblock fields after ajax
					$(qa_submit).prop('disabled', false);
					$(qa_form).find('.api-field').attr('readonly', false);

					if (data.status == 'error') {

						for (var key in postData.FORM) {
							var field = data.errors[key];
							if (field) {
								$(qa_form).find('[name=' + key + ']').addClass('api-field-error');
							}
							else
								$(qa_form).find('[name=' + key + ']').removeClass('api-field-error');
						}

						$(qa_form).find('.api-field-error').each(function () {
							$(this).on('keyup change', function () {
								if ($(this).val().length)
									$(this).removeClass('api-field-error');
							});
						});

					}
					else if (data.status == 'moderation') {
						$(qa_form).find('input:text, textarea').val('');
						$.fn.apiAlert({
							title: data.html,
						});

						//alert(data.html);
					}
					else {

						$(qa_items).append(data.html);
						$.fn.apiQaList('replace', data.id);

						var qa_item = $(qa_items + ' #api_qa_item_' + data.id);
						$('html, body').animate({
							scrollTop: $(document).find(qa_item).offset().top
						}, 400, function () {
							$(qa).find('.api-item').removeClass('api-active');
							qa_item.addClass('api-active');
						});

						$(qa_form).find('input:text, textarea').val('');
					}
				}
			});

			return false;
		},

		edit: function (id) {

			var question = $('#api_qa_item_' + id);
			$('#api_qa_form_answer_' + id).html('');

			question.find('.api-answer').addClass('api-hidden');
			question.find('.api-delete').addClass('api-hidden');
			question.find('.api-edit').addClass('api-hidden');
			question.find('.api-save').removeClass('api-hidden');
			question.find('.api-cancel').removeClass('api-hidden');

			question.addClass('api-active').siblings('.api-item').removeClass('api-active');

			question.find('[data-edit]').each(function () {
				var field_code = $(this).data('edit');
				var field_val = $(this).text();
				var field_html = $(this).html();

				//$(this).css({'display': 'block'});

				if (field_code == 'GUEST_NAME') {
					$(this).html('<input type="text" data-field="' + field_code + '" value="' + field_val + '"><div data-fake-field>' + field_html + '</div>');
				}
				else {
					$(this).html('<textarea data-field="' + field_code + '" data-autoresize>' + field_val + '</textarea><div data-fake-field>' + field_html + '</div>');
					$(this).find('textarea').focus();
				}
			});

			//autoresize textarea
			question.find('[data-autoresize]').each(function () {
				var offset = this.offsetHeight - this.clientHeight;
				var resizeTextarea = function (el) {
					$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
				};
				$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
				resizeTextarea(this);
			});

		},
		delete: function (id) {

			$.fn.apiAlert({
				type: 'confirm',
				class: 'warning',
				showIcon: true,
				title: options.LIST_QUESTION_MESS_CONFIRM_DELETE.replace(/#ID#/g, id),
				labels: {
					ok: options.alert.labelOk,
					cancel: options.alert.labelCancel,
				},
				callback: {
					onConfirm: function (isConfirm) {
						if (isConfirm) {
							var question = $('#api_qa_item_' + id);

							var postData = {
								API_QA_LIST_AJAX: 'Y',
								sessid: BX.bitrix_sessid(),
								siteId: options.SITE_ID,
								api_action: 'delete',
								id: id,
							};

							$.ajax({
								type: 'POST',
								data: postData,
								url: options.ACTION_URL,
								dataType: 'json',
								error: function (jqXHR, textStatus, errorThrown) {
									console.log('textStatus: ' + textStatus);
									console.log('errorThrown: ' + errorThrown);
									alert(textStatus);
								},
								success: function (data) {
									if (data.status == 'ok') {

										var itemId = id;
										$(qa_items).find('.api-item').each(function () {
											if ($(this).data('parent-id') == itemId) {
												itemId = $(this).data('id');
												$(this).slideUp(200, function () { $(this).remove() });
											}
											else if ($(this).data('parent-id') == id) {
												$(this).slideUp(200, function () { $(this).remove() });
											}
										});

										question.slideUp(200, function () { $(this).remove() });
									}
								}
							});
						}
					},
				}
			});

			//if (confirm(options.LIST_QUESTION_MESS_CONFIRM_DELETE.replace(/#ID#/g, id))) {}
		},
		erase: function (id) {

			$.fn.apiAlert({
				type: 'confirm',
				class: 'warning',
				showIcon: true,
				title: options.LIST_QUESTION_MESS_CONFIRM_ERASE.replace(/#ID#/g, id),
				labels: {
					ok: options.alert.labelOk,
					cancel: options.alert.labelCancel,
				},
				callback: {
					onConfirm: function (isConfirm) {
						if (isConfirm) {
							var question = $('#api_qa_item_' + id);

							var postData = {
								API_QA_LIST_AJAX: 'Y',
								sessid: BX.bitrix_sessid(),
								siteId: options.SITE_ID,
								api_action: 'erase',
								id: id,
							};

							$.ajax({
								type: 'POST',
								data: postData,
								url: options.ACTION_URL,
								dataType: 'json',
								error: function (jqXHR, textStatus, errorThrown) {
									console.log('textStatus: ' + textStatus);
									console.log('errorThrown: ' + errorThrown);
									alert(textStatus);
								},
								success: function (data) {
									if (data.status == 'ok') {
										question.find('.api-text').html(options.LIST_QUESTION_MESS_TEXT_ERASE);
									}
								}
							});
						}
					},
				}
			});

			//if (confirm(options.LIST_QUESTION_MESS_CONFIRM_ERASE.replace(/#ID#/g, id))) {}
		},
		save: function (id) {

			var question = $('#api_qa_item_' + id);

			question.find('[data-field]').attr('readonly', true);

			var postData = {
				API_QA_LIST_AJAX: 'Y',
				sessid: BX.bitrix_sessid(),
				siteId: options.SITE_ID,
				api_action: 'save',
				id: id,
				form: {}
			};

			question.find('[data-field]').each(function () {
				var name = $(this).data('field');
				postData.form[name] = $(this).val();
			});

			$.ajax({
				type: 'POST',
				data: postData,
				url: options.ACTION_URL,
				dataType: 'json',
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('textStatus: ' + textStatus);
					console.log('errorThrown: ' + errorThrown);
					alert(textStatus);
				},
				success: function (data) {
					question.find('.api-answer').removeClass('api-hidden');
					question.find('.api-delete').removeClass('api-hidden');
					question.find('.api-edit').removeClass('api-hidden');
					question.find('.api-save').addClass('api-hidden');
					question.find('.api-cancel').addClass('api-hidden');

					if (data.status == 'ok') {
						question.find('[data-edit]').each(function () {
							var name = $(this).data('edit');
							$(this).html(data.fields[name]);

							$.fn.apiQaList('replace', id);
						});
					}
					else {
						$.fn.apiQaList('cancel', id);
					}
				}
			});
		},
		cancel: function (id) {

			var question = $('#api_qa_item_' + id);

			question.find('.api-answer').removeClass('api-hidden');
			question.find('.api-delete').removeClass('api-hidden');
			question.find('.api-edit').removeClass('api-hidden');
			question.find('.api-save').addClass('api-hidden');
			question.find('.api-cancel').addClass('api-hidden');

			question.removeClass('api-active');

			question.find('[data-edit]').each(function () {

				//var field_code = $(this).data('edit');

				var field_html = $(this).find('[data-fake-field]').html();
				$(this).html(field_html);

			});
		},
		getLink: function (_this, id, url) {
			//return prompt(options.mess.review_link.replace(/\{id\}/g, ID), location.protocol + '//' + location.host + URL);
			//return prompt(options.LIST_QUESTION_MESS_LINK.replace(/#ID#/g, ID), URL);

			var txt = options.LIST_QUESTION_MESS_LINK.replace(/#ID#/g, id);
			$.fn.apiAlert({
				title: txt,
				input: {
					text: url,
				}
			});

			return false;
		},
		replace: function (id) {

			var item = id ? '#api_qa_item_' + id : qa;

			$(item + ' .api-noindex').each(function () {
				$(this).replaceWith('<a href="' + $(this).data('url') + '" title="' + $(this).text() + '" target="_blank">' + $(this).html() + '</a>');
			});
		}
	};

	$.fn.apiQaList = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiQaList');
		}
	};

})(jQuery);