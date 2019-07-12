<h3>Мы на карте</h3>
<?$APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view",
	".default",
	Array(
		"INIT_MAP_TYPE" => "MAP",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.15609313330657;s:10:\"yandex_lon\";d:61.41569211831929;s:12:\"yandex_scale\";i:12;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:61.417335492187;s:3:\"LAT\";d:55.15674465914;s:4:\"TEXT\";s:0:\"\";}}}",
		"MAP_WIDTH" => "320",
		"MAP_HEIGHT" => "240",
		"CONTROLS" => array("SMALLZOOM", "TYPECONTROL"),
		"OPTIONS" => array("ENABLE_SCROLL_ZOOM", "ENABLE_DBLCLICK_ZOOM", "ENABLE_DRAGGING"),
		"MAP_ID" => ""
	)
);?> 