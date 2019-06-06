function CLF(arForm) {

	var that = this;

	this.subscribtionsList = {
		succesfull_send: "succesfull_send",
		unsuccessful_send: "succesfull_send",
		successful_validation: "successful_validation",
		unsuccessful_validation: "unsuccessful_validation",
		recaptcha_error: "recaptcha_error"
	}

	this.forms = [];

	this.init = function() {

		$.each(arForm, function(f, form) {
			if (form.inited !== true) {
				form.id = f;
				if (form.fields.length > 0) {
					form.el = $('form[name="' + form.name + '"]');
					$.each(form.fields, function(fk, field) { //Находим и записываем элементы dom в объект формы для дальнейшего использования.
						if (field.type == "file") {
							field.el = field.name;
						} else {
							field.el = form.el.find('[name="' + field.name + '"]')
						}
					});

					if (form.arParams.AGREEMENT != '') { // Добавление согласия с политикой в поля формы.
						form.fields.push({
							el: form.el.find('.js-clf-agree'),
							errMsg: form.arParams.AGREEMENT_ERR_MSG,
							required: true,
							type: "checkbox-group",
							name: form.name + '_agree',
							label: "Согласие с политикой обработки данных"
						});
					}

					if (form.arParams.USE_RECAPTCHA === "Y") {
						form.fields.push({
							el: form.el.find('.g-recaptcha-response'),
							errMsg: form.arParams.RECAPTCHA_ERR_MSG,
							required: true,
							type: "text",
							name: form.name + '_recaptcha',
							label: "ReCaptcha",
							validationRule: "name",
							'sub_type': 'recaptcha'
						});
					}

					if ($(form.el).find('.js-additional-hidden-field').length > 0) {
						$(form.el).find('.js-additional-hidden-field').each(function() {
							form.fields.push({
								el: $(this),
								errMsg: "",
								required: false,
								type: "hidden",
								name: $(this).attr('name'),
								label: $(this).attr('data-label')
							});
						});
					}


					form.el.submit(function(e) {
						onSubmit(e, form);
					});

					attachmentMove();

					form.clearForm = function() {
						clearForm(form);;
					};

					that.forms.push(sanitizeData(form));

					form.inited = true; //Чтобы не повторяться
				}

			}
		});
	}



	this.subscribers = {
		any: [] // тип события: подписчик
	};

	this.subscribe = function(fn, type) {
		type = type || 'any';
		if (typeof this.subscribers[type] === "undefined") {
			this.subscribers[type] = [];
		}
		this.subscribers[type].push(fn);
	};

	this.unsubscribe = function(fn, type) {
		visitSubscribers("unsubscribe", fn, type);
	};

	this.publish = function(publication, type) {
		visitSubscribers("publish", publication, type);
	};


	this.subscribe(function(data) {
		var attchmentName = data.input_name;
		var parentForm = '';

		$.each(forms.forms, function(f, form) {
			$.each(form.fields, function(fld, field) {
				if (field.name == attchmentName) {
					parentForm = form;
				}
			});
		})

	}, "OnFileUploadSuccess");


	this.resetReCaptcha = function(forms) {


		for (var i = $('.g-recaptcha').length - 1; i >= 0; i--) {
			grecaptcha.reset(i);
		}

		$.each(function(f, form) {
			setTimeout(function() {
				$.each(form.fields, function(key, value) {
					value.el = form.el.find('.g-recaptcha-response');
				});
			}, 1000);
		});
	}


	function clearForm(form) {

		if (form.arParams['USE_RECAPTCHA'] == "Y") {
			form.el.find('.js-recaptcha-area .js-clf-err-msg').fadeOut();
		}

		$.each(form.fields, function(key, value) {
			if (value.sub_type !== 'recaptcha') {

				if (value.type == "text" || value.type == "email" || value.type == "tel" || value.type == "number") {
					value.el.val('');
					hideErrMsg(value);
				}

				if (value.type == "textarea") {
					value.el.val('');
					hideErrMsg(value);
				}

				if (value.type == "radio-group" || value.type == "checkbox-group") {
					value.el.prop('checked', false);
					hideErrMsg(value);
				}

				if (value.type == "select") {
					var has_default = false;
					$.each(value.values, function(k, v) {
						if (v.selected === true) {
							value.el.val(v.value);
							has_default = true;
						}
					})

					if (!has_default) {
						value.el.val("");
					}
					hideErrMsg(value);
				}

				if (value.type == "file") {
					//var fileinpName = value.el.replace(/[-_]/g, '');
					//if (value.subtype == "file") {
					//	window["FILE_INPUT_mfi" + fileinpName].clear();
					//} else {
					//	$("#file-selectdialog-mfi" + fileinpName + " .files-list tr").remove();
					//	$('[name="' + fileinpName + '[]"]').val('');
					//}
					hideErrMsg(value);
				}
			} else {
				grecaptcha.reset(form.id);
				value.el = form.el.find('.g-recaptcha-response');
			}

		})
	}



	/*Private methods*/
	function visitSubscribers(action, arg, type) {
		var pubtype = type || "any";
		var subscribers = that.subscribers[pubtype];
		var i;

		if (subscribers) {
			var max = subscribers.length;
			for (i = 0; i < max; i += 1) {
				if (action === "publish") {
					subscribers[i](arg);
				} else {
					if (subscribers[i] === arg) {
						subscribers.splice(i, 1);
					}
				}
			}
		}
	}

	function onSubmit(e, form) {
		e.preventDefault();

		validateForm(form);

		if (form.valid === true) {
			makeAjax(form);
		} else {
			return false;
		}

		return false;
	}

	function validateForm(form) {
		form.valid = true;
		$.each(form.fields, function(fk, field) {
			if (field.required) {
				if (!validation(field)) {
					form.valid = false;
				}
			}

			if (field.type != 'file') {
				field.el.on('click change keyup', function() {
					hideErrMsg(field);
				});
			}
		});


		if (form.valid) {
			that.publish({
				"form": sanitizeData(form)
			}, that.subscribtionsList.successful_validation);
		} else {
			that.publish({
				"form": sanitizeData(form)
			}, that.subscribtionsList.unsuccessful_validation);
		}
	}

	function makeAjax(form) {
		data = '';

		data = new FormData();
		var msg = form.el.serializeArray();
		$.each(msg, function(key, value) {
			data.append(value['name'], value['value']);
		});


		data.append("AJAX", "Y");
		data.append("form_name", form.name);

		/*
			Нужно избавиться от el в объекте, т.к с ним не срабатывает JSON.stringify
		*/
		var msgFields = [];
		$.each(form.fields, function(f, fv) {
			var item = {};
			$.each(fv, function(k, v) {
				if (k != 'el') {
					item[k] = v;
				}
			});

			msgFields.push(item);
		});

		data.append("form_fields", JSON.stringify(msgFields));
		data.append("mail_template", form.arParams['MAIL_TEMPLATE']);

		$.ajax({
			type: 'POST',
			url: "",
			// data: msg,
			data: data,
			//dataType: 'json',
			processData: false, // Не обрабатываем файлы (Don't process the files)
			contentType: false, // Так jQuery скажет серверу что это строковой запрос,
			beforeSend: function() {
				elMakeInactive(form.el.find('.js-clf-submit-btn'), true);
				elSetLoading(form.el.find('.js-clf-submit-btn'), true);
			},
			success: function(data) {
				elMakeInactive(form.el.find('.js-clf-submit-btn'), false);
				elSetLoading(form.el.find('.js-clf-submit-btn'), false);
				if (data) {
					data = JSON.parse(data);
				}
				//debugger;

				if (form.arParams['USE_RECAPTCHA'] == "Y") {
					if (data.recaptcha == "true") {
						that.publish({
							"response": data,
							"form": sanitizeData(form)
						}, that.subscribtionsList.succesfull_send);
					} else {
						that.publish({
							"response": data,
							"form": sanitizeData(form)
						}, that.subscribtionsList.recaptcha_error);
					}
				} else {
					that.publish({
						"response": data,
						"form": sanitizeData(form)
					}, that.subscribtionsList.succesfull_send);
				}

			},
			error: function(xhr, str) {
				elMakeInactive(form.el.find('.js-clf-submit-btn'), false);
				elSetLoading(form.el.find('.js-clf-submit-btn'), false);
				that.publish({
					"response": data,
					"form": sanitizeData(form)
				}, that.subscribtionsList.unsuccessful_send);
				console.log('Возникла ошибка: ' + xhr.responseCode);

			}
		});
	}

	function showErrMsg(field) {
		if (field.type != 'file') {
			field.el.parents('.js-clf-field').find('.js-clf-err-msg').fadeIn(100);
		} else {
			$('[data-field-name="' + field.name + '"]').parents('.js-clf-field').find('.js-clf-err-msg').fadeIn(100);
		}
	}

	function hideErrMsg(field) {
		if (field.type != 'file') {
			field.el.parents('.js-clf-field').find('.js-clf-err-msg').fadeOut(100);
		} else {
			$('[data-field-name="' + field.name + '"]').parents('.js-clf-field').find('.js-clf-err-msg').fadeOut(100);
		}
	}

	function validation(field) {
		if (field.type === 'checkbox-group' || field.type === 'radio-group') {
			var groupRes = false;
			$.each(field.el, function() {
				var checked = $(this).prop('checked'); // true/false
				if (checked) {
					groupRes = true;
				}
			});

			if (groupRes) {
				return true;
			} else {
				showErrMsg(field);
				return false;
			}
		}

		if (field.type === 'select') {
			var select = field.el.val(); // true/false
			if (select != '') {
				return true;
			} else {
				showErrMsg(field);
				return false;
			}
		}

		if (field.validationRule == 'name') {
			var name = '';
			name = field.el.val();
			if (name.length < 3) {
				showErrMsg(field);
				return false;
			} else {
				return true;
			}
		}

		if (field.validationRule == 'email') {
			var email = '';
			email = field.el.val();
			var reEmail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if (reEmail.test(email)) {
				return true;
			} else {
				showErrMsg(field)
				return false;
			}
		}

		if (field.validationRule == 'phone') {
			var phone = '';
			phone = field.el.val();
			var reNumb = /^[0-9()-+- ]+$/;
			if (phone.length >= 6 && reNumb.test(phone)) {
				return true;
			} else {
				showErrMsg(field);
				return false;
			}
		}

		if (field.validationRule == 'regexp') {
			var value = '';
			value = field.el.val();
			var regExp = new RegExp(field.validationRegExp);
			if (regExp.test(value)) {
				return true;
			} else {
				showErrMsg(field);
				return false;
			}
		}

		if (field.type == 'file') {
			var value = '';
			var name = field.el.replace(/[-_]/g, '');

			if (field.multiple == "Y") {
				value = $('[name="' + name + '[]"]').val();
			} else {
				value = $('[name="' + name + '"]').val();
			}


			if (value) {
				return true;
			} else {
				showErrMsg(field);
				return false;
			}
		}

		if (field.type == 'number') {
			var value = '';

			value = field.el.val();

			if (value == '') {
				showErrMsg(field);
				return false;
			}

			if (field.min != '' && value < field.min) {
				showErrMsg(field);
				return false;
			}


			if (field.max != '' && value > field.max) {
				showErrMsg(field);
				return false;
			}

			return true;
		}
	} //end valdation

	function attachmentMove() { //Перенос компонента attchment в нужную зону
		var attachmentFields = $('.js-cf-attachment-field');
		if (attachmentFields.length > 0) {
			attachmentFields.each(function() {
				$('[data-field-area-name="' + $(this).attr('data-field-name') + '"]').append($(this));
			});
		}
	}

	function sanitizeData(obj) {
		var res = {};
		$.each(obj, function(key, value) {
			if (key != "arParams") {
				res[key] = value;
			}
		})

		return res;
	}

	function elMakeInactive(el, isSwitchon) {
		if (isSwitchon) {
			el.addClass('clientlabform-el--inactive');
			el.prop('disabled', true);
		} else {
			el.removeClass('clientlabform-el--inactive');
			el.prop('disabled', false);
		}
	}

	function elSetLoading(el, isSwitchon) {
		if (isSwitchon) {
			el.addClass('clientlabform-el--loading');
		} else {
			el.removeClass('clientlabform-el--loading');
		}
	}

} //end CLF