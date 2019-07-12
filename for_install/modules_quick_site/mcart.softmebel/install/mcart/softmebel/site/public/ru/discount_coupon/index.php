<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/coupon_edit.php");
$APPLICATION->SetTitle("Купон на скидку");

?><b>Купоны на </b><b>5% скидку всем. Заполните форму, получите купон и приходите с ним в любой наш</b> <a href="/contacts/" >фирменный мебельный магазин</a>. 
<br />
 
<br />
 <?$APPLICATION->IncludeComponent("mcart:iblock.element.add.form", ".default", array(
	"IBLOCK_TYPE" => "catalogs",
	"IBLOCK_ID" => "#COUPON_IBLOCK_ID#",
	"STATUS_NEW" => "N",
	"LIST_URL" => "",
	"USE_CAPTCHA" => "Y",
	"USER_MESSAGE_EDIT" => "Купон успешно сохранен",
	"USER_MESSAGE_ADD" => "Купон успешно добавлен",
	"USER_MESSAGE_COUPON_CODE" => "№ купона на скидку",
	"DEFAULT_INPUT_SIZE" => "50",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => '#LAST_NAME_ID#',
		1 => "#NAME_ID#",
		2 => "#PATRONYMIC_ID#",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => '#LAST_NAME_ID#',
		1 => "#NAME_ID#",
	),
	"GROUPS" => array(
		0 => "2",
	),
	"STATUS" => "INACTIVE",
	"ELEMENT_ASSOC" => "CREATED_BY",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "N",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "/discount_coupon/",
	"CUSTOM_TITLE_NAME" => "",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?> 
<br />
 
<br />
 Покупайте мебель со скидками. <b>Спасибо.</b> 
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>