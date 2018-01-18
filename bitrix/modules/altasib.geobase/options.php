<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @package bitrix
 * @subpackage altasib.geobase
 * @copyright (c) 2006-2015 ALTASIB
 */

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
CUtil::InitJSCore(Array("jquery"));

if (!$USER->IsAdmin())
	return;
$module_id = "altasib.geobase";
$update_mode = (COption::GetOptionString($module_id, "get_update", "N") == "Y") ? true : false;
$updModeMM = (COption::GetOptionString($module_id, "mm_get_update", "N") == "Y") ? true : false;

// sites array
$arSites = Array();
$sites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
while ($Site= $sites->Fetch()){
	$arSites[$Site["LID"]] = "[".$Site["ID"]."] ".$Site["NAME"];
	$arSitesDefault .= $Site["ID"].",";
}
// array site templates
$rsData = CSiteTemplate::GetList(array($by => $order), array(), array("ID", "NAME"));
$arTemplates = Array();
while ($arTemplRes = $rsData->Fetch()){
	$arTemplates[$arTemplRes["ID"]] = $arTemplRes["NAME"]." (".$arTemplRes["ID"].")";
	$arTemplDefault .= $arTemplRes["ID"].",";
}


// Templates of components
$strCompYC = 'altasib:geobase.your.city';
$arCTempls = CComponentUtil::GetTemplatesList($strCompYC);
$arCTemplates = array();
$arDefCTempls = ".default,";
foreach($arCTempls as $Templ){
	$arCTemplates[implode(",", $Templ)] = $Templ['NAME'].(!empty($Templ['TEMPLATE']) ? ' ('.$Templ['TEMPLATE'].')' : '');
	if($Templ["NAME"] == ".default")
		$arDefCTempls = $Templ["NAME"].','.$Templ["TEMPLATE"];
}

$strCompSC = 'altasib:geobase.select.city';
$arCSC_Templs = CComponentUtil::GetTemplatesList($strCompSC);
$arCTemplateSel = array();
$arDefCSC_Templs = ".default,";
foreach($arCSC_Templs as $Templ){
	$arCTemplateSel[implode(",", $Templ)] = $Templ['NAME'].(!empty($Templ['TEMPLATE']) ? ' ('.$Templ['TEMPLATE'].')' : '');
	if($Templ["NAME"] == ".default")
		$arDefCSC_Templs = $Templ["NAME"].','.$Templ["TEMPLATE"];
}

if(CModule::IncludeModule("sale"))
{
	$rus1 = GetMessage("ALTASIB_GEOBASE_RUSSIA");
	$rus2 = GetMessage("ALTASIB_GEOBASE_RF");
	$rusID = "";

	$arLocationsList = array();
	$db_contList = CSaleLocation::GetCountryList(Array("NAME_LANG"=>"ASC"), Array(), LANG);
	while ($arContList = $db_contList->Fetch())
	{
		$arLocationsList[$arContList["ID"]] = "[".$arContList["ID"]."] ".htmlspecialcharsEx($arContList["NAME"])
			." [".htmlspecialcharsEx($arContList["NAME_LANG"]).", ".htmlspecialcharsEx($arContList["NAME_ORIG"]) ."]";
		if(empty ($rusID) && (in_array($rus1, $arContList) || in_array($rus2, $arContList)))
			$rusID = $arContList["ID"];
	}
}

// jQuery
$arJQ = Array(
	"ON" => GetMessage("ALTASIB_GEOBASE_JQUERY_YES"),
	"OFF" => GetMessage("ALTASIB_GEOBASE_JQUERY_NOT")
);

$use_source = array(
	"not_using" => GetMessage("ALTASIB_GEOBASE_NOT_USING"),
	"local_db" => GetMessage("ALTASIB_GEOBASE_LOCAL_DB"),
	"statistic" => GetMessage("ALTASIB_GEOBASE_STATISTIC"),
	"maxmind" => GetMessage("ALTASIB_GEOBASE_SOURCE_MM"),
	"ipgb_mm" => GetMessage("ALTASIB_GEOBASE_IPGEOBASE_MM")
);

