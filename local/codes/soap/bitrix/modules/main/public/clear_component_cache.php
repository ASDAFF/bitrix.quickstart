<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

if(!$USER->CanDoOperation('cache_control') || !check_bitrix_sessid())
	die(GetMessage("ACCESS_DENIED"));

if($_GET["site_id"] == '')
	die("Empty site_id.");

$sites = CSite::GetByID($_GET["site_id"]);
if(!($site = $sites->Fetch()))
	die("Incorrect site_id.");

$aComponents = explode(",", $_GET["component_name"]);
foreach($aComponents as $component_name)
{
	$componentRelativePath = CComponentEngine::MakeComponentPath($component_name);
	if (strlen($componentRelativePath) > 0)
	{
		$arComponentDescription = CComponentUtil::GetComponentDescr($component_name);
		if (isset($arComponentDescription) && is_array($arComponentDescription))
		{
			if (array_key_exists("CACHE_PATH", $arComponentDescription))
			{
				if($arComponentDescription["CACHE_PATH"] == "Y")
					$arComponentDescription["CACHE_PATH"] = "/".$site["ID"].$componentRelativePath;
				if(strlen($arComponentDescription["CACHE_PATH"]) > 0)
				{
					$obCache = new CPHPCache;
					$obCache->CleanDir($arComponentDescription["CACHE_PATH"], "cache");
					BXClearCache(true, $arComponentDescription["CACHE_PATH"]);
				}
			}
		}
	}
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
?>