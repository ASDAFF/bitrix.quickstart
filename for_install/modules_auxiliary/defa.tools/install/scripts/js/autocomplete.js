BX.ext({
        dtautocomplete: function(input, params)
        {
                this.input = BX(input);
                if(!input)
                        return false;

                if (!params || !params.url || !BX.type.isString(params.url))
                {
                        return false;
                }

                var defaultParams = {
                        'delay' : 400,
                        'minLength': 1,
                        'minWidth': 400,
                        'params': {},
                        'onSelect': '',
                        'beforeAjax': '',
                        'afterAjax': ''
                };

                for (var i in defaultParams)
                {
                        if (typeof (params[i]) == "undefined")
                                params[i] = defaultParams[i];
                }
                this.keyPressEventName = BX.browser.IsOpera() ? 'keypress' : 'keydown';
                this.params = params;
                this.timer = false;
        	this.sExp = new RegExp("[\040]*[\000-\037\041-\054\057\072-\077\133-\136\140\173-\177\230\236\246-\377\240]+[\040]*", "i");
        	this.oLast = {"str":false, "arr":false};
        	this.oThis = {"str":false, "arr":false};
        	this.oEl = {"start":false, "end":false};
        	this.oDiv = null;
        	this.oUnfinedWords = {};
        	// Flags
        	this.bReady = true, this.eFocus = true;
        	// Pointers
        	this.oActive = null, this.oPointer = Array(), this.oPointer_default = Array(), this.oPointer_this = 'input_field';

                this.__onkeypress = function(e)
                {
                        if(this.timer) clearTimeout(this.timer);
                        this.timer = setTimeout(BX.proxy(this.__onChange, this), this.params.delay);
                }

                this.getCursorPosition = function(oObj)
        	{
        		var result = {'start': 0, 'end': 0};
        		if (!oObj || (typeof oObj != 'object'))
        			return result;
        		try
        		{
        			if (document.selection != null && oObj.selectionStart == null)
        			{
        				oObj.focus();
        				var
        					oRange = document.selection.createRange(),
        					oParent = oRange.parentElement(),
        					sBookmark = oRange.getBookmark(),
        					sContents = sContents_ = oObj.value,
        					sMarker = '__' + Math.random() + '__';

        				while(sContents.indexOf(sMarker) != -1)
        				{
        					sMarker = '__' + Math.random() + '__';
        				}

        				if (!oParent || oParent == null || (oParent.type != "textarea" && oParent.type != "text"))
        				{
        					return result;
        				}

        				oRange.text = sMarker + oRange.text + sMarker;
        				sContents = oObj.value;
        				result['start'] = sContents.indexOf(sMarker);
        				sContents = sContents.replace(sMarker, "");
        				result['end'] = sContents.indexOf(sMarker);
        				oObj.value = sContents_;
        				oRange.moveToBookmark(sBookmark);
        				oRange.select();
        				return result;
        			}
        			else
        			{
        				return {
        				 	'start': oObj.selectionStart,
        					'end': oObj.selectionEnd
        				};
        			}
        		}
        		catch(e){}
        		return result;
        	}

                this.__onChange = function(e)
                {
                        if(!this.bReady) return;

        		var
        			sThis = false, tmp = 0,
        			bUnfined = false, word = "",
        			cursor = {};

        		if (this.input.value.length >= this.params.minLength)
        		{
        			// Preparing input data
        			this.oThis["arr"] = this.input.value.split(this.sExp);
        			this.oThis["str"] = this.oThis["arr"].join(":");

        			// Getting modificated element
        			if (this.oThis["str"] && (this.oThis["str"] != this.oLast["str"]))
        			{
        				cursor['position'] = this.getCursorPosition(this.input);
        				if (cursor['position']['end'] > 0 && !this.sExp.test(this.input.value.substr(cursor['position']['end']-1, 1)))
        				{
        					cursor['arr'] = this.input.value.substr(0, cursor['position']['end']).split(this.sExp);
        					sThis = this.oThis["arr"][cursor['arr'].length - 1];
        					this.oEl['start'] = cursor['position']['end'] - cursor['arr'][cursor['arr'].length - 1].length;
        					this.oEl['end'] = this.oEl['start'] + sThis.length;
        					this.oEl['content'] = sThis;

        					this.oLast["arr"] = this.oThis["arr"];
        					//this.oLast["str"] = this.oThis["str"];
        				}
        			}
        			if (sThis)
        			{
        				// Checking for UnfinedWords
        				for (tmp = 2; tmp <= sThis.length; tmp++)
        				{
        					word = sThis.substr(0, tmp);
        					if (this.oUnfinedWords[word] == '!fined')
        					{
        						bUnfined = true;
        						break;
        					}
        				}
        				if (!bUnfined)
        					this.Send(sThis);
        			}
			}
			//setTimeout(BX.proxy(this.__onChange, this), this.params.delay);
                }

                this.Send = function(sSearch)
                {
                        this.bReady = false;
                        
			if(typeof this.params.beforeAjax == "function")
                                this.params.params = this.params.beforeAjax(this.params.params);

                        BX.ajax.post(params.url, {"search": sSearch, "params": this.params.params}, BX.proxy(this.__handler, this));
                }

                this.__handler = function(data)
                {
                        this.bReady = true;
			var result = {};

			try
			{
				eval("result = " + data + ";");
				
				if(typeof this.params.afterAjax == "function")
                                        result = this.params.afterAjax(result);
                                        
				this.Show(result);
			}
			catch(e){alert(e)}
                }
                this.p = function(data)
                {
                        var div = BX('dbg');
                        if(div) div.innerHTML = data;
                }
                this.Show = function(data)
                {
                        this.Destroy();
                        if(!data.length) return;
                        
        		this.oDiv = document.body.appendChild(BX.create("DIV", {
                                                props: {
                                                        id: this.input.id+'_div',
                                                        className: 'search-popup'
                                                },
                                                style: {
                                                        position: 'absolute'
                                                }
                                        }));
        		this.aDiv = this.Print(data);

        		var pos = BX.pos(this.input);
        		if(pos["left"] <= 0) return;
        		if (pos["width"] < this.params.minWidth)
                                pos["width"] = this.params.minWidth;

                        this.oDiv.style.width = parseInt(pos["width"]) + "px";
                        this.ShowResult(this.oDiv, pos["left"], pos["bottom"]);

        		BX.bind(document, "click", BX.proxy(this.CheckMouse, this));
        		BX.bind(document, this.keyPressEventName, BX.proxy(this.CheckKeyword, this));
                }

                this.hide = function(oDiv)
                {
        		if(!oDiv) return;

        		var oFrame = BX(oDiv.id+"_frame");
        		if(oFrame)
        		{
        			oFrame.style.visibility = 'hidden';
        			oFrame.style.display = 'none';
        		}
        		oDiv.style.display = 'none';
                }

        	this.Replace = function()
        	{
        		if (typeof this.oActive == 'string')
        		{
        			var tmp = this.aDiv[this.oActive];

        			if(typeof this.params.onSelect == "function")
        			{
                                        this.input.value = this.params.onSelect(tmp, this.input);
                                }
                                else
                                {
        			        var tmp1 = '';
                			if (typeof tmp == 'object')
                			{
                				tmp1 = tmp['NAME'].replace(/\&lt;/g, "<").replace(/\&gt;/g, ">").replace(/\&quot;/g, "\"");
                			}
                			tmp = this.input.value.substring(0, this.oEl['start']) + tmp1;
                			this.input.value = this.input.value.substring(0, this.oEl['start']) + tmp1 + this.input.value.substr(this.oEl['end']);
                                }
        		}
        		return;
        	}

        	this.Clear = function()
        	{
        		var oEl = {}, ii = '';
        		oEl = this.oDiv.getElementsByTagName("div");
        		if (oEl.length > 0 && typeof oEl == 'object')
        		{
        			for (ii in oEl)
        			{
        				var oE = oEl[ii];
        				if (oE && (typeof oE == 'object') && (oE.name == this.oDiv.id + '_div'))
        				{
        					oE.className = "search-popup-row";
        				}
        			}
        		}
        	}

                this.Init = function()
                {
        		this.oActive = false;
        		this.oPointer = this.oPointer_default;
        		this.Clear();
        		this.oPointer_this = 'input_pointer';
                }

        	this.Destroy = function()
        	{
        		try
        		{
        			this.hide(this.oDiv);
        			BX.remove(this.oDiv);
        		}
        		catch(e){}

        		this.aDiv = Array();
        		this.oPointer = Array(), this.oPointer_default = Array(), this.oPointer_this = 'input_field';
        		this.bReady = true, this.eFocus = true, oError = {},
        		this.oActive = null;

        		BX.unbind("click", BX.proxy(this.CheckMouse, this));
        		BX.unbind(this.keyPressEventName, BX.proxy(this.CheckKeyword, this));
        	}
        	this.CheckMouse = function()
        	{
        		this.Replace();
        		this.Destroy();
        	}
        	this.CheckKeyword = function(e)
        	{
        		if (!e)
        			e = window.event;
        		var
        			oP = null,
        			oEl = null,
        			ii = null;
        		if ((37 < e.keyCode && e.keyCode <41) || (e.keyCode == 13))
        		{
        			this.Clear();

        			switch (e.keyCode)
        			{
        				case 38:
        					oP = this.oPointer.pop();
        					if (this.oPointer_this == oP)
        					{
        						this.oPointer.unshift(oP);
        						oP = this.oPointer.pop();
        					}
        					if (oP != 'input_field')
        					{
        						this.oActive = oP;
        						oEl = BX(oP);
        						if (oEl) oEl.className = "search-popup-row-active";
        					}
        					this.oPointer.unshift(oP);

        					break;
        				case 40:
        					oP = this.oPointer.shift();
        					if (this.oPointer_this == oP)
        					{
        						this.oPointer.push(oP);
        						oP = this.oPointer.shift();
        					}
        					if (oP != 'input_field')
        					{
        						this.oActive = oP;
        						oEl = BX(oP);
        						if (oEl) oEl.className = "search-popup-row-active";
        					}
        					this.oPointer.push(oP);

        					break;
        				case 39:
        					this.Replace();
        					this.Destroy();

        					break;
        				case 13:
        					this.Replace();
        					this.Destroy();

                                                BX.PreventDefault(e);

        					break;
        			}
        			this.oPointer_this = oP;
        		}
        		else
        		{
        			this.Destroy();
        		}
        	}

                this.Print = function(aArr)
                {
        		var
        			aEl = null, sPrefix = '', sColumn = '',
        			aResult = Array(), aRes = Array(),
        			iCnt = 0, tmp = 0, tmp_ = 0, bFirst = true,
        			oDiv = null, oSpan = null,
                                _this = this;

        		sPrefix = this.oDiv.id;

        		for (tmp_ in aArr)
        		{
        			// Math
        			aEl = aArr[tmp_];
        			aRes = aEl;
        			aRes['ID'] = (aEl['ID'] && aEl['ID'].length > 0) ? aEl['ID'] : iCnt++;
        			aRes['GID'] = sPrefix + '_' + aRes['ID'];
        			aResult[aRes['GID']] = aRes;
        			this.oPointer.push(aRes['GID']);
        			// Graph
        			oDiv = this.oDiv.appendChild(BX.create("DIV"));
        			oDiv.id = aRes['GID'];
        			oDiv.name = sPrefix + '_div';

        			oDiv.className = 'search-popup-row';

        			oDiv.onmouseover = function(){_this.Init(); this.className='search-popup-row-active';};
        			oDiv.onmouseout = function(){_this.Init(); this.className='search-popup-row';};
        			oDiv.onclick = function(){_this.oActive = this.id};

        			oSpan = oDiv.appendChild(BX.create("DIV"));
        			oSpan.id = oDiv.id + '_NAME';
        			oSpan.className = "search-popup-el search-popup-el-name";
        			oSpan.innerHTML = aRes['NAME'];
        		}
        		this.oPointer.push('input_field');
        		this.oPointer_default = this.oPointer;
        		return aResult;
                }

        	this.ShowResult = function(oDiv, iLeft, iTop)
        	{
        		if (typeof oDiv != 'object')
        			return;
                        else if(oDiv.innerHTML == "")
                                return;

        		var zIndex = parseInt(oDiv.style.zIndex);
        		if(zIndex <= 0 || isNaN(zIndex))
        			zIndex = 100;
        		oDiv.style.zIndex = zIndex;
        		oDiv.style.left = iLeft + "px";
        		oDiv.style.top = iTop + "px";
        		if(BX.browser.IsIE())
        		{
        			var oFrame = BX(oDiv.id+"_frame");
        			if(!oFrame)
        			{
                                        oFrame = document.body.appendChild(BX.create('IFRAME', {
                                                props: {
                                                        name: 'formTarget',
                                                        id: oDiv.id+"_frame",
                                                        src: 'javascript:void(0)'
                                                },
                                                style: {
                                                        position: 'absolute',
                                                        zIndex: zIndex-1
                                                }
                                        }));
        			}
        			BX.adjust(oFrame, {
                                                style: {
                                                        position: 'absolute',
                                                        zIndex: zIndex-1,
                                                        width: oDiv.offsetWidth + "px",
                                                        height: oDiv.offsetHeight + "px",
                                                        left: oDiv.style.left,
                                                        top: oDiv.style.top,
                                                        visibility: 'visible',
                                                        display: 'inline'
                                                }
                                });
        		}
        		return oDiv;
        	}

                BX.adjust(this.input, {'attrs': {'dtautocomplete': 'off'}});
                BX.bind(this.input, 'keyup', BX.proxy(this.__onkeypress, this));
                //BX.bind(this.input, 'focus', BX.proxy(this.__onChange, this));

        }
});