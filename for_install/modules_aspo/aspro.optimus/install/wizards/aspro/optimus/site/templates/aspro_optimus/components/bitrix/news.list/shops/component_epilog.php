<?/*$arShops=COptimus::prepareShopListArray($arResult["ITEMS"], $arParams);?>
<?if($arShops["SHOPS"]):?>
	<?ob_start()?>
	<?if(abs($arShops["POINTS"]["LAT"]) > 0 && abs($arShops["POINTS"]["LON"]) > 0):?>
		<?
		$mapLAT = floatval($arShops["POINTS"]["LAT"] / count($arShops["SHOPS"]));
		$mapLON = floatval($arShops["POINTS"]["LON"] / count($arShops["SHOPS"]));
		?>
		<div class="contacts_map">
			<?$APPLICATION->IncludeComponent(
				"bitrix:map.google.view",
				"map",
				array(
					"INIT_MAP_TYPE" => "ROADMAP",
					"MAP_DATA" => serialize(array("google_lat" => $mapLAT, "google_lon" => $mapLON, "google_scale" => 15, "PLACEMARKS" => $arShops["PLACEMARKS"])),
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
<?endif;*/?>