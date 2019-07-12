<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$_POST["elementname"] = iconv("UTF-8","WINDOWS-1251",$_POST["elementname"]);
$price = $_POST["elementprice"];
if(CModule::IncludeModule("sale")){
	$arFields = array(
		"PRODUCT_ID" => $_POST["elementid"],
		"PRICE" => $price,
		"CURRENCY" => "RUB",
		"QUANTITY" => 1,
		"LID" => SITE_ID,
		"DELAY" => "N",
		"CAN_BUY" => "Y",
		"NAME" => $_POST["elementname"],
		"DETAIL_PAGE_URL" => $_POST["elementdpu"]
	);
	CSaleBasket::Add($arFields);
}
?>