<?define('STOP_STATISTICS', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tcsbank.kupivkredit/include.php");
IncludeModuleLangFile(__FILE__);
$arRights = $obModule->GetGroupRight();
$arReturn = Array();
if ($arRights == "W") 
{
	$arParams = $_REQUEST["SEND"];
	$arReturn = $obModule->SendSiteRequest($arParams);

}
else $arReturn = Array("status"=>"error", "message"=>GetMessage("TCS_NO_RIGHTS"));
echo $obModule->PHPArrayToJS($arReturn);
?>