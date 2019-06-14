<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mailtrig.events/include.php");

IncludeModuleLangFile(__FILE__);

global $APPLICATION;

CModule::IncludeModule("mailtrig.events");

$obClient = new CMailTrigClient;

$sTableID = "mailtrig_events_campaigns";
$lAdmin = new CAdminList($sTableID);

$APPLICATION->SetTitle(GetMessage("MAILTRIG_EVENTS_DELIVERY_PAGE_CAMPAIGNS"));

$arCampaigns = $obClient->getCampaigns();

if($arCampaigns["status"] != "200")
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>
	<?CAdminMessage::ShowMessage(GetMessage("MAILTRIG_EVENTS_DELIVERY_ERROR_RESPONSE") . $arCampaigns["error_message"]);?>
	<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$lAdmin->AddHeaders(
	array(
		/*array(
			"id" => "id",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_ID"),
			"default" => true
		),*/
		array(
			"id" => "name",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_NAME"),
			"default" => true
		),
		array(
			"id" => "edit",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_EDIT"),
			"default" => true
		),
		array(
			"id" => "event",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_EVENT"),
			"default" => true
		),
		array(
			"id" => "status",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_STATUS"),
			"default" => true
		),
		array(
			"id" => "sent",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_SENT"),
			"default" => true
		),
		array(
			"id" => "opened",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_OPENED"),
			"default" => true
		),
		array(
			"id" => "clicked",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_CLICKED"),
			"default" => true
		),
		array(
			"id" => "time",
			"content" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_TIME"),
			"default" => true
		),
	)
);

$arCampaignStatus = array(
	"0" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_STATUS_0"),
	"1" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_STATUS_1"),
	"2" => GetMessage("MAILTRIG_EVENTS_DELIVERY_CAMPAIGN_STATUS_2")
);

$sLogin = COption::GetOptionString("mailtrig.events", "login");
$sPassword = COption::GetOptionString("mailtrig.events", "password");

if($arCampaigns["status"] == "200")
{
	foreach($arCampaigns["data"] as $keyEvent => $arEvent)
	{
		foreach($arEvent as $arValue)
		{
			$arValue["event"] = $keyEvent;
			$arValue["status"] = $arCampaignStatus[$arValue["status"]];

			$row =& $lAdmin->AddRow($arValue["id"], $arValue);

			$sResultCampaignAuthUrl = '';
			$sResultCampaignAuthUrl = $obClient->getAutoLoginResultCampaignUri($sLogin, $sPassword, $arValue["id"]);

			$row->AddViewField("id", '<a href="mailtrig_events_linechart.php?id='.$arValue["id"].'&lang='.LANG.'">'.$arValue["id"].'</a>');
			$row->AddViewField("name", '<a href="mailtrig_events_linechart.php?id='.$arValue["id"].'&lang='.LANG.'">'.$arValue["name"].'</a>');
			if(strlen($sResultCampaignAuthUrl) > 0)
			{
				$row->AddViewField("edit", '<a href="'.$sResultCampaignAuthUrl.'" target="_blank">'.GetMessage("MAILTRIG_EVENTS_DELIVERY_ROW_EDIT").'</a>');
			}
		}
	}

	$aContext = array();

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
$lAdmin->DisplayList();
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
