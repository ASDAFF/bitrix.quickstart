<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords_inner", "каталог мягкой мебели, каталог мебели");
$APPLICATION->SetPageProperty("description", "мягкая мебель от производителя: угловые диваны в санкт-петербург.");
$APPLICATION->SetPageProperty("keywords", "мебель угловые диваны, купить угловые диваны, мягкая мебель угловые диваны");
$APPLICATION->SetPageProperty("title", "Каталог мебели: угловые диваны, купить угловые диваны, мягкая мебель угловые диваны");
$APPLICATION->SetTitle("Каталог мягкой мебели");
?><? $APPLICATION->IncludeComponent("bitrix:photo", "catalog", array(
	"IBLOCK_TYPE" => "catalogs",
	"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "#SITE_DIR#catalog/",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "3600",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"USE_PERMISSIONS" => "N",
	"USE_RATING" => "N",
	"USE_REVIEW" => "N",
	"USE_FILTER" => "N",
	"SECTION_COUNT" => "50",
	"TOP_ELEMENT_COUNT" => "10",
	"TOP_LINE_ELEMENT_COUNT" => "2",
	"SECTION_SORT_FIELD" => "sort",
	"SECTION_SORT_ORDER" => "asc",
	"TOP_ELEMENT_SORT_FIELD" => "sort",
	"TOP_ELEMENT_SORT_ORDER" => "asc",
	"TOP_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"TOP_PROPERTY_CODE" => array(
		0 => "PRICE",
		1 => "",
	),
	"SECTION_PAGE_ELEMENT_COUNT" => "20",
	"SECTION_LINE_ELEMENT_COUNT" => "2",
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"LIST_FIELD_CODE" => array(
		0 => "PREVIEW_TEXT",
		1 => "",
	),
	"LIST_PROPERTY_CODE" => array(
		0 => "PRICE",
		1 => "",
	),
	"LIST_BROWSER_TITLE" => "UF_TITLE",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "TITLE",
	"DETAIL_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"DETAIL_PROPERTY_CODE" => array(
		0 => "PRICE",
		1 => "ASSOC",
		2 => "GALLERY",
		3 => "GALLERY_TRANSFORM",
		4 => "GALLERY_UPHOLSTERY",
		5 => "GALLERY_SCHEME",
		6 => "IMG_4DESIGNERS",
		7 => "PRICE",
		8 => "",
	),
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"CURRENCY_CODE" => "р",
	"AJAX_OPTION_ADDITIONAL" => "",
	"SEF_URL_TEMPLATES" => array(
		"sections_top" => "",
		"section" => "#SECTION_ID#/",
		"detail" => "#SECTION_ID#/#ELEMENT_ID#/",
	)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>