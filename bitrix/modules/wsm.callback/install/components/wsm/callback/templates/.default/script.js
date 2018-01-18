(function(window){
	
	if (BX.wsmCallback) return;

	BX.wsmCallback = function (params)
	{
		this.FORM_NAME = params.form;
		this.BLOCK = params.block_id;
		this.WINDOW = params.window_id;
		
		BX.ready(BX.defer(this.Prepare, this));
	}

	BX.wsmCallback.prototype.Prepare = function()
	{
		var i; 
		
		if (this.FORM_NAME && BX.type.isString(this.FORM_NAME))
			this.FORM = document.forms[this.FORM_NAME];
		else if (this.FORM_MARKER && BX.type.isString(this.FORM_MARKER))
			this.FORM = (BX(this.FORM_MARKER)||{form:null}).form;

		if (!BX.type.isDomNode(this.FORM))
			return false;
		
		this.FORM.onsubmit = BX.proxy(this.Send, this);

		//закрытие формы
		var close = BX.findChildren(BX(this.BLOCK), {className: 'close'}, true);
		for (i=0; i<close.length; i++)
		{
			BX.bind(close[i], 'click', BX.proxy(this.Close, this));
		}
		
		//открытие формы
		var openForm = BX.findChildren(BX(this.BLOCK), {className: 'openForm'}, true);
		for (i=0; i<close.length; i++)
		{
			BX.bind(openForm[i], 'click', BX.proxy(this.Open, this));
		}
		
		this.MESS = BX.findChildren(BX(this.BLOCK), {className: 'message'}, true);
		this.button = BX.findChild(BX(this.FORM), { tagName: 'input', attr: {type: 'submit'}}, true, false);
	}
	
	BX.wsmCallback.prototype.Message = function(message, error )
	{
		var i;
		for (i=0; i<this.MESS.length; i++)
		{
			if(message=='')
				BX.hide(this.MESS[i]);
			else
				BX.show(this.MESS[i]);
				
			this.MESS[i].innerHTML = message;
			
			if(error)
			{
				BX.removeClass(this.MESS[i], 'sucess');
				BX.addClass(this.MESS[i], 'error');
			}	
			else
			{
				BX.removeClass(this.MESS[i], 'error');
				BX.addClass(this.MESS[i], 'sucess');
			}			
			
		}
		
	}
	
	BX.wsmCallback.prototype.Open = function()
	{
		
		this.Message('', false);
		
		BX.ajax.getCaptcha(BX.proxy(this._ReloadCatcha, this));
		
		BX.show(this.FORM);
		BX.show(BX(this.WINDOW));
		
		return false;
	}
	
	BX.wsmCallback.prototype.Close = function()
	{
		this.Message('');
		BX.hide(BX(this.WINDOW));
		return false;
	}
	
	BX.wsmCallback.prototype.Send = function()
	{
		if (this.FORM && BX.isNodeInDom(this.FORM))
		{
			var i, j, el, form_data = {};

			for (i=0; i<this.FORM.elements.length; i++)
			{
				el = this.FORM.elements[i];

				if (el.name && el.name != 'sessid')
				{
					var n = el.name, v = '', t = el.type.toLowerCase();

					switch (t)
					{
						case 'button':
						case 'submit':
						case 'reset':
						case 'image':
						case 'file':
						case 'password':
							break;

						case 'radio':
						case 'checkbox':
							if (el.checked)
								v = el.value || 'on';
						break;

						case 'select-multiple':
							n = n.substring(0, n.length-2);
							v = [];
							for (j=0;j<el.options.length;j++)
							{
								if (el.options[j].selected)
								{
									v.push(el.options[j].value);
								}
							}
						break;

						default:
							v = el.value;
					}
					
					//n = _encodeName(n);
					
					if (n.indexOf('[]') > 0)
					{
						if (typeof(form_data[n]) == 'undefined')
							form_data[n] = [v];
						else
							form_data[n].push(v);
					}
					else
						form_data[n] = v;
					
					}
				}

			this.BlockForm(true);

			BX.ajax({
				url:'/bitrix/tools/wsm.callback/callback.php',
				method: 'POST',
				dataType: 'JSON',
				data: form_data,
				onsuccess: BX.proxy(this._Send, this)//,
				//onfailure: BX.proxy(this._Error, this)
				});
			}
			return false;
		}

	BX.wsmCallback.prototype._Send = function(data)
	{
		var i, el;
		//var ddata  = _decodeData(data); 
		var ddata  = data;

		if(data['CAPTCHA_RELOAD'] === true) {
			BX.ajax.getCaptcha(BX.proxy(this._ReloadCatcha, this));
			}

		this.Message(ddata['MESSAGE'], data['ERROR']);

		this.BlockForm(false);
		
		if(data['ERROR'] === false){

			BX.hide(this.FORM);

			for (i=0; i<this.FORM.elements.length; i++)	{
				el = this.FORM.elements[i];

				if (el.name && el.name != 'sessid' && el.type != 'hidden')	{
					el.value = '';
					}
				}
			}
		}

	BX.wsmCallback.prototype._Error = function(data)
	{
		this.Message('Error response', true);
		this.BlockForm(false);
		}

	BX.wsmCallback.prototype._ReloadCatcha = function(data)
	{
		var captcha_word = BX.findChild(BX(this.FORM), { tagName: 'input', attr: {name: 'CALLBACK[captcha_word]'}}, true, false),
			captcha_sid = BX.findChild(BX(this.FORM), { tagName: 'input', attr: {name: 'CALLBACK[captcha_sid]'}}, true, false),
			captcha = BX.findChild(BX(this.FORM), { tagName: 'img', attr: {name: 'captcha'}}, true, false);

		BX(captcha_word).value = '';
		BX(captcha_sid).value = data['captcha_sid']; 
		BX.adjust(BX(captcha), { attrs:{'src': '/bitrix/tools/captcha.php?captcha_sid=' + data['captcha_sid']} });
	}

	BX.wsmCallback.prototype.BlockForm = function(block)
	{
		var i, el, status;

		if(block == true)
			status = 'disabled';
		else
			status = '';

		BX.adjust(BX(this.button), {attrs:{'disabled':status}});

		for (i=0; i<this.FORM.elements.length; i++)
		{
			el = this.FORM.elements[i];
			BX.adjust(BX(el), {attrs:{'disabled':status}});
		}
	}

	function _encodeName(n)
	{
		var q = null;
		while (q = /[^a-zA-Z0-9_\-]/.exec(n))
		{
			n = n.replace(q[0], 'X' + BX.util.str_pad_left(q[0].charCodeAt(0).toString(), 6, '0') + 'X');
		}
		return n;
	}

	function _decodeName(n)
	{
		var q = null;
		while (q = /X[\d]{6}X/.exec(n))
		{
			n = n.replace(q[0], String.fromCharCode(parseInt(q[0].replace(/(^X[0]*)|(X$)/g, ''))))
		}
		return n;
	}

	function _decodeData(data)
	{
		var d = {};
		for (var i in data)
		{
			d[_decodeName(i)] = data[i];
		}
		return d;
	}

})(window);
