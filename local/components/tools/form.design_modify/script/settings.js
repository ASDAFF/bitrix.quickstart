/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

num = 0;

function OnFormDesignSettingsEdit(arParams) {

	num = 0;

	if (typeof(jQuery) == 'undefined') {

		var JQ = document.createElement('script');
		JQ.src = '/bitrix/components/elipseart/form.design_modify/script/jquery-1.6.1.min.js';
		JQ.type = 'text/javascript';
		document.getElementsByTagName('head')[0].appendChild(JQ);

		var jsOptions = arParams.data.split('||');
		var jsMessage = jsOptions[0].split(';');

		var obButton = document.createElement('span');
		arParams.oCont.appendChild(obButton);
		obButton.innerHTML = jsMessage[0];

		waitJQ();

		function waitJQ() {
			if (typeof(jQuery) == 'undefined') {
				window.setTimeout(waitJQ, 100);
			} else {
				obButton.innerHTML = '';
				OnFormDesignSettingsEdit(arParams);
			}
		}

	}else{

		$('input[name="WEB_FORM_PARAMS"]').parent().parent().hide();
		
		var obButton = document.createElement('div');
		arParams.oCont.appendChild(obButton);
		obButton.id = "EANewContParam";
		obButton.style.display = "none";

		var MoreParamDataResultDefault = '';
		var ResultFormData = arParams.data.split('||');
		for(y=1;y<ResultFormData.length;++y) {
			var MoreParamDataResult = ResultFormData[y].split('#');
			if(MoreParamDataResult[0] == "SELECTED" && MoreParamDataResult[1] != "") {
				if(MoreParamDataResultDefault != '') {
					MoreParamDataResultDefault += '||';
				}
				MoreParamDataResultDefault += ResultFormData[y];
			}
		}

		$('div#EANewContParam').after('<br /><input type="button" value="+" id="EAFormSettingsAddBtn" /><div id="EANewContParamResult"><input type="hidden" name="WEB_FORM" value="" /></div>');/*'+MoreParamDataResultDefault+'*/
		$('#EAFormSettingsAddBtn').click( function () {
			OnFormDesignSettingsAdd(arParams,'');
		});

		var jsOptions = arParams.data.split('||');
		var jsMessage = jsOptions[0].split(';');

		for(y=1;y<jsOptions.length;++y){
			var MoreParamData = jsOptions[y].split('#');
			if(MoreParamData[0] == "SELECTED" && MoreParamData[1] != "") {
				OnFormDesignSettingsAdd(arParams,jsOptions[y]);
			}
		}

		OnFormDesignSettingsAdd(arParams,'');

		$('input[name="save"][type="button"]').mouseover(function(){
			UpdateSendParam();
		});
		$('input[name="save"][type="button"]').focus(function(){
			UpdateSendParam();
		});
		$('input[name="btn_popup_save"][type="button"]').mouseover(function(){
			UpdateSendParam();
		});
		$('input[name="btn_popup_save"][type="button"]').focus(function(){
			UpdateSendParam();
		});

	}

}

function OnFormDesignSettingsAdd(arParams,select) {

	var jsOptions = arParams.data.split('||');
	var jsMessage = jsOptions[0].split(';');

	var ParamsSelectMore = '';

	var FormSelectContent = '<select id="WEB_FORM_ID_'+num+'" onChange="OnFormDesignSettingsAddMore(\''+num+'\',\''+ParamsSelectMore+'\',\''+jsMessage[4]+'\');"><option value="">'+jsMessage[1]+'</option>';
	var i = 0;
	var OtherValid = '';
	$('form').each( function () {
		if($(this).attr('id') == '') {
			$(this).attr('id','EAFormAutoID'+i);
		}
		var form_name = $(this).attr('name');
		var form_id = $(this).attr('id');
		if(form_name != '') {
			var form_param = 'name';
			var form_value = $(this).attr('name');
		}else{
			var form_param = 'id';
			var form_value = $(this).attr('id');
		}
		var ParamName = 'FORM '+form_param+'="'+form_value+'"';
		for(z=6;z<jsOptions.length;++z) {
			var ParamData = jsOptions[z].split('#');
			var ParamParam = ParamData[1];
			var ParamValue = ParamData[2];
			if(form_param == ParamParam && form_value == ParamValue) {
				var ParamName = ParamData[3];
			}
		}
		var option_selected = '';
		var option_value = 'FORM#'+form_param+'#'+form_value;
		if(select != '' && select != undefined) {
			var jsOptionsSelect = select.split('#');
			if(option_value == jsOptionsSelect[1]+'#'+jsOptionsSelect[2]+'#'+jsOptionsSelect[3]) {
				option_selected = 'selected';
				OtherValid = 'false';
			}
		}
		FormSelectContent += '<option value="'+option_value+'" '+option_selected+'>'+ParamName+'</option>';
		++i;
	});
	
	option_selected = '';
	display_other = 'style="display: none;"';
	FormOtherContentParam = '';
	
	if(select != '' && select != undefined){
		if( OtherValid != 'false' ) {
			option_selected = 'selected';
			jsOptionsSelect = select.split('#');
			display_other = '';
			FormOtherContentParam += '<input type="text" id="FormOtherContentParam'+num+'" value="'+jsOptionsSelect[1]+'#'+jsOptionsSelect[2]+'#'+jsOptionsSelect[3]+'#" /> ';
			FormOtherContentParam += '<input type="button" value="ok" OnClick="OnFormDesignSettingsAddMore(\''+num+'\',\''+jsOptionsSelect[4]+'\',\''+jsMessage[4]+'\')" />';
		}
	}
	
	FormSelectContent += '<option value="other" '+option_selected+'>'+jsMessage[5]+'</option>';
	
	FormSelectContent += '</select>';
	
	FormSelectContent += '<div id="FormOtherContent'+num+'" '+display_other+'>'+FormOtherContentParam+'</div>';
	
	FormSelectContent += '<div id="FormSelectContentMoreLink'+num+'">&nbsp;</div>';
	FormSelectContent += '<div id="FormSelectContentMoreForm'+num+'" class="FormSelectContentMoreForm" rel="'+num+'" style="display: none;"></div>';
	FormSelectContent += '<div id="FormSelectContentMoreData'+num+'" class="FormSelectContentMoreData" rel="'+num+'" style="display: none;"></div>';

	$('div#EANewContParam').before(FormSelectContent);

	if(select != '' && select != undefined){
		var jsOptionsSelect = select.split('#');
		OnFormDesignSettingsAddMore(num,jsOptionsSelect[4],jsMessage[4]);
	}

	num = num+1;

}

