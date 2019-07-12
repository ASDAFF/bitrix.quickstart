<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.line", 
	"main", 
	array(
		"COMPONENT_TEMPLATE" => "main",
		"IBLOCK_TYPE" => "presscenter",
		"IBLOCKS" => array(
			0 => "#NEWS_IBLOCK_ID#",
			1 => "#ACTION_IBLOCK_ID#",
		),
		"NEWS_COUNT" => "7",
		"FIELD_CODE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "IBLOCK_NAME",
			2 => "",
		),
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"CACHE_GROUPS" => "N",
		"ACTIVE_DATE_FORMAT" => "d.m.Y"
	),
	false
);?>