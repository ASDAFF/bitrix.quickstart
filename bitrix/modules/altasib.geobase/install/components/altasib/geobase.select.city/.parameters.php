<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '0' || $incMod == '3')
	return false;
?>
<?if($_REQUEST['bxsender'] != 'fileman_html_editor'):?>
<div style="background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;"><div style="background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff">
		<a href="http://www.is-market.ru?param=cl" target="_blank"><img src="/bitrix/components/altasib/geobase.select.city/images/is-market.gif" style="float: left; margin-right: 15px;" border="0" /></a>
		<div style="margin: 13px 0px 0px 0px">
				<a href="http://www.is-market.ru?param=cl" target="_blank" style="color: #fff; font-size: 10px; text-decoration: none"><?=GetMessage("ALTASIB_IS")?></a>
		</div>
</div></div>
<? if ($incMod == '0') ShowError(GetMessage("ALTASIB_GEOBASE_MODULE_NOT_FOUND"));
elseif ($incMod == '3') ShowError(GetMessage("ALTASIB_GEOBASE_DEMO_EXPIRED"));
elseif ($incMod == '2') ShowNote(GetMessage("ALTASIB_GEOBASE_DEMO"));?>
<? endif;

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => Array(
	),
);

$arComponentParameters["PARAMETERS"]["RIGHT_ENABLE"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("ALTASIB_GEOBASE_SELECT_CITY_RIGHT_ENABLE").(isset($arCurrentValues["SPAN_RIGHT"]) && $arCurrentValues["SPAN_RIGHT"] != GetMessage("ALTASIB_GEOBASE_SELECT_CITY_RIGHT_TEXT") ? " (\"".(SITE_CHARSET == "windows-1251" ? iconv("UTF-8", "windows-1251", $arCurrentValues["SPAN_RIGHT"]) : $arCurrentValues["SPAN_RIGHT"])."\")" : "").GetMessage("ALTASIB_GEOBASE_SELECT_CITY_RIGHT_EN_ATLD"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => 'N',
	"SORT" => 120,
);
$arComponentParameters["PARAMETERS"]["SPAN_LEFT"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("ALTASIB_GEOBASE_SELECT_CITY_LEFT"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("ALTASIB_GEOBASE_SELECT_CITY_LEFT_TEXT"),
	"SORT" => 120,
);
$arComponentParameters["PARAMETERS"]["SPAN_RIGHT"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("ALTASIB_GEOBASE_SELECT_CITY_RIGHT"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("ALTASIB_GEOBASE_SELECT_CITY_RIGHT_TEXT"),
	"SORT" => 120,
	"REFRESH" => "Y",
);
?>