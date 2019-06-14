<?
/**
 * Company developer: REASPEKT
 * Developer: adel yusupov
 * Site: http://www.reaspekt.ru
 * E-mail: adel@reaspekt.ru
 * @copyright (c) 2016 REASPEKT
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

CUtil::InitJSCore(array("jquery", "window"));

$module_id = "reaspekt.geobase";
$reaspekt_city_manual_default = Option::get($module_id, "reaspekt_city_manual_default");

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "S") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$use_source = array(
	"not_using" => Loc::getMessage("REASPEKT_GEOBASE_NOT_USING"),
	"local_db" => Loc::getMessage("REASPEKT_GEOBASE_LOCAL_DB"),
);

global $DB;

$arCityOption = array();

function ShowParamsHTMLByArray($arParams)
{
	foreach ($arParams as $Option)
	{
		__AdmSettingsDrawRow("reaspekt.geobase", $Option);
	}
}


$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('REASPEKT_GEOBASE_TAB_SETTINGS')
    ),
	array(
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("REASPEKT_GEOBASE_TAB_CITY_NAME")
    ),
	array(
		"DIV"	=> "edit3",
		"TAB"	=> Loc::getMessage("REASPEKT_GEOBASE_TAB_UPDATE_BD"),
		"TITLE" => Loc::getMessage("REASPEKT_TAB_TITLE_DATA")
	),
    array(
        "DIV" => "edit4",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    ),
);

$arAllOptions = array(
	"edit1" => array(
        array(
            'reaspekt_set_local_sql', 
            Loc::getMessage('REASPEKT_GEOBASE_FIELD_SET_SQL'),
            'local_db',
            array('selectbox',$use_source)
        ),
		array(
            'reaspekt_enable_jquery', 
            Loc::getMessage('REASPEKT_GEOBASE_JQUERY'),
            'Y',
            array('checkbox')
        ),
	),
    "edit2" => $arCityOption,
    "edit3" => array(
		array("reaspekt_set_timeout", Loc::getMessage("REASPEKT_GEOBASE_SET_TIMEOUT"), 3, array("text"))
	),
);


$reaspekt_set_local_sql = (($request->isPost() && check_bitrix_sessid()) ? $request->getPost("reaspekt_set_local_sql") : Option::get($module_id, "reaspekt_set_local_sql"));


if ($reaspekt_set_local_sql != "local_db") {
    $arAllOptions["edit1"][] = Loc::getMessage("REASPEKT_GEOBASE_ELIB_TITLE");
    $arAllOptions["edit1"][] = array("reaspekt_elib_site_code", Loc::getMessage("REASPEKT_GEOBASE_CODE_FOR"), "", array("text"));
    $arAllOptions["edit1"][] = Loc::getMessage("REASPEKT_GEOBASE_DESC_CODE_ELIB");
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);


if (
	$request->isPost() 
	&& strlen($Update.$Apply.$RestoreDefaults) > 0 
	&& check_bitrix_sessid()
) {
    
    if(strlen($RestoreDefaults) > 0) {
		Option::delete("reaspekt.geobase");
	} else {
		foreach ($aTabs as $aTab) {
            
            foreach ($arAllOptions[$aTab["DIV"]] as $arOption) {
                
                if (!is_array($arOption)) 
                    continue;

                if ($arOption['note']) 
                    continue;

                $optionName = $arOption[0];

                $optionValue = $request->getPost($optionName);

                Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue):$optionValue);
            }
        }
	}
    
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0) {
		LocalRedirect($_REQUEST["back_url_settings"]);
	} else {
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
    }
}

?>

<style>
.reaspekt_option-main-box{
	display: none;
	padding-bottom: 10px;
	margin-bottom: 5px;
	position: relative;
	width: 100%;
}
.reaspekt_option-main-box span{
	display: inline-block;
}
.reaspekt_option-main-box > div{
	margin: 10px 0 5px 0;
	text-align: right;
}
.reaspekt_option-progress-bar{
	width: 100%;
	height: 15px
}
.reaspekt_option-progress-bar span{
	position: absolute;
}
.reaspekt_option-progress-bar > span{
	border: 1px solid silver;
	width: 95%;
	left: 2px;
	height: 15px;
	text-align: left;
}
.reaspekt_option-progress-bar > span + span{
	border: none;
	width: 4%;
	height: 15px;
	left: auto;
	right: 2px;
	text-align: right
}
#progress{
	height: 15px;
	background: #637f9c;
}
#progress_MM{
	height: 15px;
	background: #637f9c;
}
.reaspekt_geobase_light{
	color: #3377EE;
}
#reaspekt_geobase_info{
	display: none;
	margin-bottom: 15px;
	margin-top: 1px;
	width: 75%;
}
#reaspekt_geobase_info option{
	padding: 3px 6px;
}
#reaspekt_geobase_info option:hover{
	background-color: #D6D6D6;
}
td #reaspekt_geobase_btn{
	margin: 10px 0px 80px;
}
#reaspekt_description_full{
	display: none;
	transition: height 250ms;
}
#reaspekt_description_close_btn{
	display: none;
}
.reaspekt_description_open_text{
	border-bottom: 1px solid;
	color: #2276cc !important;
	cursor: pointer;
	transition: color 0.3s linear 0s;
}
.reaspekt_gb_uf_edit{
	background-color: #d7e3e7;
	background: -moz-linear-gradient(center bottom , #d7e3e7, #fff);
	background-image: url("/bitrix/images/reaspekt.geobase/correct.gif");
	background-position: right 20px center;
	background-repeat: no-repeat;
	color: #3f4b54;
	display: inline-block;
	font-size: 13px;
	margin: 2px;
	outline: medium none;
	vertical-align: middle;
	border: medium none;
	border-radius: 4px;
	box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 1px rgba(0, 0, 0, 0.3), 0 1px 0 #fff inset, 0 0 1px rgba(255, 255, 255, 0.5) inset;
	cursor: pointer;
	font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
	font-weight: bold;
	position: relative;
	text-decoration: none;
	text-shadow: 0 1px rgba(255, 255, 255, 0.7);
	padding: 1px 13px 3px;
}
.reaspekt_gb_uf_edit:hover{
	background: #f3f6f7 -moz-linear-gradient(center top , #f8f8f9, #f2f6f8) repeat scroll 0 0;
	background-image: url("/bitrix/images/reaspekt.geobase/correct.gif");
	background-position: right 20px center;
	background-repeat: no-repeat;
}
#reaspekt_geobase_table_header td{
    text-align: left !important;
}
</style>

<script language="JavaScript">
$(document).ready(function(){
	$('#reaspektOptionManualUpdate').html("<?=Loc::getMessage("REASPEKT_CHECK_UPDATES")?>");

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/reaspekt_geobase_file_check.php",
		timeout: 10000,
		success: function(data){
			if(data == ''){
				$('#reaspektOptionManualUpdate').hide();
				$('#reaspektOptionUpdateUI').show();
				return;
			}
			objData = JSON.parse(data);
			if(objData.IPGEOBASE == 1){
				BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', {'action':'UPDATE'}, obHandler);
			} else {
				document.getElementById('reaspektOptionNotices').innerHTML = "<?=Loc::getMessage("REASPEKT_GEOBASE_URL_NOT_FOUND")?>";
				$('#reaspektOptionManualUpdate').hide();
				$('#reaspektOptionUpdateUI').hide();
			}
		}
	});
});

var timer, obData, updateMode;
obHandler = function (data) {
	var progress, value, title, send, notices, loader;
	updateMode = false;
	obData = JSON.parse(data);
	
    progress = document.getElementById('progress');
    value = document.getElementById('value');
    title = document.getElementById('title');
    notices = document.getElementById('reaspektOptionNotices');
    loader = document.getElementById('reaspektOptionLoaderUI');
    	
    if (obData.STATUS == 3) {
		send = {
			"action": obData.NEXT_STEP,
			"timeout": document.getElementsByName('reaspekt_set_timeout')[0].value
		};
        
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if(typeof obData.FILENAME != 'undefined')
			title.innerHTML = "<?=Loc::getMessage("REASPEKT_TITLE_LOAD_FILE")?> " + obData.FILENAME;
		BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, obHandler);
	
    } else if (obData.STATUS == 2) {
		send = {
			"action": obData.NEXT_STEP,
			"by_step": "Y",
			"filename": obData.FILENAME,
			"seek": obData.SEEK,
			"timeout": document.getElementsByName('reaspekt_set_timeout')[0].value,
            "update_db": "Y"
		};
        
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		
        if (obData.PROGRESS == 100) {
			timer = setInterval(function (){
				title.innerHTML = "<?=Loc::getMessage("REASPEKT_TITLE_UNPACK_FILE")?> " + (typeof obData.FILENAME != 'undefined' ? obData.FILENAME : '');
				progress.style.width = 0 + '%';
				value.innerHTML = 0 + '%';
				BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, obHandler);
			if(typeof obData.FILENAME != 'undefined')
				title.innerHTML = "<?=Loc::getMessage("REASPEKT_TITLE_LOAD_FILE")?> " + obData.FILENAME;
		}
	}
	else if (obData.STATUS == 1) {
		send = {
			"action"	: obData.NEXT_STEP,
			"filename"	: obData.FILENAME,
			"seek"		: obData.SEEK ? obData.SEEK : 0,
			"drop_t"	: obData.DROP_T,
			"timeout"	: document.getElementsByName('reaspekt_set_timeout')[0].value
		};
        
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		
        if (obData.PROGRESS == 100) {
			timer = setInterval(function (){
				title.innerHTML = "<?=Loc::getMessage("REASPEKT_TITLE_DB_UPDATE")?>";
				progress.style.width = 0 + '%';
				value.innerHTML		 = 0 + '%';
				BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, obHandler);
		}
	}
	else if (obData.STATUS == 0) {
		loader.style.display = 'none';
		notices.innerHTML = "<?=Loc::getMessage("REASPEKT_NOTICE_DBUPDATE_SUCCESSFUL")?>";

		notices.style.display = 'block';
	}
    
	if (obData.UPDATE == "Y") {
		if (!updateMode) {
			document.getElementById('reaspektOptionUpdateUI').style.display	 = 'block';
			document.getElementById('reaspektOptionManualUpdate').style.display = 'none';
		}
	} else if (obData.UPDATE == "N") {
		notices.innerHTML = "<?=Loc::getMessage("REASPEKT_NOTICE_UPDATE_NOT_AVAILABLE")?>";
        
		if(!$('#dbupdater').is(':visible'))
			document.getElementsByName('reaspekt_set_timeout')[0].readOnly = true;
		else
			document.getElementsByName('reaspekt_set_timeout')[0].readOnly = false;
	}
};

function updateDB(dst) {
	document.getElementsByName('reaspekt_set_timeout')[0].readOnly = true;
	
    document.getElementById('reaspektOptionNotices').style.display = 'none';
    document.getElementById('reaspektOptionLoaderUI').style.display = 'block';
    BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php',
        {'action': 'LOAD', "timeout": document.getElementsByName('reaspekt_set_timeout')[0].value}, obHandler);
}

var reaspekt_geobase = new Object();
reaspekt_geobase = {'letters':'', 'timer':'0'};

function reaspekt_geobase_delete_click(cityid) {
	var id = '';
	if(typeof cityid !== 'undefined')
		id = cityid;
	else
		return false;

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/reaspekt_geobase_selected.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
				'entry_id': id,
				'delete_city': 'Y'},
		timeout: 10000,
		success: function(data){
			reaspekt_geobase_update_table();
		}
	});
}

function reaspekt_geobase_update_table() {
	$.ajax({
		type: "POST",
		url: "/bitrix/admin/reaspekt_geobase_selected.php",
		dataType: 'html',
		data: { 'sessid': BX.message('bitrix_sessid'),
			'update': 'Y'},
		timeout: 10000,
		success: function(data){
			$('#reaspekt_geobase_cities_table .reaspekt_geobase_city_line').empty().remove();
			$('#reaspekt_geobase_cities_table').append(data);
		}
	});
}

function reaspekt_geobase_onclick(cityid){ // click button "Add"
	var id = '';
	if(typeof cityid == 'undefined' && $('#reaspekt_geobase_btn').prop('disabled')==true && cityid != 'Enter')
		return false;
	if(typeof cityid !== 'undefined' && cityid != 'Enter')
		id = cityid;
	else if(typeof reaspekt_geobase.selected_id !== 'undefined'){
		id = reaspekt_geobase.selected_id;
	}

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/reaspekt_geobase_selected.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
			'city_id': id,
			'add_city': 'Y'
		},
		timeout: 10000,
		success: function(data){
			var list = $('select#reaspekt_geobase_info');
			list.html('');
			if(data == '' || data == null)
				list.animate({ height: 'hide' }, "fast");
			else{
				if(data >= 0){
					$('#reaspekt_geobase_btn').prop('disabled',true);
					$('input#reaspekt_geobase_search').val('');
					reaspekt_geobase_update_table();
				}
			}
		}
	});
	return false;
}

function reaspekt_geobase_select_change(event){
	t = event.target || event.srcElement;
	var sel = t.options[t.selectedIndex];
	$('input#reaspekt_geobase_search').val(reaspekt_geobase.letters = BX.util.trim(sel.value));
	var id = sel.id.substr(20);
	reaspekt_geobase.selected_id = id;
}

function reaspekt_geobase_select_sizing(){
	var count = $("select#reaspekt_geobase_info option").size();
	if (count < 2)
		$("select#reaspekt_geobase_info").attr('size', count+1);
	else if (count < 20)
		$("select#reaspekt_geobase_info").attr('size', count);
	else
		$("select#reaspekt_geobase_info").attr('size', 20);
}

$(function(){
	$(document).click(function(event){
		var search = $('input#reaspekt_geobase_search');
		if($(event.target).closest("#reaspekt_geobase_info").length) return;
		$("#reaspekt_geobase_info").animate({ height: 'hide' }, "fast");
		if(search.val() == '' && !$('#reaspekt_geobase_btn').prop('disabled'))
				$('#reaspekt_geobase_btn').prop('disabled', true);

		if($(event.target).closest("#reaspekt_geobase_search").length) return;
		search.val('');
		event.stopPropagation();
	});
	var reaspektOption_obtn = $('#reaspekt_description_open_btn'),
		reaspektOption_cbtn = $('#reaspekt_description_close_btn'),
		full = $('#reaspekt_description_full');

	reaspektOption_obtn.click(function(event){
		full.show(175);
		$(this).hide();
		reaspektOption_cbtn.show();
	});

	reaspektOption_cbtn.click(function(event){
		full.hide(175);
		$(this).hide();
		reaspektOption_obtn.show();
	});
});

function reaspekt_geobase_add_city(){ // on click Select
	$('#reaspekt_geobase_btn').prop('disabled', false);
	$("#reaspekt_geobase_info").animate({ height: 'hide' }, "fast");
}

function reaspekt_geobase_load(){
	reaspekt_geobase.timer = 0;
	$.ajax({
		type: "POST",
		url: '/bitrix/admin/reaspekt_geobase_selected.php',
		dataType: 'json',
		data: { 'city_name': reaspekt_geobase.letters,
			'lang': BX.message('LANGUAGE_ID'),
			'sessid': BX.message('bitrix_sessid')
		},
		timeout: 10000,
		success: function(data){
			var list = $('select#reaspekt_geobase_info');

			list.html('');
			if(data == '' || data == null)
				list.animate({ height: 'hide' }, "fast");
			else{
				var arOut = '';
				for(var i=0; i < data.length; i++){
					var sOptVal = data[i]['CITY'] + (typeof(data[i]['REGION']) == "undefined" || data[i]['REGION'] == null ? '' : ', ' + data[i]['REGION'])
					+ (typeof(data[i]['OKRUG']) == "undefined" || data[i]['OKRUG'] == ' ' || data[i]['OKRUG'] == null ? '' : ', ' + data[i]['OKRUG']);
					arOut += '<option id="reaspekt_geobase_inp'+ (typeof(data[i]['ID']) == "undefined" ? data[i]['ID'] : data[i]['ID']) +'"'
					+'value = "'+ sOptVal +'">'+ sOptVal +'</option>\n';
				}
				list.html(arOut);
				list.reaspekt_geobase_light(reaspekt_geobase.letters);
				reaspekt_geobase_select_sizing();
				list.animate({ height: 'show' }, "fast");
			}
		}
	});
}

function reaspekt_geobase_selKey(e){ // called when a key is pressed in Select
	e=e||window.event;
	t=(window.event) ? window.event.srcElement : e.currentTarget; // The object which caused

	if(e.keyCode == 13){ // Enter
		reaspekt_geobase_onclick('Enter');
		$("#reaspekt_geobase_info").animate({ height: 'hide' }, "fast");
		return;
	}
	if(e.keyCode == 38 && t.selectedIndex == 0){ // up arrow
		$('.reaspekt_geobase_find input[name=reaspekt_geobase_search]').focus();
		$("#reaspekt_geobase_info").animate({ height: 'hide' }, "fast");
	}
}

function reaspekt_geobase_inpKey(e){ // input search
	e = e||window.event;
	t = (window.event) ? window.event.srcElement : e.currentTarget; // The object which caused
	var list = $('select#reaspekt_geobase_info');

	if(e.keyCode==40){	// down arrow
		if(list.html() != ''){
			list.animate({ height: 'show' }, "fast");
		}
		list.focus();
		return;
	}
	var sFind = BX.util.trim(t.value);

	if(reaspekt_geobase.letters == sFind)
		return; // prevent frequent requests to the server
	reaspekt_geobase.letters = sFind;
	if(reaspekt_geobase.timer){
		clearTimeout(reaspekt_geobase.timer);
		reaspekt_geobase.timer = 0;
	}
	if(reaspekt_geobase.letters.length < 2){
		list.animate({ height: 'hide' }, "fast");
		return;
	}
	reaspekt_geobase.timer = window.setTimeout('reaspekt_geobase_load()', 190); // Load through 70ms after the last keystroke
}

jQuery.fn.reaspekt_geobase_light = function(pat){
	function reaspekt_geobase_innerLight(node, pat){
		var skip = 0;
		if (node.nodeType == 3){
			var pos = node.data.toUpperCase().indexOf(pat);
			if (pos >= 0){
				var spannode = document.createElement('span');
				spannode.className = 'reaspekt_geobase_light';
				var middlebit = node.splitText(pos);
				var endbit = middlebit.splitText(pat.length);
				var middleclone = middlebit.cloneNode(true);
				spannode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(spannode, middlebit);
				skip = 1;
			}
		}
		else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)){
			for (var i = 0; i < node.childNodes.length; ++i){
				i += reaspekt_geobase_innerLight(node.childNodes[i], pat);
			}
		}
		return skip;
	}
	return this.each(function(){
		reaspekt_geobase_innerLight(this, pat.toUpperCase());
	});
};

jQuery.fn.reaspekt_geobase_removeLight = function(){
	return this.find("span.reaspekt_geobase_light").each(function(){
		this.parentNode.firstChild.nodeName;
		with(this.parentNode){
			replaceChild(this.firstChild, this);
			normalize();
		}
	}).end();
};


</script>
<?
$incMod = CModule::IncludeModuleEx($module_id);
if ($incMod == '0')
{
    CAdminMessage::ShowMessage(Array("MESSAGE" => Loc::getMessage("REASPEKT_GEOBASE_NF", Array("#MODULE#" => $module_id)), "HTML"=>true, "TYPE"=>"ERROR"));
}
elseif ($incMod == '2')
{
    ?><span class="errortext"><?=Loc::getMessage("REASPEKT_GEOBASE_DEMO_MODE", Array("#MODULE#" => $module_id))?></span><br/><?
}
elseif ($incMod == '3')
{
    CAdminMessage::ShowMessage(Array("MESSAGE" => Loc::getMessage("REASPEKT_GEOBASE_DEMO_EXPIRED", Array("#MODULE#" => $module_id)), "HTML"=>true, "TYPE"=>"ERROR"));
}
?>
<form method='POST' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>' name='reaspekt_geobase_settings'><?

    $tabControl->Begin();
	$tabControl->BeginNextTab();
        
        ShowParamsHTMLByArray($arAllOptions["edit1"]);
    
    
    $tabControl->BeginNextTab();
        if ($reaspekt_set_local_sql == "local_db") {
    ?>
        <tr class="heading">
            <td colspan="2"><?=Loc::getMessage("REASPEKT_INP_CITY_LIST")?></td>
        </tr>
        
        <tr>
            <td colspan="2">
                <table class="internal" width="100%">
                    <tbody id="reaspekt_geobase_cities_table">
                    <tr class="heading" id="reaspekt_geobase_table_header">
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD1")?></td>
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD2")?></td>
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD3")?></td>
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD4")?></td>
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD5")?></td>
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD6")?></td>
                        <td><?=Loc::getMessage("REASPEKT_GEOBASE_TABLE_DEFAULT_CITY_TD7")?></td>
                    </tr>
                    <?
                    if($incMod != '0' && $incMod != '3') {
                        echo ReaspAdminGeoIP::UpdateCityRows();
                    }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?=Loc::getMessage("REASPEKT_INP_CITY_ADD")?></td>
        </tr>
        <tr>
            <td>
                <input type="hidden" value="<?=$reaspekt_city_manual_default?>" name="reaspekt_city_manual_default" />
                <input type="text" size="100" maxlength="255" id="reaspekt_geobase_search" onkeyup="reaspekt_geobase_inpKey(event);" autocomplete="off" placeholder="<?=Loc::getMessage("REASPEKT_INP_ENTER_CITY");?>" name="reaspekt_geobase_search" value="">
                <br/>
                <select id="reaspekt_geobase_info" ondblclick="reaspekt_geobase_onclick();" onkeyup="reaspekt_geobase_selKey(event);" onchange="reaspekt_geobase_select_change(event);" onclick="reaspekt_geobase_add_city();" size="2" style="display: none;">
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="submit" id="reaspekt_geobase_btn" value="<?=Loc::getMessage("REASPEKT_TABLE_CITY_ADD");?>" onclick="reaspekt_geobase_onclick(); return false;" disabled="true">
            </td>
        </tr>
    
    <?
        } else {
            echo Loc::getMessage("REASPEKT_GEOBASE_DISABLED_NO_LOCAL_DB");
        }
    $tabControl->BeginNextTab();
        if ($reaspekt_set_local_sql == "local_db") {
    ?>
    
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("REASPEKT_GEOBASE_DB_UPDATE_IPGEOBASE")?></td>
	</tr>

	<tr>
		<td colspan="2">
			<div style="text-align: center">
				<div id="reaspektOptionNotices" class="adm-info-message" style="display: block">
					
                    <div style="display: none;" id="reaspektOptionUpdateUI">
                        <?=Loc::getMessage("REASPEKT_NOTICE_UPDATE_AVAILABLE")?>
                        <br><br>
                        <input id="dbupdater" type="button" value="<?=Loc::getMessage("REASPEKT_GEOBASE_UPDATE");?>" onclick="updateDB()">
                    </div>
                    <div id="reaspektOptionManualUpdate">
                        <?=Loc::getMessage("REASPEKT_NOTICE_UPDATE_MANUAL_MODE")?>
                        <br><br>
                        <input type="button" onclick="BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', {'action':'UPDATE'}, obHandler); return false;" value="<?=Loc::getMessage("REASPEKT_GEOBASE_CHECK_UPDATE");?>">
                    </div>
				</div>
				<div class="reaspekt_option-main-box" id="reaspektOptionLoaderUI">
					<h3 id="title"><?=Loc::getMessage("REASPEKT_TITLE_LOAD_FILE")?></h3>
					<span class="reaspekt_option-progress-bar">
						<span>
							<span id="progress"></span>
						</span>
						<span id="value">0%</span>
					</span>
				</div>
			</div>
		</td>
	</tr>
	<?ShowParamsHTMLByArray($arAllOptions["edit3"]);
    
        } else {
            echo Loc::getMessage("REASPEKT_GEOBASE_DISABLED_NO_LOCAL_DB");
        }
    $tabControl->BeginNextTab();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
    
    $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="<?echo Loc::getMessage('MAIN_SAVE')?>" class="adm-btn-save" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
    <input type="submit" name="Apply" value="<?echo Loc::getMessage('MAIN_OPT_APPLY')?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>" >
    <input type="reset" name="reset" value="<?echo Loc::getMessage('MAIN_RESET')?>">
    <input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <?=bitrix_sessid_post();?>
    
<? $tabControl->End(); ?>

</form>