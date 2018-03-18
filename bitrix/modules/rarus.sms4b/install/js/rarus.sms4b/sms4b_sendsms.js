function SendingForm(params)
{
	this.summerTime = params.summerTime;
	this.errors = $('#errors');
	this.notes = $('#notes');
	this.sendingform = $('#sendingFormAll');
	this.defSender = $('#defSender');
	this.senderNumber = $('#senderNumber');
	this.destinationNumber = $('#destinationNumber');
	this.message = $('#message');
	this.toLat = $('#toLat');
	this.toKir = $('#toKir');
	this.beginSendAt = $('#BEGIN_SEND_AT');
	this.optional = $("#optional");
	this.hideShowLink = $('#hide-show-link');
	this.activeDateActualCheckbox = $('#ACTIVE_DATE_ACTUAL');
	this.activeDateActual = $('#DATE_ACTUAL');
	this.activeNightTimeCheckbox = $('#ACTIVE_NIGHT_TIME_NS');
	this.fromNightTime = $('#DATE_FROM_NS');
	this.toNightTime = $('#DATE_TO_NS');
	this.uniformSending = $('#uniformSending');
	this.uniformText = $('#uniformText');
	this.caption = $('#caption');
	this.captionText = params.captionText;
	
	//counters
	this.deleteDoubledNumbers = $('#countDoubledLink');
	this.correctNums = $('#correct-nums');
	this.needSms = $('#need-sms');
	this.messageLength = $('#lengmess');
	this.partSize = $('#size-part');
	this.parts = $('#parts');
	
	//gmt
	this.gmtControl = $('#gmtControl');
	
	//uniform counters
	this.uniformHours = $('#uniformHours');
	this.uniformMinutes = $('#uniformMinutes');
	this.mess = params.mess;
	
	this.Init();
}
SendingForm.prototype.Init = function()
{
	this.InitEvents();
	this.DisableDaAndNt();
}

