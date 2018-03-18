function isRus(text)
{
	for (var d6 = 0; d6 < text.length; d6++)
	{
		
	  if ( text.charCodeAt(d6) > 126 || text.charAt(d6) == '[' || text.charAt(d6) == "]" || text.charAt(d6) == "\\" || text.charAt(d6) == "^" || text.charAt(d6) == "_" || text.charAt(d6) == "`" || text.charAt(d6) == "{" || text.charAt(d6) == "}" || text.charAt(d6) == "|" || text.charAt(d6) == "~")
	  {
		return true;
	  }
    }
	return false;
}
function Counters(texta, spanlength, spanpartsize, spanparts, needsms, numbersTextarea, span)
{
	var ttrans = document.getElementById('trans');
    texta = document.getElementById(texta);
    
    if (texta.value.match(/\r/g) == null)
	{
		//считаем количество символов
		var newLinesymbols = texta.value.match(/\n/g);
		
		if (newLinesymbols != null)
		{
			$newLinesymbolsCount = newLinesymbols.length;	
		}
		else
		{
			$newLinesymbolsCount = 0;
		}					
	}
	
	var text = texta.value;
	var textLength = text.length + $newLinesymbolsCount;
		
	if (isRus(text))
		messLenPart = (textLength + $newLinesymbolsCount) > 70 ? 66 : 70;
	else
		messLenPart = (textLength + $newLinesymbolsCount) > 160 ? 153 : 160;
	
	document.getElementById(spanpartsize).innerHTML = messLenPart;	
	
	
	textlen = textLength;
	document.getElementById(spanlength).innerHTML = textlen;  
	var parts = Math.ceil(textlen / messLenPart); 
   	document.getElementById(spanparts).innerHTML = parts;  
 	numbers = getTelNumber(numbersTextarea, span);
 	document.getElementById(needsms).innerHTML = numbers * parts; 
}
function disable_submit_button()
{
	form2.sub.disabled=true;
	return true;
}

function center()
{
	var obWndSize = jsUtils.GetWindowSize(); 
	var div = document.getElementById("wait_window_div");
	div.style.right = (5 - obWndSize.scrollLeft) + 'px';
	div.style.top = obWndSize.scrollTop + 5 + "px";
}

function activeNightTimeNsEvent(checkboxID, firstEditID, secondEditID)
{
    var objCheckbox = document.getElementById(checkboxID);
	var objFirstEdit =  document.getElementById(firstEditID);
	
	if (objCheckbox.checked == true)
	{	
		objFirstEdit.disabled = false;
	}
	else
	{
		objFirstEdit.disabled = true;	
	}	
	
	if (secondEditID != '')
	{
		var objSecondEdit =  document.getElementById(secondEditID);
	
		if (objCheckbox.checked == true)
		{
			objSecondEdit.disabled = false;
		}
		else
		{
			objSecondEdit.disabled = true;	
		}
	}
}

function getTelNumber(textAreaID, spanNumbers)
{
	var textareaObject = document.getElementById(textAreaID);
	var telNumberStr = textareaObject.value;
	telNumberStr = telNumberStr.replace(/<.*>/g, '');
	telNumberStr = telNumberStr.replace(/\n/g, ';');
	telNumberStr = telNumberStr.replace(/,/g, ';');
		
	var array = telNumberStr.split(';');
	var arrayWithoutEmpty = new Array();
	
	//убираем пустую строку
	for (var ind in array)
	{
		if (array[ind] != "")
		{
			arrayWithoutEmpty.push(array[ind]);	
		}
	}
	
	var result_array = arrayWithoutEmpty;
	var number = result_array.length;
	var smsCountTextObject = document.getElementById(spanNumbers);
	smsCountTextObject.innerHTML = number;
	
	return number;	
}
function CAjaxSms(pathForRequests, mainDomNodeId, sessid, mess)
{
	console.log(mess);
	this.pathForRequests = pathForRequests;
	this.mainDomNodeId = mainDomNodeId;
	this.sessid = sessid;
	this.loadButton = document.getElementById('loadNumbers');
	this.clearListOfficcerNumbers = document.getElementById('clearListOfficcerNumbers');
	this.clearButton = document.getElementById('clearListNumbers');
	this.destinationOfficer = document.getElementById('dest'); 
	this.destination = document.getElementById('destGroups');
	//ну и запускаем функцию инициализации
	this.Init();
}

CAjaxSms.prototype.Init = function()
{
	_this = this;
	this.loadButton.onclick = function() 
	{
		_this.LoadPhones();	
	};
	this.clearButton.onclick = function()
	{
		_this.destination.value = '';
		getTelNumber('destGroups', 'smsNumberGroups');	
	};
	this.clearListOfficcerNumbers.onclick = function()
	{
		_this.destinationOfficer.value = '';
		getTelNumber('dest', 'smsNumber');	
	}	
}

