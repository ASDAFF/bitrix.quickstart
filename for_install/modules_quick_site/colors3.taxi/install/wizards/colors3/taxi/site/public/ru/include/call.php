<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("Перезвоните мне");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form",
	"call",
	Array(
		"SEF_MODE" => "N",
		"IBLOCK_TYPE" => "orders",
		"IBLOCK_ID" => "#CALL_IBLOCK_ID#",
		"PROPERTY_CODES" => array("#PROP_1#","#PROP_2#","#PROP_3#","NAME"),
		"PROPERTY_CODES_REQUIRED" => array("#PROP_2#","NAME"),
		"GROUPS" => array("2"),
		"STATUS_NEW" => "NEW",
		"STATUS" => "ANY",
		"LIST_URL" => "",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"MAX_USER_ENTRIES" => "100000",
		"MAX_LEVELS" => "100000",
		"LEVEL_LAST" => "Y",
		"USE_CAPTCHA" => "N",
		"USER_MESSAGE_EDIT" => "Спасибо, ваша заявка принята. Оператор вам перезвонит через несколько минут.",
		"USER_MESSAGE_ADD" => "Спасибо, ваша заявка принята. Оператор вам перезвонит через несколько минут.",
		"DEFAULT_INPUT_SIZE" => "30",
		"RESIZE_IMAGES" => "N",
		"MAX_FILE_SIZE" => "0",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"CUSTOM_TITLE_NAME" => "",
		"CUSTOM_TITLE_TAGS" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "",
		"CUSTOM_TITLE_DETAIL_PICTURE" => ""
	)
);?>