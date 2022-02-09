<?
define("STOP_STATISTICS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once("functions.php");

CModule::IncludeModule('socialnetwork');

if (!$USER->IsAuthorized())
	die();

$SITE_ID = isset($_GET["SITE_ID"]) ? $_GET["SITE_ID"] : SITE_ID;

if ($_REQUEST["mode"] == "search")
{
	CUtil::decodeURIComponent($_GET);
	$APPLICATION->RestartBuffer();

	$arFilter = array("SITE_ID" => $SITE_ID, "%NAME" => $_GET["query"]);
	if(!CSocNetUser::IsCurrentUserModuleAdmin($SITE_ID))
		$arFilter["CHECK_PERMISSIONS"] = $USER->GetID();

	$rsGroups = CSocNetGroup::GetList(array("NAME" => "ASC"), $arFilter);
	$arGroups = array();
	while($arGroup = $rsGroups->Fetch())
	{
		$arGroups[] = group2JSItem($arGroup);
	}
	
	if (isset($_REQUEST["features_perms"]) && sizeof($_REQUEST["features_perms"]) == 2)
	{
		filterByFeaturePerms($arGroups, $_REQUEST["features_perms"]);
	}

	Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
	echo CUtil::PhpToJsObject($arGroups);
	die();
}
?>