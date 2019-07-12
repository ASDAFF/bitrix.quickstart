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
		"question",
		Array(
			"IBLOCK_TYPE" => "info",
			"IBLOCK_ID" => \Citrus\Realty\Helper::getIblock("questions"),
			"FIELDS" => "a:5:{s:4:\"NAME\";a:4:{s:11:\"IS_REQUIRED\";b:1;s:14:\"ORIGINAL_TITLE\";s:8:\"��������\";s:5:\"TITLE\";s:25:\"�������������, ����������\";s:7:\"TOOLTIP\";s:0:\"\";}s:12:\"PREVIEW_TEXT\";a:4:{s:14:\"ORIGINAL_TITLE\";s:19:\"�������� ��� ������\";s:5:\"TITLE\";s:10:\"��� ������\";s:7:\"TOOLTIP\";s:0:\"\";s:11:\"IS_REQUIRED\";b:1;}s:23:\"PROPERTY_author_address\";a:5:{s:5:\"TITLE\";s:5:\"�����\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:10:\"[77] �����\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}s:21:\"PROPERTY_author_phone\";a:5:{s:5:\"TITLE\";s:7:\"�������\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:12:\"[78] �������\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}s:21:\"PROPERTY_author_email\";a:5:{s:5:\"TITLE\";s:5:\"Email\";s:11:\"IS_REQUIRED\";b:0;s:14:\"ORIGINAL_TITLE\";s:11:\"[79] E-Mail\";s:6:\"ACTIVE\";b:1;s:7:\"TOOLTIP\";s:0:\"\";}}",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "voprosy-posetiteley",
			"GROUPS" => array("2"),
			"ACCESS_DENIED_MESSAGE" => "",
			"SEND_MESSAGE" => "Y",
			"MAIL_EVENT" => "CITRUS_REALTY_NEW_QUESTION",
			"SUCCESS_ADD_MESSAGE" => "�������, ��� ������ ������! �� �������� � ���� � ��������� ������� �����",
			"SUBMIT_TEXT" => "���������",
			"ERROR_LIST_MESSAGE" => "",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "0",
			
			"AJAX_MODE" => "Y",  // ����� AJAX
			"AJAX_OPTION_SHADOW" => "N", // ��������� �������
			"AJAX_OPTION_JUMP" => "N", // ��������� �������� �� ����������
			"AJAX_OPTION_STYLE" => "Y", // ���������� �����
			"AJAX_OPTION_HISTORY" => "N",

		),
		$component
	);?><?

if (array_key_exists('ajax', $_GET))
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
else
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");

?>