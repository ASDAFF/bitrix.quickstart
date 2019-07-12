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
		"FIELDS" => "a:6:{s:4:\"NAME\";a:4:{s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:8:\"��������\";s:5:\"TITLE\";s:25:\"�������������, ����������\";s:7:\"TOOLTIP\";s:21:\"��� � ��� ����������?\";}s:17:\"PROPERTY_CONTACTS\";a:5:{s:5:\"TITLE\";s:21:\"���������� ����������\";s:11:\"IS_REQUIRED\";b:1;s:14:\"ORIGINAL_TITLE\";s:26:\"[21] ���������� ����������\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:66:\"������� ����� �������� � ������������� ������� ��� ����� ��. �����\";}s:12:\"PREVIEW_TEXT\";a:5:{s:14:\"ORIGINAL_TITLE\";s:19:\"�������� ��� ������\";s:5:\"TITLE\";s:42:\"�������� ������ ��� ����� ������ ���������\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:93:\"������ ������� ����������� ��� ���������� ���������, ����� �������� � ����� ������ ����������\";s:11:\"IS_REQUIRED\";b:0;}s:7:\"CAPTCHA\";a:5:{s:11:\"IS_REQUIRED\";b:1;s:14:\"ORIGINAL_TITLE\";s:36:\"������ �� ��������������� ����������\";s:5:\"TITLE\";s:36:\"������ �� ��������������� ����������\";s:7:\"TOOLTIP\";s:26:\"������� ������� � ��������\";s:6:\"ACTIVE\";b:1;}s:14:\"PROPERTY_ITEMS\";a:5:{s:5:\"TITLE\";s:13:\"������ ������\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:18:\"[36] ������ ������\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}s:13:\"PROPERTY_href\";a:5:{s:5:\"TITLE\";s:22:\"���������� �� ��������\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:27:\"[46] ���������� �� ��������\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}}",
		"SUCCESS_ADD_MESSAGE" => "���� ������ �������! ���� ��������� �������� � ���� � ��������� ������� �����.",
		"SUBMIT_TEXT" => "���������",
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