<?
/**
 * Copyright (c) 3/12/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<div class="span12">
<div id="post-14" class="post-14 page type-page status-publish hentry page">
<div class="row">
<div class="span12">
<div class="google-map">
<?$APPLICATION->IncludeComponent("bitrix:map.google.view", ".default", array(
	"KEY" => "ABQIAAAAOSNukcWVjXaGbDo6npRDcxS1yLxjXbTnpHav15fICwCqFS-qhhSby0EyD6rK_qL4vuBSKpeCz5cOjw",
	"INIT_MAP_TYPE" => "NORMAL",
	"MAP_DATA" => "a:3:{s:10:\"google_lat\";s:7:\"55.7383\";s:10:\"google_lon\";s:7:\"37.5946\";s:12:\"google_scale\";i:13;}",
	"MAP_WIDTH" => "100%",
	"MAP_HEIGHT" => "300",
	"CONTROLS" => array(
		0 => "LARGE_MAP_CONTROL",
		1 => "MINIMAP",
		2 => "HTYPECONTROL",
		3 => "SCALELINE",
	),
	"OPTIONS" => array(
		0 => "ENABLE_SCROLL_ZOOM",
		1 => "ENABLE_DBLCLICK_ZOOM",
		2 => "ENABLE_DRAGGING",
	),
	"MAP_ID" => ""
	),
	false
);?>
</div><br /><br /></div>
<div class="span4"><h2>Контакты</h2>
<h5>Обратитесь к нашим специалистам и получите профессиональную консультацию.</h5>
<p>Вы можете обратиться к нам по телефону, по электронной почте или договориться о встрече в нашем офисе. Будем рады помочь вам и ответить на все ваши вопросы.<br>
</p><address>
<strong>Компания Окна.<br>
225340, Москва<br>
ул гоголя д.40</strong><br>
Телефон: +7 800 603 6035<br>
Факс: +7 800 889 9898<br>
E-mail: <a href="mailto:info@demolink.org">mail@demolink.org<script type="text/javascript">
</script></a><br>
</address> </div>
<?$APPLICATION->IncludeComponent("likeit:main.feedback", "contacts", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => "maks@likeit.pro",
	"REQUIRED_FIELDS" => array(
	),
	"EVENT_MESSAGE_ID" => array(
		0 => "7",
	)
	),
	false
);?>
</div>
<div class="clear"></div>

</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>