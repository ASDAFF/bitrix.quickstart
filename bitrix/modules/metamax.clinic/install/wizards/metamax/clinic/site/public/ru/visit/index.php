<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������ �� �����");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "page",
		"AREA_FILE_SUFFIX" => "inc",
		"EDIT_TEMPLATE" => ""
	),
false
);?>

<?$APPLICATION->IncludeComponent("bitrix:iblock.element.add.form", "visit_form", array(
	"IBLOCK_TYPE" => "clinic",
	"IBLOCK_ID" => "#VISIT_IBLOCK_ID#",
	"STATUS_NEW" => "ANY",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "Y",
	"USER_MESSAGE_EDIT" => "���� ������ ������� ����������. �������!",
	"USER_MESSAGE_ADD" => "���� ������ ������� ����������. �������!",
	"DEFAULT_INPUT_SIZE" => "40",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "PREVIEW_TEXT",
		2 => "DETAIL_TEXT",
		3 => "#PHONE_PROPERTY_ID#",
		4 => "#EMAIL_PROPERTY_ID#",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "NAME",
		1 => "#PHONE_PROPERTY_ID#",
	),
	"GROUPS" => array(
		0 => "2",
	),
	"STATUS" => "ANY",
	"ELEMENT_ASSOC" => "CREATED_BY",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "N",
	"SEF_FOLDER" => SITE_DIR."/visit/",
	"CUSTOM_TITLE_NAME" => "���� ���",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "���� �������� ��� ������������� �����",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "�������� ���� � �����",
	"CUSTOM_TITLE_DETAIL_PICTURE" => "",
	"DOCTOR_ID" => $_REQUEST["ID"]
	),
	false
);?>

<p align="right"><img src="#SITE_DIR#images/doctor.gif" border="0" width="257" height="540" /></p>
<div class="clear"></div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>