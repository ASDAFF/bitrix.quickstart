<?
$module_id = "grain.customsettings";

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_mode = CModule::IncludeModuleEx($module_id);

$GKS_RIGHT = $APPLICATION->GetGroupRight("grain.customsettings");

if($GKS_RIGHT>="R" && ($module_mode==MODULE_INSTALLED || $module_mode==MODULE_DEMO)):

$gks_test_mode = false;

$aTabs = array(
	array(
		"DIV" => "settings",
		"TAB" => GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_TAB_SETTINGS"),
		"TITLE" => GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_TAB_TITLE_SETTINGS"),
	),
	array(
		"DIV" => "menu",
		"TAB" => GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_TAB_MENU"),
		"TITLE" => GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_TAB_TITLE_MENU"),
	),

	array(
		"DIV" => "rights",
		"TAB" => GetMessage("MAIN_TAB_RIGHTS"),
		"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"),
	),
);


$tabControl = new CAdminTabControl("tabControl", $aTabs);


$redirect_to_url="";

$arErrors = Array();

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid() && $GKS_RIGHT=="W") {


	if (strlen($RestoreDefaults)>0)
	{

		$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data_default.php", "r");
		$settings_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data_default.php"));
		fclose($handle);

		$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php", "w");
		fwrite($handle, $settings_data);
		fclose($handle);

		$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/default_option_default.php", "r");
		$default_options_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/default_option_default.php"));
		fclose($handle);

		$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/default_option.php", "w");
		fwrite($handle, $default_options_data);
		fclose($handle);

		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));

	
	} else {

		if(!is_array($TABS)) $TABS = Array();

		if(is_array($SETTINGS)) {

			// Check for errors

			$no_identifier_error = false;

    		foreach($TABS as $tab) 
    			foreach($tab["FIELDS"] as $option) 
    				if($option["NAME"]===NULL || strlen(trim($option["NAME"]))<=0) 
    					$no_identifier_error = true;
    		
    		if($no_identifier_error) $arErrors[] = GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_ERROR_NO_IDENTIFIER");

			if(count($arErrors)<=0) {

				// Saving default values
	
				$default_options = Array();
				foreach($TABS as $tab) 
					foreach($tab["FIELDS"] as $option) 
						if($option["DEFAULT_VALUE"]!==NULL) 
							$default_options[$option["NAME"]] = $option["DEFAULT_VALUE"];
						else
							$default_options[$option["NAME"]] = "";
				$default_options_data = "<?\n\$grain_customsettings_default_option = ".var_export($default_options,true).";\n?>";		
	
				$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/default_option.php", "w");
				fwrite($handle, $default_options_data);
				fclose($handle);
	
				// Saving settings data
	
				$settings_data = "<?\n\n\$arCustomPage = ".var_export($SETTINGS,true).";\n\n";
				$settings_data .= "\$arCustomSettings = ".var_export($TABS,true).";\n\n?>";
			
				$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php", "w");
				fwrite($handle, $settings_data);
				fclose($handle);
				
			} else {
			
				$arCustomPage = $SETTINGS;
				$arCustomSettings = $TABS;
				
			}
			
		}

	}


	if(count($arErrors)<=0) {

		if(strlen($_REQUEST["back_url_settings"])>0 && strlen($Apply)<=0) $redirect_to_url=$_REQUEST["back_url_settings"];
		else $redirect_to_url = $APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam();

	}

}


if(count($arErrors)<=0) {

	$arCustomPage = Array();
	$arCustomSettings = Array();
	
	$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php", "r");
	$settings_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/grain.customsettings/admin/settings_data.php"));
	fclose($handle);
	
	ob_start();
	$settings_data_error = eval("?>".$settings_data."<?")===false;
	$err = ob_get_contents();
	ob_end_clean();
	
	if($settings_data_error) {
		$arCustomPage = Array();
		$arCustomSettings = Array();
	}

}