$arAllOptions = array(
	"main" => Array(
		Array("set_cookie", GetMessage("ALTASIB_GEOBASE_SET_COOKIE"), "Y", Array("checkbox")),
		Array("set_sql", GetMessage("ALTASIB_GEOBASE_SET_SQL"), "Y", Array("checkbox")),
		Array("enable_jquery", GetMessage("ALTASIB_GEOBASE_JQUERY"), "ON", array("selectbox", $arJQ)),
	),
	"data" => Array(
		Array("online_enable", GetMessage("ALTASIB_GEOBASE_ONLINE_ENABLE"), "Y", Array("checkbox")),
		Array("source", GetMessage("ALTASIB_GEOBASE_SOURCE"), "local_db", array("selectbox", $use_source))
	),
	"update" => Array(
		Array("set_timeout", GetMessage("ALTASIB_GEOBASE_SET_TIMEOUT"), 3, Array("text")),
		Array("get_update", GetMessage("ALTASIB_GEOBASE_GET_UPDATE"), ($update_mode ? "Y" : "N"), Array("checkbox")),
		Array("mm_get_update", GetMessage("ALTASIB_GEOBASE_MM_GET_UPDATE"), ($updModeMM ? "Y" : "N"), Array("checkbox")),
	),
	"auto_display" => Array(
		Array("your_city_enable", GetMessage("ALTASIB_GEOBASE_WIN_YOUR_CITY_ENABLE"), "Y", Array("checkbox")),

		Array("note" => GetMessage("ALTASIB_GEOBASE_YOUR_CITY_DESCR")),
		Array("your_city_templates", GetMessage("ALTASIB_GEOBASE_YOUR_CITY_TEMPLATES"), $arDefCTempls, array("selectbox", $arCTemplates)),
		Array("note" => GetMessage("ALTASIB_GEOBASE_SELECT_CITY_DESCR")),
		Array("select_city_templates", GetMessage("ALTASIB_GEOBASE_SELECT_CITY_TEMPLATES"), $arDefCSC_Templs, array("selectbox", $arCTemplateSel)),
		Array("sites", GetMessage("ALTASIB_GEOBASE_SITES"), $arSitesDefault, array("multiselectbox", $arSites, 2)),
		Array("template", GetMessage("ALTASIB_GEOBASE_TEMPLATE"), $arTemplDefault, array("multiselectbox", $arTemplates)),
	),
	"global_components" => Array(
		Array("popup_back", GetMessage("ALTASIB_GEOBASE_POPUP_BACK"), "Y", Array("checkbox")),
		Array("region_disable", GetMessage("ALTASIB_GEOBASE_REGION_DISABLE"), "N", Array("checkbox")),
		Array("only_select_cities", GetMessage("ALTASIB_GEOBASE_ONLY_SELECT_CITIES"), "N", Array("checkbox")),
		Array("autodetect_enable", GetMessage("ALTASIB_GEOBASE_AUTODETECT_EN"), "Y", Array("checkbox")),
		Array("cities_world_enable", GetMessage("ALTASIB_GEOBASE_CITIES_WORLD_ENABLE"), "Y", Array("checkbox")),
	),
	"locations" => Array(
		Array("def_location", GetMessage("ALTASIB_GEOBASE_SALE_LOCATION"), $rusID, Array("selectbox", $arLocationsList)),
		Array("section_link", GetMessage("ALTASIB_GEOBASE_SECTION_LINK"), "/personal/order/make/", Array("textarea", 3, 44)),
		Array("field_loc_ind", GetMessage("ALTASIB_GEOBASE_FIELD_LOC_IND"), "ORDER_PROP_2", Array("text")),
		Array("field_loc_leg", GetMessage("ALTASIB_GEOBASE_FIELD_LOC_LEG"), "ORDER_PROP_3", Array("text")),
	),
);
$aTabs = array(
	array(
		"DIV"	=> "edit1",
		"TAB"	=> GetMessage("MAIN_TAB_SET"),
		"TITLE" => GetMessage("MAIN_TAB_TITLE_SET")
	),
	array(
		"DIV"	=> "edit2",
		"TAB"	=> GetMessage("ALTASIB_TAB_BD_DATA"),
		"ICON"	=> "altasib_comments_settings",
		"TITLE" => GetMessage("ALTASIB_TAB_TITLE_DATA")
	),
	array(
		"DIV"	=> "edit3",
		"TAB"	=> GetMessage("ALTASIB_TAB_BD_CITIES"),
		"ICON"	=> "altasib_comments_settings",
		"TITLE" => GetMessage("ALTASIB_TAB_TITLE_DB_CITIES")
	),
	array(
		"DIV"	=> "edit4",
		"TAB"	=> GetMessage("MAIN_TAB_RIGHTS"),
		"ICON"	=>"altasib_comments_settings",
		"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
	)
);

