<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?><?$APPLICATION->IncludeComponent(
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
);?> <br>
 <br>
<div style="margin-bottom:40px;" class="JS-Dropdown">
 <a class="btn btn-default JS-Dropdown-Switcher" href="javascript:;">Задать вопрос</a>
	<div class="JS-Dropdown-Bar" style="display:none;">
<?$APPLICATION->IncludeComponent(
	"rsmonopoly:forms", 
	"monopoly", 
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
		"INPUT_NAME_RS_EMAIL" => "Адрес вашей электронной почты",
		"INPUT_NAME_RS_TEXTAREA" => "Текст сообщения",
		"ALFA_MESSAGE_AGREE" => "Спасибо, ваше сообщение отправлено!",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"AJAX_OPTION_ADDITIONAL" => "",
		"EVENT_TYPE" => "RS_MONOPOLY_CONTACTS",
		"FORM_TITLE" => "Задать вопрос",
		"FORM_DESCRIPTION" => "",
		"EMAIL_TO" => "",
		"USE_CAPTCHA" => "Y",
		"MESSAGE_AGREE" => "Спасибо, ваше сообщение отправлено!",
		"RS_MONOPOLY_EXT_FIELDS_COUNT" => "0"
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
<h2><span style="font-size: 22px;">ООО «Монополия»</span></h2>
<table cellpadding="0" cellspacing="0" height="228">
<tbody>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">&nbsp;Адрес: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">440013, Россия, г. Москва, ул. Егоркина, д. 125, кор. 2, офис 3145 </span>
	</td>
</tr>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">Телефоны: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">8 800 000-00-00</span><br>
 <span style="font-size: 13px;">
		80 927 0 888 88 88</span><br>
 <span style="font-size: 13px;">
		8 800 000-00-00</span>
	</td>
</tr>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">Факс: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">
		8 800 000-00-00</span><br>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">Эл. почта: </span>
	</td>
	<td colspan="1" valign="top">
 <a href="mailto:info@info.com"><span style="font-size: 13px;">info@info.com</span></a>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">Режим работы: </span>
	</td>
	<td colspan="1" valign="top">
 <span style="font-size: 13px;">Понедельник–пятница, с 8:00 до 18:00</span><br>
 <span style="font-size: 13px;">
		Суббота, с 8:00 до 15:00</span><br>
 <span style="font-size: 13px;">
		Воскресенье — выходной </span>
	</td>
</tr>
</tbody>
</table>
<h2 style="font-size: 22px;">
Отдел оптовой торговли</h2>
<table style="margin-bottom:25px;" cellpadding="4" cellspacing="0" height="228">
<tbody>
<tr>
	<td valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">
		Адрес: </span>
	</td>
	<td valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">440013, Россия, г. Москва, ул. Егоркина, д. 125, кор. 2, офис 3100</span>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Руководитель:</span><br>
	</td>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Максим Смирнов</span>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Специалисты:</span>
	</td>
	<td colspan="1" style="font-size: 13px;padding-bottom:7px;" valign="top">
 <span style="font-size: 13px;">Егор Кнонов</span><br>
 <span style="font-size: 13px;">Тимур Абидов</span><br>
 <span style="font-size: 13px;">Елена Константинова</span><br>
	</td>
</tr>
<tr>
	<td valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Телефоны: </span>
	</td>
	<td valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">8 800 000-00-00 </span><br>
 <span style="font-size: 13px;">80 927 0 888 88 88</span><br>
 <span style="font-size: 13px;">8 800 000-00-00</span><br>
 <span style="font-size: 13px;">8 800 000-00-00</span><br>
 <span style="font-size: 13px;">80 927 0 888 88 88</span><br>
	</td>
</tr>
<tr>
	<td valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Факс: </span>
	</td>
	<td valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">
		8 800 000-00-00</span><br>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Эл. почта: </span>
	</td>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <a href="mailto:info@info.com"><span style="font-size: 13px;">info@info.com</span></a>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Режим работы: </span>
	</td>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Понедельник–пятница, с 8:00 до 18:00</span><br>
 <span style="font-size: 13px;">
		Суббота, с 8:00 до 15:00</span><br>
 <span style="font-size: 13px;">
		Воскресенье — выходной </span>
	</td>
</tr>
</tbody>
</table>
<h2 style="font-size: 22px;">Юридический отдел</h2>
 <span style="margin-bottom:30px;">
<table class="" cellpadding="0" cellspacing="0" height="228">
<tbody>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">
		&nbsp;Адрес: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">440013, Россия, г. Москва, ул. Егоркина, д. 125, кор. 2, офис 3250 </span>
	</td>
</tr>
<tr>
	<td colspan="1">
 <span style="font-size: 13px;">Руководитель:</span>
	</td>
	<td colspan="1">
 <span style="font-size: 13px;">Елена&nbsp; Смирнова</span>
	</td>
</tr>
<tr>
	<td colspan="1">
 <span style="font-size: 13px;">Специалисты:</span>
	</td>
	<td colspan="1">
 <span style="font-size: 13px;">Анна Завьялова</span>
	</td>
</tr>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">Телефоны: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">8 800 000-00-00</span><br>
 <span style="font-size: 13px;">
		80 927 0 888 88 88</span><br>
 <span style="font-size: 13px;"> </span>
	</td>
</tr>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">Факс: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">
		8 800 000-00-00</span><br>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">Эл. почта: </span>
	</td>
	<td colspan="1" valign="top">
 <a href="mailto:info@info.com"><span style="font-size: 13px;">info@info.com</span></a>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">Режим работы: </span>
	</td>
	<td colspan="1" valign="top">
 <span style="font-size: 13px;">Понедельник–пятница, с 8:00 до 18:00</span><br>
 <span style="font-size: 13px;">
		Суббота, воскресенье — выходной</span>
	</td>
</tr>
</tbody>
</table>
 </span>
<h2><span style="font-size: 22px;">Реквизиты компании</span></h2>
<div style="font-size:13px;line-height:19px;">
	<p style="margin-bottom:20px;">
		 Свидетельство: серия 59 № 888888888 от 20.12.14 года<br>
		 выдано ИМНС РФ по Новому району г. Москвы
	</p>
	<p style="margin-bottom:20px;">
		 ОГРН 404088888800666<br>
		 ИНН 888800000099<br>
		 р/с 88888800000069696988<br>
	</p>
	<p style="margin-bottom:20px;">
		 Московское ОСБ № 8960<br>
		 БИК 088656565<br>
		 к/с 88888810000000000999<br>
		 ОКОНХ 88000<br>
		 ОКВЭД 54.58.32.; 60.22.9.<br>
		 ОКПО 088809099
	</p>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>