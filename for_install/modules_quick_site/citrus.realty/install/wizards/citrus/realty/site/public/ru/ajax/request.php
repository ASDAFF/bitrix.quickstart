<?

if (array_key_exists('ajax', $_GET))
{
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

	$APPLICATION->AddHeadScript(BX_ROOT . '/js/main/ajax.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/colors.css', true);

	echo $APPLICATION->ShowHeadStrings();
	echo $APPLICATION->ShowCSS();
}
else
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

?><?$APPLICATION->IncludeComponent(
	"citrus:iblock.element.form", 
	"order", 
	array(
		"IBLOCK_TYPE" => "realty",
		"IBLOCK_ID" => \Citrus\Realty\Helper::getIblock("requests"),
		"FIELDS" => "a:6:{s:4:\"NAME\";a:4:{s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:8:\"Название\";s:5:\"TITLE\";s:25:\"Представьтесь, пожалуйста\";s:7:\"TOOLTIP\";s:21:\"Как к вам обращаться?\";}s:17:\"PROPERTY_CONTACTS\";a:5:{s:5:\"TITLE\";s:21:\"Контактная информация\";s:11:\"IS_REQUIRED\";b:1;s:14:\"ORIGINAL_TITLE\";s:26:\"[21] Контактная информация\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:66:\"Укажите номер телефона в международном формате или адрес эл. почты\";}s:12:\"PREVIEW_TEXT\";a:5:{s:14:\"ORIGINAL_TITLE\";s:19:\"Описание для анонса\";s:5:\"TITLE\";s:42:\"Описание заявки или текст вашего сообщения\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:93:\"Можете указать необходимое вам количество продукции, адрес доставки и любую другую информацию\";s:11:\"IS_REQUIRED\";b:0;}s:7:\"CAPTCHA\";a:5:{s:11:\"IS_REQUIRED\";b:1;s:14:\"ORIGINAL_TITLE\";s:36:\"Защита от автоматического заполнения\";s:5:\"TITLE\";s:36:\"Защита от автоматического заполнения\";s:7:\"TOOLTIP\";s:26:\"Введите символы с картинки\";s:6:\"ACTIVE\";b:1;}s:14:\"PROPERTY_ITEMS\";a:5:{s:5:\"TITLE\";s:13:\"Состав заявки\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:18:\"[36] Состав заказа\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}s:13:\"PROPERTY_href\";a:5:{s:5:\"TITLE\";s:22:\"Отправлено со страницы\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:27:\"[46] Отправлено со страницы\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}}",
		"SUCCESS_ADD_MESSAGE" => "Ваша заявка принята! Наши менеджеры свяжутся с вами в ближайшее рабочее время.",
		"SUBMIT_TEXT" => "Отправить",
		"ERROR_LIST_MESSAGE" => "",
		"SEND_MESSAGE" => "Y",
		"MAIL_EVENT" => "CITRUS_REALTY_NEW_REQUEST",
		"GROUPS" => array(
			0 => "2",
		),
		"ACCESS_DENIED_MESSAGE" => "",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "0",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?><?

if (array_key_exists('ajax', $_GET))
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
else
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

?>