if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"] == "GET" && strlen($RestoreDefaults) > 0 && check_bitrix_sessid()) {
	COption::RemoveOption($module_id);
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($arParams){
	foreach ($arParams as $Option){
		__AdmSettingsDrawRow("altasib.geobase", $Option);
	}
}

if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && check_bitrix_sessid()) {
	if (strlen($RestoreDefaults) > 0) {
		COption::RemoveOption($module_id);
	} else {
		foreach ($arAllOptions as $aOptGroup) {
			foreach ($aOptGroup as $option) {
				__AdmSettingsSaveOption($module_id, $option);
			}
		}
	}
	if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0){
		LocalRedirect($_REQUEST["back_url_settings"]);
	} else {
		LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
	}
}?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsEx($mid)?>&amp;lang=<?echo LANG?>">
	<?$tabControl->Begin();
	$tabControl->BeginNextTab();?>
	<div style="background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E; margin-bottom: 15px;">
		<div style="background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff">
			<a href="http://www.is-market.ru?param=cl" target="_blank"><img
					src="/bitrix/images/altasib.geobase/is-market.gif" style="float: left; margin-right: 15px;"
					border="0"/></a>
			<div style="margin: 13px 0 0 0">
				<a href="http://www.is-market.ru?param=cl" target="_blank"
					style="color: #fff; font-size: 10px; text-decoration: none"><?=GetMessage("ALTASIB_IS")?></a>
			</div>
		</div>
	</div><?
	$incMod = CModule::IncludeModuleEx($module_id);
	if ($incMod == '0'){
		CAdminMessage::ShowMessage(Array("MESSAGE"=>GetMessage("ALTASIB_GEOBASE_NF", Array("#MODULE#" => $module_id)), "HTML"=>true, "TYPE"=>"ERROR"));
	} elseif ($incMod == '2'){
		?><span class="errortext"><?=GetMessage("ALTASIB_GEOBASE_DEMO_MODE", Array("#MODULE#" => $module_id))?></span><br/><?
	} elseif ($incMod == '3'){
		CAdminMessage::ShowMessage(Array("MESSAGE"=>GetMessage("ALTASIB_GEOBASE_DEMO_EXPIRED", Array("#MODULE#" => $module_id)), "HTML"=>true, "TYPE"=>"ERROR"));
	}
	?>
	<tr>
		<td colspan="2">
			<div class="notes">
				<table cellspacing="0" cellpadding="0" border="0" class="notes" align="center">
					<tr class="top">
						<td class="left">
							<div class="empty"></div>
						</td>

						<td>
							<div class="empty"></div>
						</td>
						<td class="right">
							<div class="empty"></div>
						</td>
					</tr>
					<tr>
						<td class="left">
							<div class="empty"></div>
						</td>
						<td class="content">
							<?echo GetMessage("ALTASIB_GEOBASE_DESCR")?>
						</td>
						<td class="right">
							<div class="empty"></div>
						</td>
					</tr>
					<tr class="bottom">
						<td class="left">
							<div class="empty"></div>
						</td>
						<td>
							<div class="empty"></div>
						</td>
						<td class="right">
							<div class="empty"></div>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<?ShowParamsHTMLByArray($arAllOptions["main"]);?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("ALTASIB_GEOBASE_AUTO_DISPLAY")?></td>
	</tr><?
	ShowParamsHTMLByArray($arAllOptions["auto_display"]);?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("ALTASIB_GEOBASE_GLOBAL_COMPONENTS")?></td>
	</tr><?
	ShowParamsHTMLByArray($arAllOptions["global_components"]);?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("ALTASIB_GEOBASE_LOCATIONS")?></td>
	</tr><?
	ShowParamsHTMLByArray($arAllOptions["locations"]);?>
	<?$tabControl->BeginNextTab();?>
	<?ShowParamsHTMLByArray($arAllOptions["data"]);?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("ALTASIB_GEOBASE_DB_UPDATE_IPGEOBASE")?></td>
	</tr><?
	?>

