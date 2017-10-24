
//Блок опций для Робокассы
function SetPaymentOptions (arParams){
	var obSTORAGE_BOX='';
		if (arParams.data.LANG.SAVE_MESSAGE!=''){
			obSTORAGE_BOX = '<p style="background-color: #C8DE74; padding: 15px 10px; margin: 10px 0 25px 0; width: 300px; text-shadow: 0 1px #fff;">'+arParams.data.LANG.SAVE_MESSAGE+'</p>'
		}
		
		obSTORAGE_BOX+= '<div class="RK_BOK_SAVE_OPTION_FORM" 	data-action="'+arParams.data.EXECUTE_FILE+'" data-onsuccess="'+arParams.data.LANG.ONSUCCESS+'" data-onfailure="'+arParams.data.LANG.ONFAILURE+'">'+
							'<label style="display: block; height: 6px;">'+arParams.data.LANG.STORAGE_LOGIN+':</label><br />'+
							'<input size="20" class="LOGIN" type="text" placeholder="'+arParams.data.LANG.HIDDEN+'"> <br /><br />'+
							'<label style="display: block; height: 6px;">'+arParams.data.LANG.PAYMENT_PASSWORD+':</label><br />'+
							'<input size="20" class="PASSWORD" type="password" placeholder="'+arParams.data.LANG.HIDDEN+'"> <br /><br />'+
							'<label style="display: block; height: 6px;">'+arParams.data.LANG.PAYMENT_PASSWORD_2+':</label><br />'+
							'<input size="20" class="PASSWORD_2" type="password" placeholder="'+arParams.data.LANG.HIDDEN+'"> <br /><br />'+
							'<p class="RK_BOK_MESSAGE" style="padding: 0 0 20px 0; display:none;"></p>'+
							'<br /><a href="javascript:void(0);" name="STORAGE_SAVE_BUTTON" onclick="SavePaymentOptions(this)" class="adm-btn-save" style="padding: 6px 8px; color: #FFF; text-decoration: none;">'+arParams.data.LANG.SAVE_BUTTON+'</a>'+
							'<br /><br />'+
							'<input type="hidden" name="'+arParams.propertyParams.ID+'" value="RK_BOC" />'+
						'</div>';
	
	arParams.oCont.innerHTML=obSTORAGE_BOX;// добавляем в контейнер
}

function SavePaymentOptions(e){
	var RK_BOK_BOX_OPTION = BX.findParent(BX(e),{"tag":"div", "class":"RK_BOK_SAVE_OPTION_FORM"});
	var RK_BOK_URL = RK_BOK_BOX_OPTION.getAttribute('data-action');
	var RK_BOK_MESSAGE = BX.findChildren(BX(RK_BOK_BOX_OPTION),{"tag":"p", "class":"RK_BOK_MESSAGE"})[0]
	var RK_BOK_ONSUCCESS = RK_BOK_BOX_OPTION.getAttribute('data-onsuccess');
	var RK_BOK_ONFAILURE = RK_BOK_BOX_OPTION.getAttribute('data-onfailure');
	
	var RK_BOK_DATA = {
		'TYPE':'PAYMENT',
		'LOGIN':BX.findChildren(BX(RK_BOK_BOX_OPTION),{"tag":"input", "class":"LOGIN"})[0].value,
		'PASSWORD':BX.findChildren(BX(RK_BOK_BOX_OPTION),{"tag":"input", "class":"PASSWORD"})[0].value,
		'PASSWORD_2':BX.findChildren(BX(RK_BOK_BOX_OPTION),{"tag":"input", "class":"PASSWORD_2"})[0].value
	}
	
	BX.style(RK_BOK_MESSAGE, 'display', 'none');
	
	BX.ajax({
		url: RK_BOK_URL,
		data: RK_BOK_DATA,
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		async: true,
		processData: true,
		scriptsRunFirst: true,
		emulateOnload: true,
		start: true,
		cache: false,
		onsuccess: function(data){
			if (data.RESULT=='Y'){
				BX.style(RK_BOK_MESSAGE, 'color', '#648900');
				BX.style(RK_BOK_MESSAGE, 'display', 'block');
				RK_BOK_MESSAGE.innerHTML=RK_BOK_ONSUCCESS;
			} else {
				BX.style(RK_BOK_MESSAGE, 'color', 'red');
				BX.style(RK_BOK_MESSAGE, 'display', 'block');
				RK_BOK_MESSAGE.innerHTML=RK_BOK_ONFAILURE+" — «"+data.ERROR+"»";	
			}
		},
		onfailure: function(){
			BX.style(RK_BOK_MESSAGE, 'color', 'red');
			BX.style(RK_BOK_MESSAGE, 'display', 'block');
			RK_BOK_MESSAGE.innerHTML=RK_BOK_ONFAILURE;
		}
	});
}


