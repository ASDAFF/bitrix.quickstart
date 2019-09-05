// Константы контроллера
const FC_AJAXGATE = location.href;
const FC_ORDERFORM = ".theform";

// Константы форм
const SF_DIVPREFIX = ".orderform-";
const SF_SCRIPTPOSTFIX = ".php";
const SF_NOTREADY_HTML = "";

/**
 *	Эмулятор критических секций. 
 *	Позволяет задать область кода, которая может выполняться 
 *	только один раз в конкретный промежуток времени.
 *	В данном коде предотвращает 'закликивание' формы.
 */
var critical = (function() {
	var criticals = {};
	var executed = [];

	function validateArguments(props) {
		if (typeof props.id !== "string")
			return false;

		if (typeof props.criticalFunction !== "function")
			return false;

		return true;
	}

	function isExecuted(id) {
		for (var i = 0; i < executed.length; i++) {
			if (executed[i] == id) {
				return true;
			}
		}
		return false;
	}

	return {
		/**
		 *	Определить критическую секцию.
		 *	Внутри критической секции по окончании её выполнения 
		 *	необходимо вызвать функцию endCritical(), которая 
		 *	освободит id секции и позволит ей выполняться в дальнейшем.
		 *	props: {
		 *		id: String - Произвольный уникальный идентификатор секции
		 *		criticalFunction: function(endCritical) - функция с участком критической секции
		 *	}
		 */
		enterCritical: function(props) {
			function endCritical() {
				delete criticals[props.id];
			}

			if (!validateArguments(props))
				throw { message: "bad arguments" };
			if (typeof criticals[props.id] !== "undefined") {
				// критическая секция уже выполняется
				return;
			} else {
				// запуск
				criticals[props.id] = 1;
				executed.push(props.id);
				props.criticalFunction(endCritical);
			}
		},

		/**
		 *	Определить участок кода, который выполнится только один раз.
		 *	В основном требуется в целях отладки, либо
		 *	инициализации чего-либо.
		 *	props: {
		 *		id: String - Произвольный уникальный идентификатор секции
		 *		criticalFunction: function(endCritical) - функция с участком критической секции
		 *	}
		 */
		once: function(props) {
			if (!validateArguments(props))
				throw { message: "bad arguments" };
			if (isExecuted(props.id)) {
				// критическая секция уже выполнена
				if (typeof props.elseFunction == "function")
					props.elseFunction();
				return;
			} else {
				// запуск
				executed.push(props.id);
				props.criticalFunction();
			}
		}
	}
})();

/**
 *	Контроллер форм
 *	props: Object - объект с параметрами
 *	props.ajaxgate: String - url, на который будем отправлять запросы получения данных и вьюх
 *	props.readyField: String - селектор поля, блокирующего случайное оформление заказа
 *	props.orderDiv: String - селектор дива, в который будем помещать ошибки
 */
function FormController(props) {
	// Для дебага.
	// this.name = "controller";
	// this.id = 0;
	props = props ? props : {};
	// проверки вводимых данных нет, потому как по сути область использования данного класса определена
	this.ajaxgate = props.ajaxgate ? props.ajaxgate : location.href;
	this.selReadyField = props.readyField ? props.readyField : "#orderfield_READY";
	this.selErrorDiv = props.errorDiv ? props.errorDiv : ".order-errors";

	// Данные, полученные от последнего запроса
	this.post = null;

	// Имеющиеся формы
	this.forms = [];

	// флаг изменения (защита от закликивания формы)
	//this.changed = false;
}

// Сравнение данных
FormController.prototype.equalData = function(obj1, obj2) {
	if($.toJSON(obj1) == $.toJSON(obj2))
		return true;
	else
		return false;
}

// Создаёт новую форму и добавляет её в массив контроллера
FormController.prototype.addForm = function(props) {
	try {
		var form = new StepForm(props);
	} catch(e) {
		throw e;
	}
	form.controller = this;
	this.forms.push(form);
	return form;
};

// Находит форму по имени
FormController.prototype.findForm = function(formName) {
	for(var i = 0; i < this.forms.length; i++) {
		if(this.forms[i].name == formName)
			return this.forms[i];
	}
	return null;
};

// Составляет строку запроса из всех инпутов формы
FormController.prototype.makeQuery = function(sForm) {
	var arQueryString = [];
	$.each($(sForm).find('input'), function() {
		if(this.type == "radio") {
			// в строку запроса должно быть записано только значение отмеченного радио
			if($(this)[0].checked) {
				arQueryString.push( $(this)[0].name + "=" + $(this)[0].value );
			}
		} else {
			arQueryString.push( $(this)[0].name + "=" + $(this)[0].value );
		}
	});
	return arQueryString.join("&");
};