<style>
.alx-gbase-main-box {
	display: none;
	padding-bottom: 10px;
	margin-bottom: 5px;
	position: relative;
	width: 100%;
}
.alx-gbase-main-box span {
	display: inline-block;
}
.alx-gbase-main-box > div {
	margin: 10px 0 5px 0;
	text-align: right;
}
.alx-gbase-progress-bar {
	width: 100%;
	height: 15px
}
.alx-gbase-progress-bar span {
	position: absolute;
}
.alx-gbase-progress-bar > span {
	border: 1px solid silver;
	width: 95%;
	left: 2px;
	height: 15px;
	text-align: left;
}
.alx-gbase-progress-bar > span + span {
	border: none;
	width: 4%;
	height: 15px;
	left: auto;
	right: 2px;
	text-align: right
}
#progress {
	height: 15px;
	background: #637f9c;
}
#progress_MM {
	height: 15px;
	background: #637f9c;
}
.altasib_geobase_light {
	color: #3377EE;
}
#altasib_geobase_info{
	display: none;
	margin-bottom: 15px;
	margin-top: 1px;
	width: auto;
}
#altasib_geobase_info option{
	padding: 3px 6px;
}
#altasib_geobase_info option:hover{
	background-color: #D6D6D6;
}
td #altasib_geobase_btn{
	margin: 10px 0px 80px;
}
#altasib_description_full{
	display: none;
	transition: height 250ms;
}
#altasib_description_close_btn{
	display: none;
}
.altasib_description_open_text{
	border-bottom: 1px solid;
	color: #2276cc !important;
	cursor: pointer;
	transition: color 0.3s linear 0s;
}
</style>

<script language="JavaScript">
$(document).ready(function(){
	$('#alxManualUpdate').html("<?=GetMessage("ALTASIB_CHECK_UPDATES")?>");
	$('#alxManualUpdateMM').html("<?=GetMessage("ALTASIB_CHECK_MM_UPDATES")?>");

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/altasib_geobase_file_check.php",
		timeout: 10000,
		success: function(data){
			if(data == ''){
				$('#alxManualUpdate').hide();
				$('#alxManualUpdateMM').hide();
				$('#alxUpdateUI').show();
				$('#alxUpdateUI_MM').show();
				return;
			}
			objData = JSON.parse(data);
			if(objData.IPGEOBASE == 1){
				BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', {'action':'UPDATE'}, obHandler);
			} else {
				document.getElementById('alxNotices').innerHTML = "<?=GetMessage("ALTASIB_GEOBASE_URL_NOT_FOUND")?>";
				$('#alxManualUpdate').hide();
				$('#alxUpdateUI').hide();
			}

			if(objData.MAXMIND == 1){
				BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', {'action':'UPDATE', 'database':'MaxMind'}, obHandler);
			} else {
				document.getElementById('alxNoticesMM').innerHTML = "<?=GetMessage("ALTASIB_GEOBASE_URL_NOT_FOUND")?>";
				$('#alxManualUpdateMM').hide();
				$('#alxUpdateUI_MM').hide();
			}
		}
	});


	altasib_geobase_select_size('sites[]');
	altasib_geobase_select_size('template[]');
});