function OnFormDesignSettingsAddMore(SelObjNum,SelObjParamSelected,SelObjText) {

	var ParamObjMore = 'select#WEB_FORM_ID_'+SelObjNum;
	
	$('#FormSelectContentMoreLink'+SelObjNum).html('');
	$('#FormSelectContentMoreForm'+SelObjNum).html('');
	$('#FormSelectContentMoreData'+SelObjNum).html('');

	if( $(ParamObjMore).attr('value') != '') {
		
		if( $(ParamObjMore).attr('value') == 'other' && ( $('#FormOtherContentParam'+SelObjNum).attr('value') == '' || $('#FormOtherContentParam'+SelObjNum).attr('value') == undefined ) ) {
			
			FormOtherContentParam = '';
			FormOtherContentParam += '<input type="text" id="FormOtherContentParam'+SelObjNum+'" value="" /> ';
			FormOtherContentParam += '<input type="button" value="ok" OnClick="OnFormDesignSettingsAddMore(\''+SelObjNum+'\',\''+SelObjParamSelected+'\',\''+SelObjText+'\')" />';
			
			$('#FormOtherContent'+SelObjNum).html(FormOtherContentParam);
			
			$('#FormOtherContent'+SelObjNum).show();
			
		}else{
			
			if( $(ParamObjMore).attr('value') == 'other' ) {
			
				var ParamDataMore = $('#FormOtherContentParam'+SelObjNum).attr('value').split('#');
				
			}else{
			
				$('#FormOtherContent'+SelObjNum).hide();
				$('#FormOtherContent'+SelObjNum).html('');
				
				var ParamDataMore = $(ParamObjMore).attr('value').split('#');
				
			}
			
			$('#FormSelectContentMoreLink'+SelObjNum+' a').die('click');
			
			FormSelectContentMoreLink = '';
			FormSelectContentMoreLink += '<a href="#" onclick="(new BX.CDialog({ content_url: \'/bitrix/components/elipseart/form.design_modify/script/settings.php?SelObjNum='+SelObjNum+'&SelObjParamSelected='+SelObjParamSelected+'&ParamDataMore='+ParamDataMore+'\', width: 700, height: 500, resizable: true })).Show(); return false;">'+SelObjText+'</a><br /><br />';
			
			FormSelectContentMoreForm = '';
			FormSelectContentMoreForm += ParamDataMore[0]+'#';
			FormSelectContentMoreForm += ParamDataMore[1]+'#';
			FormSelectContentMoreForm += ParamDataMore[2]+'#';
	
			FormSelectContentMoreData = SelObjParamSelected;
			
			$('#FormSelectContentMoreLink'+SelObjNum).html(FormSelectContentMoreLink);
			$('#FormSelectContentMoreForm'+SelObjNum).html(FormSelectContentMoreForm);
			$('#FormSelectContentMoreData'+SelObjNum).html(FormSelectContentMoreData);
		
		}

	}

}

function UpdateSendParam() {
	
	FormUpdateContent = '';
	
	a = 0;
	$('div.FormSelectContentMoreForm').each( function () {
		
		if( a > 0 ) {
			FormUpdateContent += '||';			
		}
		
		FormUpdateContent += $(this).html();
		FormUpdateContent += $('#FormSelectContentMoreData'+$(this).attr('rel')+'').html();
		
		a = a+1;
	});
	
	$('input[name="WEB_FORM"]').val('');
	$('input[name="WEB_FORM"]').change();
	
	$('input[name="WEB_FORM_PARAMS"]').val(FormUpdateContent);
	$('input[name="WEB_FORM_PARAMS"]').change();
}
