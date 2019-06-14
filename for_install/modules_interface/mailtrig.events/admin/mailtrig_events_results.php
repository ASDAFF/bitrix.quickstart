<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mailtrig.events/include.php");

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

CModule::IncludeModule("mailtrig.events");

$obClient = new CMailTrigClient;

$sTableID = "mailtrig_events_campaigns";
$lAdmin = new CAdminList($sTableID);

$APPLICATION->SetTitle(GetMessage("MAILTRIG_EVENTS_DELIVERY_PAGE_STAT"));

if($_GET["datefrom"]) {
	$datefrom = $_GET["datefrom"];
	$datefromConvert = ConvertDateTime($_GET["datefrom"], "YYYY-MM-DD");
}
if($_GET["dateto"]) {
	$dateto = $_GET["dateto"];
	$datetoConvert = ConvertDateTime($_GET["dateto"], "YYYY-MM-DD");
}

$arResults = $obClient->getResults($datefromConvert, $datetoConvert);

if($arResults["status"] != "200")
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>
	<?CAdminMessage::ShowMessage(GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_RESPONSE") . $arResults["error_message"]);?>
	<a href="mailtrig_events_campaigns.php?lang=<?=LANG?>"><?=GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_BACK_TO_CAMPAIGNS")?></a>
	<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$lAdmin->AddHeaders(
	array(
		array(
			"id" => "sent",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_RESULTS_SENT"),
			"default" => true
		),
		array(
			"id" => "opened",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_RESULTS_OPENED"),
			"default" => true
		),
		array(
			"id" => "clicked",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_RESULTS_CLICKED"),
			"default" => true
		),
		array(
			"id" => "spam",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_RESULTS_SPAM"),
			"default" => true
		),
		array(
			"id" => "hard_bounced",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_RESULTS_HARD_BOUNCED"),
			"default" => true
		),
		array(
			"id" => "soft_bounced",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_RESULTS_SOFT_BOUNCED"),
			"default" => true
		),
	)
);

if($arResults["status"] == "200")
{
	$row =& $lAdmin->AddRow($arValue["id"], $arResults["data"]);
}

$sLogin = COption::GetOptionString("mailtrig.events", "login");
$sPassword = COption::GetOptionString("mailtrig.events", "password");
if(!empty($sLogin) && !empty($sPassword))
{
	$sAuthUrl = $obClient->getAutoLoginUri($sLogin, $sPassword);
	if(!empty($sAuthUrl))
	{
		$aContext[] = array(
			"TEXT" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_GOTO_SERVICE"),
			"LINK" => $sAuthUrl,
			"LINK_PARAM" => " target=\"_blank\"",
			"TITLE" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_GOTO_SERVICE"),
			"ICON" => "btn_new",
		);
	}
}

$aContext[] = array(
	"TEXT" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_CAMPAIGNS"),
	"LINK" => "mailtrig_events_campaigns.php?lang=".LANG,
	"TITLE" => GetMessage("MAILTRIG_EVENTS_DELIVERY_MENU_CAMPAIGNS"),
);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

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
	<input type="hidden" name="id" value="325459" />
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<tr>
		<td><?=GetMessage("MAILTRIG_EVENTS_DELIVERY_FILTER_DATE")?></td>
		<td><?=CalendarPeriod("datefrom", htmlspecialcharsex($find_datefrom), "dateto", htmlspecialcharsex($find_dateto), "find_form", "Y")?></td>
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
// выведем таблицу списка элементов
$lAdmin->DisplayList();
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