var timer, obData, updateMode, updateMM;
obHandler = function (data) {
	var progress, value, title, send, MM, notices, loader;
	updateMode = <?=($update_mode ? 'true' : 'false')?>;
	updateMM = <?=($updModeMM ? 'true' : 'false')?>;
	obData = JSON.parse(data);
	if(obData.DATABASE == "MaxMind") {
		MM = true;
		progress = document.getElementById('progress_MM');
		value = document.getElementById('value_MM');
		title = document.getElementById('title_MM');
		notices = document.getElementById('alxNoticesMM');
		loader = document.getElementById('alxLoaderUI_MM');
	} else {
		MM = false;
		progress = document.getElementById('progress');
		value = document.getElementById('value');
		title = document.getElementById('title');
		notices = document.getElementById('alxNotices');
		loader = document.getElementById('alxLoaderUI');
	}
	if (obData.STATUS == 3) {
		send = {
			"action": obData.NEXT_STEP,
			"timeout": document.getElementsByName('set_timeout')[0].value
		};
		if(MM)
			send.database = 'MaxMind';
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if(typeof obData.FILENAME != 'undefined')
			title.innerHTML = "<?=GetMessage("ALTASIB_TITLE_LOAD_FILE")?> " + obData.FILENAME;
		BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', send, obHandler);
	}
	else if (obData.STATUS == 2) {
		send = {
			"action": obData.NEXT_STEP,
			"by_step": "Y",
			"filename": obData.FILENAME,
			"seek": obData.SEEK,
			"timeout": document.getElementsByName('set_timeout')[0].value
		};
		if(MM)
			send.database = 'MaxMind';
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if (obData.PROGRESS == 100) {
			timer = setInterval(function () {
				title.innerHTML = "<?=GetMessage("ALTASIB_TITLE_UNPACK_FILE")?> " + (typeof obData.FILENAME != 'undefined' ? obData.FILENAME : '');
				progress.style.width = 0 + '%';
				value.innerHTML = 0 + '%';
				BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', send, obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', send, obHandler);
			if(typeof obData.FILENAME != 'undefined')
				title.innerHTML = "<?=GetMessage("ALTASIB_TITLE_LOAD_FILE")?> " + obData.FILENAME;
		}
	}
	else if (obData.STATUS == 1) {
		send = {
			"action"	: obData.NEXT_STEP,
			"filename"	: obData.FILENAME,
			"seek"		: obData.SEEK ? obData.SEEK : 0,
			"drop_t"	: obData.DROP_T,
			"timeout"	: document.getElementsByName('set_timeout')[0].value
		};
		if(MM)
			send.database = 'MaxMind';
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if (obData.PROGRESS == 100) {
			timer = setInterval(function () {
				title.innerHTML = (MM ? "<?=GetMessage("ALTASIB_TITLE_MM_DB_UPDATE")?>" : "<?=GetMessage("ALTASIB_TITLE_DB_UPDATE")?>");
				progress.style.width = 0 + '%';
				value.innerHTML		 = 0 + '%';
				BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', send, obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', send, obHandler);
		}
	}
	else if (obData.STATUS == 0) {
		loader.style.display = 'none';
		notices.innerHTML = (MM ? "<?=GetMessage("ALTASIB_NOTICE_MM_DBUPDATE_SUCCESSFUL")?>"
			: "<?=GetMessage("ALTASIB_NOTICE_DBUPDATE_SUCCESSFUL")?>");

		notices.style.display = 'block';
	}
	if (obData.UPDATE == "Y") {
		if(MM && !updateMM) {
			document.getElementById('alxUpdateUI_MM').style.display		= 'block';
			document.getElementById('alxManualUpdateMM').style.display	= 'none';
		}
		if (!MM && !updateMode){
			document.getElementById('alxUpdateUI').style.display	 = 'block';
			document.getElementById('alxManualUpdate').style.display = 'none';
		}
	}
	else if (obData.UPDATE == "N") {
		notices.innerHTML = (MM ? "<?=GetMessage("ALTASIB_NOTICE_MM_UPDATE_NOT_AVAILABLE")?>"
			: "<?=GetMessage("ALTASIB_NOTICE_UPDATE_NOT_AVAILABLE")?>");
		if(!$('#dbupdater').is(':visible') && !$('#dbupdaterMM').is(':visible'))
			document.getElementsByName('set_timeout')[0].disabled = true;
		else
			document.getElementsByName('set_timeout')[0].disabled = false;
	}
};
function updateDB(dst) {
	document.getElementsByName('set_timeout')[0].disabled = true;
	if(dst == 'Maxmind'){
		document.getElementById('alxNoticesMM').style.display = 'none';
		document.getElementById('alxLoaderUI_MM').style.display = 'block';
		BX.ajax.post('/bitrix/admin/altasib_geobase_update.php',
			{'action':'LOAD', 'database':'MaxMind',
				"timeout":document.getElementsByName('set_timeout')[0].value}, obHandler);
	} else{
		document.getElementById('alxNotices').style.display = 'none';
		document.getElementById('alxLoaderUI').style.display = 'block';
		BX.ajax.post('/bitrix/admin/altasib_geobase_update.php',
			{'action': 'LOAD', "timeout": document.getElementsByName('set_timeout')[0].value}, obHandler);
	}
}
</script>

	<tr>
		<td colspan="2">
			<div style="text-align: center">
				<div id="alxNotices" class="adm-info-message" style="display: block">
					<?if ($update_mode): ?>
						<?=GetMessage("ALTASIB_NOTICE_UPDATE_AVAILABLE")?>
						<br><br>
						<input id="dbupdater" type="button" value="<?=GetMessage("ALTASIB_GEOBASE_UPDATE");?>" onclick="updateDB()">
						<script>BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', {'action': 'UPDATE'}, obHandler);</script>
					<?else:?>
						<div style="display: none;" id="alxUpdateUI">
							<?=GetMessage("ALTASIB_NOTICE_UPDATE_AVAILABLE")?>
							<br><br>
							<input id="dbupdater" type="button" value="<?=GetMessage("ALTASIB_GEOBASE_UPDATE");?>" onclick="updateDB()">
						</div>
						<div id="alxManualUpdate">
							<?=GetMessage("ALTASIB_NOTICE_UPDATE_MANUAL_MODE")?>
							<br><br>
							<input type="button" onclick="BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', {'action':'UPDATE'}, obHandler); return false;" value="<?=GetMessage("ALTASIB_GEOBASE_CHECK_UPDATE");?>">
						</div>
					<?endif;?>
				</div>
				<div class="alx-gbase-main-box" id="alxLoaderUI">
					<h3 id="title"><?=GetMessage("ALTASIB_TITLE_LOAD_FILE")?></h3>
					<span class="alx-gbase-progress-bar">
						<span>
							<span id="progress"></span>
						</span>
						<span id="value">0%</span>
					</span>
				</div>
			<!------------->
				<div id="alxNoticesMM" class="adm-info-message" style="display: block">
					<?if ($updModeMM): ?>
						<?=GetMessage("ALTASIB_NOTICE_MM_UPDATE_AVAILABLE")?>
						<br><br>
						<input id="dbupdaterMM" type="button" value="<?=GetMessage("ALTASIB_GEOBASE_UPDATE");?>" onclick="updateDB('Maxmind')">
						<script>BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', {'action':'UPDATE', 'database':'MaxMind'}, obHandler);</script>
					<?else:?>
						<div style="display: none;" id="alxUpdateUI_MM">
							<?=GetMessage("ALTASIB_NOTICE_MM_UPDATE_AVAILABLE")?>
							<br><br>
							<input id="dbupdaterMM" type="button" value="<?=GetMessage("ALTASIB_GEOBASE_UPDATE");?>" onclick="updateDB('Maxmind')">
						</div>
						<div id="alxManualUpdateMM">
							<?=GetMessage("ALTASIB_NOTICE_MM_UPDATE_MANUAL_MODE")?>
							<br><br>
							<input type="button" onclick="BX.ajax.post('/bitrix/admin/altasib_geobase_update.php', {'action':'UPDATE', 'database':'MaxMind'}, obHandler); return false;" value="<?=GetMessage("ALTASIB_GEOBASE_CHECK_UPDATE");?>">
						</div>
					<?endif;?>
				</div>
				<div class="alx-gbase-main-box" id="alxLoaderUI_MM">
					<h3 id="title_MM"><?=GetMessage("ALTASIB_TITLE_LOAD_FILE")?></h3>
					<span class="alx-gbase-progress-bar">
						<span>
							<span id="progress_MM"></span>
						</span>
						<span id="value_MM">0%</span>
					</span>
				</div>
			</div>
		</td>
	</tr>
	<?ShowParamsHTMLByArray($arAllOptions["update"]);?>
	<?
	$tabControl->BeginNextTab();
	?>
	<tr class="heading">
		<td colspan="2" valign="top" align="center"><b><?=GetMessage("ALTASIB_TITLE_CITIES_LIST");?></b></td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="internal">
				<tbody id="altasib_geobase_cities_table">
				<tr width="100%" class="heading" id="altasib_geobase_table_header">
					<td colspan="3"><?=GetMessage("ALTASIB_TABLE_CITY_NAME");?></td>
					<td><?=GetMessage("ALTASIB_TABLE_CITY_CODE");?></td>
					<td><?=GetMessage("ALTASIB_TABLE_DISTRICT");?></td>
					<td><?=GetMessage("ALTASIB_TABLE_REGION");?></td>
					<td><?=GetMessage("ALTASIB_TABLE_COUNTRY_CODE");?></td>
					<td><?=GetMessage("ALTASIB_TABLE_COUNTRY");?></td>
					<td><?=GetMessage("ALTASIB_TABLE_CITY_ACT");?></td>
				</tr>
				<?
				if ($incMod != '0' && $incMod != '3') {
					echo CAltasibGeoBaseSelected::UpdateCityRows();
				}
				?>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><?=GetMessage("ALTASIB_INP_CITY_ADD");?></td>
	</tr>
	<tr>
		<td>
			<input type="text" size="100" maxlength="255" id="altasib_geobase_search" type="text" onkeyup="altasib_geobase_inpKey(event);" autocomplete="off" placeholder="<?=GetMessage("ALTASIB_INP_ENTER_CITY");?>" name="altasib_geobase_search" value="">
			<br/>
			<select id="altasib_geobase_info" ondblclick="altasib_geobase_onclick();" onkeyup="altasib_geobase_selKey(event);" onchange="altasib_geobase_select_change(event);" onclick="altasib_geobase_add_city();" size="2" style="display: none;">
			</select>
		</td>
	</tr>
	<tr>
		<td><input type="submit" id="altasib_geobase_btn" value="<?=GetMessage("ALTASIB_TABLE_CITY_ADD");?>" onclick="altasib_geobase_onclick(); return false;" disabled="true">
		</td>
	</tr>

<script language="JavaScript">
var altasib_geobase = new Object();
altasib_geobase = {'letters':'', 'timer':'0'};

function altasib_geobase_delete_click(cityid){
	var id = '';
	if(typeof cityid !== 'undefined')
		id = cityid;
	else
		return false;

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/altasib_geobase_selected.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
				'entry_id': id,
				'delete_city': 'Y'},
		timeout: 10000,
		success: function(data){
			altasib_geobase_update_table();
		}
	});
}

