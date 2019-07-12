<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"",
	Array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.73829999999371;s:10:\"yandex_lon\";d:37.59459999999997;s:12:\"yandex_scale\";i:10;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.618975915527315;s:3:\"LAT\";d:55.74556286632916;s:4:\"TEXT\";s:46:\"Тел.: 8 (777) 666 55 44###RN###Факс: 666 55 22\";}}}",
		"MAP_WIDTH" => "100%",
		"MAP_HEIGHT" => "340",
		"CONTROLS" => array("ZOOM","MINIMAP","TYPECONTROL","SCALELINE"),
		"OPTIONS" => array("ENABLE_SCROLL_ZOOM","ENABLE_DBLCLICK_ZOOM","ENABLE_DRAGGING"),
		"MAP_ID" => "",
		"COMPONENT_TEMPLATE" => ".default"
	)
);?>

<br>
<br>
<div style="margin-bottom:40px;" class="JS-Dropdown">
 <a class="btn btn-default btn2 clearfix JS-Dropdown-Switcher" href="javascript:;">Задать вопрос</a>
	<div class="JS-Dropdown-Bar" style="display:none;">
		<?$APPLICATION->IncludeComponent(
			"rsflyaway:forms", 
			"flyaway", 
			array(
				"TITLE_FOR_WEBFORM" => "",
				"DESCRIPTION_FOR_WEBFORM" => "",
				"ALFA_EMAIL_TO" => "",
				"SHOW_FIELDS" => array(
					0 => "RS_NAME",
					1 => "RS_EMAIL",
					2 => "RS_TEXTAREA",
				),
				"REQUIRED_FIELDS" => array(
					0 => "RS_NAME",
					1 => "RS_EMAIL",
				),
				"ALFA_USE_CAPTCHA" => "N",
				"INPUT_NAME_RS_NAME" => "Ваше имя",
				"INPUT_NAME_RS_EMAIL" => "Адрес электронной почты",
				"INPUT_NAME_RS_TEXTAREA" => "Текст сообщения",
				"ALFA_MESSAGE_AGREE" => "Спасибо, ваше сообщение отправлено!",
				"AJAX_MODE" => "Y",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "3600",
				"AJAX_OPTION_ADDITIONAL" => "",
				"EVENT_TYPE" => "RS_FLYAWAY_CONTACTS",
				"FORM_TITLE" => "Задать вопрос",
				"FORM_DESCRIPTION" => "",
				"EMAIL_TO" => "",
				"USE_CAPTCHA" => "Y",
				"MESSAGE_AGREE" => "Спасибо, ваше сообщение отправлено!",
				"RS_FLYAWAY_EXT_FIELDS_COUNT" => "0"
			),
			false
		);?>
	</div>
</div>

<script>
jQuery('.JS-Dropdown').each(function() {
	var $switcher = jQuery(this).find('.JS-Dropdown-Switcher'),
			$bar = jQuery(this).find('.JS-Dropdown-Bar');

	$switcher.click(function() {
		$bar.show();
		jQuery(this).hide();
	});
});
</script>

<?$APPLICATION->IncludeFile("#SITE_DIR#include_areas/contacts.php",array(),array("MODE"=>"html"));?>
						
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