// Получает все возможные данные из компонента путём AJAX-запроса
FormController.prototype.getData = function(handler) {
	var self = this;
	var query = self.makeQuery(FC_ORDERFORM);

	$.ajax({
		url: FC_AJAXGATE,
		type: "POST",
		data: query,
		dataType: "json",
		success: function(data) {
			self.post = data;
			if(typeof(handler) == "function")
				handler(data);
		},
		error: function(details) {
			console.log("AJAX ERROR");
			console.log(details);
		}
	});
};

// конструктор контроллера
FormController.prototype.run = function(handler) {
	if(typeof handler == "function")
		handler();

	this.refresh();
};

/**
 *	Обновление всех форм.
 *	В ранних версиях скрипта эта функция использовалась для полного
 *	обновления всех форм с учётом проверки на зависимости.
 *	В дальнейшем эта возможность показалась мне избыточной.
 *	Функция же осталась для первоначального построения форм, которые
 *	не зависят от других (параметр requires равен пустому массиву).
 *	Возможно она окажется полезной при дальнейшей разработке.
 */
FormController.prototype.refresh = function() {
	var self = this;

	function checkDependencies(form) {
		// выкидываем, если хотя бы одна из требуемых форм не готова
		for (var i = 0; i < form.requires.length; i++) {
			var requiredForm = self.findForm(form.requires[i]);
			if(!requiredForm.isReady)
				return false;
		}
		return true;
	}

	this.getData(function(data) {
		self.changed = false;
		for (var i = 0; i < self.forms.length; i++) {
			var form = self.forms[i];
			if (checkDependencies(form)) {
				if(form.isBuilt) {
					form.update(form.requiredData(data));
				}
			}
		}
		for (var i = 0; i < self.forms.length; i++) {
			var form = self.forms[i];
			if (checkDependencies(form)){
				if(!form.isBuilt) {
					form.loadView(form.requiredData(data));
				}
			} else {
				form.destroy();
			}
		}
		
	});
};

// Обрабочик нажатия кнопки submit
FormController.prototype.submitForm = function() {
	var self = this;
	var errors = [];
	
	for (var i = 0; i < this.forms.length; i++) {
		var formErrors = this.forms[i].validate();
		errors = errors.concat(formErrors);
	}

	console.log(errors);

	if (errors.length > 0) {
		showErrors(errors);
		return false;
	} else {
		// !ЗАВИСИМОСТЬ
		/* поле, разрешающее серверу оформить заказ. 
		В случае его отсутствия при получении данных AJAX-запросом 
		битрикс может решить, что мы готовы оформить заказ */
		$(self.selReadyField).val("ready");
		var query = self.makeQuery(FC_ORDERFORM);
		// эти параметры обозначают непосредственно готовность оформить заказ
		query += "&confirmorder=Y&SUBMIT_FORM=Y";
		$.ajax({
			url: FC_AJAXGATE,
			type: "POST",
			data: query,
			dataType: "json",
			success: function(data) {
				if (typeof data.redirect == "string") {
					window.top.location = data.redirect;
				} else if (data.error.length >= 1) {
					$(self.selReadyField).val("");
					showErrors(data.error);
				}
			},
			error: function(details) {
				console.log("AJAX ERROR");
				console.log(details);
			}
		});
	}

	function showErrors(arErrors) {
		window.scrollTo(0,0);
		$(self.selErrorDiv).removeClass("hidden");
		var sErrors = "";
		for (var i = 0; i < arErrors.length; i++) {
			sErrors += '<span class="order-error">' + (arErrors[i]) + '</span>';
		}
		$(self.selErrorDiv).html(sErrors);
	}
};

/**
 *	Класс формы
 *	props: Object - объект с параметрами
 *	Обязательные параметры:
 *	props.name: String - уникальное имя формы
 *	props.requires: Array - массив, в котором содержатся
 *		имена форм, необходимых для построения данной
 *	Необязательные парамерты:
 *	props.requiredData: function(data) - функция, возвращающая
 *		данные из общего потока, необходимые для построения формы.
 *	props.onChange: function - обработчик изменения данных формы
 *	props.onBuild: function - обработчик построения формы. В нём 
 *		следует определить те элементы, которые могут вызвать onChange
 *	props.update: function - обработчик обновления формы
 *	props.validate: function - специфичная для данной формы функция валидации
 */
