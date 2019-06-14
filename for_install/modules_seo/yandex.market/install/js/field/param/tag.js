(function(BX, jQuery, window) {

	var Reference = BX.namespace('YandexMarket.Field.Reference');
	var Param = BX.namespace('YandexMarket.Field.Param');
	var Source = BX.namespace('YandexMarket.Source');
	var utils = BX.namespace('YandexMarket.Utils');

	var constructor = Param.Tag = Reference.Complex.extend({

		defaults: {
			inputElement: '.js-param-tag__input',
			childElement: '.js-param-tag__child',

			warningPlaceElement: '.js-param-tag__warning-place',
			warningWrapElement: '.js-param-tag__warning-wrap',
			warningTemplate: '<div class="b-message type--warning"><span class="b-message__icon"></span> #MESSAGE#</div>',

			nodeChangeDelay: 150,

			lang: {},
			langPrefix: 'YANDEX_MARKET_FIELD_PARAM_'
		},

		initialize: function() {
			this.callParent('initialize', constructor);
			this.bind();
			this.refreshWarning();
		},

		destroy: function() {
			this.unbind();
			this.callParent('destroy', constructor);
		},

		bind: function() {
			this.handleNodeChange(true);
		},

		unbind: function() {
			this.handleNodeChange(false);
		},

		handleNodeChange: function(dir) {
			if (!dir || this.isNeedListenNodeChange()) {
				this.$el[dir ? 'on' : 'off']('FieldParamNodeChange', utils.debounce(this.onNodeChange, this.options.nodeChangeDelay, this));
			}
		},

		onNodeChange: function() {
			this.refreshWarning();
		},

		clear: function() {
			this.callParent('clear', constructor);
			this.clearWarning();
		},

		refreshWarning: function() {
			var warningPlace;
			var warning;
			var tagValues;
			var warningMessage;

			if (this.isSupportWarning()) {
				tagValues = this.getTagValues();
				warningMessage = this.getWarningMessage(tagValues);
			}

			this.updateWarning(warningMessage);
		},

		clearWarning: function() {
			this.updateWarning(null);
		},

		updateWarning: function(warningMessage) {
			var warningWrap = this.getElement('warningWrap');
			var warningPlace = this.getElement('warningPlace');
			var contents = '';
			var template;
			var hasWarning = !!warningMessage;

			if (hasWarning) {
				template = this.getTemplate('warning');
				contents = utils.compileTemplate(template, {
					'MESSAGE': warningMessage
				});
			}

			warningWrap.toggleClass('is--empty', !hasWarning);
			warningPlace.html(contents);
		},

		isSupportWarning: function() {
			var supportTags = [ 'param' ];

			return (supportTags.indexOf(this.options.type) !== -1);
		},

		getWarningMessage: function(tagValues) {
			var result;

			switch (this.options.type) {
				case 'param':
					result = this.getWarningMessageForParam(tagValues);
				break;
			}

			return result;
		},

		getWarningMessageForParam: function(tagValues) {
			var nameAttribute = tagValues['attributes']['name'];
			var unitAttribute = tagValues['attributes']['unit'];
			var isSizeTag = false;
			var hasUnit;
			var result;

			if (nameAttribute && nameAttribute.type === 'text') {
				isSizeTag = ((nameAttribute.field || '').toLowerCase().indexOf(this.getLang('PARAM_SIZE_NAME')) !== -1);
			}

			if (isSizeTag) {
				hasUnit = (unitAttribute && unitAttribute.field != null && unitAttribute.field !== '');

				if (!hasUnit) {
					result = this.getLang('PARAM_SIZE_WARNINIG_REQUIRE_UNIT');
				}
			}

			return result;
		},

		getTagValues: function() {
			var nodeCollection = this.getChildField('PARAM_VALUE');
			var nodeValues;
			var nodeValue;
			var i;
			var result = {
				value: null,
				attributes: {}
			};

			if (nodeCollection) {
				nodeValues = nodeCollection.getValue();

				for (i = 0; i < nodeValues.length; i++) {
					nodeValue = nodeValues[i];
					
					switch (nodeValue['XML_TYPE']) {
						case 'value':
							result.value = {
								type: nodeValue['SOURCE_TYPE'],
								field: nodeValue['SOURCE_FIELD']
							};
						break;

						case 'attribute':
							if (nodeValue['XML_ATTRIBUTE_NAME']) {
								result.attributes[nodeValue['XML_ATTRIBUTE_NAME']] = {
									type: nodeValue['SOURCE_TYPE'],
									field: nodeValue['SOURCE_FIELD']
								};
							}
						break;
					}
				}
			}

			return result;
		},

		isNeedListenNodeChange: function() {
			return this.isSupportWarning();
		}

	}, {
		dataName: 'FieldParamTag'
	});

})(BX, jQuery, window);
