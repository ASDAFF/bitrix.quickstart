<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подать заявку на поступление");
?>
<br />
<?$APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form",
	"entrance",
	Array(
		"SEF_MODE" => "Y",
		"IBLOCK_TYPE" => "services",
		"IBLOCK_ID" => "#APPLICATIONS_FOR_ADMISSION_IBLOCK_ID#",
        "PROPERTY_CODES" => #PROPERTY_CODES#,
        "PROPERTY_CODES_REQUIRED" => #PROPERTY_CODES_REQUIRED#,
        "GROUPS" => array(
            0 => "2",
        ),
		"STATUS_NEW" => "ANY",
		"STATUS" => "ANY",
		"LIST_URL" => "",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"MAX_USER_ENTRIES" => "100000",
		"MAX_LEVELS" => "100000",
		"LEVEL_LAST" => "Y",
		"USE_CAPTCHA" => "N",
		"USER_MESSAGE_EDIT" => "Ваша заявка отправлена",
		"USER_MESSAGE_ADD" => "Ваша заявка отправлена",
		"DEFAULT_INPUT_SIZE" => "30",
		"RESIZE_IMAGES" => "N",
		"MAX_FILE_SIZE" => "0",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"CUSTOM_TITLE_NAME" => "Ф.И.О. ребенка",
		"CUSTOM_TITLE_TAGS" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "Адрес прописки ребенка",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "Адрес фактического проживания ребенка",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
		"SEF_FOLDER" => "#SITE_DIR#admission/apply_for_admission/",
		"VARIABLE_ALIASES" => Array(
		)
	)
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>