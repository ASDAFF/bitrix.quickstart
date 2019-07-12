<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("header-compare-block");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.list",
	"compare_top",
	Array(
		"IBLOCK_TYPE" => "aspro_optimus_catalog",
		"IBLOCK_ID" => "#IBLOCK_CATALOG_ID#",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"DETAIL_URL" => SITE_DIR."catalog/#SECTION_CODE_PATH#/#ELEMENT_ID#/",
		"COMPARE_URL" => SITE_DIR."catalog/compare.php",
		"NAME" => "CATALOG_COMPARE_LIST",
		"AJAX_OPTION_ADDITIONAL" => ""
	)
);?>
<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("header-compare-block", "");?>