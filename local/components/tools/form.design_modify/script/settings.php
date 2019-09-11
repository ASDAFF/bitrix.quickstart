<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/elipseart/form.design_modify/lang/'.LANGUAGE_ID.'/settings.php');

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED")); 

$obJSPopup = new CJSPopup('',
	array(
		'TITLE' => GetMessage("TITLE"),
		'SUFFIX' => 'form_design_modify',
		'ARGS' => ''
	)
);

$form_data = explode(",",$_REQUEST["ParamDataMore"]);
$form_tag = $form_data[0];
$form_param = $form_data[1];
$form_value = $form_data[2];
$form_more_data = explode("(***)",$_REQUEST["SelObjParamSelected"]);
?>

<script type="text/javascript" src="/bitrix/components/elipseart/form.design_modify/script/function.js"></script>

<script type="text/javascript">
	
	var EAFunctionName = [
		{
			'name' : 'ValidModify',
			'title' : '<?=GetMessage('VALID_MODIFY')?>',
			'fields' : 'ALL'
		},
		{
			'name' : 'ValidSize',
			'title' : '<?=GetMessage('VALID_SIZE')?>',
			'fields' : [
				'TEXTAREA',
				'SELECT',
			]
		},
		{
			'name' : 'ValidEmpty',
			'title' : '<?=GetMessage('VALID_EMPTY')?>',
			'fields' : [
				'TEXT',
				'TEXTAREA',
				'FILE',
				'SELECT',
				'CHECKBOX',
				'RADIO'
			]
		},
		{
			'name' : 'ValidEmail',
			'title' : '<?=GetMessage('VALID_EMAIL')?>',
			'fields' : [
				'TEXT'
			]
		}
	];
	
	function CheckMoreParam (CheckboxParam,ParamNum) {
		if(CheckboxParam.checked == true) {
			$('#EAFormParamMoreSection'+ParamNum+'_tag').show();
			$('#EAFormParamMoreSection'+ParamNum+'_param').show();
			$('#EAFormParamMoreSection'+ParamNum+'_value').show();
			$('#EAFormParamMoreSection'+ParamNum+'_type').show();
			$('#EAFormParamMoreSection'+ParamNum+'_align').show();
			$('#EAFormParamMoreSection'+ParamNum+'_clear').show();
			$('#EAFormParamMoreSection'+ParamNum+'_size').show();
			$('#EAFormParamMoreSection'+ParamNum+'_function').show();
		} else {
			$('#EAFormParamMoreSection'+ParamNum+'_tag').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_param').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_value').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_type').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_align').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_clear').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_size').hide();
			$('#EAFormParamMoreSection'+ParamNum+'_function').hide();
		}
	}

	function CheckSizeParam (obj,num,type,cwidth,cheight,multi) {
		
		if(obj.value == 'custom') {
			
			val = '<input type="text" size="5" id="EAFormParamMoreSection'+num+'_size_width_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][SIZE][WIDTH]" value="'+cwidth+'" title="<?=GetMessage('FORM_PARAM_SIZE_WIDTH')?>" />';
			
			if(type == 'TEXTAREA' || type == 'SELECT' && multi == 'true') {
				val += ' x <input type="text" size="5" id="EAFormParamMoreSection'+num+'_size_height_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][SIZE][HEIGHT]" value="'+cheight+'" title="<?=GetMessage('FORM_PARAM_SIZE_HEIGHT')?>" />';
			} else {
				
			}
			
			$('#EAFormParamMoreSection'+num+'_size_value_user').show();
			$('#EAFormParamMoreSection'+num+'_size_value_user').html(val);
		
		} else {
			
			$('#EAFormParamMoreSection'+num+'_size_value_user').html('');
			$('#EAFormParamMoreSection'+num+'_size_value_user').hide();
		
		}
	
	}
	
	function AddFunction (x, tag) {
		
		FormSelectContentMore = '';
		
		FormSelectContentMore += '<select name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][FUNCTION][]" value="">';
		FormSelectContentMore += '<option value=""><?=GetMessage('NOT_SELECT')?></option>';
		for(v=0;v<EAFunctionName.length;++v) {
			if( jQuery.inArray(tag,EAFunctionName[v]['fields'] ) != -1 || EAFunctionName[v]['fields'] == 'ALL' ) {
				if( selected == EAFunctionName[v]['name'] ) { selected = 'selected'; } else { selected = ''; }
				if( EAFunctionName[v]['title'] != '' && EAFunctionName[v]['title'] != undefined ) {
					Message = EAFunctionName[v]['title'];
				}else{
					Message = EAFunctionName[v]['name'];
				}
				FormSelectContentMore += '<option value="'+EAFunctionName[v]['name']+'" '+selected+'>'+Message+'</option>';
			}
		}
		FormSelectContentMore += '</select>';
		FormSelectContentMore += '<br />';
		
		$('#EAFormParamMoreSection'+x+'_function_btn').before(FormSelectContentMore);
		
	}

	function SendParamData () {
		SendData = '';
		ParamNum = $('#EAFormDesignModifyMoreParamPopupParamNum').val();
		c = 0;
		for(i=0;i<ParamNum;++i) {
			if( $('#WEB_FORM_<?=$_REQUEST["SelObjNum"]?>_PARAM_MORE_'+i).attr('checked') == true
				|| $('#WEB_FORM_<?=$_REQUEST["SelObjNum"]?>_PARAM_MORE_'+i).attr('checked') == 'checked' ) {
				if( c > 0 ) {
					SendData += '(***)';
				}
				if( $('#EAFormParamMoreSection'+i+'_clear_value').attr('checked') == true
					|| $('#EAFormParamMoreSection'+i+'_clear_value').attr('checked') == 'checked' ) {
					clear = 'Y';
				} else {
					clear = '';
				}
				if( $('#EAFormParamMoreSection'+i+'_replace_value_tag').attr('value') != undefined &&
					$('#EAFormParamMoreSection'+i+'_replace_value_tag').attr('value') != '' &&
					$('#EAFormParamMoreSection'+i+'_replace_value_param').attr('value') != undefined &&
					$('#EAFormParamMoreSection'+i+'_replace_value_param').attr('value') != '' &&
					$('#EAFormParamMoreSection'+i+'_replace_value_value').attr('value') != undefined &&
					$('#EAFormParamMoreSection'+i+'_replace_value_value').attr('value') != ''
				){
					SendData += $('#EAFormParamMoreSection'+i+'_replace_value_tag').attr('value');
					SendData += '(*)';
					SendData += $('#EAFormParamMoreSection'+i+'_replace_value_param').attr('value');
					SendData += '(*)';
					SendData += $('#EAFormParamMoreSection'+i+'_replace_value_value').attr('value');
					SendData += '(*)';
					if( $('#EAFormParamMoreSection'+i+'_type_value').attr('value') != undefined ) {
						SendData += $('#EAFormParamMoreSection'+i+'_type_value').attr('value');
					}else{
						SendData += '';
					}
					SendData += '(*)';
					if( $('#EAFormParamMoreSection'+i+'_align_value').attr('value') != undefined ) {
						SendData += $('#EAFormParamMoreSection'+i+'_align_value').attr('value');
					}else{
						SendData += '';
					}
					SendData += '(*)';
					SendData += clear;
					SendData += '(*)';
					if( $('#EAFormParamMoreSection'+i+'_size_value_value').attr('value') != undefined ) {
						SendData += $('#EAFormParamMoreSection'+i+'_size_value_value').attr('value');
					}else{
						SendData += '';
					}
					SendData += '(*)';
					if( $('#EAFormParamMoreSection'+i+'_size_width_value').attr('value') != undefined ) {
						SendData += $('#EAFormParamMoreSection'+i+'_size_width_value').attr('value');
					}else{
						SendData += '';
					}
					if( $('#EAFormParamMoreSection'+i+'_size_height_value').attr('value') != undefined ) {
						SendData += ';'+$('#EAFormParamMoreSection'+i+'_size_height_value').attr('value');
					}else{
						SendData += '';
					}
					SendData += '(*)';
					FunctionData = '';
					$('#EAFormParamMoreSection'+i+'_function').find('select').each( function() {
						if( $(this).attr('value') != undefined && $(this).attr('value') != '' ) {
							if( FunctionData != '' ) FunctionData += ';';
							FunctionData += $(this).attr('value');
						}
					});
					if( FunctionData != '' ) {
						SendData += FunctionData;
					}
				}
				c = c+1;
			}
		}
		$('#FormSelectContentMoreData<?=$_REQUEST["SelObjNum"];?>').html(SendData);
		
		$('#FormSelectContentMoreLink<?=$_REQUEST["SelObjNum"];?> a').removeAttr('onclick');
		$('#FormSelectContentMoreLink<?=$_REQUEST["SelObjNum"];?> a').unbind('click');
		$('#FormSelectContentMoreLink<?=$_REQUEST["SelObjNum"];?> a').click( function() {
			(new BX.CDialog({
				content_url: '/bitrix/components/elipseart/form.design_modify/script/settings.php?SelObjNum='+<?=$_REQUEST["SelObjNum"]?>+'&SelObjParamSelected='+SendData+'&ParamDataMore=<?=$_REQUEST["ParamDataMore"]?>',
				width: 700,
				height: 500,
				resizable: true
			})).Show();
			return false;
		});
		
		BX.WindowManager.Get().Close();
	}
	
	function AddCustomParam ( num, SelectedParams ) {
		
		if( num == '' || num == undefined ) {
			num = parseInt($('#EAFormDesignModifyMoreParamPopupParamNum').val())+1;
		}
		
		FormSelectContentMore = '';
		
		option_selected = '';
		option_selected_more = 'style="display: none"';
		option_selected_more_template = '.default';
		option_selected_more_type = 'default';
		option_selected_more_align = '';
		option_selected_more_clear = '';
		option_selected_more_size_value = 'default';
		option_selected_more_size_width = '';
		option_selected_more_size_height = '';
		option_selected_more_function = '';
		
		if( SelectedParams == '' || SelectedParams == undefined ) {
			
			option_selected_more_tag = '';
			option_selected_more_param = '';
			option_selected_more_value = '';
			
		}else{
			
			var SelObjParamData = SelectedParams.split('(*)');
				
			option_selected = 'checked';
			option_selected_more = '';
			
			option_selected_more_tag = SelObjParamData[0]
			option_selected_more_param = SelObjParamData[1]
			option_selected_more_value = SelObjParamData[2]
			
			if(SelObjParamData[3] != '' && SelObjParamData[3] != undefined) {
				option_selected_more_type = SelObjParamData[3];
			}
			if(SelObjParamData[4] != '' && SelObjParamData[4] != undefined) {
				option_selected_more_align = SelObjParamData[4];
			}
			if(SelObjParamData[5] != '' && SelObjParamData[5] != undefined) {
				option_selected_more_clear = SelObjParamData[5];
			}
			if(SelObjParamData[6] != '' && SelObjParamData[6] != undefined) {
				option_selected_more_size_value = SelObjParamData[6];
			}
			if(SelObjParamData[7] != '' && SelObjParamData[7] != undefined) {
				SelObjParamData[7] = SelObjParamData[7].split(';');
				if( SelObjParamData[7][0] != '' && SelObjParamData[7][0] != undefined ) {
					option_selected_more_size_width = SelObjParamData[7][0];
				}
				if( SelObjParamData[7][1] != '' && SelObjParamData[7][1] != undefined ) {
					option_selected_more_size_height = SelObjParamData[7][1];
				}
			}
			if(SelObjParamData[8] != '' && SelObjParamData[8] != undefined) {
				option_selected_more_function = SelObjParamData[8].split(';');
			}
				
		}	
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'" class="section">';
		FormSelectContentMore += '<td colspan="2">';
		FormSelectContentMore += '<table cellspacing="0">';
		FormSelectContentMore += '<tbody>';
		FormSelectContentMore += '<tr>';
		FormSelectContentMore += '<td><a class="bx-popup-sign bx-popup-minus" title="<?=GetMessage('CLOSE_OPEN_SECTION')?>" onclick="ShowSection(this)" href="javascript:void(0)"></a></td>';
		FormSelectContentMore += '<td><?=GetMessage('FIELD')?>: <?=GetMessage('FIELD_CUSTOM')?></td>';
		FormSelectContentMore += '</tr>';
		FormSelectContentMore += '</tbody>';
		FormSelectContentMore += '</table>';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('REPLACE')?>:</td>';
		FormSelectContentMore += '<td><input type="checkbox" id="WEB_FORM_<?=$_REQUEST["SelObjNum"]?>_PARAM_MORE_'+num+'" OnClick="CheckMoreParam(this,'+num+')" '+option_selected+' /> <?/*<img style="margin-left: 5px" src="/bitrix/themes/.default/public/popup/hint.gif" width="12" height="12" />*/?></td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_tag" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FIELD_CUSTOM_TAG')?>:</td>';
		FormSelectContentMore += '<td>';
		FormSelectContentMore += '<input type="text" id="EAFormParamMoreSection'+num+'_replace_value_tag" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][TAG]" value="'+option_selected_more_tag+'" />';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_param" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FIELD_CUSTOM_PARAM')?>:</td>';
		FormSelectContentMore += '<td>';
		FormSelectContentMore += '<input type="text" id="EAFormParamMoreSection'+num+'_replace_value_param" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][PARAM]" value="'+option_selected_more_param+'" />';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_value" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FIELD_CUSTOM_VALUE')?>:</td>';
		FormSelectContentMore += '<td>';
		FormSelectContentMore += '<input type="text" id="EAFormParamMoreSection'+num+'_replace_value_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][VALUE]" value="'+option_selected_more_value+'" />';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_type" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_TYPE')?>:</td>';
		FormSelectContentMore += '<td>';
		FormSelectContentMore += '<select id="EAFormParamMoreSection'+num+'_type_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][TYPE]" value="">';
		FormSelectContentMore += '<option value="default"><?=GetMessage('FORM_PARAM_TYPE_DEFAULT')?></option>';
		FormSelectContentMore += '</select>';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_align" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_ALIGN')?>:</td>';
		FormSelectContentMore += '<td>';
		FormSelectContentMore += '<select id="EAFormParamMoreSection'+num+'_align_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][ALIGN]" value="">';
		if(option_selected_more_align == 'default') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="default" '+selected+'><?=GetMessage('FORM_PARAM_ALIGN_DEFAULT')?></option>';
		if(option_selected_more_align == 'left') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="left" '+selected+'><?=GetMessage('FORM_PARAM_ALIGN_LEFT')?></option>';
		if(option_selected_more_align == 'right') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="right" '+selected+'><?=GetMessage('FORM_PARAM_ALIGN_RIGHT')?></option>';
		FormSelectContentMore += '</select>';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_clear" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_CLEAR')?>:</td>';
		FormSelectContentMore += '<td>';
		if(option_selected_more_clear == 'Y') { checked = 'checked'; } else { checked = ''; }
		FormSelectContentMore += '<input type="checkbox" id="EAFormParamMoreSection'+x+'_clear_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][CLEAR]" value="" '+checked+' />';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_size" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_SIZE')?>:</td>';
		FormSelectContentMore += '<td>';
		FormSelectContentMore += '<select id="EAFormParamMoreSection'+num+'_size_value_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][SIZE][VALUE]" value="" onChange="CheckSizeParam(this,\''+num+'\',\''+FormValidMoreTextType+'\',\''+option_selected_more_size_width+'\',\''+option_selected_more_size_height+'\',\''+FormValidMoreTextTypeMl+'\')">';
		if(option_selected_more_size_value == 'default') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="default" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_DEFAULT')?></option>';
		if(option_selected_more_size_value == 'small') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="small" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_SMALL')?></option>';
		if(option_selected_more_size_value == 'big') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="big" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_BIG')?></option>';
		if(option_selected_more_size_value == 'custom') { selected = 'selected'; } else { selected = ''; }
		FormSelectContentMore += '<option value="custom" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_USER')?></option>';
		FormSelectContentMore += '</select>';
		FormSelectContentMore += '<div id="EAFormParamMoreSection'+num+'_size_value_user">';
		if(option_selected_more_size_value == 'custom') {
			FormSelectContentMore += '<input type="text" size="5" id="EAFormParamMoreSection'+num+'_size_width_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][SIZE][WIDTH]" value="'+option_selected_more_size_width+'" title="<?=GetMessage('FORM_PARAM_SIZE_WIDTH')?>" />';
			FormSelectContentMore += ' x <input type="text" size="5" id="EAFormParamMoreSection'+num+'_size_height_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][SIZE][HEIGHT]" value="'+option_selected_more_size_height+'" title="<?=GetMessage('FORM_PARAM_SIZE_HEIGHT')?>" />';
		}
		FormSelectContentMore += '</div>';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSection'+num+'_function" '+option_selected_more+'>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_FUNCTION')?>:</td>';
		FormSelectContentMore += '<td id="EAFormParamMoreSection'+num+'_function_value">';
		for(c=0;c<option_selected_more_function.length;++c) {
			FormSelectContentMore += '<select name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][FUNCTION][]" value="">';
			FormSelectContentMore += '<option value=""><?=GetMessage('NOT_SELECT')?></option>';
			for(v=0;v<EAFunctionName.length;++v) {
				if( jQuery.inArray(option_selected_more_tag,EAFunctionName[v]['fields'] ) != -1 || EAFunctionName[v]['fields'] == 'ALL' ) {
					if( option_selected_more_function[c] == EAFunctionName[v]['name'] ) { selected = 'selected'; } else { selected = ''; }
					if( EAFunctionName[v]['title'] != '' && EAFunctionName[v]['title'] != undefined ) {
						Message = EAFunctionName[v]['title'];
					}else{
						Message = EAFunctionName[v]['name'];
					}
					FormSelectContentMore += '<option value="'+EAFunctionName[v]['name']+'" '+selected+'>'+Message+'</option>';
				}
			}
			FormSelectContentMore += '</select>';
			FormSelectContentMore += '<br />';
		}
		FormSelectContentMore += '<select name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+num+'][FUNCTION][]" value="">';
		FormSelectContentMore += '<option value=""><?=GetMessage('NOT_SELECT')?></option>';
		for(v=0;v<EAFunctionName.length;++v) {
			if( jQuery.inArray(option_selected_more_tag,EAFunctionName[v]['fields'] ) != -1 || EAFunctionName[v]['fields'] == 'ALL' ) {
				if( EAFunctionName[v]['title'] != '' && EAFunctionName[v]['title'] != undefined ) {
					Message = EAFunctionName[v]['title'];
				}else{
					Message = EAFunctionName[v]['name'];
				}
				FormSelectContentMore += '<option value="'+EAFunctionName[v]['name']+'">'+Message+'</option>';
			}
		}
		FormSelectContentMore += '</select>';
		FormSelectContentMore += '<br />';
		FormSelectContentMore += '<input id="EAFormParamMoreSection'+num+'_function_btn" type="button" OnClick="AddFunction('+num+',\''+option_selected_more_tag+'\');" value="+" />';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
			
		FormSelectContentMore += '<tr class="empty">';
		FormSelectContentMore += '<td colspan="2">';
		FormSelectContentMore += '<div class="empty"></div>';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		$('#EAFormParamMoreSectionAddCustom').before( FormSelectContentMore );
		
		$('#EAFormDesignModifyMoreParamPopupParamNum').val(num+1);
		
	}

	$(document).ready( function () {

		ValidReplaceGroup = '';
		ValidOtherType = '<?=$_REQUEST["SelObjParamSelected"]?>';
		
		FormSelectContentMore = '';
		FormSelectContentMore += '<table width="100%" border="0" cellspacing="0" cellpadding="0">';

		var x = 0;
		var y = 0;
		$('input[type="text"],input[type="password"],input[type="file"],input[type="checkbox"],input[type="radio"],input[type="button"],input[type="submit"],input[type="reset"],textarea,select,button').each( function () {
			
			if($(this).attr('id') == '') {
				$(this).attr('id','EAFormInputAutoID'+y);
			}
			
			var ThisTagName = this.tagName;
			
			if(ThisTagName == 'INPUT') {
				var ThisTagType = 'type';
				var ThisTagValue = $(this).attr('type');
				if(ThisTagValue == 'text') { ThisTagValue2 = 'TEXT'; }
				else if(ThisTagValue == 'password') { ThisTagValue2 = 'TEXT'; }
				else if(ThisTagValue == 'file') { ThisTagValue2 = 'FILE'; }
				else if(ThisTagValue == 'checkbox') { ThisTagValue2 = 'CHECKBOX'; }
				else if(ThisTagValue == 'radio') { ThisTagValue2 = 'RADIO'; }
				else if(ThisTagValue == 'button') { ThisTagValue2 = 'BUTTON'; }
				else if(ThisTagValue == 'submit') { ThisTagValue2 = 'BUTTON'; }
				else if(ThisTagValue == 'reset') { ThisTagValue2 = 'BUTTON'; }
			} else if(ThisTagName == 'TEXTAREA') {
				ThisTagValue2 = 'TEXTAREA';
			} else if(ThisTagName == 'SELECT') {
				ThisTagValue2 = 'SELECT';
			} else if(ThisTagName == 'BUTTON') {
				ThisTagValue2 = 'BUTTON';
			}

			var FormValidMore = $(this).parents().map( function () {
				if(this.tagName == '<?=$form_tag?>' && $(this).attr('<?=$form_param?>') == '<?=$form_value?>') { 
					return "true";
				}
			}).get();
			
			if( FormValidMore == 'true' && ValidReplaceGroup != $(this).attr('name') ) {
				FormValidMoreTag = ThisTagValue2;
				FormValidMoreParam = '';
				FormValidMoreValue = '';
				if($(this).attr('name') != '') {
					FormValidMoreParam = 'name';
					FormValidMoreValue = $(this).attr('name');
				} else {
					FormValidMoreParam = 'id';
					FormValidMoreValue = $(this).attr('id');
				}
				if(FormValidMoreParam != '') {
					if(ThisTagValue != '' && ThisTagValue != undefined) {
						FormValidMoreText = ThisTagName+' type="'+ThisTagValue+'" '+FormValidMoreParam+'="'+FormValidMoreValue+'"';
						FormValidMoreTextType = ThisTagValue;
						FormValidMoreTextTypeMl = $(this).attr('multiple');
					} else {
						FormValidMoreText = ThisTagName+' '+FormValidMoreParam+'="'+FormValidMoreValue+'"';
						FormValidMoreTextType = ThisTagName;
						FormValidMoreTextTypeMl = $(this).attr('multiple');
					}
					
					option_selected = '';
					option_selected_more = 'style="display: none"';
					option_selected_more_template = '.default';
					option_selected_more_type = 'default';
					option_selected_more_align = '';
					option_selected_more_clear = '';
					option_selected_more_size_value = 'default';
					option_selected_more_size_width = '';
					option_selected_more_size_height = '';
					option_selected_more_function = '';

					var SelObjParamSelected = '<?=$_REQUEST["SelObjParamSelected"]?>';
					var option_value = FormValidMoreTag+'#'+FormValidMoreParam+'#'+FormValidMoreValue;
					if(SelObjParamSelected != '' && SelObjParamSelected != undefined) {
						var SelObjParamSelectedData = SelObjParamSelected.split('(***)');
						for(i=0;i<SelObjParamSelectedData.length;++i) {
							var SelObjParamData = SelObjParamSelectedData[i].split('(*)');
							if(option_value == SelObjParamData[0]+'#'+SelObjParamData[1]+'#'+SelObjParamData[2]) {
								
								ValidOtherType = ValidOtherType.replace(SelObjParamSelectedData[i],'');
								
								option_selected = 'checked';
								option_selected_more = '';
								if(SelObjParamData[3] != '' && SelObjParamData[3] != undefined) {
									option_selected_more_type = SelObjParamData[3];
								}
								if(SelObjParamData[4] != '' && SelObjParamData[4] != undefined) {
									option_selected_more_align = SelObjParamData[4];
								}
								if(SelObjParamData[5] != '' && SelObjParamData[5] != undefined) {
									option_selected_more_clear = SelObjParamData[5];
								}
								if(SelObjParamData[6] != '' && SelObjParamData[6] != undefined) {
									option_selected_more_size_value = SelObjParamData[6];
								}
								if(SelObjParamData[7] != '' && SelObjParamData[7] != undefined) {
									SelObjParamData[7] = SelObjParamData[7].split(';');
									if( SelObjParamData[7][0] != '' && SelObjParamData[7][0] != undefined ) {
										option_selected_more_size_width = SelObjParamData[7][0];
									}
									if( SelObjParamData[7][1] != '' && SelObjParamData[7][1] != undefined ) {
										option_selected_more_size_height = SelObjParamData[7][1];
									}
								}
								if(SelObjParamData[8] != '' && SelObjParamData[8] != undefined) {
									option_selected_more_function = SelObjParamData[8].split(';');
								}
								
							}
						}
					}

					FormSelectContentMore += '<tr id="EAFormParamMoreSection'+x+'" class="section">';
					FormSelectContentMore += '<td colspan="2">';
					FormSelectContentMore += '<table cellspacing="0">';
					FormSelectContentMore += '<tbody>';
					FormSelectContentMore += '<tr>';
					FormSelectContentMore += '<td><a class="bx-popup-sign bx-popup-minus" title="<?=GetMessage('CLOSE_OPEN_SECTION')?>" onclick="ShowSection(this)" href="javascript:void(0)"></a></td>';
					FormSelectContentMore += '<td><?=GetMessage('FIELD')?>: '+FormValidMoreText+'</td>';
					FormSelectContentMore += '</tr>';
					FormSelectContentMore += '</tbody>';
					FormSelectContentMore += '</table>';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';

					FormSelectContentMore += '<tr>';
					FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('REPLACE')?>:</td>';
					FormSelectContentMore += '<td><input type="checkbox" id="WEB_FORM_<?=$_REQUEST["SelObjNum"]?>_PARAM_MORE_'+x+'" OnClick="CheckMoreParam(this,'+x+')" '+option_selected+' /> <?/*<img style="margin-left: 5px" src="/bitrix/themes/.default/public/popup/hint.gif" width="12" height="12" />*/?></td>';
					FormSelectContentMore += '</tr>';

					FormSelectContentMore += '<input type="hidden" id="EAFormParamMoreSection'+x+'_replace_value_tag" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][TAG]" value="'+FormValidMoreTag+'" />';
					FormSelectContentMore += '<input type="hidden" id="EAFormParamMoreSection'+x+'_replace_value_param" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][PARAM]" value="'+FormValidMoreParam+'" />';
					FormSelectContentMore += '<input type="hidden" id="EAFormParamMoreSection'+x+'_replace_value_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][VALUE]" value="'+FormValidMoreValue+'" />';

					FormSelectContentMore += '<tr id="EAFormParamMoreSection'+x+'_type" '+option_selected_more+'>';
					FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_TYPE')?>:</td>';
					FormSelectContentMore += '<td>';
					FormSelectContentMore += '<select id="EAFormParamMoreSection'+x+'_type_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][TYPE]" value="">';
					FormSelectContentMore += '<option value="default"><?=GetMessage('FORM_PARAM_TYPE_DEFAULT')?></option>';
					if(FormValidMoreTextType == 'text') {
						
					}
					FormSelectContentMore += '</select>';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';

					FormSelectContentMore += '<tr id="EAFormParamMoreSection'+x+'_align" '+option_selected_more+'>';
					FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_ALIGN')?>:</td>';
					FormSelectContentMore += '<td>';
					FormSelectContentMore += '<select id="EAFormParamMoreSection'+x+'_align_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][ALIGN]" value="">';
					if(option_selected_more_align == 'default') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="default" '+selected+'><?=GetMessage('FORM_PARAM_ALIGN_DEFAULT')?></option>';
					if(option_selected_more_align == 'left') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="left" '+selected+'><?=GetMessage('FORM_PARAM_ALIGN_LEFT')?></option>';
					if(option_selected_more_align == 'right') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="right" '+selected+'><?=GetMessage('FORM_PARAM_ALIGN_RIGHT')?></option>';
					FormSelectContentMore += '</select>';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';

					FormSelectContentMore += '<tr id="EAFormParamMoreSection'+x+'_clear" '+option_selected_more+'>';
					FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_CLEAR')?>:</td>';
					FormSelectContentMore += '<td>';
					if(option_selected_more_clear == 'Y') { checked = 'checked'; } else { checked = ''; }
					FormSelectContentMore += '<input type="checkbox" id="EAFormParamMoreSection'+x+'_clear_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][CLEAR]" value="" '+checked+' />';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';

					FormSelectContentMore += '<tr id="EAFormParamMoreSection'+x+'_size" '+option_selected_more+'>';
					FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_SIZE')?>:</td>';
					FormSelectContentMore += '<td>';
					FormSelectContentMore += '<select id="EAFormParamMoreSection'+x+'_size_value_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][SIZE][VALUE]" value="" onChange="CheckSizeParam(this,\''+x+'\',\''+FormValidMoreTextType+'\',\''+option_selected_more_size_width+'\',\''+option_selected_more_size_height+'\',\''+FormValidMoreTextTypeMl+'\')">';
					if(option_selected_more_size_value == 'default') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="default" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_DEFAULT')?></option>';
					if(option_selected_more_size_value == 'small') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="small" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_SMALL')?></option>';
					if(option_selected_more_size_value == 'big') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="big" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_BIG')?></option>';
					if(option_selected_more_size_value == 'custom') { selected = 'selected'; } else { selected = ''; }
					FormSelectContentMore += '<option value="custom" '+selected+'><?=GetMessage('FORM_PARAM_SIZE_USER')?></option>';
					FormSelectContentMore += '</select>';
					FormSelectContentMore += '<div id="EAFormParamMoreSection'+x+'_size_value_user">';
					if(option_selected_more_size_value == 'custom') {
						FormSelectContentMore += '<input type="text" size="5" id="EAFormParamMoreSection'+x+'_size_width_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][SIZE][WIDTH]" value="'+option_selected_more_size_width+'" title="<?=GetMessage('FORM_PARAM_SIZE_WIDTH')?>" />';
						if(FormValidMoreTextType == 'TEXTAREA' || FormValidMoreTextType == 'SELECT' && FormValidMoreTextTypeMl == true) {
							FormSelectContentMore += ' x <input type="text" size="5" id="EAFormParamMoreSection'+x+'_size_height_value" name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][SIZE][HEIGHT]" value="'+option_selected_more_size_height+'" title="<?=GetMessage('FORM_PARAM_SIZE_HEIGHT')?>" />';
						}
					}
					FormSelectContentMore += '</div>';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';
					
					FormSelectContentMore += '<tr id="EAFormParamMoreSection'+x+'_function" '+option_selected_more+'>';
					FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FORM_PARAM_FUNCTION')?>:</td>';
					FormSelectContentMore += '<td id="EAFormParamMoreSection'+x+'_function_value">';
					for(c=0;c<option_selected_more_function.length;++c) {
						FormSelectContentMore += '<select name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][FUNCTION][]" value="">';
						FormSelectContentMore += '<option value=""><?=GetMessage('NOT_SELECT')?></option>';
						for(v=0;v<EAFunctionName.length;++v) {
							if( jQuery.inArray(FormValidMoreTag,EAFunctionName[v]['fields'] ) != -1 || EAFunctionName[v]['fields'] == 'ALL' ) {
								if( option_selected_more_function[c] == EAFunctionName[v]['name'] ) { selected = 'selected'; } else { selected = ''; }
								if( EAFunctionName[v]['title'] != '' && EAFunctionName[v]['title'] != undefined ) {
									Message = EAFunctionName[v]['title'];
								}else{
									Message = EAFunctionName[v]['name'];
								}
								FormSelectContentMore += '<option value="'+EAFunctionName[v]['name']+'" '+selected+'>'+Message+'</option>';
							}
						}
						FormSelectContentMore += '</select>';
						FormSelectContentMore += '<br />';
					}
					FormSelectContentMore += '<select name="WEB_FORM[<?=$_REQUEST["SelObjNum"]?>][PARAM_MORE]['+x+'][FUNCTION][]" value="">';
					FormSelectContentMore += '<option value=""><?=GetMessage('NOT_SELECT')?></option>';
					for(v=0;v<EAFunctionName.length;++v) {
						if( jQuery.inArray(FormValidMoreTag,EAFunctionName[v]['fields'] ) != -1 || EAFunctionName[v]['fields'] == 'ALL' ) {
							if( EAFunctionName[v]['title'] != '' && EAFunctionName[v]['title'] != undefined ) {
								Message = EAFunctionName[v]['title'];
							}else{
								Message = EAFunctionName[v]['name'];
							}
							FormSelectContentMore += '<option value="'+EAFunctionName[v]['name']+'">'+Message+'</option>';
						}
					}
					FormSelectContentMore += '</select>';
					FormSelectContentMore += '<br />';
					FormSelectContentMore += '<input id="EAFormParamMoreSection'+x+'_function_btn" type="button" OnClick="AddFunction('+x+',\''+FormValidMoreTag+'\');" value="+" />';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';

					FormSelectContentMore += '<tr class="empty">';
					FormSelectContentMore += '<td colspan="2">';
					FormSelectContentMore += '<div class="empty"></div>';
					FormSelectContentMore += '</td>';
					FormSelectContentMore += '</tr>';

					++x;
				}
			}
			
			ValidReplaceGroup = $(this).attr('name');

			++y;
		});
		
		$('#EAFormDesignModifyMoreParamPopupContent').after('<input type="hidden" id="EAFormDesignModifyMoreParamPopupParamNum" value="'+x+'" />');
		
		FormSelectContentMore += '<tr id="EAFormParamMoreSectionAddCustom" class="section">';
		FormSelectContentMore += '<td colspan="2">';
		FormSelectContentMore += '<table cellspacing="0">';
		FormSelectContentMore += '<tbody>';
		FormSelectContentMore += '<tr>';
		FormSelectContentMore += '<td><a class="bx-popup-sign bx-popup-minus" title="<?=GetMessage('CLOSE_OPEN_SECTION')?>" onclick="ShowSection(this)" href="javascript:void(0)"></a></td>';
		FormSelectContentMore += '<td><?=GetMessage('FIELD')?>: <?=GetMessage('FIELD_CUSTOM')?></td>';
		FormSelectContentMore += '</tr>';
		FormSelectContentMore += '</tbody>';
		FormSelectContentMore += '</table>';
		FormSelectContentMore += '</td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '<tr>';
		FormSelectContentMore += '<td class="bx-popup-label bx-width50"><?=GetMessage('FIELD_CUSTOM_ADD')?>:</td>';
		FormSelectContentMore += '<td><input type="button" OnClick="AddCustomParam();" value="+" /></td>';
		FormSelectContentMore += '</tr>';
		
		FormSelectContentMore += '</table>';
		
		$('#EAFormDesignModifyMoreParamPopupContent').html(FormSelectContentMore);
		
		if( ValidOtherType.replace('(***)','') != '' ) {
			
			var SelObjParamSelected = ValidOtherType;
			var SelObjParamSelectedData = SelObjParamSelected.split('(***)');
			for(i=0,v=0;i<SelObjParamSelectedData.length;++i) {
			
				if( SelObjParamSelectedData[i] != '' ) {
					AddCustomParam(x+v,SelObjParamSelectedData[i]);
					++v;
				}
				
			}
			
		}
		
		$('#EAFormDesignModifyMoreParamPopupContent').after('<input type="hidden" value="" name="__closed_sections" />');

	});
</script>

<form name="bx_popup_form_design_modify">
<?
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('bx-edit-menu');
?>

<p><b><?echo GetMessage('TITLE2')?></b></p>
<p class="note"><?echo GetMessage('DESC')?></p>

<?
$obJSPopup->StartContent();
?>

<div id="EAFormDesignModifyMoreParamPopupContent"></div>

<?
$obJSPopup->StartButtons();
?>

<input type="submit" value="<?echo GetMessage('SAVE')?>" onclick="SendParamData();" />

<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>