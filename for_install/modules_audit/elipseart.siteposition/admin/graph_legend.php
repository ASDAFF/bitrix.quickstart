<?
define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$module_id = "elipseart.siteposition";

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/img.php");

$width = 45;
$height = 2;

$ImageHandle = CreateImageHandle($width, $height, "FFFFFF", true);

$dec = ReColor($color);
$color = ImageColorAllocate($ImageHandle,$dec[0],$dec[1],$dec[2]);
ImageFill($ImageHandle, 0, 0, $color);

ShowImageHeader($ImageHandle);
?>