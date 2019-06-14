(function(BX, $, window) {

	var Plugin = BX.namespace('YandexMarket.Plugin');
	var Admin = BX.namespace('YandexMarket.Ui.Admin');
	var utils = BX.namespace('YandexMarket.Utils');

	var constructor = Admin.ExportForm = Plugin.Base.extend({

		defaults: {
			messageElement: '.js-export-form__message',

			runButtonElement: '.js-export-form__run-button',
			stopButtonElement: '.js-export-form__stop-button',

			timerHolderElement: '.js-export-form__timer-holder',
			timerElement: '.js-export-form__timer',

			errorTemplate: '<div class="adm-info-message-wrap adm-info-message-red"><div class="adm-info-message"><div class="adm-info-message-title">#TITLE#</div><textarea cols="60" rows="5"></textarea><div class="adm-info-message-icon"></div></div></div>',

			langPrefix: 'YANDEX_MARKET_EXPORT_FORM_',
			lang: {}
		},

		initVars: function() {
			this.callParent('initVars', constructor);

			this._formData = null;
			this._query = null;
			this._queryTimeout = null;
			this._state = null;
			this._timerInterval = null;
		},

		initialize: function() {
			this.bind();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handlRunClick(true);
			this.handleStopClick(true);
		},

		unbind: function() {
			this.handlRunClick(false);
			this.handleStopClick(false);
		},

		handlRunClick: function(dir) {
			var button = this.getElement('runButton');

			button[dir ? 'on' : 'off']('click', $.proxy(this.onRunClick, this));
		},

		handleStopClick: function(dir) {
			var button = this.getElement('stopButton');

			button[dir ? 'on' : 'off']('click', $.proxy(this.onStopClick, this));
		},

		onRunClick: function() {
			this.switchButtons('run');
			this.showMessage('');
			this.startTimer();
			this.query('run');
		},

		onStopClick: function() {
			this.query('stop');
		},

		queryDelayed: function(action, delay) {
			this.queryDelayedCancel();

			this._queryTimeout = setTimeout(
				$.proxy(this.query, this, action),
				(parseInt(delay, 10) || 1) * 1000
			);
		},

		queryDelayedCancel: function() {
			clearTimeout(this._queryTimeout);
		},

		query: function(action) {
			this.queryDelayedCancel();
			this.queryCancel(true);

			this._query = this.makeQuery(action);

			this._query.then(
				$.proxy(this.queryEnd, this),
				$.proxy(this.queryStop, this)
			);
		},

		queryCancel: function(isSilent) {
			if (this._query !== null) {
				this._query.abort(isSilent ? 'silent' : 'manual');
			}
		},

		queryStop: function(xhr, textStatus) {
			var message;

			this._query = null;

			if (textStatus === 'silent') { return; }

			message = this.buildQueryErrorMessage(xhr, textStatus);

			this.showMessage(message);
			this.resetButtons();
			this.releaseFormData();
			this.releaseState();
			this.stopTimer();
		},

		queryEnd: function(response, textStatus, xhr) {
			var data;

			try {
				data = $.parseJSON(response);

				if (!$.isPlainObject(data)) {
					throw new Error('not valid response');
				}
			} catch (e) {
				this.queryStop(xhr, 'parseerror');
				return;
			}

			this._query = null;

			this.showMessage(data.message);

			switch (data.status) {
				case 'progress':
					this.queryDelayed('run', this.getFormValue('TIME_SLEEP'));
					this.setState(data.state);
				break;

				default:
					this.resetButtons();
					this.releaseFormData();
					this.releaseState();
					this.stopTimer();
				break;
			}
		},

		makeQuery: function(action) {
			var config = {
				url: '',
				type: 'post',
				data: this.getFormData()
			};
			var state = this.getState();
			var stateKey;

			config.data.push({
				name: 'action',
				value: action
			});

			if (state !== null) {
				for (stateKey in state) {
					if (state.hasOwnProperty(stateKey)) {
						config.data.push({
							name: stateKey,
							value: state[stateKey]
						});
					}
				}
			}

			return $.ajax(config);
		},

		showMessage: function(text) {
			var messageElement = this.getElement('message');

			if (text instanceof $) {
				messageElement.empty().append(text);
			} else {
				messageElement.html(text || '');
			}
		},

		switchButtons: function(action) {
			var runButton = this.getElement('runButton');
			var stopButton = this.getElement('stopButton');

			runButton.prop('disabled', (action === 'run'));
			stopButton.prop('disabled', (action === 'stop'));
		},

		resetButtons: function() {
			this.switchButtons('stop');
		},

		getState: function() {
			return this._state;
		},

		setState: function(state) {
			this._state = state;
		},

		releaseState: function() {
			this._state = null;
		},

		getFormValue: function(field) {
			var formData = this.getFormData();
			var i;
			var result;

			for (i = formData.length - 1; i >= 0; i--) {
				if (formData[i].name === field) {
					result = formData[i].value;
					break;
				}
			}

			return result;
		},

		getFormData: function() {
			if (this._formData === null) {
				this._formData = this.$el.serializeArray();
			}

			return this._formData.slice();
		},

		releaseFormData: function() {
			this._formData = null;
		},

		startTimer: function() {
			var startDate = new Date();
			var timerElement = this.getElement('timer');
			var timerHolderElement = this.getElement('timerHolder');

			timerHolderElement.removeClass('is--hidden');
			timerElement.text('00:00');

			clearTimeout(this._timerInterval);

			this._timerInterval = setInterval(function() {
				var nowDate = new Date();
				var diff = (nowDate - startDate) / 1000;
				var seconds = '' + parseInt(diff % 60, 10);
				var minutes = '' + Math.floor(diff / 60);

				if (minutes.length === 1) {
					minutes = '0' + minutes;
				}

				if (seconds.length === 1) {
					seconds = '0' + seconds;
				}

				timerElement.text(minutes + ':' + seconds);
			}, 1000);
		},

		stopTimer: function() {
			clearTimeout(this._timerInterval);
		},

		buildQueryErrorMessage: function(xhr, textStatus) {
			var template = this.getTemplate('error');
			var html = utils.compileTemplate(template, {
				'TITLE': this.getLang('QUERY_ERROR_TITLE')
			});
			var result = $(html);
			var text = this.getLang('QUERY_ERROR_TEXT', {
				'HTTP_STATUS': xhr && xhr.status,
				'TEXT_STATUS': textStatus,
				'RESPONSE': xhr && xhr.responseText
			});

			result.find('textarea').val(text);

			return result;
		}

	}, {
		dataName: 'uiAdminExportForm'
	});

})(BX, jQuery, window);