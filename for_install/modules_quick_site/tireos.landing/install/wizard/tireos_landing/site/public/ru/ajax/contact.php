<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
?>


<? /*$APPLICATION->IncludeComponent(
	"custom:form.result.new",
	"orderproj",
	Array(
		"AJAX_MODE" => "Y",
		"SEF_MODE" => "N",
		"WEB_FORM_ID" => "2",
		"LIST_URL" => "",
		"EDIT_URL" => "",
		"SUCCESS_URL" => "/",//"/success",
		"CHAIN_ITEM_TEXT" => "",
		"CHAIN_ITEM_LINK" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"USE_EXTENDED_ERRORS" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"VARIABLE_ALIASES" => Array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID"
		)
	)
);*/?>

<?$APPLICATION->IncludeComponent("bitrix:main.feedback","contact",Array(
		"AJAX_MODE" => "Y",
        "USE_CAPTCHA" => "Y",
        "AJAX_OPTION_JUMP" => "N",
        "OK_TEXT" => "Спасибо, ваше сообщение принято.",
        "EMAIL_TO" => "pashchok@yandex.ru",
        "REQUIRED_FIELDS" => Array("NAME","EMAIL","MESSAGE"),
        "EVENT_MESSAGE_ID" => Array("7")
    )
);?>

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>