function altasib_geobase_update_table(){
	$.ajax({
		type: "POST",
		url: "/bitrix/admin/altasib_geobase_selected.php",
		dataType: 'html',
		data: { 'sessid': BX.message('bitrix_sessid'),
				'update': 'Y'},
		timeout: 10000,
		success: function(data){
			$('#altasib_geobase_cities_table .altasib_geobase_city_line').empty().remove();
			$('#altasib_geobase_cities_table').append(data);
		}
	});
}

function altasib_geobase_onclick(cityid){ // click button "Add"
	var id = '';
	if(typeof cityid == 'undefined' && $('#altasib_geobase_btn').prop('disabled')==true && cityid != 'Enter')
		return false;
	if(typeof cityid !== 'undefined' && cityid != 'Enter')
		id = cityid;
	else if(typeof altasib_geobase.selected_id !== 'undefined'){
		id = altasib_geobase.selected_id;
	}

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/altasib_geobase_selected.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
				'city_id': id,
				'add_city': 'Y'},
		timeout: 10000,
		success: function(data){
			var list = $('select#altasib_geobase_info');
			list.html('');
			if(data == '' || data == null)
				list.animate({ height: 'hide' }, "fast");
			else{
				if(data >= 0){
					$('#altasib_geobase_btn').prop('disabled',true);
					$('input#altasib_geobase_search').val('');
					altasib_geobase_update_table();
				}
			}
		}
	});
	return false;
}