//Блок опций для SMS
function SetSMSOptions (arParams){
	
	var obSMS_BOX='';
		console.log(arParams);
		if (arParams.data.LANG.SAVE_MESSAGE!=''){
			obSMS_BOX = '<p style="background-color: #C8DE74; padding: 15px 10px; margin: 10px 0 25px 0; width: 300px; text-shadow: 0 1px #fff;">'+arParams.data.LANG.SAVE_MESSAGE+'</p>'
		}
		
		console.log(arParams);
		obSMS_BOX+= '<div class="SMS_SAVE_OPTION_FORM" 	data-action="'+arParams.data.EXECUTE_FILE+'" data-onsuccess="'+arParams.data.LANG.ONSUCCESS+'" data-onfailure="'+arParams.data.LANG.ONFAILURE+'">'+
						'<input size="20" class="SMS_API_KEY" type="text" placeholder="'+arParams.data.LANG.HIDDEN+'"> <br /><br />'+
						'<p class="SMS_MESSAGE" style="padding: 0 0 20px 0; display:none;"></p>'+
						'<a href="javascript:void(0);" name="SMS_SAVE_BUTTON" onclick="SaveSMSOptions(this)" class="adm-btn-save" style="padding: 6px 8px; color: #FFF; text-decoration: none;">'+arParams.data.LANG.SAVE_BUTTON+'</a>'+
						'<br /><br />'+
						'<input type="hidden" name="'+arParams.propertyParams.ID+'" value="SMSRU_API_KEY" />'+
					'</div>';
	
	arParams.oCont.innerHTML=obSMS_BOX;// добавляем в контейнер	
	
}

function SaveSMSOptions(e){
	var SMS_BOX_OPTION = BX.findParent(BX(e),{"tag":"div", "class":"SMS_SAVE_OPTION_FORM"});
	var SMS_URL = SMS_BOX_OPTION.getAttribute('data-action');
	var SMS_MESSAGE = BX.findChildren(BX(SMS_BOX_OPTION),{"tag":"p", "class":"SMS_MESSAGE"})[0]
	var SMS_ONSUCCESS = SMS_BOX_OPTION.getAttribute('data-onsuccess');
	var SMS_ONFAILURE = SMS_BOX_OPTION.getAttribute('data-onfailure');
	
	var SMS_DATA = {
		'TYPE':'SMS',
		'SMS_API_KEY':BX.findChildren(BX(SMS_BOX_OPTION),{"tag":"input", "class":"SMS_API_KEY"})[0].value,
	}
	
	BX.style(SMS_MESSAGE, 'display', 'none');
	
	BX.ajax({
		url: SMS_URL,
		data: SMS_DATA,
		method: 'POST',
		dataType: 'json',
		timeout: 30,
		async: true,
		processData: true,
		scriptsRunFirst: true,
		emulateOnload: true,
		start: true,
		cache: false,
		onsuccess: function(data){
			if (data.RESULT=='Y'){
				BX.style(SMS_MESSAGE, 'color', '#648900');
				BX.style(SMS_MESSAGE, 'display', 'block');
				SMS_MESSAGE.innerHTML=SMS_ONSUCCESS;
			} else {
				BX.style(SMS_MESSAGE, 'color', 'red');
				BX.style(SMS_MESSAGE, 'display', 'block');
				SMS_MESSAGE.innerHTML=SMS_ONFAILURE+" — «"+data.ERROR+"»";	
			}
		},
		onfailure: function(){
			BX.style(SMS_MESSAGE, 'color', 'red');
			BX.style(SMS_MESSAGE, 'display', 'block');
			SMS_MESSAGE.innerHTML=SMS_ONFAILURE;
		}
	});
}

