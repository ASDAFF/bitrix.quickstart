<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Contacts");
?><?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"",
	Array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.73829999999371;s:10:\"yandex_lon\";d:37.59459999999997;s:12:\"yandex_scale\";i:10;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.618975915527315;s:3:\"LAT\";d:55.74556286632916;s:4:\"TEXT\";s:46:\"Phone.: 8 (777) 666 55 44###RN###Fax: 666 55 22\";}}}",
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
 <a class="btn btn-default JS-Dropdown-Switcher" href="javascript:;">Ask a Question</a>
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
		"INPUT_NAME_RS_NAME" => "Your name",
		"INPUT_NAME_RS_EMAIL" => "Your e-mail address",
		"INPUT_NAME_RS_TEXTAREA" => "Message text",
		"ALFA_MESSAGE_AGREE" => "Thank you, your message has been sent!",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"AJAX_OPTION_ADDITIONAL" => "",
		"EVENT_TYPE" => "RS_MONOPOLY_CONTACTS",
		"FORM_TITLE" => "Ask a Question",
		"FORM_DESCRIPTION" => "",
		"EMAIL_TO" => "",
		"USE_CAPTCHA" => "Y",
		"MESSAGE_AGREE" => "Thank you, your message has been sent!",
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
<h2><span style="font-size: 22px;">JSC &laquo;Monopoly&raquo;</span></h2>
<table cellpadding="0" cellspacing="0" height="228">
<tbody>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">&nbsp;Address: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">440013, Russia, Moscow city, Yegorkina st., home 125, housing 2, office 3145 </span>
	</td>
</tr>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">Phones: </span>
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
 <span style="font-size: 13px;">Fax: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">
		8 800 000-00-00</span><br>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">E-mail: </span>
	</td>
	<td colspan="1" valign="top">
 <a href="mailto:info@info.com"><span style="font-size: 13px;">info@info.com</span></a>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">Shedule: </span>
	</td>
	<td colspan="1" valign="top">
 <span style="font-size: 13px;">Monday-Friday, 8:00 - 18:00</span><br>
 <span style="font-size: 13px;">
		Saturday, 8:00 - 15:00</span><br>
 <span style="font-size: 13px;">
		Sunday — day off </span>
	</td>
</tr>
</tbody>
</table>
<h2 style="font-size: 22px;">
Wholesale Department</h2>
<table style="margin-bottom:25px;" cellpadding="4" cellspacing="0" height="228">
<tbody>
<tr>
	<td valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">
		Address: </span>
	</td>
	<td valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">440013, Russia, Moscow city, Yegorkina st., home 125, housing 2, office 3100</span>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Head:</span><br>
	</td>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Maxim Smirnov</span>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Specialists:</span>
	</td>
	<td colspan="1" style="font-size: 13px;padding-bottom:7px;" valign="top">
 <span style="font-size: 13px;">Yegor Knonov</span><br>
 <span style="font-size: 13px;">Timur Abidov</span><br>
 <span style="font-size: 13px;">Elena Konstantinova</span><br>
	</td>
</tr>
<tr>
	<td valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Phones: </span>
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
 <span style="font-size: 13px;">Fax: </span>
	</td>
	<td valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">
		8 800 000-00-00</span><br>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">E-mail: </span>
	</td>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <a href="mailto:info@info.com"><span style="font-size: 13px;">info@info.com</span></a>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Shedule: </span>
	</td>
	<td colspan="1" valign="top" style="padding-bottom:7px;">
 <span style="font-size: 13px;">Monday-Friday, 8:00 - 18:00</span><br>
 <span style="font-size: 13px;">
		Saturday, 8:00 - 15:00</span><br>
 <span style="font-size: 13px;">
		Sunday — day off </span>
	</td>
</tr>
</tbody>
</table>
<h2 style="font-size: 22px;">Legal Department</h2>
 <span style="margin-bottom:30px;">
<table class="" cellpadding="0" cellspacing="0" height="228">
<tbody>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">
		&nbsp;Address: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">440013, Russia, Moscow city, Yegorkina st., home 125, housing 2, office 3250 </span>
	</td>
</tr>
<tr>
	<td colspan="1">
 <span style="font-size: 13px;">Head:</span>
	</td>
	<td colspan="1">
 <span style="font-size: 13px;">Elena Smirnova</span>
	</td>
</tr>
<tr>
	<td colspan="1">
 <span style="font-size: 13px;">Specialists:</span>
	</td>
	<td colspan="1">
 <span style="font-size: 13px;">Anna Zavyalova</span>
	</td>
</tr>
<tr>
	<td valign="top" width="170">
 <span style="font-size: 13px;">Phones: </span>
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
 <span style="font-size: 13px;">Fax: </span>
	</td>
	<td valign="top">
 <span style="font-size: 13px;">
		8 800 000-00-00</span><br>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">E-mail: </span>
	</td>
	<td colspan="1" valign="top">
 <a href="mailto:info@info.com"><span style="font-size: 13px;">info@info.com</span></a>
	</td>
</tr>
<tr>
	<td colspan="1" valign="top" width="170">
 <span style="font-size: 13px;">Shedule: </span>
	</td>
	<td colspan="1" valign="top">
 <span style="font-size: 13px;">Monday-Friday, 8:00 - 18:00</span><br>
 <span style="font-size: 13px;">
		Saturday, Sunday — day off</span>
	</td>
</tr>
</tbody>
</table>
 </span>
<h2><span style="font-size: 22px;">Company details</span></h2>
<div style="font-size:13px;line-height:19px;">
	<p style="margin-bottom:20px;">
		Certificate: series 59 ¹ 888888888 of 20.12.2014<br>
		issued by the Inspectorate of the Russian Federation on the new district of Moscow
	</p>
	<p style="margin-bottom:20px;">
		 BIN 404088888800666<br>
		 INN 888800000099<br>
		 p/s 88888800000069696988<br>
	</p>
	<p style="margin-bottom:20px;">
		 Moscow OSB ¹ 8960<br>
		 BIC 088656565<br>
		 k/s 88888810000000000999<br>
		 OKONH 88000<br>
		 NACE 54.58.32.; 60.22.9.<br>
		 OKPO 088809099
	</p>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>