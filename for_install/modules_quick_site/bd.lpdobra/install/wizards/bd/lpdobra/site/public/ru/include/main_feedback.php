<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback", 
	"main_form", 
	array(
		"USE_CAPTCHA" => "N",
		"AJAX_MODE"=>"Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "mymail@mymail.ru",
		"REQUIRED_FIELDS" => array(
			0 => "NAME",
			1 => "EMAIL",
			2 => "MESSAGE",
		),
		"EVENT_MESSAGE_ID" => array(
		)
	),
	false
);?>