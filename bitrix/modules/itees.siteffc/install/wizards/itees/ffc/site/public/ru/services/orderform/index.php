<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Форма online заказа");
?><?$APPLICATION->IncludeComponent("bitrix:form.result.new", "formorder", Array(
	"SEF_MODE" => "N",
	"WEB_FORM_ID" => "#ORDERFORM_ID#",
	"LIST_URL" => "result_list.php",
	"EDIT_URL" => "result_edit.php",
	"SUCCESS_URL" => "/services/orderform/orderform_end.php",
	"CHAIN_ITEM_TEXT" => "",
	"CHAIN_ITEM_LINK" => "",
	"IGNORE_CUSTOM_TEMPLATE" => "N",
	"USE_EXTENDED_ERRORS" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"VARIABLE_ALIASES" => array(
		"WEB_FORM_ID" => "WEB_FORM_ID",
		"RESULT_ID" => "RESULT_ID",
	)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>