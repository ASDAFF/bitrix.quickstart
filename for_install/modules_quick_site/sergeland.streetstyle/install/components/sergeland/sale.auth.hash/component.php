<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;

if(strlen($_REQUEST["hash"]) > 30 && CModule::IncludeModule("sale"))
{	
	$dbSales = CSaleOrder::GetList(array(), array("ADDITIONAL_INFO" => $_REQUEST["hash"]));
	if($arSales = $dbSales->Fetch())
	{
		$USER->Authorize($arSales["USER_ID"]);
		LocalRedirect($APPLICATION->GetCurPage());		
	}
}
?>