function altasib_geobase_select_change(event){
	t = event.target || event.srcElement;
	var sel = t.options[t.selectedIndex];
	$('input#altasib_geobase_search').val(altasib_geobase.letters = BX.util.trim(sel.value));
	var id = sel.id.substr(20);
	altasib_geobase.selected_id = id;
}

function altasib_geobase_select_sizing(){
	var count = $("select#altasib_geobase_info option").size();
	if (count < 2)
		$("select#altasib_geobase_info").attr('size', count+1);
	else if (count < 20)
		$("select#altasib_geobase_info").attr('size', count);
	else
		$("select#altasib_geobase_info").attr('size', 20);
}

$(function(){
	$(document).click(function(event){
		var search = $('input#altasib_geobase_search');
		if($(event.target).closest("#altasib_geobase_info").length) return;
		$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
		if(search.val() == '' && !$('#altasib_geobase_btn').prop('disabled'))
				$('#altasib_geobase_btn').prop('disabled', true);

		if($(event.target).closest("#altasib_geobase_search").length) return;
		search.val('');
		event.stopPropagation();
	});
	var alx_obtn = $('#altasib_description_open_btn'),
		alx_cbtn = $('#altasib_description_close_btn'),
		full = $('#altasib_description_full');

	alx_obtn.click(function(event){
		full.show(175);
		$(this).hide();
		alx_cbtn.show();
	});

	alx_cbtn.click(function(event){
		full.hide(175);
		$(this).hide();
		alx_obtn.show();
	});
});

function altasib_geobase_add_city(){ // on click Select
	$('#altasib_geobase_btn').prop('disabled', false);
	$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
}

function altasib_geobase_load(){
	altasib_geobase.timer = 0;
	$.ajax({
		type: "POST",
		url: '/bitrix/admin/altasib_geobase_selected.php',
		dataType: 'json',
		data: { 'city_name': altasib_geobase.letters,
				'lang': BX.message('LANGUAGE_ID'),
				'sessid': BX.message('bitrix_sessid')
			},
		timeout: 10000,
		success: function(data){
			var list = $('select#altasib_geobase_info');

			list.html('');
			if(data == '' || data == null)
				list.animate({ height: 'hide' }, "fast");
			else{
				var arOut = '';
				for(var i=0; i < data.length; i++){
					var sOptVal = data[i]['CITY'] + (typeof(data[i]['REGION']) == "undefined" || data[i]['REGION'] == null ? '' : ', ' + data[i]['REGION'])
					+ (typeof(data[i]['DISTRICT']) == "undefined" || data[i]['DISTRICT'] == ' ' || data[i]['DISTRICT'] == null ? '' : ', ' + data[i]['DISTRICT'])
					+ (typeof(data[i]['COUNTRY']) == "undefined" || data[i]['COUNTRY'] == '' ? '' : ', ' + data[i]['COUNTRY']);
					arOut += '<option id="altasib_geobase_inp_'+ (typeof(data[i]['C_CODE']) == "undefined" ? data[i]['ID'] : data[i]['C_CODE']) +'"'
					+'value = "'+ sOptVal +'">'+ sOptVal +'</option>\n';
				}
				list.html(arOut);
				list.altasib_geobase_light(altasib_geobase.letters);
				altasib_geobase_select_sizing();
				list.animate({ height: 'show' }, "fast");
			}
		}
	});
}