function StepForm(props) {
	// состояния готовности формы
	this.isReady = false;
	this.isBuilt = false;

	// ссылка на контроллер, для фидбека
	this.controller = null;

	// регекспы для полей формы
	this._fieldRules = [];

	if(typeof props.name == "string")
		this.name = props.name;
	else
		throw { message: "invalid name" };

	if(typeof props.div == "string")
		this.div = props.div;
	else
		this.div = SF_DIVPREFIX + props.name;

	if(typeof props.script == "string")
		this.script = props.script;
	else
		this.script = props.name + SF_SCRIPTPOSTFIX;

	if(props.requires instanceof Array)
		this.requires = props.requires;
	else
		this.requires = [];

	// Перегрузка функций
	if(typeof props.requiredData == "function")
		this.requiredData = props.requiredData;

	if(typeof props.onChange == "function")
		this.onChange = props.onChange;

	if(typeof props.onBuild == "function")
		this.onBuild = props.onBuild;

	if(typeof props.update == "function")
		this.update = props.update;

	if(typeof props.validate == "function")
		this.validate = props.validate;
}

StepForm.prototype.ready = function() { this.isReady = true; };
StepForm.prototype.notReady = function() { this.isReady = false; };

/**
 *	Грузит форму через ajax-gate.php, который просто инклюдит нужный скрипт.
 *	Результат его работы вставляется в нужный элемент. 
 *	Подключаемый скрипт просто выполняет роль вьюшки для отправляемых данных.
 */
StepForm.prototype.loadView = function(data, handler) {
	var self = this;

	if(typeof(data) == "object")
		sData = $.toJSON(data);

	// на всякий случай
	sData = encodeURI(sData);

	$.ajax({
		url: FC_AJAXGATE,
		type: "POST",
		// Параметр AJAX_QUERY сообщает серверу о намерении получить данные, либо html для формы
		data: "AJAX_QUERY=Y&LOAD_STEP=" + self.script + "&DATA=" + sData + "&SUBMIT_FORM=N",
		dataType: "text",
		success: function(html) {
			$(self.div).html(html);
			self.isBuilt = true;
			if(typeof self.onBuild == "function")
				self.onBuild(data);
		},
		error: function(details) {
			console.log(self.name + ".loadView: ajax error");
			console.log(details);
		},
		complete: function() {
			if(typeof(handler) == "function")
				handler();
		}
	});
};

// Выполняет полную перезагрузку формы с получением новых данных.
StepForm.prototype.reload = function(handler) {
	var self = this;
	var controller = this.controller;
	controller.getData(function(data) {
		self.build(self.requiredData(data), function() {
			if(typeof handler == "function")
				handler();
		});
	});
};

// Зависимую форму нужно уничтожить в случае нехватки данных для её отображения
StepForm.prototype.destroy = function() {
	$(this.div).html(SF_NOTREADY_HTML);
	this.isBuilt = false;
};

// Обработчик изменения формы
StepForm.prototype.onChange = function() {
	//this.controller.refresh();
};

// Фильтр данных, возвращающий только те, что требуются для конкретной формы
StepForm.prototype.requiredData = function(data) {
	return data;
};

// Обработчик обновления по умолчанию
StepForm.prototype.update = function() { };

// Добавить регексп, фильтрующий значение поля
StepForm.prototype.addFieldRule = function(props) {
	if(typeof props.fieldname !== "string")
		throw { message: "bad arguments" };
	if(typeof props.regexp !== "string")
		throw { message: "bad arguments" };
	if(typeof props.message !== "string")
		throw { message: "bad arguments" };
	this._fieldRules.push({
		fieldname: props.fieldname,
		regexp: props.regexp,
		message: props.message
	});
	return this;
};

/**
 *	Валидатор формы
 *	Просто по очереди проверяем заданные поля на соответствие их регулярным выражениям
 *	! Эта функция рассчитана на текстовые поля. Для других применяйте перегрузку.
 */
StepForm.prototype.validate = function() {
	var errors = [];
	console.log(this._fieldRules);
	for (var i = 0; i < this._fieldRules.length; i++) {
		var selector = "input[name=" + this._fieldRules[i].fieldname + "]";
		// если поле не видно для пользователя, то проверять его нет смысла.
		if ( $(selector).length == 0 || $(selector).parent().is(":hidden") )
			continue;
		var regexp = new RegExp(this._fieldRules[i].regexp);
		var str = $(selector).val();
		if (!regexp.test(str))
			errors.push(this._fieldRules[i].message);
	}
	return errors;
};