$rsLang = CLanguage::GetList($by="sort", $order="asc",Array("ACTIVE" => "Y"));
global $arLang;
$arLang = Array();
while($arLng = $rsLang->Fetch()) $arLang[$arLng["LID"]] = $arLng;
//echo "<pre>"; print_r($arLang); echo "</pre>";

if($module_mode==MODULE_DEMO) require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/trial/trial.php");

if(count($arErrors)>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>implode("<br />",$arErrors), "TYPE"=>"ERROR"));


$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>" name="gcs_settings_form">
<?

foreach($aTabs as $aTab):
	$tabControl->BeginNextTab();

	if ($aTab["DIV"]=="rights") require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");

	elseif($aTab["DIV"]=="settings") {
	?>	
		<tr><td valign="top" width="100%">

<style>

div.gcustomsettings-settings-tab { border: 1px solid rgb(208, 215, 216); margin-bottom: 10px; padding: 10px }

table.gcustomsettings-settings-tab-headers { border: 1px solid rgb(208, 215, 216); border-collapse: collapse; margin-bottom: 7px !important }
table.gcustomsettings-settings-tab-headers td { border: 1px solid rgb(208, 215, 216) !important; padding: 3px !important }
table.gcustomsettings-settings-tab-headers thead td { background-color: rgb(224, 232, 234); color: rgb(75, 98, 103); text-align: center; font-weight: bold }
table.gcustomsettings-settings-tab-headers td table { border: none !important }
table.gcustomsettings-settings-tab-headers td table td { border: none !important; padding: 2px !important }

div.gcustomsettings-settings-option { }
div.gcustomsettings-settings-option table.gcustomsettings-settings-option-table { border: 1px solid rgb(208, 215, 216); width: 100%; border-collapse: collapse; margin: 7px 0 !important; background-color: rgb(240,244,244) !important }
div.gcustomsettings-settings-option table.gcustomsettings-settings-option-table td { border: 1px solid rgb(208, 215, 216); padding: 3px }
div.gcustomsettings-settings-option table.gcustomsettings-settings-option-table thead td { background-color: rgb(224, 232, 234); color: rgb(75, 98, 103); text-align: center }
div.gcustomsettings-settings-option table.gcustomsettings-settings-option-table td table { border: none }
div.gcustomsettings-settings-option table.gcustomsettings-settings-option-table td table td { border: none; padding: 2px }

div.gcustomsettings-settings-option-add { padding: 5px 0 15px 0 }

div.gcustomsettings-settings-selectvalue { }

table.gcustomsettings-settings-selectvalue-table { width: 100%; border-collapse: collapse !important; background: none }
table.gcustomsettings-settings-selectvalue-table td { text-align: center !important; padding: 2px !important; border: none }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-value { width: 70px }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-value input { width: 60px; margin: 0 }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-name {  }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-name table { margin: 0 auto; width: 100% }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-name table td { padding: 2px !important; border: none !important }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-name table td.gcustomsettings-settings-selectvalue-col-name-lang-id { width: 1% }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-name input { width: 100%; margin: 0 }
table.gcustomsettings-settings-selectvalue-table td.gcustomsettings-settings-selectvalue-col-remove { width: 18px }

div.gcustomsettings-settings-option input[type=text] {
	-webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
    -moz-box-sizing: border-box;    /* Firefox, other Gecko */
    box-sizing: border-box;
}

div.gcustomsettings-settings-selectvalue-add { padding: 1px 3px 6px 2px }

a.gcustomsettings-settings-tab-add { text-decoration: none; color: black }
a.gcustomsettings-settings-tab-add img { vertical-align: middle }
a.gcustomsettings-settings-tab-add span { text-decoration: underline }

a.gcustomsettings-settings-tab-remove { text-decoration: none; color: black }
a.gcustomsettings-settings-tab-remove img { vertical-align: middle }
a.gcustomsettings-settings-tab-remove span { text-decoration: underline }

div.gcustomsettings-settings-option-add a { text-decoration: none; color: black }
div.gcustomsettings-settings-option-add a img { vertical-align: middle }
div.gcustomsettings-settings-option-add a span { text-decoration: underline }

div.gcustomsettings-settings-selectvalue-add a { text-decoration: none; color: black }
div.gcustomsettings-settings-selectvalue-add a img { vertical-align: middle }
div.gcustomsettings-settings-selectvalue-add a span { text-decoration: underline }

a.gcustomsettings-settings-option-remove { margin: 3px }

table.gcustomsettings-module-options { border: none }
table.gcustomsettings-module-options td { border: none; padding: 2px !important }

/* this is strict copy of styles from /bitrix/modules/grain.links/include.php */
#grain_customsettings_data_source_params { padding-top: 8px }
table.grain-links-dsparams { border: 1px solid rgb(208, 215, 216); border-collapse: collapse; width: 100% !important; margin: 0 0 8px 0 !important }
table.grain-links-dsparams td { border: 1px solid rgb(208, 215, 216); padding: 5px 5px !important; background-color: white }
table.grain-links-dsparams td.grain-links-dsparams-col-name { text-align: right }
table.grain-links-dsparams td.grain-links-dsparams-col-input { text-align: left }
table.grain-links-dsparams .grain-links-dsparams-multiple { width: 190px }
table.grain-links-dsparams .grain-links-dsparams-multiple input { display: block }

</style>

<!--[if IE]>
<style>
div.gcustomsettings-settings-tab, div.gcustomsettings-settings-tab * { zoom: 1 }
</style>
<![endif]-->


<script type="text/javascript">

function gksAddNewTab()
{

	gks_tabs_counter++;

	gks_tabs_quantity++;

	var newGksTab_id = gks_tabs_counter;

	var obGksTab = document.getElementById('new_gks_tab_template');
	var obParent = document.getElementById('gks_tabs_container');
	
	if (obGksTab && obParent)
	{
	
		gks_options[newGksTab_id] = 0;
		gks_selectvalues[newGksTab_id]=[];
	
		var newGksTab = obGksTab.cloneNode(true);
		newGksTab.style.display = '';
		newGksTab.id = 'gks_tab_' + newGksTab_id;

		var newGksTab_html = newGksTab.innerHTML;
		newGksTab_html = newGksTab_html.replace(/--GKS_TAB_ID--/g,newGksTab_id);
		newGksTab.innerHTML = newGksTab_html;
		
		obParent.appendChild(newGksTab);
	}
	
	if(gks_tabs_quantity>=gks_tabs_max) document.getElementById('gks_tab_add').style.display = "none";


	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksAddNewTab<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tabs_counter: " + gks_tabs_counter + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tabs_quantity: " + gks_tabs_quantity + "<br />";

	<?endif?>

	
}

function gksRemoveTab(gks_tab_id)
{

	var obGksTab = document.getElementById('gks_tab_' + gks_tab_id);
	if (obGksTab) obGksTab.parentNode.removeChild(obGksTab);
	gks_tabs_quantity--;
	if(gks_tabs_quantity<gks_tabs_max) document.getElementById('gks_tab_add').style.display = "";

	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksRemoveTab<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tabs_counter: " + gks_tabs_counter + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tabs_quantity: " + gks_tabs_quantity + "<br />";

	<?endif?>

}


function gksAddOption(gks_tab_id)
{

	gks_options[gks_tab_id]++;

	var newGksOption_id = gks_options[gks_tab_id];

	var obGksOption = document.getElementById('new_gks_option_template');
	var obParent = document.getElementById('gks_tab_options_'+gks_tab_id);
	
	if (obGksOption && obParent)
	{
		var newGksOption = obGksOption.cloneNode(true);
		newGksOption.style.display = '';
		newGksOption.id = 'gks_option_' + gks_tab_id + '_' + newGksOption_id;

		var newGksOption_html = newGksOption.innerHTML;
		newGksOption_html = newGksOption_html.replace(/--GKS_TAB_ID--/g,gks_tab_id);
		newGksOption_html = newGksOption_html.replace(/--GKS_OPTION_ID--/g,newGksOption_id);
		newGksOption.innerHTML = newGksOption_html;
		
		obParent.appendChild(newGksOption);
	}


	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksAddOption<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tab_id: " + gks_tab_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_options[gks_tab_id]: " + gks_options[gks_tab_id] + "<br />";

	<?endif?>

}


function gksRemoveOption(gks_tab_id,gks_option_id)
{

	var obGksOption = document.getElementById('gks_option_' + gks_tab_id + '_' + gks_option_id);
	if (obGksOption) obGksOption.parentNode.removeChild(obGksOption);

	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksRemoveOption<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tab_id: " + gks_tab_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_option_id: " + gks_option_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_options[gks_tab_id]: " + gks_options[gks_tab_id] + "<br />";

	<?endif?>

}

function gksOptionChangeType(gks_tab_id,gks_option_id,obSel)
{

	var option_type = obSel.options[obSel.selectedIndex].value;

	if(option_type=="select") gks_selectvalues[gks_tab_id][gks_option_id] = 0;

	var obGksOptionCustom = document.getElementById('new_gks_option_custom_template_'+option_type);
	var obContainer = document.getElementById('gks_tab_option_custom_'+gks_tab_id+'_'+gks_option_id);
	
	if (obGksOptionCustom && obContainer)
	{
		var newGksOptionCustom = obGksOptionCustom.cloneNode(true);
		var newGksOptionCustom_html = newGksOptionCustom.innerHTML;

		newGksOptionCustom_html = newGksOptionCustom_html.replace(/--GKS_TAB_ID--/g,gks_tab_id);
		newGksOptionCustom_html = newGksOptionCustom_html.replace(/--GKS_OPTION_ID--/g,gks_option_id);
		
		obContainer.innerHTML = newGksOptionCustom_html;
	}


	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksOptionChangeType<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tab_id: " + gks_tab_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_option_id: " + gks_option_id + "<br />";

	<?endif?>

}

function gksAddSelectValue(gks_tab_id,gks_option_id) {

	gks_selectvalues[gks_tab_id][gks_option_id]++;

	var newGksSelectvalue_id = gks_selectvalues[gks_tab_id][gks_option_id];

	var obGksSelectvalue = document.getElementById('new_gks_selectvalue_template');
	var obParent = document.getElementById('gks_option_selectvalues_'+gks_tab_id+'_'+gks_option_id);
	
	if (obGksSelectvalue && obParent)
	{
		var newGksSelectvalue = obGksSelectvalue.cloneNode(true);
		newGksSelectvalue.style.display = '';
		newGksSelectvalue.id = 'gks_selectvalue_' + gks_tab_id + '_' + gks_option_id + '_' + newGksSelectvalue_id;

		var newGksSelectvalue_html = newGksSelectvalue.innerHTML;
		newGksSelectvalue_html = newGksSelectvalue_html.replace(/--GKS_TAB_ID--/g,gks_tab_id);
		newGksSelectvalue_html = newGksSelectvalue_html.replace(/--GKS_OPTION_ID--/g,gks_option_id);
		newGksSelectvalue_html = newGksSelectvalue_html.replace(/--GKS_SELECTVALUE_ID--/g,newGksSelectvalue_id);
		newGksSelectvalue.innerHTML = newGksSelectvalue_html;
		
		obParent.appendChild(newGksSelectvalue);
	}


	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksAddSelectValue<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tab_id: " + gks_tab_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_option_id: " + gks_option_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_selectvalues[gks_tab_id][gks_option_id]: " + gks_selectvalues[gks_tab_id][gks_option_id] + "<br />";

	<?endif?>



}

function gksRemoveSelectValue(gks_tab_id,gks_option_id,gks_selectvalue_id) {

	var obGksSelectvalue = document.getElementById('gks_selectvalue_' + gks_tab_id + '_' + gks_option_id + '_' + gks_selectvalue_id);
	if (obGksSelectvalue) obGksSelectvalue.parentNode.removeChild(obGksSelectvalue);

	<?if($gks_test_mode):?>

	document.getElementById("gks_test_data").innerHTML = "gksRemoveSelectValue<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_tab_id: " + gks_tab_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_option_id: " + gks_option_id + "<br />";
	document.getElementById("gks_test_data").innerHTML += "gks_selectvalue_id: " + gks_selectvalue_id + "<br />";

	<?endif?>

}

<?if(CGrain_CustomSettingsOptions::IsLinksInstalled()):?>

function gksShowLinksDataSourcePopup(name_prefix,hidden_inputs_container_id) {

	var name_prefix_tmp = 'GKS_TEMP_DSPARAMS';

	<?
	$popup = "";

	$module_mode=CModule::IncludeModuleEx("grain.links");

	if($module_mode==MODULE_DEMO_EXPIRED) {

		$popup .= GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_MODULE_TRIAL_EXPIRED");

	} else {
	
		$arDataSourceList = CGrain_LinksAdminTools::GetDataSourceList(true);

		$popup .= '<div style="text-align: center" id="grain_customsettings_data_source_window">\n';
		$popup .= GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_DATA_SOURCE").': ';
		$popup .= '<select name="--NAME--PREFIX--[DATA_SOURCE]" onchange="window.grain_customsettings_dsparams_refresh(true,\\\'--NAME--PREFIX--\\\');" id="grain_customsettings_data_source_select">\n';
		$popup .= '\t<option value=""></option>\n';
		foreach($arDataSourceList as $k=>$v):
			$popup .= '\t<option value="'.$k.'">'.$v.'</option>\n';
		endforeach;
		$popup .= '</select>\n';
		$popup .= '<div id="grain_customsettings_data_source_params">\n';
		$popup .= '</div>';
		$popup .= '</div>';
	
	}

	?>
	
	var obHiddenInputsContainer = document.getElementById(hidden_inputs_container_id);
	
	if(typeof(window.grain_customsettings_dsparams_popup)!="undefined" && window.grain_customsettings_dsparams_popup.DIV)
		window.grain_customsettings_dsparams_popup.DIV.parentNode.removeChild(window.grain_customsettings_dsparams_popup.DIV);

	window.grain_customsettings_dsparams_popup = new BX.CAdminDialog({
	    'title': '<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_POPUP_HEADER")?>',
	    'content': '',
	    'draggable': true,
	    'resizable': true,
	    'buttons': []
	});
	

	var btn_save = {
	    title: BX.message('JS_CORE_WINDOW_SAVE'),
	    id: 'savebtn',
	    name: 'savebtn',
	    className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
	    action: function () {
	    	
	    	var obDSParamsContainer = document.getElementById('grain_customsettings_data_source_window');
	    	
	    	var ar = window.grain_customsettings_dsparams_getvalues(obDSParamsContainer,true);
	    	obHiddenInputsContainer.innerHTML = "";
	    	
	    	for(var i in ar) {
	    		var newInput = document.createElement("input");
	    		newInput.setAttribute("type", "hidden");
	    		newInput.setAttribute("name", ar[i].NAME.replace(name_prefix_tmp,name_prefix));
	    		newInput.setAttribute("value", ar[i].VALUE);
	    		obHiddenInputsContainer.appendChild(newInput);
	    	}
	    	
	    	this.parentWindow.Close();
	    	
	    }
	};
	
	window.grain_customsettings_dsparams_popup.ClearButtons();
	window.grain_customsettings_dsparams_popup.SetButtons([btn_save, BX.CAdminDialog.btnCancel]);
	window.grain_customsettings_dsparams_popup.SetContent('<?=$popup?>'.replace(/--NAME--PREFIX--/g,name_prefix_tmp));
	
	//var wait = BX.showWait();
	//window.grain_customsettings_dsparams_popup.DIV.style.zIndex+=10;
	//window.grain_customsettings_dsparams_popup.CreateOverlay(parseInt(window.grain_customsettings_dsparams_popup.DIV.style.zIndex)+10/* parseInt(BX.style(wait, 'z-index'))-1 */);
	//window.grain_customsettings_dsparams_popup.OVERLAY.style.display = 'block';
	//window.grain_customsettings_dsparams_popup.OVERLAY.className = 'bx-core-dialog-overlay';

	var ar = window.grain_customsettings_dsparams_getvalues(obHiddenInputsContainer,false);
    var q = [];
    var data_source = '';
    for(var i=0;i<ar.length;i++) {
    	var name_tmp = ar[i].NAME.replace(name_prefix,name_prefix_tmp);
		if(name_prefix_tmp+'[DATA_SOURCE]'==name_tmp)
			data_source = ar[i].VALUE;
		else
	    	q.push(name_tmp+"="+ar[i].VALUE);
    }

    var obContainer = document.getElementById("grain_customsettings_data_source_params");
    if(!obContainer) return;
   	obContainer.innerHTML = "";

    var obSelect = document.getElementById("grain_customsettings_data_source_select");
    if(!obSelect) return;
	obSelect.value = data_source;

    var url = '<?=BX_ROOT?>/admin/grain_links_data_source_params.php?lang=<?=LANGUAGE_ID?>&NAME_PREFIX='+name_prefix_tmp+'&DATA_SOURCE='+data_source+'&MODULE_JS_PREFIX=grain_customsettings';

    BX.ajax.post(url, q.join("&"), function(result){
    	obContainer.innerHTML = result;
   		//BX.closeWait(null, wait);
		window.grain_customsettings_dsparams_popup.Show();
    });

}

// this is strict copy from /bitrix/modules/grain.links/include.php
window.grain_customsettings_dsparams_addvalues_change = function(obSelect) {
    var obAddValueInput = document.getElementById("grain_customsettings_dspadd_"+obSelect.name);
    if(!obAddValueInput) return;
    if(obSelect.value.length) {
    	obAddValueInput.disabled = true;
    	obAddValueInput.removeAttribute('name');
    	obAddValueInput.value="";
    } else {
    	obAddValueInput.disabled = false;
    	obAddValueInput.setAttribute('name',obSelect.name);
    }
};
window.grain_customsettings_dsparams_addvalues_add = function(obButton)
{
    var element = obButton.parentNode.getElementsByTagName('input');
    var target_element = false;
    if (element && element.length > 0 && element[0])
    {
    	for(var i=0;i<element.length;i++)
    		if(element[i].type=="text")
    			target_element = element[i];
        target_element.parentNode.appendChild(target_element.cloneNode(true));
    }
};
window.grain_customsettings_dsparams_refresh = function(bReset,name_prefix) {

    var obContainer = document.getElementById("grain_customsettings_data_source_params");
    if(!obContainer) return;

    var obSelect = document.getElementById("grain_customsettings_data_source_select");
    if(!obSelect) return;
    var data_source = obSelect.value;
    if(!data_source) {
    	obContainer.innerHTML = "";
    	return;
    }

    var url = '<?=BX_ROOT?>/admin/grain_links_data_source_params.php?lang=<?=LANGUAGE_ID?>&NAME_PREFIX='+name_prefix+'&DATA_SOURCE='+data_source+'&MODULE_JS_PREFIX=grain_customsettings';

    var ar = [];
    if(!bReset)
    	ar = window.grain_customsettings_dsparams_getvalues(obContainer,true);

    var q = [];
    for(var i=0;i<ar.length;i++) 
    	q.push(ar[i].NAME+"="+ar[i].VALUE);
    
    BX.ajax.post(url, q.join("&"), function(result){
    	obContainer.innerHTML = result;
    });
    
};
window.grain_customsettings_dsparams_getvalues = function(obContainer,bDisable) {

    var ar = [];
    
    var arElements = obContainer.getElementsByTagName("*");
    for(var i=0;i<arElements.length;i++) {
        // see https://code.google.com/p/form-serialize/
        if (arElements[i].name === "") {
        	continue;
        }
        switch (arElements[i].nodeName) {
        case 'INPUT':
        	switch (arElements[i].type) {
        	case 'text':
        	case 'hidden':
        	case 'password':
        	case 'button':
        	case 'reset':
        	case 'submit':
        		ar.push({"NAME":arElements[i].name,"VALUE":arElements[i].value});
        		break;
        	case 'checkbox':
        	case 'radio':
        		if (arElements[i].checked) {
        			ar.push({"NAME":arElements[i].name,"VALUE":arElements[i].value});
        		}						
        		break;
        	case 'file':
        		break;
        	}
        	break;			 
        case 'TEXTAREA':
        	ar.push({"NAME":arElements[i].name,"VALUE":arElements[i].value});
        	break;
        case 'SELECT':
        	switch (arElements[i].type) {
        	case 'select-one':
        		ar.push({"NAME":arElements[i].name,"VALUE":arElements[i].value});
        		break;
        	case 'select-multiple':
        		for (j = arElements[i].options.length - 1; j >= 0; j = j - 1) {
        			if (arElements[i].options[j].selected) {
        				ar.push({"NAME":arElements[i].name,"VALUE":arElements[i].options[j].value});
        			}
        		}
        		break;
        	}
        	break;
        case 'BUTTON':
        	switch (arElements[i].type) {
        	case 'reset':
        	case 'submit':
        	case 'button':
        		ar.push({"NAME":arElements[i].name,"VALUE":arElements[i].options[j].value});
        		break;
        	}
        	break;
        }
		
		if(bDisable) {
	        // disable inputs to prevent change
	        switch (arElements[i].nodeName) {
	        case 'INPUT':
	        case 'TEXTAREA':
	        case 'SELECT':
	        case 'BUTTON':
	        	arElements[i].disabled = true;
	        break;
	        }
        }
        
    }

    return ar;

};


<?endif?>

</script>


<script type="text/javascript">

gks_tabs_quantity=<?=count($arCustomSettings)?>;

gks_tabs_counter=<?=count($arCustomSettings)?>;

gks_tabs_max = 100;

gks_options=[];

gks_selectvalues=[];

</script>	


<div id="gks_tabs_container">



<?
$count=1;
foreach($arCustomSettings as $tab_id=>$tab_data) {
	echo CGrain_CustomSettingsOptions::ShowTab($count,$tab_data,true);
	$count++;	
}

?>
</div>



	
<a id="gks_tab_add" class="gcustomsettings-settings-tab-add" href="#" onclick="gksAddNewTab(); return false"><img src="/bitrix/images/grain.customsettings/gcustomsettings_options_tab_icon_add.gif" width="30" height="15" border="0" />&nbsp;&nbsp;<span><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_ADD_TAB")?></span></a>

			
		</td></tr>
	
	<?
	} else {
	?>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU")?>
			</td>
			<td valign="top" width="50%">
				<select name="SETTINGS[PARENT_MENU]">
					<option value="global_menu_content"<?if($arCustomPage["PARENT_MENU"]=="global_menu_content"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_content")?></option>
					<option value="global_menu_services"<?if($arCustomPage["PARENT_MENU"]=="global_menu_services"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_services")?></option>
					<option value="global_menu_store"<?if($arCustomPage["PARENT_MENU"]=="global_menu_store"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_store")?></option>
					<option value="global_menu_statistics"<?if($arCustomPage["PARENT_MENU"]=="global_menu_statistics"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_statistics")?></option>
					<option value="global_menu_settings"<?if($arCustomPage["PARENT_MENU"]=="global_menu_settings"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_settings")?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_SORT")?>
			</td>
			<td valign="top" width="50%">
				<input type="text" name="SETTINGS[SORT]" size="4" value="<?=htmlspecialchars($arCustomPage["SORT"])?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_TEXT")?>
			</td>
			<td valign="top" width="50%">
				<table cellspacing="0" cellpadding="0" class="gcustomsettings-module-options">
					<?foreach($arLang as $lang_id => $lang):?>
						<tr>
							<td><?=$lang_id?></td>
							<td><input type="text" name="SETTINGS[LANG][<?=$lang_id?>][MENU_TEXT]" size="30" value="<?=htmlspecialchars($arCustomPage["LANG"][$lang_id]["MENU_TEXT"])?>" /></td>
						</tr>
					<?endforeach;?>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_MENU_TITLE")?>
			</td>
			<td valign="top" width="50%">
				<table cellspacing="0" cellpadding="0" class="gcustomsettings-module-options">
					<?foreach($arLang as $lang_id => $lang):?>
						<tr>
							<td><?=$lang_id?></td>
							<td><input type="text" name="SETTINGS[LANG][<?=$lang_id?>][MENU_TITLE]" size="30" value="<?=htmlspecialchars($arCustomPage["LANG"][$lang_id]["MENU_TITLE"])?>" /></td>
						</tr>
					<?endforeach;?>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_MENU_PARENT_PAGE_TITLE")?>
			</td>
			<td valign="top" width="50%">
				<table cellspacing="0" cellpadding="0" class="gcustomsettings-module-options">
					<?foreach($arLang as $lang_id => $lang):?>
						<tr>
							<td><?=$lang_id?></td>
							<td><input type="text" name="SETTINGS[LANG][<?=$lang_id?>][PAGE_TITLE]" size="30" value="<?=htmlspecialchars($arCustomPage["LANG"][$lang_id]["PAGE_TITLE"])?>" /></td>
						</tr>
					<?endforeach;?>
				</table>
			</td>
		</tr>

	<?
	}
endforeach;?>


<?$tabControl->Buttons();?>
<input type="submit" <?if ($GKS_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="submit" <?if ($GKS_RIGHT<"W") echo "disabled" ?> name="Apply" value="<?echo GetMessage("MAIN_APPLY")?>">
<?if(strlen($_REQUEST["back_url_settings"])>0):?>
	<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
	<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
<?endif?>
	<input type="submit" <?if ($GKS_RIGHT<"W") echo "disabled" ?> name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?
if(strlen($redirect_to_url)>0) LocalRedirect($redirect_to_url);
?>

<div style="display: none" id="new_gks_tab_template"><?=CGrain_CustomSettingsOptions::ShowTab("--GKS_TAB_ID--",Array(),false)?></div>
<div style="display: none" id="new_gks_option_template"><?=CGrain_CustomSettingsOptions::ShowOption("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"text","SIZE"=>"20"),false)?></div>
<div style="display: none" id="new_gks_option_custom_template_text"><?=CGrain_CustomSettingsOptions::ShowOptionCustom("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"text","SIZE"=>"20"),false)?></div>
<div style="display: none" id="new_gks_option_custom_template_textarea"><?=CGrain_CustomSettingsOptions::ShowOptionCustom("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"textarea","COLS"=>"20","ROWS"=>"5"),false)?></div>
<div style="display: none" id="new_gks_option_custom_template_checkbox"><?=CGrain_CustomSettingsOptions::ShowOptionCustom("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"checkbox"),false)?></div>
<div style="display: none" id="new_gks_option_custom_template_select"><?=CGrain_CustomSettingsOptions::ShowOptionCustom("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"select","VALUES"=>Array()),false)?></div>
<div style="display: none" id="new_gks_option_custom_template_date"><?=CGrain_CustomSettingsOptions::ShowOptionCustom("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"date"),false)?></div>
<div style="display: none" id="new_gks_option_custom_template_link"><?=CGrain_CustomSettingsOptions::ShowOptionCustom("--GKS_TAB_ID--","--GKS_OPTION_ID--",Array("TYPE"=>"link"),false)?></div>

<div style="display: none" id="new_gks_selectvalue_template"><?=CGrain_CustomSettingsOptions::ShowOptionSelectvalue("--GKS_TAB_ID--","--GKS_OPTION_ID--","--GKS_SELECTVALUE_ID--",Array(),false)?></div>


<?if($gks_test_mode):?>

<div id="gks_test_data"></div>

<?endif?>

<?echo BeginNote();?>
	<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_NOTE")?>
<?echo EndNote();?>

<?echo BeginNote();?>
	<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_NOTE2")?>
<?echo EndNote();?>

<?elseif($module_mode==MODULE_DEMO_EXPIRED):?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/install/trial/expired.php");?>

<?endif;?>
