<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once(dirname(__FILE__)."/../include.php");
include(dirname(__FILE__)."/../constants.php");
IncludeModuleLangFile(dirname(__FILE__)."/status.php");
$arRights = $obModule->GetGroupRight();
$arReturn = Array();
if ($arRights > "D") 
{
	if(($iOrderID = IntVal($_REQUEST["order_id"])) && strlen($_SESSION["TCSBANK"]["ORDER_PDF"][$iOrderID]))
	{
		$sData = ($_SESSION["TCSBANK"]["ORDER_PDF"][$iOrderID]);
		unset($_SESSION["TCSBANK"]["ORDER_PDF"][$iOrderID]);	
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: application/pdf");
		header("Content-Disposition: attachment; filename=kupivkredit_agreement_{$iOrderID}.pdf");
		header("Expires: 0");
		header("Pragma: public");	

		echo (string)base64_decode($sData);
		
		exit();
	}
}
?>