var controller;

$(document).ready(function() {
	controller = new FormController();

	// persontype
	controller.addForm({
		name: "persontype",
		requires: [],
		requiredData: function(data) {
			return { PERSON_TYPE: data.PERSON_TYPE };
		}
	});

	// properties
	controller.addForm({
		name: "properties",
		requires: [],
		requiredData: function(data) {
			return { ORDER_PROP: data.ORDER_PROP, DELIVERY: data.DELIVERY, FIELDS: data.FIELDS};
		},
		onBuild: function(data) {
			var self = this;

			function findTrueLocation(fieldValue, source) {
				fieldValue = fieldValue.toLowerCase();
				for(var i = 0; i < source.length; i++) {
					if(source[i].CITY_NAME != null)
						if(fieldValue == source[i].CITY_NAME.toLowerCase())
							return source[i];
				}
				return null;
			}
			
			var locationFields = [];

			// Перебираем свойства и ищем города
			for(var i in data.ORDER_PROP.USER_PROPS_N) {
				// Имя объекта представляет из себя цифру
				var property = data.ORDER_PROP.USER_PROPS_N[i];
				if(property.TYPE == "LOCATION")
					locationFields.push(property);
			}

			// Обрабатываем города
			for(var i = 0; i < locationFields.length; i++) {
				var locationField = locationFields[i];
				var variants = locationField.VARIANTS;
				var autocomplete = [];
				for(var j = 0; j < variants.length; j++) {
					autocomplete[j] = {}
					if(variants[j].CITY_NAME !== null) {
						autocomplete[j].label = variants[j].CITY_NAME;
						autocomplete[j].value = variants[j].NAME;
					}
				}

				// На сервер отправляется id города. Поле со значением нужно только для удобного ввода.
				var idField = "#" + locationField.FIELD_ID;
				var valueField = "#" + locationField.FIELD_ID + "_VAL";

				function upd() {
					var trueLocation = findTrueLocation($(valueField).val(), variants);
					if(trueLocation != null) {
						$(valueField).val(trueLocation.NAME);
						$(idField).val(trueLocation.ID);
						self.ready();
						self.onChange();
					} else {
						$(valueField).val("");
						$(idField).val(0);
						var f_delivery = self.controller.findForm("delivery");
						var f_paysystem = self.controller.findForm("paysystem");
						f_delivery.destroy();
						f_paysystem.destroy();
					}
				}

				// Значимое поле
				$(valueField).autocomplete({
					source: autocomplete,
					change: function(e, ui) {
						upd();
					},
					select: function(e, ui) {
						upd();
					}
				});
			}

			// показываем нужные
			var requiredFields = data.FIELDS.required;
			for(var i = 0; i < requiredFields.length; i++) {
				$("#" + requiredFields[i]).removeClass("hidden");
			}
		},
		update: function(data) {
			var self = this;

			function updateRows(arVisible, arRequired) {
				function inArray(tofind) {
					for (i = 0; i < this.length; i++)
						if (this[i] == tofind)
							return true;
					return false;
				}

				arVisible.inArray = inArray;
				arRequired.inArray = inArray;

				console.log(arVisible);
				$.each($(self.div).find('tr'), function() {
					// обрабатываем только необязательные поля
					if (!arRequired.inArray(this.id)) {
						if (arVisible.inArray(this.id)) {
							$(this).removeClass("hidden");
						} else {
							if (!$(this).hasClass("hidden"))
								$(this).addClass("hidden");
						}
					}
				});
			}

			var f_delivery = controller.findForm("delivery");
			console.log(self.controller.post.FIELDS[f_delivery.deliveryValue]);
			if (self.controller.post.FIELDS[f_delivery.deliveryValue] instanceof Array)
				updateRows(self.controller.post.FIELDS[f_delivery.deliveryValue],self.controller.post.FIELDS["required"]);
		},
		onChange: function() {
			var self = this;
			critical.enterCritical({id: "properties_onchange", criticalFunction: function(endCritical) {
				var f_delivery = self.controller.findForm("delivery");
				self.controller.getData(function(data) {
					f_delivery.loadView(f_delivery.requiredData(data), function() {
						endCritical();
					});
				});
			}});
		}
	}).addFieldRule({
		fieldname: "ORDER_PROP_2", // E-MAIL
		regexp: "^[a-zA-Z0-9_.-]+@[\\w\\W]+$", // weak regexp
		message: "Некорректный e-mail адрес"
	}).addFieldRule({
		fieldname: "ORDER_PROP_3", // ADRESS
		regexp: "^[0-9а-яА-Я.,\\s]+$",
		message: "Введите адрес"
	}).addFieldRule({
		fieldname: "ORDER_PROP_4", // PHONE
		regexp: "^[0-9()+]+$",
		message: "Введите телефон"
	}).addFieldRule({
		fieldname: "ORDER_PROP_5", // FIRST_NAME
		regexp: "^[a-zA-Zа-яА-Я]+$",
		message: "Введите имя"
	}).addFieldRule({
		fieldname: "ORDER_PROP_6", // LAST_NAME
		regexp: "^[a-zA-Zа-яА-Я]+$",
		message: "Введите фамилию"
	}).addFieldRule({
		fieldname: "ORDER_PROP_7", // INDEX
		regexp: "^[0-9]{6}$",
		message: "Введите индекс в формате 123456"
	});

	// delivery
	controller.addForm({
		name: "delivery",
		requires: ["properties"],
		requiredData: function(data) {
			return data.DELIVERY;
		},
		onBuild: function(data) {
			var self = this;
			self.notReady();
			self.deliveryValue = "required";

			function uncheck() {
				$.each($(self.div).find('input'), function() {
					if(this.type == "radio") {
						if($(this)[0].checked == true) {
							$(this)[0].checked = false;
						}
					}
				});
			}

			var f_paysystem = self.controller.findForm("paysystem");
			f_paysystem.destroy();
			var f_properties = self.controller.findForm("properties");
			f_properties.update();

			uncheck();

			$(".delivery-radio").click(function() {
				self.ready();
				self.deliveryValue = this.value;
				self.onChange();
			});
		},
		update: function(data) {

		},
		onChange: function() {
			var self = this;
			critical.enterCritical({id: "delivery_onchange", criticalFunction: function(endCritical) {
				var f_properties = self.controller.findForm("properties");
				var f_paysystem = self.controller.findForm("paysystem");
				var f_summary = self.controller.findForm("summary");
				f_properties.update();
				self.controller.getData(function(data) {
					f_paysystem.loadView(f_paysystem.requiredData(data), function() {
						f_summary.loadView(f_summary.requiredData(data), function() {
							endCritical();
						});
					});
				});
			}});
		}
	}).addFieldRule({
		fieldname: "PP_SMS_PHONE", // телефон pickpoint
		regexp: "^[0-9]{11}$",
		message: "Заполните номер телефона для PickPoint"
	}).addFieldRule({
		fieldname: "PP_ID", // постамат pickpoint
		regexp: "^[0-9\-]+$",
		message: "Выберите постамат"
	});

	// paysystem
	controller.addForm({
		name: "paysystem",
		requires: ["properties", "delivery"],
		requiredData: function(data) {
			return { PAY_SYSTEM: data.PAY_SYSTEM, PAY_FROM_ACCOUNT: data.PAY_FROM_ACCOUNT };
		},
		onBuild: function(data) {
			
		},
		update: function(data) {

		},
		onChange: function() {
			critical.enterCritical({id: "paysystem_onchange", criticalFunction: function(endCritical) {
				var f_summary = self.controller.findForm("summary");
				self.controller.getData(function(data) {
					f_summary.loadView(f_summary.requiredData(data), function() {
						endCritical();
					});
				});
			}});
		}
	});

	// summary
	controller.addForm({
		name: "summary",
		requires: [],
		requiredData: function(data) {
			return {
				BASKET_ITEMS: data.BASKET_ITEMS,
				DISCOUNT_PRICE: data.DISCOUNT_PRICE,
				DISCOUNT_PERCENT_FORMATED: data.DISCOUNT_PERCENT_FORMATED,
				DISCOUNT_PRICE_FORMATED: data.DISCOUNT_PRICE_FORMATED,
				DELIVERY_PRICE: data.DELIVERY_PRICE,
				ORDER_TOTAL_PRICE_FORMATED: data.ORDER_TOTAL_PRICE_FORMATED,
				DELIVERY_PRICE_FORMATED: data.DELIVERY_PRICE_FORMATED,
				ORDER_PRICE_FORMATED: data.ORDER_PRICE_FORMATED,
				ORDER_WEIGHT_FORMATED: data.ORDER_WEIGHT_FORMATED
			};
		},
		onBuild: function(data) {
			
		},
		update: function(data) {
			this.build(data)
		},
		onChange: function() {
			
		}
	});

	controller.run();
});

// Обработчик кнопки субмит на форме
function submitForm() {
	controller.submitForm();
}