//�������������� ������� 
SendingForm.prototype.InitEvents = function()
{
	var _this = this;
	
	var hideDest = false;
	this.destinationNumber.click(function() {
		if (!hideDest && _this.destinationNumber.hasClass('gray'))
		{
			_this.destinationNumber.val('');
			hideDest=true;
		}
		$(this).removeClass('gray');
	});
	
	var hideMess = false;
	this.message.click(function() {
		if (!hideMess && _this.message.hasClass('gray'))
		{
			_this.message.val('');
			hideMess=true;
		}
		$(this).removeClass('gray');
	});
	
	this.SelectForm();
	this.destinationNumber.keyup(function() {_this.CountDestination(); _this.ProccessUniformSending();});
	this.message.keyup(function() {_this.Recount()});
	this.toLat.click(function() { mainObject = _this; mainObject.message.val(mainObject.KirillicToLatin()); mainObject.Recount(); });
	this.toKir.click(function() { mainObject = _this; mainObject.message.val(mainObject.LatinToKirillic()); mainObject.Recount(); });
	this.deleteDoubledNumbers.click(function() {_this.CountDestinationEx()});
	this.gmtControl.change(function() {_this.ChangeGmt()}); 
	this.beginSendAt.keyup(function() {_this.ProccessUniformSending();});
	this.activeDateActual.keyup(function() {_this.ProccessUniformSending();});
	this.activeDateActualCheckbox.click(function() {_this.ProccessUniformSending();});
	this.uniformSending.click(function() {_this.ProccessUniformSending();});
	this.activeNightTimeCheckbox.click(function() {_this.ProccessUniformSending();});
	this.fromNightTime.change(function() {_this.ProccessUniformSending();})
	this.toNightTime.change(function() {_this.ProccessUniformSending();})
	this.caption.click(function() {
		mainObject = _this;
		str = _this.message.val() +' '+_this.captionText;
		_this.message.val(str);
		mainObject.Recount();
	});
}
SendingForm.prototype.SelectForm = function()
{
}
//�������� gmt
SendingForm.prototype.ChangeGmt = function()
{
	var selectedGmt = Number(this.gmtControl.val()) + Number(this.summerTime);
	var myDate = new Date();
	var gmtTime = new Date(myDate.getUTCFullYear(), myDate.getUTCMonth(), myDate.getUTCDate(), myDate.getUTCHours(), myDate.getUTCMinutes(), myDate.getUTCSeconds(), myDate.getUTCMilliseconds());
	gmtTime.setTime(gmtTime.getTime()+3600*selectedGmt*1000);
	
	var beginSendMonth = String(gmtTime.getMonth()+1);
	var beginSendMonth = String(beginSendMonth).length == 1 ? "0"+String(beginSendMonth) : beginSendMonth;
	var beginSendDay = String(gmtTime.getDate()).length == 1 ? "0"+gmtTime.getDate() : gmtTime.getDate();
	var beginSendHours = String(gmtTime.getHours()).length == 1 ? "0"+gmtTime.getHours() : gmtTime.getHours();  
	var beginSendMinutes = String(gmtTime.getMinutes()).length == 1 ? "0"+gmtTime.getMinutes() : gmtTime.getMinutes();  
	this.beginSendAt.val(beginSendDay+"."+beginSendMonth+"."+gmtTime.getFullYear()+" "+beginSendHours+":"+beginSendMinutes);
	
	gmtTime.setTime(gmtTime.getTime()+3600*1000);
	var actualSendMonth = String(gmtTime.getMonth()+1);
	var actualSendMonth = String(actualSendMonth).length == 1 ? "0"+String(actualSendMonth) : actualSendMonth;
	var actualSendDay = String(gmtTime.getDate()).length == 1 ? "0"+gmtTime.getDate() : gmtTime.getDate();
	var actualSendHours = String(gmtTime.getHours()).length == 1 ? "0"+gmtTime.getHours() : gmtTime.getHours();
	var actualSendMinutes = String(gmtTime.getMinutes()).length == 1 ? "0"+gmtTime.getMinutes() : gmtTime.getMinutes();
	this.activeDateActual.val(actualSendDay+"."+actualSendMonth+"."+gmtTime.getFullYear()+" "+actualSendHours+":"+actualSendMinutes);
}
//uniForm
SendingForm.prototype.ProccessUniformSending = function()
{
	//���� �� ������� ������� ����������� �������� ��� ���������� ������� �� ����� ������, ����� ������ � �� ���������
	if (Number(this.correctNums.text()) <= 1 || this.uniformSending.attr('checked') == false)
	{		
		this.uniformText.text('');
		return;
	}
	
	var hours = '';
	var interval = '';
	var gmt = '';
	//���� ������� ������� ���������� ��������
	if (this.activeDateActualCheckbox.attr('checked'))
	{
		//������� ������� ������� ����
		gmt = this.gmtControl.val();
		
		//���� ��������� ������� ���� � ������������ � ������� ������, ������� ������ � selected
		var startSend = this.beginSendAt.val();
		var startSendDateParts = startSend.split(' ');
		var startSendDayMonthYear = startSendDateParts[0].split('.');
		var startSendHoursMinutes = startSendDateParts[1].split(':');
		var startSendYear = startSendDayMonthYear[2];
		var startSendMonth = startSendDayMonthYear[1] - 1;
		var startSendDay = startSendDayMonthYear[0];
		var startSendHour = startSendHoursMinutes[0];
		var startSendMinutes = startSendHoursMinutes[1];
		var startSendObject = new Date(startSendYear, startSendMonth, startSendDay, startSendHour, startSendMinutes, 0, 0);
		
		//����� ����� �������� ���� ������������
		var actualDate = this.activeDateActual.val();
		var actualDateParts = actualDate.split(' ');
		var actualDayMonthYear = actualDateParts[0].split('.');
		var actualHoursMinutes = actualDateParts[1].split(':');
		var actualYear = actualDayMonthYear[2];
		var actualMonth = actualDayMonthYear[1] - 1;
		var actualDay = actualDayMonthYear[0];
		var actualHour = actualHoursMinutes[0];
		var actualMinutes = actualHoursMinutes[1];
		var actualDateObject = new Date(actualYear, actualMonth, actualDay, actualHour, actualMinutes, 0+1, 0);
		
		//������� ��� ��� ��������, �������� ����� � ������� �������� �������� ����� ������������
		var timestampIntervalMinutes = ((((actualDateObject.getTime() - startSendObject.getTime()) / 1000) / 60));
		var timestampIntervalDays = Math.ceil((timestampIntervalMinutes/60)/24);
			
		//����� ������ �����
		if (this.activeNightTimeCheckbox.attr('checked'))
		{
			var from = this.fromNightTime.find(':selected').text();
			from = from.split(':')[0];
			
			var to = this.toNightTime.find(':selected').text();
			to = to.split(':')[0];	
			
			//��������� �������� ����� ��������� ��������
			var timeWhenCanSend = 24 - ((24 - Number(from)) + Number(to));
/*			console.log("����� ������� ����� ���������� - "+timeWhenCanSend);*/
			var intervalForSendInMinutesUp = to * 60;
			var intervalForSendInMinutesDown = from * 60;
			var sendStartInMinute = Number(startSendHour*60) + Number(startSendMinutes);
			var sendActualInMinute = Number(actualHour*60) + Number(actualMinutes);
			
			/*console.log("������ ������� ��������� - " + intervalForSendInMinutesUp);
			console.log("������� ������� ��������� - " + intervalForSendInMinutesDown);
			console.log("������ �������� � ������� - " + sendStartInMinute);
			console.log("������������ �������� � ������� - " + sendActualInMinute);*/
			
			var deleteUpInterval = 0;
			if (sendStartInMinute > intervalForSendInMinutesUp)
			{
				deleteUpInterval = sendStartInMinute - intervalForSendInMinutesUp;		
/*				console.log("������ �������� ��� ������ - "+deleteUpInterval);*/
			}
			
			var deleteDownInterval = 0;
			if (sendActualInMinute < intervalForSendInMinutesDown)
			{
				deleteDownInterval = intervalForSendInMinutesDown - sendActualInMinute;
/*				console.log("������� �������� ��� ������- "+deleteDownInterval);*/
			}
			
			var minutes = timeWhenCanSend * timestampIntervalDays * 60;
			/*console.log("������� ����� - " + minutes); */
			
			/*console.log("������ �������� ��� ������ c ������ ���������� ���� - "+deleteUpInterval);
			console.log("������� �������� ��� ������ � ������ ���������� ���� - "+deleteDownInterval);*/
			minutes = minutes - deleteUpInterval - deleteDownInterval;
		    /*console.log("����� ����� ������ - " + minutes);                                             */
			hours = minutes / 60;
			var minuteForStr = Math.floor(minutes % 60);
			
			var smsNum = Number(this.correctNums.text());
			interval = (hours / smsNum) * 60;
			
			if (hours > 1)
			{
				hours = Math.floor(hours) + mess['hours'];
				if (minuteForStr != 0)
				{
					hours += ' '+minuteForStr+mess['minutes'];
				}		
			}
			else
			{
				hours = Math.floor(hours * 60) + mess['minutes-more'];
			}

			interval = Math.ceil(interval);
			
			//��������� �� ���������� ���
			if (interval > 60)
			{
				var intervalHour = Math.floor(interval / 60);
				var intervalMinute = Math.floor(interval % 60); 
				
				interval = intervalHour + mess['hours'];
				if (intervalMinute != 0)
				{
					interval += ' ' + intervalMinute + mess['minutes-more'];
				} 
			}
			else if (interval < 1)
			{
				//������ � �������
				interval = Math.ceil(interval*60) + mess['seconds'];
			}
			else
			{
				interval = Math.ceil(interval);
				interval += mess['minutes-more'];
			}
/*			console.log("���������� ����� - "+hours);*/
		}
		else
		{
			//� �����
			var hours = timestampIntervalMinutes / 60;
			var minuteForStr = Math.floor(timestampIntervalMinutes % 60);
			
			var smsNum = Number(this.correctNums.text());
			var interval = (hours / smsNum) * 60;
			
			if (hours > 1)
			{
				hours = Math.floor(hours) + mess['hours'];
				if (minuteForStr != 0)
				{
					hours += ' '+minuteForStr+mess['minutes'];
				}
			}
			else
			{
				hours = Math.ceil(Number(hours) * 60) + mess['minutes-more'];
			}
		
			//��������� �� ���������� ���
			if (interval > 60)
			{
				var intervalHour = Math.floor(interval / 60);
				var intervalMinute = Math.floor(interval % 60); 
				
				interval = intervalHour + mess['hours'];
				if (intervalMinute != 0)
				{
					interval += ' ' + intervalMinute + mess['minutes-more'];
				} 
			}
			else if (interval < 1)
			{
				//������ � �������
				interval = Math.ceil(interval*60) + mess['seconds'];
			}
			else
			{
				interval = Math.ceil(interval);
				interval += mess['minutes-more'];
			}	
		}
	}
	else
	{
		//�� ��������� 6 �����
		hours = '6 �����';
		var smsNum = Number(this.correctNums.text());
		
		//�������� � �������
		var interval = (6*3600 / smsNum) / 60;
		
		if (interval > 60)
		{
			var intervalHour = Math.floor(interval / 60);
			var intervalMinute = Math.floor(interval % 60); 
			
			interval = intervalHour + mess['hours'];
			if (intervalMinute != 0)
			{
				interval += ' ' + intervalMinute + mess['minutes-more'];
			} 
		}
		else if (interval < 1)
		{
			//������ � �������
			interval = Math.ceil(interval*60) + mess['seconds'];
		}
		else
		{
			interval = Math.ceil(interval);
			interval += mess['minutes-more'];
		}		
	}
	
	this.uniformText.text(mess['in-duration'] + hours + mess['with-interval'] + interval);	
}  
//������� ��������� ����������� ���� ������������ � ��������� ������ ��������
SendingForm.prototype.DisableDaAndNt = function()
{
	activeNightTimeNsEvent('ACTIVE_DATE_ACTUAL','DATE_ACTUAL', '');
	activeNightTimeNsEvent('ACTIVE_NIGHT_TIME_NS','DATE_FROM_NS','DATE_TO_NS');	
}
//������� ���������� ���������
SendingForm.prototype.Recount = function()
{
	var text = this.message.val();
	/*����������� ���
	* \n - � FF - ��� ���� ������, � IE - ���, ������� ��������� ������ ���  
	*/
	//����������, IE ��� ��� ���
	if (text.match(/\r/g) == null)
	{
		//������� ���������� ��������
		var newLinesymbols = text.match(/\n/g);
		newLinesymbolsCount = (newLinesymbols != null)? newLinesymbols.length : 0;					
	}
	
	//������ �� ���� ������� ���������� ��������
	textLength = text.length + newLinesymbolsCount;
	//���������� ����� �����
	messLenPart = (isRus(text)) ? ((textLength) > 70 ? 66 : 70) : ((textLength) > 160 ? 153 : 160);
	
	var parts = Math.ceil(textLength / messLenPart);
	
	this.messageLength.text(textLength);
	this.partSize.text(messLenPart);
	this.parts.text(parts);
	this.needSms.text(parts * this.correctNums.text());
}

