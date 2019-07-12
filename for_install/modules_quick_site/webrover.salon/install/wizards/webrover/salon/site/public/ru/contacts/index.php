<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?> 
<p>Обратитесь к нашим специалистам и получите профессиональную консультацию по услугам нашего салона красоты.</p>
 
<p>Вы можете обратиться к нам по телефону, по <a href="mailto:example@example.com" >электронной почте</a> или договориться о встрече в нашем офисе. Будем рады помочь вам и ответить на все ваши вопросы.</p>
 
<p><strong>Телефоны:</strong> 
  <br />
 &mdash; (495) 133-65-98 
  <br />
 &mdash; (495) 133-65-99</p>
 
<br />
 
<p><strong>Схема проезда к салону красоты</strong></p>
<?$APPLICATION->IncludeComponent("bitrix:map.google.view", ".default", array(
	"INIT_MAP_TYPE" => "ROADMAP",
	"MAP_DATA" => "a:4:{s:10:\"google_lat\";d:55.7383;s:10:\"google_lon\";d:37.5946;s:12:\"google_scale\";i:13;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:83:\"Салон красоты###RN###пр. Кирова, 128, ст. 1, оф. 31###RN###\";s:3:\"LON\";d:37.589428701;s:3:\"LAT\";d:55.7416037689;}}}",
	"MAP_WIDTH" => "500",
	"MAP_HEIGHT" => "500",
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
	"MAP_ID" => ""
	),
	false
);?>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>