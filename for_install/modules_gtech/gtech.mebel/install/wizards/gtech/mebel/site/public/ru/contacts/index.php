<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");?> 
<table width="100%" border="0" cellpadding="5" cellspacing="1"> 
  <tbody> 
    <tr><td align="left" valign="top">Адрес: г. Благовещенск, ул. Мебельная, 8/2, офис 113  
        <br />
       Тел.: 8 (4162) 000-000,  факс: 8 (4162) 00-00-00 </td><td align="left" valign="top"> <?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
	"INIT_MAP_TYPE" => "PUBLIC",
	"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:50.260602913358255;s:10:\"yandex_lon\";d:127.53600212808558;s:12:\"yandex_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:127.53602358575775;s:3:\"LAT\";d:50.2607198358486;s:4:\"TEXT\";s:19:\"\"Мебельный магазин\"\";}}}",
	"MAP_WIDTH" => "600",
	"MAP_HEIGHT" => "500",
	"CONTROLS" => array(
		0 => "ZOOM",
		1 => "MINIMAP",
		2 => "TYPECONTROL",
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
);?> </td></tr>
   </tbody>
 </table>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>