//������� ������������ ���������� �������, ������� KeyUp
SendingForm.prototype.CountDestination = function ()
{
	//�������� ��� ������
	dest_num = this.destinationNumber.val();
	dest_num = dest_num.replace(/\n/g, ';');
	dest_num = dest_num.replace(/,/g, ';');
	
	array = dest_num.split(";");
	
	arrayWithoutEmpty = new Array();
	
	//���� ������ ������� �� ����� �������, �� ��������� �������������� ���������
	if (array.length < 3000)
	{
		this.deleteDoubledNumbers.hide();
		//����������� �� ������ ��������� �������
		for (var ind in array)
		{
			if (array[ind] != "")
			{
				arrayWithoutEmpty.push(array[ind]);	
			}
		}
		
		//�������� ������ ���������� ��������
		result_array = array_unique(arrayWithoutEmpty);
	}
	else
	{
		this.deleteDoubledNumbers.show();
		result_array = array;
	}

	//���������� ���������� �������
	uniqueLength = result_array.length;
	//��������� ����������� �������� � ������
	if (uniqueLength == "1")
	{
		this.uniformSending.attr("disabled", true);
	}
	else
	{
		this.uniformSending.removeAttr("disabled");	
	}
	//���������� ���������� �������
	this.correctNums.text(uniqueLength);
	//������ ���������� SMS
	this.needSms.text(uniqueLength * this.parts.text());
}
//�������������� �������
SendingForm.prototype.CountDestinationEx = function ()
{
	//�������� ��� ������
	dest_num = this.destinationNumber.val();
	dest_num = dest_num.replace(/\n/g, ';');
	dest_num = dest_num.replace(/,/g, ';');
	
	array = dest_num.split(";");
	
	arrayWithoutEmpty = new Array();
	
	//����������� �� ������ ��������� �������
	for (var ind in array)
	{
		if (array[ind] != "")
		{
			arrayWithoutEmpty.push(array[ind]);	
		}
	}
	
	//�������� ������ ���������� ��������
	result_array = array_unique(arrayWithoutEmpty);
	
	//���������� ���������� �������
	uniqueLength = result_array.length;
	//���������� ���������� �������
	this.correctNums.text(uniqueLength);
	//������ ���������� SMS
	this.needSms.text(uniqueLength * this.parts.text());
}

