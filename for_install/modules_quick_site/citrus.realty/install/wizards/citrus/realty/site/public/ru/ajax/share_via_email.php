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
	"citrus:realty.send_event", 
	"share", 
	array(
		"EVENT_TYPE" => "CITRUS_REALTY_SHARE",
		"FIELDS" => "a:3:{s:8:\"EMAIL_TO\";a:5:{s:14:\"ORIGINAL_TITLE\";s:36:\"#EMAIL_TO# - Email ���������� ������\";s:5:\"TITLE\";s:4:\"����\";s:7:\"TOOLTIP\";s:17:\"E-mail ����������\";s:11:\"IS_REQUIRED\";b:1;s:8:\"IS_EMAIL\";b:1;}s:10:\"EMAIL_FROM\";a:5:{s:14:\"ORIGINAL_TITLE\";s:39:\"#EMAIL_FROM# - Email ����������� ������\";s:5:\"TITLE\";s:7:\"�� ����\";s:7:\"TOOLTIP\";s:18:\"E-mail �����������\";s:11:\"IS_REQUIRED\";b:1;s:8:\"IS_EMAIL\";b:1;}s:4:\"LINK\";a:5:{s:14:\"ORIGINAL_TITLE\";s:19:\"#LINK# - URL ������\";s:5:\"TITLE\";s:12:\"����� ������\";s:7:\"TOOLTIP\";s:12:\"����� ������\";s:11:\"IS_REQUIRED\";b:1;s:8:\"IS_EMAIL\";b:0;}}",
		"SUCCESS_SEND_MESSAGE" => "���� ��������� ����������.",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?><?

if (array_key_exists('ajax', $_GET))
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
else
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

?>