<h3>Мы на карте</h3>
<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	"",
	Array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.822708885935214;s:10:\"yandex_lon\";d:37.60503645501709;s:12:\"yandex_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.60589476190185;s:3:\"LAT\";d:55.822908198854975;s:4:\"TEXT\";s:0:\"\";}}}",
		"MAP_WIDTH" => "320",
		"MAP_HEIGHT" => "240",
		"CONTROLS" => array("ZOOM"),
		"OPTIONS" => array("ENABLE_SCROLL_ZOOM", "ENABLE_DBLCLICK_ZOOM", "ENABLE_DRAGGING"),
		"MAP_ID" => ""
	),
false
);?>