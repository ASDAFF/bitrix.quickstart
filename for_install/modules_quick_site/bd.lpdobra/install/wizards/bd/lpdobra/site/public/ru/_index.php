<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Самая добрая посадочная страница");
$APPLICATION->SetTitle("Новости");
$GLOBALS["arrFilterMainTheme"] = array("PROPERTY_MAIN_VALUE" => 1);
$GLOBALS["arrFilterMain"] = array("PROPERTY_MAIN_VALUE" => 1);
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"filter",
	Array(
		"IBLOCK_TYPE" => "lpdobra",
		"IBLOCK_ID" => "#GALLERY_IBLOCK_ID#",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "2",
		"SECTION_FIELDS" => array(0=>"",1=>"",),
		"SECTION_USER_FIELDS" => array(0=>"",1=>"",),
		"SECTION_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"VIEW_MODE" => "LINE",
		"SHOW_PARENT_NAME" => "Y"
	)
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.line",
	"portfolio",
	Array(
		"IBLOCK_TYPE" => "lpdobra",
		"IBLOCKS" => array(0=>"#GALLERY_IBLOCK_ID#",),
		"NEWS_COUNT" => "20",
		 "FIELD_CODE" => array(0=>"CODE",1=>"PREVIEW_PICTURE",2=>"DETAIL_PICTURE",3=>"",),
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "300",
		"CACHE_GROUPS" => "Y",
		"ACTIVE_DATE_FORMAT" => "d.m.Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>