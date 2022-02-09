#!/usr/local/php/bin/php -q
<?php
$_SERVER["DOCUMENT_ROOT"] = "#DOCUMENT_ROOT#";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","ru");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

$profile_id = $argv[1];

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (CModule::IncludeModule("catalog"))
{
	$profile_id = IntVal($profile_id);
	if ($profile_id<=0) die("No profile_id");

	$ar_profile = CCatalogImport::GetByID($profile_id);
	if (!$ar_profile) die("No profile");

	if ($ar_profile["DEFAULT_PROFILE"]!="Y")
	{
		parse_str($ar_profile["SETUP_VARS"]);
	}

	$strFile = CATALOG_PATH2IMPORTS.$ar_profile["FILE_NAME"]."_run.php";
	if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
	{
		$strFile = CATALOG_PATH2IMPORTS_DEF.$ar_profile["FILE_NAME"]."_run.php";
		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$strFile))
		{
			die("No import script");
		}
	}

	$bFirstLoadStep = True;
	CCatalogDiscountSave::Disable();
	@include($_SERVER["DOCUMENT_ROOT"].$strFile);
	CCatalogDiscountSave::Enable();
	CCatalogImport::Update($profile_id, array(
		"=LAST_USE" => $DB->GetNowFunction()
		));
}
?>