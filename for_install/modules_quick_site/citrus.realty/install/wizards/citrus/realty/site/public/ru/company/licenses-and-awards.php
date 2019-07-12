<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Лицензии, награды");
if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	die();
?> 
<?$APPLICATION->IncludeComponent(
	"bitrix:news.line",
	"image",
	Array(
		"RESIZE_IMAGE_WIDTH" => "260",
		"RESIZE_IMAGE_HEIGHT" => "375",
		"COLORBOX_MAXWIDTH" => "600",
		"COLORBOX_MAXHEIGHT" => "800",
		"IBLOCK_TYPE" => "company",
		"IBLOCKS" => array(\Citrus\Realty\Helper::getIblock("licenses")),
		"NEWS_COUNT" => "20",
		"FIELD_CODE" => array("NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE"),
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "ID",
		"SORT_ORDER2" => "ASC",
		"DETAIL_URL" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"CACHE_GROUPS" => "N"
	),
false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>