//��������� ������
function Check(rest)
{
	var need = document.getElementById('need-sms').innerHTML;
	var oNoCash = document.getElementById('errRor1');
	var oTooMuch = document.getElementById('errRor2');
	var rest = Math.ceil(rest);
	
	if (need > rest && need != 0)
	{
		oNoCash.innerHTML = "<font style = 'color:red'><b>"+mess['no-balance']+"</b></font>";
	}
	else
	{
		oNoCash.innerHTML = "";	
	}
}

//������� ��������������
SendingForm.prototype.KirillicToLatin = function()
{
	text = this.message.val();
	var ntext = '';
	var ch = '';
	for (var d6 = 0; d6 < text.length; d6++)
	{
		ch = '';
		for(val in a)
		{
			if (text.substr(d6,1) == a["mark1"] || text.substr(d6,1) == a["mark2"])
				ch = "'";
			if (text.substr(d6,1) == a["mark3"] || text.substr(d6,1) == a["mark4"])
				ch = "\"";
			if (text.substr(d6,1) == '�')
				ch = "'";
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
			if (text.substr(d6,1) == '�')
				ch = "N";
			if (text.substr(d6,1) == '�')
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
//�������������� �� �������� � ����������
SendingForm.prototype.LatinToKirillic = function()
{
	text = this.message.val();
	var ntext = '';
	
	for (var d6 = 0; d6 < text.length; d6++)
	{
		var ch = '';
		for(var val in a)
		{ 
			if (text.substr(d6,3) == val) ch = a[val];						
		}
	    //���� ����� �� 3 �� ��� ���������� ���� �� 2
		if (ch == "")
		{
			//���� �� 2
			for(var val in a)
			{
		 		if (text.substr(d6,2) == val) ch = a[val];
			}
			
			if (ch == "")
			{
				//���� �� 1
				for(var val in a)
				{
		 			if (text.substr(d6,1) == val) ch = a[val];
		 			
		 			if (text.substr(d6,1) == "'") ch = a["mark1"];
		 			if (text.substr(d6,1) == "\"") ch = a["mark3"];
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
//��������� ������� �� �����
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
function disable_submit_button()
{
	form1.sub.disabled=true;
	return true;
}
//������ ��� ������� �������� � ������ �����
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
		var objSecondEdit = document.getElementById(secondEditID);
	
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
//��������� �� �������������� � �������
function in_array(needle, haystack) 
{
	var found = false, key;

	for (key in haystack) 
	{
		if (haystack[key] === needle)
		{
			found = true;
			break;
		}
	}

	return found;
}
//���������� ���������� ������ � ������ ������������ ��������� �������
function array_unique(arr) 
{
	var tmp_arr = new Array();
	
	for (i = 0; i < arr.length; i++) 
	{
		if (arr[i].length == 11 && arr[i][0] == 8)
		{
			arr[i] = "7" + arr[i].slice(1); 	
		}
		
		if (!in_array(arr[i], tmp_arr)) 
		{
			tmp_arr.push(arr[i]);
		}
	}
	
	return tmp_arr;
}