CAjaxSms.prototype.LoadPhones = function()
{   
	var divContainer = document.getElementById(this.mainDomNodeId);
	
	var checkboxes = divContainer.getElementsByTagName('INPUT');

	var _this = this;
	
	var array = new Array();

	for (i = 0; i < checkboxes.length; i++)
	{
		if (checkboxes[i].checked)
		{
			array.push(checkboxes[i].value);
		}
	}
	
	if (!checkboxes) 
	{
		alert('¬ы не выбрали ни одной группы <-');
		return;
	}
	
	function onSuccess(data)
	{
		if (!data.errcode) 
		{
			onLoaded(data);
			showLoading(false);
		} 
		else 
		{
			showLoading(false);
			onLoadError(data);
		}	
	}
	
	function onLoaded(data)
	{
		var str = '';
		for(i = 0; i < data.length; i++)
		{
			str += data[i].phone + '<'+data[i].secondName+' '+data[i].name+', '+data[i].department+'>' + '\n';
			
		}
	    _this.destination.value = str;
		//подсчет
		getTelNumber('destGroups', 'smsNumberGroups');
		Counters('message2','group-text-length', 'group-part-size', 'group-parts', 'group-need-sms', 'destGroups', 'smsNumberGroups')
	}
	
	function onLoadError(error) 
	{
		var msg = "ќшибка "+error.errcode;
		if (error.message) msg = msg + ' :'+error.message;
		alert(msg);
	}

	function showLoading(on) 
	{
		var expand = document.getElementById('loader');
		expand.className = on ? 'Loading' : '';				
	}
	
	function onAjaxError(xhr, status)
	{
		showLoading(false);
		var errinfo = { errcode: status };
		if (xhr.status != 200) 
		{
			// может быть статус 200, а ошибка
			// из-за некорректного JSON
			errinfo.message = xhr.statusText;
		} 
		else 
		{
			errinfo.message = 'Ќекорректные данные с сервера';
		}
		onLoadError(errinfo);
	}

	showLoading(true);
	
	$.ajax({
			url: this.pathForRequests,
			data: { ajaxRequest: 'Y', sessid: _this.sessid, action: 'getNumbersBySections', 'selectedSections[]': array },
			dataType: "json",
			type: "POST",
			success: onSuccess,
			error: onAjaxError,
			cache: false
	});
}

CAjaxSms.prototype.trans = function(text)
{
	var ntext = '';
	var ch = '';
	for (var d6 = 0; d6 < text.length; d6++)
	{
		ch = '';
		for(val in a)
		{
			if (text.substr(d6,1) == 'ь' || text.substr(d6,1) == '№')
				ch = "'";
			if (text.substr(d6,1) == 'ъ' || text.substr(d6,1) == 'Џ')
				ch = "\"";
			if (text.substr(d6,1) == '['  || text.substr(d6,1) == '{')
				ch = "(";
			if (text.substr(d6,1) == ']'  || text.substr(d6,1) == '}')
				ch = ")";
			if (text.substr(d6,1) == '\\')
				ch = "/";
			if (text.substr(d6,1) == '^')
				ch = "'";
			if (text.substr(d6,1) == '_')
				ch = "-";
			if (text.substr(d6,1) == '`')
				ch = "'";
			if (text.substr(d6,1) == '|')
				ch = "i";
			if (text.substr(d6,1) == '~')
				ch = "-";
			if (text.substr(d6,1) == 'є')
				ch = "N";
			if (text.substr(d6,1) == 'Ф')
				ch = "\"";	
			
		  	if (text.substr(d6,1) == a[val])
				ch = val;
		}
		
		if (ch == "")
		    ntext = ntext + text.substr(d6,1);
		else
			ntext = ntext + ch;
    }
	return ntext;
}

CAjaxSms.prototype.trans_lat_to_kir = function(text)
{
	var ntext = '';
	
	for (var d6 = 0; d6 < text.length; d6++)
	{
		var ch = '';
		for(var val in a)
		{ 
			if (text.substr(d6,3) == val) ch = a[val];						
		}
	    //если поиск по 3 не дал результата ищем по 2
		if (ch == "")
		{
			//ищем по 2
			for(var val in a)
			{
		 		if (text.substr(d6,2) == val) ch = a[val];
			}
			
			if (ch == "")
			{
				//ищем по 1
				for(var val in a)
				{
		 			if (text.substr(d6,1) == val) ch = a[val];
		 			
		 			if (text.substr(d6,1) == "'") ch = "ь";
		 			if (text.substr(d6,1) == "\"") ch = "ъ";
				}
				
				if (ch == "")
		    		ntext = ntext + text.substr(d6,1);
				else
					ntext = ntext + ch;	
			}
			else
			{
				ntext = ntext + ch;
				d6+=1;
			}						
		}
		else
		{
			ntext = ntext + ch;
			d6+=2;
		}
	}		
	return ntext;
}

