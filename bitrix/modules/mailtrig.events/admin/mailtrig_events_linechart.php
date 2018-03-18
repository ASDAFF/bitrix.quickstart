<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mailtrig.events/include.php");

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

CModule::IncludeModule("mailtrig.events");

$obClient = new CMailTrigClient;

$sTableID = "mailtrig_events_campaigns";
$lAdmin = new CAdminList($sTableID);

$id = intval($_GET["id"]);

$arError = array();

if($id <= 0)
	$arError[] = GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_EMPTY_ID");

/*if($arLineCharts["status"] != "200")
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>
	<?CAdminMessage::ShowMessage("Some error");?>
	<a href="<?echo htmlspecialcharsbx("iblock_admin.php?lang=".LANGUAGE_ID."&type=".urlencode($_REQUEST["type"]))?>"><?echo GetMessage("IBLOCK_BACK_TO_ADMIN")?></a>
	<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}*/

$arCampaigns = $obClient->getCampaigns($id);

if($arCampaigns["status"] != "200")
	$arError[] = GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_RESPONSE") . $arCampaigns["error_message"];

foreach($arCampaigns["data"] as $arType)
	$sName = $arType[0]["name"];

$APPLICATION->SetTitle(GetMessage("MAILTRIG_EVENTS_DELIVERY_PAGE_LINECHART") . $sName);

if($_GET["datefrom"]) {
	$datefrom = $_GET["datefrom"];
	$datefromConvert = ConvertDateTime($_GET["datefrom"], "YYYY-MM-DD");
}
if($_GET["dateto"]) {
	$dateto = $_GET["dateto"];
	$datetoConvert = ConvertDateTime($_GET["dateto"], "YYYY-MM-DD");
}


$arLineCharts = $obClient->getLinechart($id, $datefromConvert, $datetoConvert);

if($arLineCharts["status"] != "200")
	$arError[] = GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_RESPONSE") . $arLineCharts["error_message"];

if(!empty($arError))
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>
	<?CAdminMessage::ShowMessage(implode("<br />", $arError));?>
	<a href="mailtrig_events_campaigns.php?lang=<?=LANG?>"><?=GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_BACK_TO_CAMPAIGNS")?></a>
	<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

/*
$arFilter = array(
	"datefrom",
	"dateto",
);
$lAdmin->InitFilter($arFilter);
*/

$lAdmin->AddHeaders(
	array(
		array(
			"id" => "time",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_TIME"),
			"default" => true
		),
		array(
			"id" => "delivered",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_DELIVERED"),
			"default" => true
		),
		array(
			"id" => "total",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_TOTAL"),
			"default" => true
		),
		array(
			"id" => "junk",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_JUNK"),
			"default" => true
		),
		array(
			"id" => "opened",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_OPENED"),
			"default" => true
		),
		array(
			"id" => "clicked",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_CLICKED"),
			"default" => true
		),
		array(
			"id" => "bounces",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_BOUNCES"),
			"default" => true
		),
		array(
			"id" => "unsubscribed",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_UNSUBSCRIBED"),
			"default" => true
		),
		array(
			"id" => "bounces_p",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_BOUNCES_P"),
			"default" => true
		),
		array(
			"id" => "opened_p",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_OPENED_P"),
			"default" => true
		),
		array(
			"id" => "clicked_p",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_CLICKED_P"),
			"default" => true
		),
		array(
			"id" => "junk_p",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_JUNK_P"),
			"default" => true
		),
		array(
			"id" => "unsubscribed_p",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_LINECHART_UNSUBSCRIBED_P"),
			"default" => true
		),
	)
);

if($arLineCharts["status"] == "200")
{
	$arLineCharts["data"] = array_reverse($arLineCharts["data"]); // to sort by date

	foreach($arLineCharts["data"] as $arValue)
	{
		$row =& $lAdmin->AddRow($arValue["id"], $arValue);
	}

	$aContext = array();

	$sLogin = COption::GetOptionString("mailtrig.events", "login");
	$sPassword = COption::GetOptionString("mailtrig.events", "password");
	if(!empty($sLogin) && !empty($sPassword))
	{
		$sAuthUrl = $obClient->getAutoLoginUri($sLogin, $sPassword);
		$sResultCampaignAuthUrl = $obClient->getAutoLoginResultCampaignUri($sLogin, $sPassword, $id);
		if(!empty($sAuthUrl))
		{
			$aContext[] = array(
				"TEXT" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_GOTO_SERVICE"),
				"LINK" => $sAuthUrl,
				"LINK_PARAM" => " target=\"_blank\"",
				"TITLE" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_GOTO_SERVICE"),
				"ICON" => "btn_new",
			);
			$aContext[] = array(
				"TEXT" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_GOTO_SERVICE_EDIT"),
				"LINK" => $sResultCampaignAuthUrl,
				"LINK_PARAM" => " target=\"_blank\"",
				"TITLE" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_GOTO_SERVICE_EDIT"),
			);
		}
	}

	$aContext[] = array(
		"TEXT" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_CAMPAIGNS"),
		"LINK" => "mailtrig_events_campaigns.php?lang=".LANG,
		"TITLE" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_CAMPAIGNS"),
	);
	$aContext[] = array(
		"TEXT" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_STAT"),
		"LINK" => "mailtrig_events_results.php?lang=".LANG,
		"TITLE" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_STAT"),
	);

	$lAdmin->AddAdminContextMenu($aContext);

	$lAdmin->CheckListMode();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("MAILTRIG_EVENTS_DELIVERY_FILTER_DATE"),
	)
);
?>
<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?$oFilter->Begin();?>
	<input type="hidden" name="id" value="<?=$id?>" />
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<tr>
		<td><?=GetMessage("MAILTRIG_EVENTS_DELIVERY_FILTER_DATE")?></td>
		<td><?=CalendarPeriod("datefrom", htmlspecialcharsex($datefrom), "dateto", htmlspecialcharsex($dateto), "find_form", "Y")?></td>
	</tr>
	<?
	$oFilter->Buttons();
	?>
	<input type="submit" name="S" value="<?=GetMessage("MAILTRIG_EVENTS_DELIVERY_FILTER_SUBMIT")?>" />
	<?
	$oFilter->End();
	?>
</form>
<?
$lAdmin->DisplayList();
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
