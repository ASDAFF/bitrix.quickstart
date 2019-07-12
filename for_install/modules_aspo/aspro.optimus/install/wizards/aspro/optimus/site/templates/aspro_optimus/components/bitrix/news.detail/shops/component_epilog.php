<?
$arResult['TITLE'] = (in_array('NAME', $arParams['FIELD_CODE']) ? $arResult['NAME'] : '');
$arResult['ADDRESS'] = (in_array('ADDRESS', $arParams['PROPERTY_CODE']) ? $arResult['PROPERTIES']['ADDRESS']['VALUE'] : '');
$arResult['ADDRESS'] = $arResult['TITLE'].((strlen($arResult['TITLE']) && strlen($arResult['ADDRESS'])) ? ', ' : '').$arResult['ADDRESS'];
$_SESSION['SHOP_TITLE'] = $arResult['ADDRESS'];
?>
<?$arShop=COptimus::prepareShopDetailArray($arResult, $arParams);?>
<?ob_start()?>
	<?if(abs($arShop["POINTS"]["LAT"]) > 0 && abs($arShop["POINTS"]["LON"]) > 0):?>
		<div class="contacts_map">
			<?$APPLICATION->IncludeComponent(
			"bitrix:map.google.view",
			"map",
			array(
				"INIT_MAP_TYPE" => "ROADMAP",
				"MAP_DATA" => serialize(array("google_lat" => $arShop["POINTS"]["LAT"], "google_lon" => $arShop["POINTS"]["LON"], "google_scale" => 16, "PLACEMARKS" => $arShop["PLACEMARKS"])),
				"MAP_WIDTH" => "100%",
				"MAP_HEIGHT" => "400",
				"CONTROLS" => array(
				),
				"OPTIONS" => array(
					0 => "ENABLE_DBLCLICK_ZOOM",
					1 => "ENABLE_DRAGGING",
				),
				"MAP_ID" => "",
				"ZOOM_BLOCK" => array(
					"POSITION" => "right center",
				),
				"COMPONENT_TEMPLATE" => "map",
				"API_KEY" => $arParams["GOOGLE_API_KEY"],
				"COMPOSITE_FRAME_MODE" => "A",
				"COMPOSITE_FRAME_TYPE" => "AUTO"
			),
			false, array("HIDE_ICONS" =>"Y")
		);?>
		</div>
	<?endif;?>
<?$html=ob_get_clean();?>
<?$APPLICATION->AddViewContent('map_content', $html);?>