CAjaxSms.prototype.SWW = function()
{
	_this = this;
	
	if (document.getElementById('wait')) return;
	
	var obWaitWindow = document.body.appendChild(document.createElement('DIV'));
	obWaitWindow.className = 'wait';
	obWaitWindow.id = 'wait';
	
	if (jsAjaxUtil.IsIE())
	{
		// дл€ MSIE Ц разместим его посередине текущего отображаемого контента окна
		var left = parseInt(document.body.scrollLeft + document.body.clientWidth/2 - obWaitWindow.offsetWidth/2);
		var top = parseInt(document.body.scrollTop + document.body.clientHeight/2 - obWaitWindow.offsetHeight/2);
	}
	else
	{
		// дл€ остального сделаем ее посередине окна и с фиксированным положением
		var left = parseInt(document.body.clientWidth/2 - obWaitWindow.offsetWidth/2);
		var top = parseInt(document.body.clientHeight/2 - obWaitWindow.offsetHeight/2);
		obWaitWindow.style.position = 'fixed';
	}
	
	obWaitWindow.style.top = top;
	obWaitWindow.style.left = left;
	
	obWaitWindow.innerHTML = '<div>»дет обработка отправки ...</div>';
	
	if(jsAjaxUtil.IsIE())
	{
		// дл€ IE6 и ниже создадим под сообщением плавающий фрейм 
		var frame = document.createElement("IFRAME");
		frame.src = "javascript:''";
		frame.id = 'waitframe';
		frame.className = 'wait';
		frame.style.width = obWaitWindow.offsetWidth + "px";
		frame.style.height = obWaitWindow.offsetHeight + "px";
		frame.style.left = obWaitWindow.style.left;
		frame.style.top = obWaitWindow.style.top;
		document.body.appendChild(frame);
	}
	
	var obWaitWindowShadow = document.body.appendChild(document.createElement('DIV'));
	obWaitWindowShadow.className = 'waitshadow';
	obWaitWindowShadow.id = 'waitshadow';
	if (jsAjaxUtil.IsIE()) 
	{
		// дл€ MSIE Ц раскроем тень по всей высоте содержимого окна
		obWaitWindowShadow.style.height = document.body.scrollHeight + 'px';
	}
	else 
	{
		// дл€ остального сделаем ее фиксированной
		obWaitWindowShadow.style.position = 'fixed';
	}
	
	function __Close(e)
	{
		if (!e) e = window.event
		if (!e) return;
		if (e.keyCode == 27)
		{
			_this.CWW();
			jsEvent.removeEvent(document, 'keypress', __Close);
		}
	}
		
	jsEvent.addEvent(document, 'keypress', __Close);
}

CAjaxSms.prototype.CWW = function()
{
	//удал€ем надпись
	var obWaitWindow = document.getElementById('wait');
	if (obWaitWindow)
			document.body.removeChild(obWaitWindow);
	
	//удал€ем дл€ IE6
	var obWaitMessageFrame = document.getElementById('waitframe');
	if (obWaitMessageFrame)
			document.body.removeChild(obWaitMessageFrame);
	
	//удал€ем тень
	var obWaitWindowShadow = document.getElementById('waitshadow');
	if (obWaitWindowShadow)
			document.body.removeChild(obWaitWindowShadow);		
}

//dialog
function BxecCS_CheckGroup(el)
{
	var obj_div = document.getElementById(el.id.replace("dep_",""));
	
	if(obj_div)
	{
		/*users in this group*/
		var obj = jsUtils.FindChildObject(obj_div, 'div', 'vcsd-user-contact', true);
		do
		{
			var chbox = jsUtils.FindChildObject(obj, 'input', false, true);
			if(chbox)
				chbox.checked = el.checked;
		}
		while(obj = jsUtils.FindNextSibling(obj, 'div'));
        
		//subgroups
		obj = jsUtils.FindChildObject(obj_div, 'div', 'vcsd-user-section', true);
		if(obj)
		{
			do
			{
				var chbox = jsUtils.FindChildObject(obj, 'input', false, true);
				if(chbox)
				{
					chbox.checked = el.checked;
					BxecCS_CheckGroup(chbox);
				}
			}
			while(obj = jsUtils.FindNextSibling(obj, 'div'));
		}
	}
}
function BxecCS_SwitchSection(el, div_id, e)
{
	if (e)
	{
		if(e.target)
			e.targetElement = e.target;
		else if(e.srcElement)
			e.targetElement = e.srcElement;

		if (e.targetElement.nodeName.toUpperCase() == 'INPUT') // Checkbox
			return true;
	}

	var bCollapse = (el.className == 'vcsd-arrow-down');
	el.className = (bCollapse? 'vcsd-arrow-right' : 'vcsd-arrow-down');
	document.getElementById(div_id).style.display = (bCollapse? 'none' : 'block');
}


