<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?> 
<h4>Позвоните нам</h4>
 
<div>+7 (495) 777-77-77</div>
 
<div>+7 (495) 777-77-77</div>
 
<div>+7 (495) 777-77-77</div>

<div>
  <br />
</div>
 
<h4>Наш адрес</h4>
 
<div>Россия, г. Москва, ул Аккадемика Королева 12, офис 7</div>
 
<div> 
  <br />
 </div>
 <?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
	"INIT_MAP_TYPE" => "MAP",
	"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.15609313330657;s:10:\"yandex_lon\";d:61.41569211831929;s:12:\"yandex_scale\";i:12;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:61.417335492187;s:3:\"LAT\";d:55.15674465914;s:4:\"TEXT\";s:0:\"\";}}}",
	"MAP_WIDTH" => "660",
	"MAP_HEIGHT" => "240",
	"CONTROLS" => array(
		0 => "SMALLZOOM",
		1 => "TYPECONTROL",
	),
	"OPTIONS" => array(
		0 => "ENABLE_SCROLL_ZOOM",
		1 => "ENABLE_DBLCLICK_ZOOM",
		2 => "ENABLE_DRAGGING",
	),
	"MAP_ID" => ""
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>