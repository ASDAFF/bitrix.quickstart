<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?> 

  <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/contacts.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
<p><strong>Схема проезда:</strong></p>
<p><?$APPLICATION->IncludeComponent("bitrix:map.google.view", ".default", array(
	"INIT_MAP_TYPE" => "ROADMAP",
	"MAP_DATA" => "a:4:{s:10:\"google_lat\";d:#LAT#;s:10:\"google_lon\";d:#LON#;s:12:\"google_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:#stringLenght#:\"#siteName#\";s:3:\"LON\";d:#LON#;s:3:\"LAT\";d:#LAT#;}}}",
	"MAP_WIDTH" => "450",
	"MAP_HEIGHT" => "450",
	"CONTROLS" => array(
		0 => "SMALL_ZOOM_CONTROL",
		1 => "TYPECONTROL",
		2 => "SCALELINE",
	),
	"OPTIONS" => array(
		0 => "ENABLE_SCROLL_ZOOM",
		1 => "ENABLE_DBLCLICK_ZOOM",
		2 => "ENABLE_DRAGGING",
		3 => "ENABLE_KEYBOARD",
	),
	"MAP_ID" => "our_adress"
	),
	false
);?></p>
<h3>Отправить нам вопрос</h3>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "#EMAIL#",
		"REQUIRED_FIELDS" => array(
			0 => "NAME",
			1 => "EMAIL",
			2 => "MESSAGE",
		),
		"EVENT_MESSAGE_ID" => array(
			0 => "#FEEDBACK_EVENT_MES_ID#",
		)
	)
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>