(function(window) {

	if (BX.TreeConditions)
		return;

	BX.TreeCondCtrlAtom = function(parentContainer, state, arParams)
	{
		this.boolResult = false;
		if (!parentContainer)
			return this.boolResult;
		if (!state || !state.values)
			return this.boolResult;
		this.parentContainer = parentContainer;
		this.valuesContainer = state.values;
		if (!arParams)
			return this.boolResult;

		if (BX.type.isNotEmptyString(arParams))
		{
			arParams = {
				text: arParams,
				type: 'string'
			};
		}

		if (typeof (arParams) == "object")
		{
			if (!arParams.type)
				return this.boolResult;
		
			this.arStartParams = arParams;

			this.id = null;
			this.name = null;
			this.type = arParams.type;

			if ('string' != arParams.type)
			{
				if (!arParams.id)
					return this.boolResult;
				
				this.id = arParams.id;
				
				this.name = (!arParams.name ? arParams.id : arParams.name);
				
				this.defaultText = (BX.type.isNotEmptyString(arParams.defaultText) ? arParams.defaultText : '...');
				this.defaultValue = (arParams.defaultValue && 0 < arParams.defaultValue.length ? arParams.defaultValue : '');
				if (!this.valuesContainer[this.id] || 0 == this.valuesContainer[this.id].length)
					this.valuesContainer[this.id] = this.defaultValue;
			}
			this.boolResult = true;
			if ('string' == this.type)
			{
				this.Init();
			}
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlAtom.prototype.Init = function()
	{
		if (this.boolResult)
		{
			this.parentContainer = BX(this.parentContainer);
			if (!!this.parentContainer)
			{
				if ('string' == this.type)
				{
					//this.parentContainer.appendChild(document.createTextNode(this.arStartParams.text));
					this.parentContainer.appendChild(BX.create(
						'SPAN',
						{
							props: {
								className: 'control-string'
							},
							html: BX.util.htmlspecialchars(this.arStartParams.text)
						}
					));
				}
				else
				{
					this.CreateLink();
				}
			}
			else
			{
				this.boolResult = false;
			}
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlAtom.prototype.IsValue = function()
	{
		return (this.valuesContainer[this.id] && 0 < this.valuesContainer[this.id].length);
	};
	
	BX.TreeCondCtrlAtom.prototype.InitValue = function()
	{
		return this.IsValue();
	};
	
	BX.TreeCondCtrlAtom.prototype.ReInitValue = function(controls)
	{
		if (BX.util.in_array(this.id, controls))
		{
			this.InitValue();
		}
	};
	
	BX.TreeCondCtrlAtom.prototype.SetValue = function()
	{
		return this.IsValue();
	};
	
	BX.TreeCondCtrlAtom.prototype.View = function(boolShow)
	{
		
	};
	
	BX.TreeCondCtrlAtom.prototype.onChange = function()
	{
		this.SetValue();
		this.View(false);
	};
	
	BX.TreeCondCtrlAtom.prototype.Delete = function()
	{
		if ('string' != this.type)
		{
			if (this.link)
			{
				BX.unbindAll(this.link);
				this.link = BX.remove(this.link);
			}
		}
	};
	
	BX.TreeCondCtrlAtom.prototype.CreateLink = function()
	{
		if (this.boolResult)
		{
			this.link = null;
			this.link = this.parentContainer.appendChild(BX.create(
				'A',
				{
					props: {
						id: this.parentContainer.id+'_'+this.id+'_link',
						className: ''
					},
					style: {
						display: ''
					},
					html: (this.IsValue() ? BX.util.htmlspecialchars(this.valuesContainer[this.id]) : this.defaultText)
				}
			));
			if (!this.link)
				this.boolResult = false;
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlAtom.prototype.prepareData = function(arData, prefix)
	{
		var data = '';
		if (BX.type.isString(arData))
			data = arData;
		else if (null != arData)
		{
			for(var i in arData)
			{
				if (data.length > 0) data += '&';
				var name = BX.util.urlencode(i);
				if(prefix)
					name = prefix + '[' + name + ']';
				if(typeof arData[i] == 'object')
					data += this.prepareData(arData[i], name);
				else
					data += name + '=' + BX.util.urlencode(arData[i]);
			}
		}
		return data;
	};

	BX.TreeCondCtrlInput = function(parentContainer, state, arParams)
	{
		if (BX.TreeCondCtrlInput.superclass.constructor.apply(this, arguments))
		{
			this.Init();
		}
		return this.boolResult;
	};
	BX.extend(BX.TreeCondCtrlInput, BX.TreeCondCtrlAtom);
	
	BX.TreeCondCtrlInput.prototype.Init = function()
	{
		if (this.boolResult)
		{
			if (BX.TreeCondCtrlInput.superclass.Init.apply(this, arguments))
			{
				this.input = null;
				this.input = this.parentContainer.appendChild(BX.create(
					'INPUT',
					{
						props: {
							type: 'text',
							id: this.parentContainer.id+'_'+this.id,
							name: this.name,
							className: '',
							value: (this.IsValue() ? this.valuesContainer[this.id] : '')
						},
						style: {
							display: 'none'
						}
					}
				));
				if (this.input)
				{
					BX.bind(this.input, 'change', BX.delegate(
						this.onChange, this
					));
					BX.bind(this.input, 'blur', BX.delegate(
						this.onChange, this
					));
					BX.bind(this.input, 'keypress', BX.delegate(
						function(e){
							if (!e) e = window.event;
							if (e.keyCode && (e.keyCode == 13 || e.keyCode == 27))
							{
								if (e.keyCode == 13)
								{
									this.onChange();
								}
								else if (e.keyCode == 27)
								{
									this.InitValue();
									this.View(false);
								}
							}
						}, this
					));
				}
				else
				{
					this.boolResult = false;
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlInput.prototype.InitValue = function()
	{
		if (BX.TreeCondCtrlInput.superclass.InitValue.apply(this, arguments))
		{
			BX.adjust(this.link, {html : BX.util.htmlspecialchars(this.valuesContainer[this.id]) });
			this.input.value = this.valuesContainer[this.id];
		}
		else
		{
			BX.adjust(this.link, {html : this.defaultText });
			this.input.value = '';
		}
	};
	
	BX.TreeCondCtrlInput.prototype.SetValue = function()
	{
		this.valuesContainer[this.id] = this.input.value;
		if (BX.TreeCondCtrlInput.superclass.SetValue.apply(this, arguments))
		{
			BX.adjust(this.link, {html : BX.util.htmlspecialchars(this.valuesContainer[this.id]) });
		}
		else
		{
			BX.adjust(this.link, {html : this.defaultText });
		}
	};
	
	BX.TreeCondCtrlInput.prototype.View = function(boolShow)
	{
		boolShow = !!boolShow;
		BX.TreeCondCtrlInput.superclass.View.apply(this, arguments);
		if (boolShow)
		{
			BX.style(this.link, 'display', 'none');
			BX.style(this.input, 'display', '');
			BX.focus(this.input);
		}
		else
		{
			BX.style(this.input, 'display', 'none');
			BX.style(this.link, 'display', '');
			this.input.blur();
		}	
	};
	
	BX.TreeCondCtrlInput.prototype.Delete = function()
	{
		BX.TreeCondCtrlInput.superclass.Delete.apply(this, arguments);
		if (this.input)
		{
			BX.unbindAll(this.input);
			this.input = BX.remove(this.input);
		}
	};
	
	BX.TreeCondCtrlInput.prototype.CreateLink = function()
	{
		if (BX.TreeCondCtrlInput.superclass.CreateLink.apply(this, arguments))
		{
			BX.bind(this.link, 'click', BX.delegate(
				function () {
					this.InitValue();
					this.View(true);
				}, this
			));
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlSelect = function(parentContainer, state, arParams)
	{
		if (BX.TreeCondCtrlSelect.superclass.constructor.apply(this, arguments))
		{
			this.values = new Array();
			this.labels = new Array();
			if (!arParams.values || !(typeof(arParams.values) == 'object'))
				return this.boolResult;
			for (var i in arParams.values)
			{
				this.values[this.values.length] = i;
				this.labels[this.labels.length] = arParams.values[i];
			}
			if (0 == this.values.length)
				return this.boolResult;
			if (0 < this.defaultValue.length)
			{
				i = BX.util.array_search(this.defaultValue, this.values);
				if (-1 < i)
				{
					this.defaultText = this.labels[i];
				}
				else
				{
					this.defaultValue = '';
				}
			}
			
			if (!arParams.multiple)
				arParams.multiple = 'N';
			if ('Y' != arParams.multiple)
				arParams.multiple = 'N';
			this.multiple = ('Y' == arParams.multiple ? true : false);
			this.size = 3;
			if (arParams.size && 0 < parseInt(arParams.size))
			{
				this.size = parseInt(arParams.size);
			}
			
			this.first_option = '...';
			if (!!arParams.first_option)
			{
				this.first_option = arParams.first_option;
			}
			
			this.boolVisual = false;
			this.visual = null;
			if (!!arParams.events && typeof(arParams.events) == 'object')
			{
				if (!!arParams.events.visual && BX.type.isFunction(arParams.events.visual))
				{
					this.boolVisual = true;
					this.visual = arParams.events.visual;
				}
			}
			this.Init();
		}
		return this.boolResult;
	};
	BX.extend(BX.TreeCondCtrlSelect, BX.TreeCondCtrlAtom);

	BX.TreeCondCtrlSelect.prototype.Init = function()
	{
		if (this.boolResult)
		{
			if (BX.TreeCondCtrlSelect.superclass.Init.apply(this, arguments))
			{
				var arProps = {
					id: this.parentContainer.id+'_'+this.id,
					name: this.name,
					className: '',
					selectedIndex: -1
				};
				if (this.multiple)
				{
					name: this.name+'[]',
					arProps.multiple = true;
					arProps.size = this.size;
				}
				this.select = this.parentContainer.appendChild(BX.create(
					'SELECT',
					{
						props: arProps,
						style: {
							display: 'none'
						}
					}
				));
				if (this.select)
				{
					if (!this.multiple)
					{
						this.select.appendChild(BX.create(
								'OPTION',
								{
									props: {
										value: ''
									},
									html: this.first_option
								}
						));
					}
					for (var i in this.values) {
						this.select.appendChild(BX.create(
							'OPTION',
							{
								props: {
									value: this.values[i]
								},
								html: this.labels[i]
							}
						));
					}
					BX.bind(this.select, 'change', BX.delegate(
						this.onChange, this
					));
					BX.bind(this.select, 'blur', BX.delegate(
						function(){
							this.View(false);
						}, this
					));
					BX.bind(this.select, 'keypress', BX.delegate(
						function(e){
							if (!e) e = window.event;
							if (e.keyCode && (e.keyCode == 13 || e.keyCode == 27))
							{
								if (e.keyCode == 13)
								{
									this.View(false);
								}
								else if (e.keyCode == 27)
								{
									this.View(false);
								}
							}
						}, this
					));
					this.InitValue();
					this.boolResult = true;
				}
				else
				{
					this.boolResult = false;
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlSelect.prototype.InitValue = function()
	{
		if (BX.TreeCondCtrlSelect.superclass.InitValue.apply(this, arguments))
		{
			if (BX.type.isString(this.valuesContainer[this.id]))
				this.valuesContainer[this.id] = this.valuesContainer[this.id].split(',');

			var arText = new Array();
			this.select.selectedIndex = -1;

			for (var i = 0; i < this.select.options.length; i++)
			{
				if (BX.util.in_array(this.select.options[i].value, this.valuesContainer[this.id]))
				{
					var j = BX.util.array_search(this.select.options[i].value, this.values);
					if (-1 < j)
					{
						this.select.options[i].selected = true;
						if (-1 == this.select.selectedIndex)
							this.select.selectedIndex = i;
						arText[arText.length] = this.labels[j];
					}
					else
					{
						this.select.options[i].selected = false;
					}
				}
				else
				{
					this.select.options[i].selected = false;
				}
			}
			if (0 == arText.length)
				arText[0] = this.defaultText;

			BX.adjust(this.link, { html: BX.util.htmlspecialchars(arText.join(', ')) });
		}
		else
		{
			this.select.selectedIndex = -1;
		}
	};
	
	BX.TreeCondCtrlSelect.prototype.SetValue = function()
	{
		var arText = new Array();
		var arSelVal = new Array();
		if (this.multiple)
		{
			for (var i = 0; i < this.select.options.length; i++)
			{
				if (this.select.options[i].selected)
				{
					arSelVal[arSelVal.length] = this.select.options[i].value;
					var j = BX.util.array_search(this.select.options[i].value, this.values);
					if (-1 < j)
						arText[arText.length] = this.labels[j];
				}
			}
			if (0 == arText.length)
				arText[0] = this.defaultText;
			this.valuesContainer[this.id] = arSelVal;
		}
		else
		{
			if (-1 < this.select.selectedIndex && this.select.options[this.select.selectedIndex])
			{
				this.valuesContainer[this.id] = [this.select.options[this.select.selectedIndex].value];
				var  i = BX.util.array_search(this.select.options[this.select.selectedIndex].value, this.values);
				if (-1 < i)
					arText[0] = this.labels[i];
				else
					arText[0] = this.defaultText;
			}
		}
		if (BX.TreeCondCtrlSelect.superclass.SetValue.apply(this, arguments))
		{
			BX.adjust(this.link, {html : BX.util.htmlspecialchars(arText.join(', ')) });
		}
		else
		{
			BX.adjust(this.link, {html : this.defaultText });
		}
	};

	BX.TreeCondCtrlSelect.prototype.View = function(boolShow)
	{
		BX.TreeCondCtrlSelect.superclass.View.apply(this, arguments);
		boolShow = !!boolShow;
		if (boolShow)
		{
			BX.style(this.link, 'display', 'none');
			BX.style(this.select, 'display', '');
			BX.focus(this.select);
		}
		else
		{
			BX.style(this.select, 'display', 'none');
			BX.style(this.link, 'display', '');
			this.select.blur();
		}	
	};
	
	BX.TreeCondCtrlSelect.prototype.onChange = function()
	{
		this.SetValue();
		if (!this.multiple)
			this.View(false);
		if (this.boolVisual)
		{
			this.visual();
		}
	};
	
	BX.TreeCondCtrlSelect.prototype.Delete = function()
	{
		BX.TreeCondCtrlSelect.superclass.Delete.apply(this, arguments);
		if (this.select)
		{
			BX.unbindAll(this.select);
			this.select = BX.remove(this.select);
		}
		if (this.boolVisual)
		{
			this.visual = null;
		}
	};
	
	BX.TreeCondCtrlSelect.prototype.CreateLink = function()
	{
		if (BX.TreeCondCtrlSelect.superclass.CreateLink.apply(this, arguments))
		{
			BX.bind(this.link, 'click', BX.delegate(
				function () {
					this.View(true);
				}, this
			));
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlPopup = function(parentContainer, state, arParams)
	{
		if (BX.TreeCondCtrlPopup.superclass.constructor.apply(this, arguments))
		{
			if (!arParams.popup_url)
				return this.boolResult;
			this.popup_url = arParams.popup_url;
			
			this.popup_params = {};
			if (arParams.popup_params)
			{
				for (var i in arParams.popup_params)
					this.popup_params[i] = arParams.popup_params[i];
			}
			
			this.popup_param_id = null;
			if (BX.type.isNotEmptyString(arParams.param_id))
				this.popup_param_id = arParams.param_id;

			this.label = '';
			if (!!state.labels && !!state.labels[this.id])
				this.label = state.labels[this.id];
			if (0 == this.label.length)
			{	
				if (0 < this.valuesContainer[this.id].length)
					this.label = this.valuesContainer[this.id];
				else
					this.label = this.defaultText;
			}
			
			this.Init();
		}
		return this.boolResult;
	};
	BX.extend(BX.TreeCondCtrlPopup, BX.TreeCondCtrlAtom);
	
	BX.TreeCondCtrlPopup.prototype.Init = function()
	{
		if (this.boolResult)
		{
			if (BX.TreeCondCtrlSelect.superclass.Init.apply(this, arguments))
			{
				if (this.popup_param_id)
					this.popup_params[this.popup_param_id] = this.parentContainer.id+'_'+this.id;
				this.input = null;
				this.input = this.parentContainer.appendChild(BX.create(
					'INPUT',
					{
						props: {
							type: 'hidden',
							id: this.parentContainer.id+'_'+this.id,
							name: this.name,
							className: '',
							value: (this.IsValue() ? this.valuesContainer[this.id] : '')
						},
						style: {
							display: 'none'
						}
					}
				));
				if (this.input)
				{
					BX.bind(this.input, 'change', BX.delegate(this.onChange, this));
				}
				else
				{
					this.boolResult = false;
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlPopup.prototype.CreateLink = function()
	{
		if (this.boolResult)
		{
			this.link = null;
			this.link = this.parentContainer.appendChild(BX.create(
				'A',
				{
					props: {
						id: this.parentContainer.id+'_'+this.id+'_link',
						className: ''
					},
					style: {
						display: ''
					},
					html: (this.IsValue() ? BX.util.htmlspecialchars(this.label) : this.defaultText)
				}
			));
			if (this.link)
			{
				BX.bind(this.link, 'click', BX.delegate(this.PopupShow, this));
			}
			else
			{
				this.boolResult = false;
			}
		}
		return this.boolResult;
	};
	
	BX.TreeCondCtrlPopup.prototype.PopupShow = function()
	{
		var url = this.popup_url;
		var data = this.prepareData(this.popup_params);
		if (0 < data.length)
		{
			url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
		}
		window.open(url,'', 'scrollbars=yes,resizable=yes,width=900,height=600,top='+parseInt((screen.height - 500)/2-14)+',left='+parseInt((screen.width - 600)/2-5));
	};
	
	BX.TreeCondCtrlPopup.prototype.onChange = function()
	{
		this.valuesContainer[this.id] = this.input.value;
	};
	
	BX.TreeCondCtrlPopup.prototype.Delete = function()
	{
		BX.TreeCondCtrlPopup.superclass.Delete.apply(this, arguments);
		if (this.input)
		{
			BX.unbindAll(this.input);
			this.input = BX.remove(this.input);
		}
	};
	
/*	BX.TreeCondCtrlInputPopup = function(parentContainer, state, arParams)
	{
		if (BX.TreeCondCtrlInputPopup.superclass.constructor.apply(this, arguments))
		{
			if (!arParams.popup_url)
				return this.boolResult;
			this.popup_url = arParams.popup_url;
			
			this.popup_params = {};
			if (arParams.popup_params)
			{
				for (var i in arParams.popup_params)
					this.popup_params[i] = arParams.popup_params[i];
			}
			
			this.popup_param_id = null;
			if (BX.type.isNotEmptyString(arParams.param_id))
				this.popup_param_id = arParams.param_id;

			this.label = '';
			if (!!state.labels && !!state.labels[this.id])
				this.label = state.labels[this.id];
			if (0 == this.label.length)
			{	
				if (0 < this.valuesContainer[this.id].length)
					this.label = this.valuesContainer[this.id];
				else
					this.label = this.defaultText;
			}
			
			if (!arParams.multiple)
				arParams.multiple = 'N';
			if ('Y' != arParams.multiple)
				arParams.multiple = 'N';
			this.multiple = ('Y' == arParams.multiple ? true : false);

			
		}
	};
	BX.extend(BX.TreeCondCtrlInputPopup, BX.TreeCondCtrlAtom);
	*/
	BX.TreeCondCtrlDateTime = function(parentContainer, state, arParams)
	{
		if (BX.TreeCondCtrlDateTime.superclass.constructor.apply(this, arguments))
		{
			this.Init();
		}
		return this.boolResult;
	};
	BX.extend(BX.TreeCondCtrlDateTime, BX.TreeCondCtrlAtom);
	
	BX.TreeCondCtrlDateTime.prototype.Init = function()
	{
		if (this.boolResult)
		{
			if (BX.TreeCondCtrlDateTime.superclass.Init.apply(this, arguments))
			{
				this.calendar = null;
				this.calendar = this.parentContainer.appendChild(BX.create(
					'DIV',
					{
						props: {
							id: this.parentContainer.id+'_'+this.id+'_calendar',
							className: 'adm-filter-alignment adm-filter-calendar-block'
						},
						style: {
							display: 'none'
						}
					}
				));
				if (!!this.calendar)
				{
					var obDiv1 = this.calendar.appendChild(BX.create(
						'DIV',
						{
							props: {
								className: 'adm-filter-box-sizing'
							}
						}
					));
					if (!!obDiv1)
					{
						var obDiv2 = obDiv1.appendChild(BX.create(
							'DIV',
							{
								props: {
									className: 'adm-input-wrap'
								}
							}
						));
						if (!!obDiv2)
						{
							this.input = null;
							this.input = obDiv2.appendChild(BX.create(
								'INPUT',
								{
									props: {
										type: 'text',
										id: this.parentContainer.id+'_'+this.id,
										name: this.name,
										className: 'adm-input',
										value: (this.IsValue() ? this.valuesContainer[this.id] : '')
									}
								}
							));
							this.icon = obDiv2.appendChild(BX.create(
								'SPAN',
								{
									props: {
										id: this.parentContainer.id+'_'+this.id+'_icon',
										className: 'adm-calendar-icon',
										title: BX.message('JC_CORE_TREE_CONTROL_DATETIME_ICON')
									}
								}
							));
							if (!!this.input && !!this.icon)
							{
								BX.bind(this.input, 'change', BX.delegate(
									this.onChange, this
								));
								BX.bind(this.calendar, 'blur', BX.delegate(
									function(e){
										if (!e) e = window.event;
										var target = e.target || e.srcElement;
										if (target != this.icon)
											this.onChange();
									}, this
								));
								BX.bind(this.input, 'keypress', BX.delegate(
									function(e){
										if (!e) e = window.event;
										if (e.keyCode && (e.keyCode == 13 || e.keyCode == 27))
										{
											if (e.keyCode == 13)
											{
												this.onChange();
											}
											else if (e.keyCode == 27)
											{
												this.InitValue();
												this.View(false);
											}
										}
									}, this
								));
								BX.bind(this.icon, 'click', BX.delegate(
									function(e){
										BX.calendar(
											{
												node: obDiv2,
												field: this.input,
												form: '',
												bTime: true,
												bHideTime: false
											}
										);
									}, this
								));
							}
						}
						else
						{
							this.boolResult = false;
						}
					}
					else
					{
						this.boolResult = false;
					}
				}
				else
				{
					this.boolResult = false;
				}
			}
		}
	};
	
	BX.TreeCondCtrlDateTime.prototype.InitValue = function()
	{
		if (BX.TreeCondCtrlDateTime.superclass.InitValue.apply(this, arguments))
		{
			BX.adjust(this.link, {html : BX.util.htmlspecialchars(this.valuesContainer[this.id]) });
			this.input.value = this.valuesContainer[this.id];
		}
		else
		{
			BX.adjust(this.link, {html : this.defaultText });
			this.input.value = '';
		}
	};
	
	BX.TreeCondCtrlDateTime.prototype.SetValue = function()
	{
		this.valuesContainer[this.id] = this.input.value;
		if (BX.TreeCondCtrlDateTime.superclass.SetValue.apply(this, arguments))
		{
			BX.adjust(this.link, {html : BX.util.htmlspecialchars(this.valuesContainer[this.id]) });
		}
		else
		{
			BX.adjust(this.link, {html : this.defaultText });
		}
	};
	
	BX.TreeCondCtrlDateTime.prototype.View = function(boolShow)
	{
		boolShow = !!boolShow;
		BX.TreeCondCtrlDateTime.superclass.View.apply(this, arguments);
		if (boolShow)
		{
			BX.style(this.link, 'display', 'none');
			BX.style(this.calendar, 'display', 'inline-block');
			BX.focus(this.input);
		}
		else
		{
			BX.style(this.calendar, 'display', 'none');
			BX.style(this.link, 'display', '');
		}	
	};
	
	BX.TreeCondCtrlDateTime.prototype.Delete = function()
	{
		BX.TreeCondCtrlDateTime.superclass.Delete.apply(this, arguments);
		if (this.input)
		{
			BX.unbindAll(this.input);
			this.input = BX.remove(this.input);
		}
		if (this.icon)
		{
			BX.unbindAll(this.icon);
			this.input = BX.remove(this.icon);
		}
		if (this.calendar)
		{
			BX.unbindAll(this.calendar);
			this.calendar = BX.remove(this.calendar);
		}
	};
	
	BX.TreeCondCtrlDateTime.prototype.CreateLink = function()
	{
		if (BX.TreeCondCtrlDateTime.superclass.CreateLink.apply(this, arguments))
		{
			BX.bind(this.link, 'click', BX.delegate(
				function () {
					this.InitValue();
					this.View(true);
				}, this
			));
		}
		return this.boolResult;
	};
	
	BX.TreeConditions = function(arParams, obTree, obControls)
	{
		this.boolResult = false;
		if (!arParams || typeof(arParams) != 'object')
			return this.boolResult;
		if (!arParams.parentContainer)
			return this.boolResult;
		this.parentContainer = arParams.parentContainer;
		if (!arParams.form && !arParams.formName)
			return this.boolResult;
		this.arStartParams = arParams;
		this.form = (!!arParams.form ? arParams.form : null);
		this.formName = (!!arParams.formName ? arParams.formName : null);
		this.mess = null;
		if (!!arParams.mess && typeof(arParams.mess) == 'object')
		{
			this.mess = arParams.mess;
			BX.message(this.mess);
		}
		this.sepID = (!!arParams.sepID ? arParams.sepID : '__');
		this.sepName = (!!arParams.sepName ? arParams.sepName : this.sepID);
		this.prefix = (!!arParams.prefix ? arParams.prefix : 'rule');

		this.AtomTypes = {
			input: BX.TreeCondCtrlInput,
			select: BX.TreeCondCtrlSelect,
			popup: BX.TreeCondCtrlPopup,
			datetime: BX.TreeCondCtrlDateTime
		};
		
		if (!!arParams.atomtypes && typeof(arParams.atomtypes) == 'object')
		{
			for (var i in arParams.atomtypes)
			{
				if (!this.AtomTypes[i])
					this.AtomTypes[i] = arParams.atomtypes[i];
			}
		}
		
		if (!obTree || typeof(obTree) != 'object')
			return this.boolResult;
		this.tree = obTree;

		if (!obControls || !BX.type.isArray(obControls))
			return this.boolResult;
		this.controls = obControls;
		this.boolResult = true;
		BX.ready(BX.delegate(this.RenderTree, this));
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.Delete = function()
	{
		if (this.tree)
			this.DeleteLevel(this.tree);
	};
	
	BX.TreeConditions.prototype.ControlSearch = function(controlId)
	{
		var curControl = false;
		if (this.boolResult)
		{
			if (!!this.controls)
			{	
				for (var i = 0; i < this.controls.length; i++)
				{
					if (!!this.controls[i].controlgroup)
					{
						curControl = this.ControlInGrpSearch(this.controls[i].children, controlId);
						if (false != curControl)
							break;
					}
					else
					{
						if (controlId == this.controls[i].controlId)
						{
							curControl = this.controls[i];
							break;
						}
					}
				}
			}
		}
		return curControl;
	};
	
	BX.TreeConditions.prototype.ControlInGrpSearch = function(controls, controlId)
	{
		var curControl = false;
		if (this.boolResult)
		{
			if (!!controls)
			{
				for (var i = 0; i < controls.length; i++)
				{
					if (controlId == controls[i].controlId)
					{
						curControl = controls[i];
						break;
					}
				}
			}
		}
		return curControl;
	};
	
	BX.TreeConditions.prototype.RenderTree = function()
	{
		if (this.boolResult)
		{
			if (this.form)
			{
				this.form = BX(this.form);
			}
			else
			{
				this.form = document.forms[this.formName];
			}
			if (!this.form)
			{
				this.boolResult = false;
			}
			else
			{
				this.formName = this.form.name;
				this.parentContainer = BX(this.parentContainer);
				if (!!this.parentContainer)
				{
					BX.adjust(this.parentContainer, {style: {position: 'relative', zIndex: 1}});
					this.RenderLevel(this.parentContainer, null, this.tree);
				}
				else
				{
					this.boolResult = false;
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.RenderLevel = function(parentContainer, obParent, obTreeLevel, obParams)
	{
		if (this.boolResult)
		{
			if (!parentContainer)
			{
				this.boolResult = false;
				return this.boolResult;
			}
			if (typeof(obTreeLevel) != 'object')
			{
				this.boolResult = false;
				return this.boolResult;
			}
			if (null == obTreeLevel.id || 'undefined' == obTreeLevel.id)
			{
				this.boolResult = false;
				return this.boolResult;
			}
			if (null == obTreeLevel.controlId || 'undefined' == obTreeLevel.controlId)
			{
				this.boolResult = false;
				return this.boolResult;
			}
			
			var CurControl = this.ControlSearch(obTreeLevel.controlId);
			if (!CurControl)
			{
				this.boolResult = false;
				return this.boolResult;
			}
			
			var strContClassName = (!!CurControl.group ? (null != obParent ? 'condition-container' : 'condition-border') : 'condition-simple-control');
			
			var wrapper = null;
			var zIndex = parseInt(BX.style(parentContainer, 'z-index'));
			if (isNaN(zIndex))
				zIndex = 1;
			var wrapper = BX.create(
				'DIV',
				{
					props: {
						id: parentContainer.id + this.sepID + obTreeLevel.id+'_wrap',
						className: 'condition-wrapper'
					},
					style: {
						zIndex: zIndex+100
					}
				}
			);
			
			var logic = null;
			var div = wrapper.appendChild(BX.create(
				'DIV',
				{
					props: {
						id: parentContainer.id + this.sepID + obTreeLevel.id,
						className: strContClassName
					},
					style: {
						zIndex: zIndex+110
					}
				}
			));
			if (!div)
			{
				this.boolResult = false;
				return this.boolResult;
			}
			if (parentContainer.childNodes.length == 0)
			{
				parentContainer.appendChild(wrapper);
			}
			else
			{
				parentContainer.insertBefore(wrapper, parentContainer.childNodes[parentContainer.childNodes.length - 1]);
			}

			div.appendChild(BX.create(
				'INPUT',
				{
					props: {
						type: 'hidden',
						id: div.id+'_controlId',
						name: (this.prefix + '[' + parentContainer.id + this.sepID + obTreeLevel.id + '][controlId]').replace(this.parentContainer.id+this.sepID,''),
						className: '',
						value: obTreeLevel.controlId
					}
				}
			));
			
			obTreeLevel.wrapper = wrapper;
			obTreeLevel.logic = logic;
			obTreeLevel.container = div;
			obTreeLevel.obj = [];
			obTreeLevel.addBtn = null;
			obTreeLevel.deleteBtn = null;
			obTreeLevel.visual = null;
			
			if (null != obParent)
			{
				if (null == obTreeLevel.showDeleteButton || 'undefined' == obTreeLevel.showDeleteButton || true == obTreeLevel.showDeleteButton)
				{
					this.RenderDeleteBtn(obTreeLevel, obParent);
				}
			}
			
			if (!!obTreeLevel.err_cond && 'Y' == obTreeLevel.err_cond)
			{
				div.appendChild(BX.create(
					'SPAN',
					{
						props: {
							className: 'condition-alert',
							title: (!!obTreeLevel.err_cond_mess ? obTreeLevel.err_cond_mess : (!obTreeLevel.fatal_err_cond ? BX.message('JC_CORE_TREE_CONDITION_ERROR') : BX.message('JC_CORE_TREE_CONDITION_FATAL_ERROR')))
						}
					}
				));
			}
			
			if (!obTreeLevel.fatal_err_cond)
			{
				if (!!CurControl.group)
				{
					if (!!CurControl.visual && typeof (CurControl.visual) == 'object')
					{
						obTreeLevel.visual = CurControl.visual;
						if (!!obTreeLevel.visual.values && BX.type.isArray(obTreeLevel.visual.values)
							&& !!obTreeLevel.visual.logic && BX.type.isArray(obTreeLevel.visual.logic)
							&& obTreeLevel.visual.values.length == obTreeLevel.visual.logic.length
						)
						{
							
						}
						else
						{
							obTreeLevel.visual = null;
						}
					}
				}
				for (var i = 0; i < CurControl.control.length; i++)
				{
					var elem = null;
					if (0 < i)
						div.appendChild(BX.create(
							'SPAN',
							{
								props: {
									className: 'condition-space'
								},
								html: '&nbsp;'
							}
						));
					var item = CurControl.control[i];

					if (typeof(item) == 'object')
					{
						var params = {};
						for (var k in item)
						{
							if ('name' == k)
							{
								params[k] = (this.prefix + '[' + parentContainer.id + this.sepID + obTreeLevel.id + '][' + item[k] + ']').replace(this.parentContainer.id+this.sepID,''); 
							}
							else
							{
								params[k] = item[k];
							}
						}
						
						if (!!obTreeLevel.visual)
						{
							if (BX.util.in_array(item.id, obTreeLevel.visual.controls))
							{
								if (!params.events)
									params.events = {};
								params.events.visual = BX.delegate(function(){ this.ChangeVisual(obTreeLevel); }, this);
							}
						}
					
						if (!!this.AtomTypes[item.type])
						{
							elem = new this.AtomTypes[item.type](div, obTreeLevel, params);
							obTreeLevel.obj[obTreeLevel.obj.length] = elem;
						}
					}
					else
					{
						elem = new BX.TreeCondCtrlAtom(div, obTreeLevel, item);
					}
				}
			
				if (!!CurControl.group)
				{
					div.appendChild(BX.create(
						'DIV',
						{
							props: {
								className: 'condition-group-sep'
							}
						}
					));
					
					this.RenderCreateBtn(obTreeLevel, CurControl);
					if (!!obTreeLevel.children && !!obTreeLevel.children.length && 0 < obTreeLevel.children.length)
					{
						if (!!obTreeLevel.visual && typeof (obTreeLevel.visual) == 'object')
						{
							var intCurrentIndex = this.SearchVisual(obTreeLevel);
							if (-1 < intCurrentIndex)
							{
								var obLogicParams = obTreeLevel.visual.logic[intCurrentIndex];
								obLogicParams.visual = BX.delegate(function(){ this.NextVisual(obTreeLevel); }, this);
								for (var j = 0; j < obTreeLevel.children.length; j++)
								{
									this.RenderLevel(div, obTreeLevel, obTreeLevel.children[j]);
									if (j < (obTreeLevel.children.length - 1))
										this.CreateLogic(obTreeLevel.children[j], obTreeLevel, obLogicParams);
								}
							}
							else
							{
								for (var j = 0; j < obTreeLevel.children.length; j++)
								{
									this.RenderLevel(div, obTreeLevel, obTreeLevel.children[j]);
								}
							}
						}
						else
						{
							for (var j = 0; j < obTreeLevel.children.length; j++)
							{
								this.RenderLevel(div, obTreeLevel, obTreeLevel.children[j]);
							}
						}
					}
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.CreateLevel = function(obTreeLevel, controlId, num)
	{
		if (!!obTreeLevel && !!obTreeLevel.children)
		{
			if ('undefined' == num || null == num)
				num = obTreeLevel.children.length;
			obTreeLevel.children[obTreeLevel.children.length] = {
				id: num,
				controlId: controlId,
				values: {},
				children: []
			};
			if (!this.RenderLevel(obTreeLevel.container, obTreeLevel, obTreeLevel.children[obTreeLevel.children.length-1]))
			{
				obTreeLevel.children.pop();
			}
			else
			{
				var indexPrev = this.SearchForCreateLogic(obTreeLevel);
				if (-1 < indexPrev)
				{
					var intCurrentIndex = this.SearchVisual(obTreeLevel);
					if (-1 < intCurrentIndex)
					{
						var obLogicParams = obTreeLevel.visual.logic[intCurrentIndex];
						obLogicParams.visual = BX.delegate(function(){ this.NextVisual(obTreeLevel); }, this);
						this.CreateLogic(obTreeLevel.children[indexPrev], obTreeLevel, obLogicParams);
					}
				}
				BX.onCustomEvent('onAdminTabsChange');
			}
		}
	};
	
	BX.TreeConditions.prototype.SearchForDeleteLevel = function(obTreeLevel, obParent)
	{
		var arRes = {
			indexDel: -1,
			indexPrev: -1
		};
		
		if (!!obParent)
		{
			if (!!obParent.children)
			{
				for (var j = 0; j < obParent.children.length; j++)
				{
					if (!!obParent.children[j] && obParent.children[j] == obTreeLevel)
					{
						arRes.indexDel = j;
						break;
					}
				}
				if (-1 < arRes.indexDel)
				{
					if (!!obParent.visual && typeof(obParent.visual) == 'object')
					{
						var boolNeedDelVisual = true;
						for (j = arRes.indexDel + 1; j < obParent.children.length; j++)
						{
							if (!!obParent.children[j])
							{
								boolNeedDelVisual = false;
								break;
							}
						}
						if (boolNeedDelVisual)
						{
							for (j = arRes.indexDel - 1; j > -1; j--)
							{
								if (!!obParent.children[j])
								{
									arRes.indexPrev = j;
									break;
								}
							}
						}
					}
				}
			}
		}
		return arRes;
	};
	
	BX.TreeConditions.prototype.SearchForCreateLogic = function(obTreeLevel, indexCurrent)
	{
		var indexPrev = -1;
		if (!!obTreeLevel && !!obTreeLevel.children)
		{
			if (!!obTreeLevel.visual && typeof(obTreeLevel.visual) == 'object')
			{
				if ('undefined' == indexCurrent || null == indexCurrent)
					indexCurrent = obTreeLevel.children.length-1;
				for (var j = indexCurrent-1; j > -1; j--)
				{
					if (!!obTreeLevel.children[j])
					{
						indexPrev = j;
						break;
					}
				}
			}
		}
		return indexPrev;
	};
	
	BX.TreeConditions.prototype.DeleteLevel = function(obTreeLevel, obParent)
	{
		if (!!obTreeLevel)
		{
			if (!!obTreeLevel.children)
			{
				if (0 < obTreeLevel.children.length)
				{
					for (var j = 0; j < obTreeLevel.children.length; j++)
						this.DeleteLevel(obTreeLevel.children[j]);
				}
				obTreeLevel.children.length = 0;
			}
			if (!!obTreeLevel.addBtn)
			{
				if (obTreeLevel.addBtn.link)
				{
					BX.unbindAll(obTreeLevel.addBtn.link);
					obTreeLevel.addBtn.link = BX.remove(obTreeLevel.addBtn.link);
				}
				if (obTreeLevel.addBtn.select)
				{
					BX.unbindAll(obTreeLevel.addBtn.select);
					obTreeLevel.addBtn.link = BX.remove(obTreeLevel.addBtn.select);
				}
				obTreeLevel.addBtn = BX.remove(obTreeLevel.addBtn);
			}
			if (!!obTreeLevel.obj)
			{
				if (0 < obTreeLevel.obj.length)
				{
					for (var j = 0; j < obTreeLevel.obj.length; j++)
						obTreeLevel.obj[j].Delete();
					obTreeLevel.obj.length = 0;
				}
			}
			if (!!obTreeLevel.deleteBtn)
			{
				BX.unbindAll(obTreeLevel.deleteBtn);
				obTreeLevel.deleteBtn = BX.remove(obTreeLevel.deleteBtn);
			}
			
			BX.unbindAll(obTreeLevel.container);
			obTreeLevel.container = BX.remove(obTreeLevel.container);
			if (!!obTreeLevel.logic)
			{
				BX.unbindAll(obTreeLevel.logic);
				obTreeLevel.logic = BX.remove(obTreeLevel.logic);
			}
			BX.unbindAll(obTreeLevel.wrapper);
			obTreeLevel.wrapper = BX.remove(obTreeLevel.wrapper);
			
			var arDel = this.SearchForDeleteLevel(obTreeLevel, obParent);
			if (-1 < arDel.indexDel)
			{
				obParent.children[arDel.indexDel] = null;
				obTreeLevel = null;				
			}
			if (-1 < arDel.indexPrev)
			{
				this.DeleteLogic(obParent.children[arDel.indexPrev]);
			}
			BX.onCustomEvent('onAdminTabsChange');
		}
	};
	
	BX.TreeConditions.prototype.RenderCreateBtn = function(obTreeLevel, CurControl)
	{
		if (this.boolResult)
		{
			if (!!obTreeLevel.container)
			{
				if (CurControl.group)
				{
					var divAdd = obTreeLevel.container.appendChild(BX.create(
						'DIV',
						{
							props: {
								id: obTreeLevel.container.id + '_add',
								className: 'condition-add'
							}
						}
					));
					if (!divAdd)
					{
						this.boolResult = false;
						return this.boolResult;
					}
					obTreeLevel.addBtn = divAdd;
					var addBtn = divAdd.appendChild(BX.create(
						'A',
						{
							props: {
								id: divAdd.id + '_link',
								className: ''
							},
							style: {
								display: ''
							},
							html: BX.message('JC_CORE_TREE_ADD_CONTROL')
						}
					));
					var addSelect = divAdd.appendChild(BX.create(
						'SELECT',
						{
							props: {
								id: divAdd.id + '_select',
								className: ''
							},
							style: {
								display: 'none'
							}
						}
					));
					if (!!addSelect)
					{
						addSelect.appendChild(BX.create(
							'OPTION',
							{
								props: {
									value: ''
								},
								html: BX.message('JC_CORE_TREE_SELECT_CONTROL')
							}
						));
				
						for (var i in this.controls)
						{
							if (BX.util.in_array(CurControl.controlId, this.controls[i].showIn))
							{
								if (!!this.controls[i].controlgroup)
								{
									var grp = BX.create(
										'OPTGROUP',
										{
											props: {
												label: this.controls[i].label
											}
										}
									);
									if (!!grp && !!this.controls[i].children && !!this.controls[i].children.length && 0 < this.controls[i].children.length)
									{
										for (var j in this.controls[i].children)
										{
											grp.appendChild(BX.create(
												'OPTION',
												{
													props: {
														value: this.controls[i].children[j].controlId
													},
													html: this.controls[i].children[j].label
												}
											));
										}
										addSelect.appendChild(grp);
									}
								}
								else
								{
									addSelect.appendChild(BX.create(
										'OPTION',
										{
											props: {
												value: this.controls[i].controlId
											},
											html: this.controls[i].label
										}
									));
								}
							}
						}
						
					}
					if (!!addBtn && !!addSelect)
					{
						divAdd.link = addBtn;
						divAdd.select = addSelect;
						BX.bind(addBtn,'click', BX.delegate(
							function(){
								BX.style(divAdd.select, 'display', '');
								BX.style(divAdd.link, 'display', 'none');
								BX.focus(divAdd.select);
							}, divAdd
						));
						BX.bind(addSelect, 'change', BX.delegate(
							function(){
								if (0 < divAdd.select.selectedIndex)
								{
									this.CreateLevel(obTreeLevel, divAdd.select.options[divAdd.select.selectedIndex].value);
								}
								divAdd.select.selectedIndex = 0;
								BX.style(divAdd.select, 'display', 'none');
								BX.style(divAdd.link, 'display', '');
							}, this
						));
						BX.bind(addSelect, 'blur', BX.delegate(
							function(){
								divAdd.select.selectedIndex = 0;
								BX.style(divAdd.select, 'display', 'none');
								BX.style(divAdd.link, 'display', '');
							}, divAdd
						));
						BX.bind(addSelect, 'keypress', BX.delegate(
							function(e){
								if (!e) e = window.event;
								if (e.keyCode && (e.keyCode == 13 || e.keyCode == 27))
								{
									if (e.keyCode == 13)
									{
										if (0 < divAdd.select.selectedIndex)
										{
											this.CreateLevel(obTreeLevel);
										}
										divAdd.select.selectedIndex = 0;
									}
									else if (e.keyCode == 27)
									{
										divAdd.select.selectedIndex = 0;
									}
									BX.style(divAdd.select, 'display', 'none');
									BX.style(divAdd.link, 'display', '');
								}
							}, this
						));


					}
					else
					{
						this.boolResult = false;
					}
				}
			}
			else
			{
				this.boolResult = false;
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.RenderDeleteBtn = function(obTreeLevel, obParent)
	{
		if (this.boolResult)
		{
			if (!!obTreeLevel.container)
			{
				var delBtn = obTreeLevel.container.appendChild(BX.create(
					'DIV',
					{
						props: {
							id: obTreeLevel.id + '_del',
							className: 'condition-delete',
							title: BX.message('JC_CORE_TREE_DELETE_CONTROL')
						}
					}
				));
				if (!!delBtn)
				{
					obTreeLevel.delBtn = delBtn;
					BX.bind(delBtn, 'click', BX.delegate(
						function(){
							this.DeleteLevel(obTreeLevel, obParent);
						},
						this
					));
					BX.bind(obTreeLevel.container, 'mouseover', BX.delegate(
						function(e){
							BX.style(delBtn, 'display', 'block');
							return BX.eventCancelBubble(e);
						},
						this
					));
					BX.bind(obTreeLevel.container, 'mouseout', BX.delegate(
						function(e){
							BX.style(delBtn, 'display', 'none');
							return BX.eventCancelBubble(e);
						},
						this
					));
				}
				else
				{
					this.boolResult = false;
				}
			}
			else
			{
				this.boolResult = false;
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.CreateLogic = function(obTreeLevel, obParent, obParams)
	{
		if (this.boolResult)
		{
			if (!!obTreeLevel.logic && typeof (obTreeLevel.logic) == 'object')
			{
				this.boolResult = this.UpdateLogic(obTreeLevel, obParams);
			}
			else
			{
				var logic = null;
				var strClass = 'condition-logic';
				if (!!obParams.style)
				{
					strClass = strClass.concat(' ', obParams.style);
				}
				logic = BX.create(
					'DIV',
					{
						props: {
							className: strClass
						},
						style: {
							zIndex: parseInt(BX.style(obTreeLevel.wrapper, 'z-index'))+1
						},
						html: obParams.message
					}
				);
				if (!!logic)
				{
					obTreeLevel.wrapper.insertBefore(logic,obTreeLevel.wrapper.childNodes[0]);
					obTreeLevel.logic = logic;
					BX.bind(obTreeLevel.logic, 'click', obParams.visual);
				}
				else
				{
					this.boolResult = false;
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.DeleteLogic = function(obTreeLevel)
	{
		if (this.boolResult)
		{
			if (!!obTreeLevel.logic && typeof (obTreeLevel.logic) == 'object')
			{
				BX.unbindAll(obTreeLevel.logic);
				obTreeLevel.logic = BX.remove(obTreeLevel.logic);
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.UpdateLogic = function(obTreeLevel, obParams)
	{
		if (this.boolResult)
		{
			if (!!obTreeLevel.logic && typeof (obTreeLevel.logic) == 'object')
			{
				var strClass = 'condition-logic';
				if (!!obParams.style)
				{
					strClass = strClass.concat(' ', obParams.style);
				}
				BX.adjust(obTreeLevel.logic, {props: {className: strClass}, html : obParams.message });
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.SearchVisual = function(obTreeLevel)
	{
		var intCurrentIndex = -1;
		if (this.boolResult)
		{
			if (!!obTreeLevel.visual && typeof (obTreeLevel.visual) == 'object')
			{
				if (!!obTreeLevel.visual.controls)
				{
					var arCurrent = {};
					for (var i in obTreeLevel.visual.controls)
					{
						var strName = obTreeLevel.visual.controls[i];
						arCurrent[strName] = obTreeLevel.values[strName];
					}
					if (!!obTreeLevel.visual.values)
					{
						for (var j = 0; j < obTreeLevel.visual.values.length; j++)
						{
							var oneRow = obTreeLevel.visual.values[j];
							var boolEqual = true;
							for (var k in arCurrent)
							{
								if (oneRow[k] != arCurrent[k])
								{
									boolEqual = false;
									break;
								}
							}
							if (boolEqual)
							{
								intCurrentIndex = j;
								break;
							}
						}
					}
				}
			}
		}
		return intCurrentIndex;
	};
	
	BX.TreeConditions.prototype.ChangeVisual = function(obTreeLevel)
	{
		if (this.boolResult)
		{
			var intCurrentIndex = this.SearchVisual(obTreeLevel);
			if (-1 < intCurrentIndex)
			{
				var obParams = obTreeLevel.visual.logic[intCurrentIndex];
				for (var j = 0; j < obTreeLevel.children.length; j++)
				{
					if (!!obTreeLevel.children[j])
						this.UpdateLogic(obTreeLevel.children[j], obParams);
				}
			}
		}
		return this.boolResult;
	};
	
	BX.TreeConditions.prototype.NextVisual = function(obTreeLevel)
	{
		if (this.boolResult)
		{
			var intCurrentIndex = this.SearchVisual(obTreeLevel);
			if (-1 < intCurrentIndex)
			{
				intCurrentIndex++;
				if (intCurrentIndex >= obTreeLevel.visual.logic.length)
					intCurrentIndex = 0;
				
				var arValues = obTreeLevel.visual.values[intCurrentIndex];
				for (var j in arValues)
				{
					obTreeLevel.values[j] = arValues[j];
				}
				for (var i = 0; i < obTreeLevel.obj.length; i++)
				{
					obTreeLevel.obj[i].ReInitValue(obTreeLevel.visual.controls);
				}

				var obParams = obTreeLevel.visual.logic[intCurrentIndex];
				for (var j = 0; j < obTreeLevel.children.length; j++)
				{
					if (!!obTreeLevel.children[j])
						this.UpdateLogic(obTreeLevel.children[j], obParams);
				}
			}
		}
	};
})(window);