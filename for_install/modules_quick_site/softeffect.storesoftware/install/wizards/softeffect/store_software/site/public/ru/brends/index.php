<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Все бренды");
global $IB_CATALOG;
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"tmp_allbrends",
	Array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => $IB_CATALOG,
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_URL" => "",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "1",
		"SECTION_FIELDS" => "",
		"SECTION_USER_FIELDS" => "",
		"ADD_SECTIONS_CHAIN" => "Y",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y"
	)
);?> 
<br />
<br />
<div style="clear: both;"></div>
 <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"tmp_allbrends_pic",
	Array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => $IB_CATALOG,
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_URL" => "",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "1",
		"SECTION_FIELDS" => array(),
		"SECTION_USER_FIELDS" => array("UF_LOGO_ALLBRANDS"),
		"ADD_SECTIONS_CHAIN" => "Y",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y"
	)
);?>
<br clear="both" /><br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>