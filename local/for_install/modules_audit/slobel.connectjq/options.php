<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
if(!$USER->IsAdmin()) return;

$module_id = "slobel_connectjs";
$strWarning = "";

$arAllOptions = array(
		"main" => Array(
                Array("slobel-compress", GetMessage("SLOBEL_COMPRESS")." ", '0', array("selectbox", array('uncompressed','minified'))),
				Array("slobel-file", GetMessage("SLOBEL_FILE")." ", "Y", array("checkbox")),
        ),
        "slobel-jquery" => Array(
                Array("slobel-jquery", GetMessage("SLOBEL_CONNECT")." ", "Y", array("checkbox")),
        		Array("slobel-jquery-ver", GetMessage("SLOBEL_VERSION")." ", '2.0.3', array("selectbox", array('2.0.3','2.0.2','2.0.1','2.0.0','1.10.2','1.10.1','1.10.0','1.9.1','1.9.0','1.8.3','1.8.2','1.8.1','1.8.0','1.7.2','1.7.1','1.7.0','1.6.4','1.6.3','1.6.2','1.6.1','1.6.0','1.5.2','1.5.1','1.5.0','1.4.4','1.4.3','1.4.2','1.4.1','1.4.0','1.3.2','1.3.1','1.3.0','1.2.6','1.2.5','1.2.4','1.2.3','1.2.2','1.2.1','1.2.0'))),
        ),
		"slobel-jquery-migrate" => Array(
				Array("slobel-jquery-migrate", GetMessage("SLOBEL_CONNECT")." ", 'N', array("checkbox")),
				Array("slobel-jquery-migrate-ver", GetMessage("SLOBEL_VERSION")." ", '1.2.1', array("selectbox", array('1.2.1','1.2.0','1.1.1','1.1.0','1.0.0'))),
				
		),
		"slobel-jquery-ui" => Array(
				Array("slobel-jquery-ui", GetMessage("SLOBEL_CONNECT")." ", "N", array("checkbox")),
				Array("slobel-jquery-ui-ver", GetMessage("SLOBEL_VERSION")." ", '1.10.3', array("selectbox", array('1.10.3','1.10.2','1.10.1','1.10.0','1.9.2','1.9.1','1.9.0','1.8.24','1.8.23','1.8.22','1.8.21','1.8.20','1.8.19','1.8.18','1.8.17','1.8.16','1.8.15','1.8.14','1.8.13','1.8.12','1.8.11','1.8.10','1.8.9','1.8.8','1.8.7','1.8.6','1.8.5','1.8.4','1.8.3','1.8.2','1.8.1','1.8.0','1.7.3','1.7.2','1.7.1','1.7.0'))),
				Array("slobel-jquery-ui-theme", GetMessage("SLOBEL_THEME")." ", "-", array("selectbox", array('-','black-tie','blitzer','cupertino','dark-hive','dot-luv','eggplant','excite-bike','flick','hot-sneaks','humanity','le-frog','mint-choc','overcast','pepper-grinder','redmond','smoothness','south-street','start','sunny','swanky-purse','trontastic','ui-darkness','ui-lightness','vader'))),
        ),
		"slobel-jquery-mobile" => Array(
				Array("slobel-jquery-mobile", GetMessage("SLOBEL_CONNECT")." ", "N", array("checkbox")),
				Array("slobel-jquery-mobile-ver", GetMessage("SLOBEL_VERSION")." ", '1.4.0', array("selectbox", array('1.4.0','1.3.2','1.3.1','1.3.0','1.2.1','1.2.0','1.1.2','1.1.1','1.1.0','1.0.1','1.0.0'))),
				Array("slobel-jquery-mobile-theme", GetMessage("SLOBEL_THEME")." ", "-", array("selectbox", array('-','general','structure'))),
		),
		"slobel-jquery-touch-punch" => Array(
				Array("slobel-jquery-touch-punch", GetMessage("SLOBEL_CONNECT")." ", "N", array("checkbox")),
		),
		"slobel-jquery-color" => Array(
				Array("slobel-jquery-color", GetMessage("SLOBEL_CONNECT")." ", "N", array("checkbox")),
				Array("slobel-jquery-color-ver", GetMessage("SLOBEL_VERSION")." ", '2.1.2', array("selectbox", array('2.1.2','2.1.1','2.1.0','2.0.0'))),
				Array("slobel-jquery-color-svg", GetMessage("SLOBEL_COLOR_CSV")." ", 'N', array("checkbox")),
		),
		"slobel-qunit" => Array(
				Array("slobel-qunit", GetMessage("SLOBEL_CONNECT")." ", "N", array("checkbox")),
				Array("slobel-qunit-ver", GetMessage("SLOBEL_VERSION")." ", '1.12.0', array("selectbox", array('1.12.0','1.11.0','1.10.0','1.9.0','1.5.0','1.2.0','1.1.0','1.0.0'))),
				Array("slobel-qunit-theme", GetMessage("SLOBEL_THEME")." ", "-", array("selectbox", array('-','general'))),
		)
);
$aTabs = array(
        array("DIV" => "settings", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "access", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);

//Restore defaults
if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
        COption::RemoveOption("slobel_connectjs");
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($arParams)
{
        foreach($arParams as $Option)
        {
                 __AdmSettingsDrawRow("slobel_connectjs", $Option);
        }
}

//Save options
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
        if(strlen($RestoreDefaults)>0)
        {
                COption::RemoveOption("slobel_connectjs");
        }
        else
        {
                foreach($arAllOptions as $aOptGroup)
                {
                        foreach($aOptGroup as $option)
                        {
                                __AdmSettingsSaveOption($module_id, $option);
                        }
                }
        }
        if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
                LocalRedirect($_REQUEST["back_url_settings"]);
        else
                LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
<?

foreach($arAllOptions as $optionsKey => $optionValue){
	foreach($optionValue as $optionsKeyTo => $optionValueTo){
		if($optionValueTo[3][1]){
		foreach($optionValueTo[3][1] as $optionsKeyTre => $optionValueTre){
			switch ($optionValueTre){
				case '-': $val = GetMessage("SLOBEL_NO_ITEMS"); break;
				case 'uncompressed': $val = GetMessage("SLOBEL_uncompressed"); break;
				case 'minified': $val = GetMessage("SLOBEL_minified"); break;
				case 'general': $val = GetMessage("SLOBEL_general"); break;
				case 'structure': $val = GetMessage("SLOBEL_structure"); break;
				default: $val = $optionValueTre; break;
			}
			$arAllOptions[$optionsKey][$optionsKeyTo][3][1][$optionValueTre]=$val;
			unset($arAllOptions[$optionsKey][$optionsKeyTo][3][1][$optionsKeyTre]);
		}
		}
	}
} 
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<?ShowParamsHTMLByArray($arAllOptions["main"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_JQ")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-jquery"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_JQ_MIG")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-jquery-migrate"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_JQ_UI")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-jquery-ui"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_JQ_M")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-jquery-mobile"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_JQ_UI_TOUCH")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-jquery-touch-punch"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_JQ_C")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-jquery-color"])?>

<tr class="heading"><td colspan="2"><?=GetMessage("SLOBEL_QU")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["slobel-qunit"])?>
<?
$tabControl->BeginNextTab();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
$tabControl->Buttons();
?>
<script language="JavaScript">
function RestoreDefaults()
{
        if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<div align="left">
        <input type="hidden" name="Update" value="Y">
        <input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
        <input type="button" <?if(!$USER->IsAdmin())echo " disabled ";?>  type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
</div>
<?$tabControl->End();?>
<?=bitrix_sessid_post();?>
</form>
