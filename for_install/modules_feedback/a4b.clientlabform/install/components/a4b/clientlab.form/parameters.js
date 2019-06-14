var mutationObserver = new MutationObserver(function(mutations) {
	for (var i = mutations.length - 1; i >= 0; i--) {
		var mutation = mutations[i];
		var found = false;

		if (mutation.addedNodes.length > 0) {
			if (typeof mutation.addedNodes[0]['className'] !== 'undefined') {
				for (var j = mutation.addedNodes.length - 1; j >= 0; j--) {
					if (_jQuery(mutation.addedNodes[j]).find('input[data-bx-property-id="OPTIONS"]').length > 0) {
						initBuilder(_jQuery(mutation.addedNodes[j]).find('input[data-bx-property-id="OPTIONS"]'));
						found = true;
						break;
					}
				}
			}
		}

		if (found) {
			break;
		}
	}
});

mutationObserver.observe(document.documentElement, {
	childList: true,
	subtree: true
});



function initBuilder(textfield) {

	/*var textfield = __jQuery('input[data-bx-property-id="OPTIONS"]');

	if (textfield.length < 1) {
		return;
	}
	console.log('initBiulder');


	if (__jQuery('#js-editor').length > 0) {
		//__jQuery('#js-editor').remove();
	}*/

	textfield.attr('readonly', true);
	//console.log('__jQuery(#js-editor)', __jQuery('#js-editor').length);
	textfield.after(_jQuery('<div id="js-editor"></div>'));

	var element = _jQuery('#js-editor');

	var options = {
		"typeUserAttrs": {
			"paragraph": {
				"content": {
					"label": 'Содержимое',
				},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"subtype": {
					"label": 'Тип поля',
					"options": {
						'p': 'Абзац текста',
					},
				},
			},
			"hidden": {
				"name": {
					"label": 'Имя поля',
					"value": "",
					"placeholder": 'Только латинские символы и цифры'
				},
				"value": {
					"label": 'Значение',
					"value": "",
					"placeholder": 'Значение или #GET параметр#'
				},
			},
			"text": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"placeholder": {
					"label": 'Замещающий текст',
					"value": '',
					"placeholder": 'Отображается внутри поля, если оно пусто'
				},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"value": {
					"label": 'Значение поля',
					"value": '',
					"placeholder": ''
				},
				"maxlength": {
					"label": 'Максимальная длина ввода',
					"value": '',
				},
				"subtype": {
					"label": 'Тип поля',
					"options": {
						'text': 'Простое текстовое поле',
						'password': 'Поле ввода пароля',
						'email': 'Поле ввода Email',
						'color': 'Выбор цвета',
						'tel': 'Поле для телефона'
					},
				},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"validationRule": {
					"label": 'Правило валидации поля',
					"options": {
						'name': 'Имя',
						'phone': 'Телефон',
						'email': 'Email адрес',
						'regexp': 'Регулярное выражение.'
					},
				},
				"validationRegExp": {
					"label": 'Регулярное выражение для валидации',
					"value": '',
					"placeholder": 'Пример: [А-Яа-я]'
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
					"placeholder": 'Сообщение для неверно заполненного поля'
				}
			},
			"number": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"placeholder": {
					"label": 'Замещающий текст',
					"value": '',
					"placeholder": 'Отображается внутри поля, если оно пусто'
				},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"value": {
					"label": 'Значение поля',
					"value": '',
					"placeholder": ''
				},

				"min": {
					"label": 'Минимум',
					"value": '',
					"placeholder": 'Числовое значение'
				},
				"max": {
					"label": 'Максимум',
					"value": '',
					"placeholder": 'Числовое значение'
				},
				"step": {
					"label": 'Шаг',
					"value": '',
					"placeholder": 'Числовое значение'
				},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
					"placeholder": 'Сообщение для неверно заполненного поля'
				},
			},
			"textarea": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"placeholder": {
					"label": 'Замещающий текст',
					"value": '',
					"placeholder": 'Отображается внутри поля, если оно пусто'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"value": {
					"label": 'Значение поля',
					"value": '',
					"placeholder": ''
				},
				"subtype": {
					"label": "Тип поля",
					"options": {
						'textarea': 'Область текста',
					},
				},
				"maxlength": {
					"label": 'Максимальная длина ввода',
					"value": '',
				},
				"rows": {},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"validationRule": {
					"label": 'Правило валидации поля',
					"options": {
						'name': 'Имя',
						'phone': 'Телефон',
						'email': 'Email адрес',
						'regexp': 'Регулярное выражение.'
					},
				},
				"validationRegExp": {
					"label": 'Регулярное выражение для валидации',
					"value": '',
					"placeholder": 'Регулярное выражение javaScript'
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
				},
			},
			"select": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"placeholder": {},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"multiple": {
					"label": 'Разрешить множественный выбор',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
					"placeholder": 'Сообщение для неверно заполненного поля'
				},
			},
			"checkbox-group": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"inline": {
					"label": 'В одну строку',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"toggle": {},
				"other": {},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
					"placeholder": 'Сообщение для неверно заполненного поля'
				}
			},
			"radio-group": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"inline": {
					"label": 'В одну строку',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
					"placeholder": 'Сообщение для неверно заполненного поля'
				},
				"other": {},
			},
			"header": {
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},
				"label": {
					"label": 'Текст',
					"value": '',
					"placeholder": 'Текст заголовка'
				},
				"subtype": {
					"label": 'Уровень заголовка',
					"options": {
						'h1': 'H1',
						'h2': 'H2',
						'h3': 'H3',
						'h4': 'H4',
					},
				},
			},
			"file": {
				"name": {
					"label": 'Название поля',
					"value": '',
					"placeholder": 'Только латинские буквы'
				},
				"label": {
					"label": 'Метка поля',
					"value": '',
					"placeholder": 'Отображается перед полем'
				},
				"description": {
					"label": 'Подсказка',
					"value": '',
					"placeholder": 'Отображается под полем'
				},
				"placeholder": {},
				"className": {
					"label": 'CSS клас поля',
					"value": '',
					"placeholder": 'CSS классы разделенные пробелами'
				},

				"multiple": {
					'label': 'Множественная загрузка файлов',
					"options": {
						'Y': 'Y',
						'N': 'N'
					},
				},
				"subtype": {
					"label": 'Вид загрузчика',
					"options": {
						'file': 'Кнопка',
						'fineuploader': 'Область Drag & Drop',
					},
				},
				"allow_upload": {
					"label": 'Разрешить загружать',
					"options": {
						'F': 'Файлы',
						'I': 'Изображения',
						'A': 'Все подряд'
					},
					//"description": "какой тип файлов будем грузить: F - файлы, I - картинки, A - все подряд.",
				},
				"allow_upload_ext": {
					label: 'Разрешить только эти типы файлов',
					value: '',
					"placeholder": 'zip,rar,doc и пр. Если поле "Разрешить загружать" = Файлы'
				},
				"required": {
					"label": 'Обязательное поле',
					"options": {
						'false': 'Нет',
						'true': 'Да'
					},
				},
				"errMsg": {
					"label": 'Текст ошибки валидации',
					"value": 'Поле заполнено неверно',
					"placeholder": 'Сообщение для неверно заполненного поля'
				}
			}
		},
		disableFields: ['autocomplete', 'button', 'date'],
		disabledAttrs: ['access'],
		onSave: function(formData) {
			var data = fb.actions.getData();
			_jQuery('input[data-bx-property-id="OPTIONS"]').val(JSON.stringify(data));
		}
	};


	if (typeof updateOption_interval !== 'undefined') {
		clearInterval(updateOption_interval);
	}

	var fb = _jQuery("#js-editor").formBuilder(options);
	fb.promise.then(function(fb) {
		fb.actions.setLang("ru-RU");
		if (_jQuery('input[data-bx-property-id="OPTIONS"]').length > 0) {
			var savedParams = _jQuery('input[data-bx-property-id="OPTIONS"]').val().trim().replace(/\\"/g, '"');
			if (savedParams !== '') {
				try {
					fb.actions.setData(JSON.parse(savedParams));
				} catch (e) {
					console.log('e', e);
				}
			}

			updateOption_interval = setInterval(function() {
				try {
					var data = fb.actions.getData();
					_jQuery('input[data-bx-property-id="OPTIONS"]').val(JSON.stringify(data));
				} catch (e) {
					console.log('e', e);
				}
			}, 300);
		}
	});

}