function altasib_geobase_selKey(e){ // called when a key is pressed in Select
	e=e||window.event;
	t=(window.event) ? window.event.srcElement : e.currentTarget; // The object which caused

	if(e.keyCode == 13){ // Enter
		altasib_geobase_onclick('Enter');
		$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
		return;
	}
	if(e.keyCode == 38 && t.selectedIndex == 0){ // up arrow
		$('.altasib_geobase_find input[name=altasib_geobase_search]').focus();
		$("#altasib_geobase_info").animate({ height: 'hide' }, "fast");
	}
}

function altasib_geobase_inpKey(e){ // input search
	e = e||window.event;
	t = (window.event) ? window.event.srcElement : e.currentTarget; // The object which caused
	var list = $('select#altasib_geobase_info');

	if(e.keyCode==40){	// down arrow
		if(list.html() != ''){
			list.animate({ height: 'show' }, "fast");
		}
		list.focus();
		return;
	}
	var sFind = BX.util.trim(t.value);

	if(altasib_geobase.letters == sFind)
		return; // prevent frequent requests to the server
	altasib_geobase.letters = sFind;
	if(altasib_geobase.timer){
		clearTimeout(altasib_geobase.timer);
		altasib_geobase.timer = 0;
	}
	if(altasib_geobase.letters.length < 2){
		list.animate({ height: 'hide' }, "fast");
		return;
	}
	altasib_geobase.timer = window.setTimeout('altasib_geobase_load()', 190); // Load through 70ms after the last keystroke
}

function altasib_geobase_select_size(name) {
	var count = $("select[name='" + name + "'] option").size();
	if (count < 5)
		$("select[name='" + name + "']").attr('size', count + 1);
}

jQuery.fn.altasib_geobase_light = function(pat) {
	function altasib_geobase_innerLight(node, pat) {
		var skip = 0;
		if (node.nodeType == 3) {
			var pos = node.data.toUpperCase().indexOf(pat);
			if (pos >= 0) {
				var spannode = document.createElement('span');
				spannode.className = 'altasib_geobase_light';
				var middlebit = node.splitText(pos);
				var endbit = middlebit.splitText(pat.length);
				var middleclone = middlebit.cloneNode(true);
				spannode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(spannode, middlebit);
				skip = 1;
			}
		}
		else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
			for (var i = 0; i < node.childNodes.length; ++i) {
				i += altasib_geobase_innerLight(node.childNodes[i], pat);
			}
		}
		return skip;
	}
	return this.each(function() {
		altasib_geobase_innerLight(this, pat.toUpperCase());
	});
};

jQuery.fn.altasib_geobase_removeLight = function() {
	return this.find("span.altasib_geobase_light").each(function() {
		this.parentNode.firstChild.nodeName;
		with (this.parentNode) {
			replaceChild(this.firstChild, this);
			normalize();
		}
	}).end();
};

</script>
<?

?><?$tabControl->BeginNextTab();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	$tabControl->Buttons();?>
	<script language="JavaScript">
		function RestoreDefaults() {
			if (confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
				window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
		}
	</script>
	<div align="left">
		<input type="hidden" name="Update" value="Y">
		<input type="submit" <?if (!$USER->IsAdmin()) echo " disabled ";?> name="Update"
			value="<?echo GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
		<input type="reset" <?if (!$USER->IsAdmin()) echo " disabled ";?> name="reset"
			value="<?echo GetMessage("MAIN_RESET")?>" onClick="window.location.reload()">
		<input type="button" <?if (!$USER->IsAdmin()) echo " disabled ";?> type="button"
			title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();"
			value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	</div>
	<?$tabControl->End();?>
	<?=bitrix_sessid_post();?>
</form>