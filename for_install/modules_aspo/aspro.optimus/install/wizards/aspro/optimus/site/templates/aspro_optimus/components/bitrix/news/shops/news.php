<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

<?
$arItemsFilter = COptimus::GetIBlockAllElementsFilter($arParams);

if(!($bMap = in_array('MAP', $arParams['LIST_PROPERTY_CODE']))){
	$itemsCnt = COptimusCache::CIBlockElement_GetList(array('CACHE' => array('TAG' => COptimusCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), $arItemsFilter, array());
}
else{
	// get items & coordinates
	$arItems = COptimusCache::CIBlockElement_GetList(array('CACHE' => array('TAG' => COptimusCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'URL_TEMPLATE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'])), $arItemsFilter, false, false, array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_TEXT', 'PROPERTY_ADDRESS', 'PROPERTY_PHONE', 'PROPERTY_EMAIL', 'PROPERTY_SCHEDULE', 'PROPERTY_METRO', 'PROPERTY_MAP'));
	$itemsCnt = count($arItems);
}
// print_r($arItems);
if($bMap && $itemsCnt){
		foreach($arItems as $arItem){
			// element coordinates
			$arItem['GPS_S'] = $arItem['GPS_N'] = false;
			if($arStoreMap = explode(',', $arItem['PROPERTY_MAP_VALUE'])){
				$arItem['GPS_S'] = $arStoreMap[0];
				$arItem['GPS_N'] = $arStoreMap[1];
			}

			// use detail link?
			$bDetailLink = $arParams['SHOW_DETAIL_LINK'] !== 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);

			$html = '';
			// element name
			if(in_array('NAME', $arParams['LIST_FIELD_CODE']) && strlen($arItem['NAME'])){
				$html .= '<div class="title">';
					if($bDetailLink){
						$html .= '<a class="dark_font" href="'.$arItem['DETAIL_PAGE_URL'].'">';
					}
					$html .= $arItem['NAME'];
					
					$bAddress = in_array('ADDRESS', $arParams['LIST_PROPERTY_CODE']) && ($arItem['PROPERTY_ADDRESS_VALUE'] || (!is_array($arItem['PROPERTY_ADDRESS_VALUE']) && strlen($arItem['PROPERTY_ADDRESS_VALUE'])));
					if($bAddress){
							$value = (is_array($arItem['PROPERTY_ADDRESS_VALUE']) ? implode('<br />', $arItem['PROPERTY_ADDRESS_VALUE']) : (strlen($arItem['PROPERTY_ADDRESS_VALUE']) ? $arItem['PROPERTY_ADDRESS_VALUE'] : ''));
							if($value){
								$html .= ((strlen($arItem['NAME']) && strlen($value)) ? ', ' : '').$value;
							}
						}

					if($bDetailLink){
						$html .= '</a>';
					}
				$html .= '</div>';
			}

			$html .= '<div class="info-content">';
				// element metro				
				$bMetro = in_array('METRO', $arParams['LIST_PROPERTY_CODE']) && ($arItem['PROPERTY_METRO_VALUE'] || (!is_array($arItem['PROPERTY_METRO_VALUE']) && strlen($arItem['PROPERTY_METRO_VALUE'])));
				if($bMetro){
					if($bMetro){
						$value = (is_array($arItem['PROPERTY_METRO_VALUE']) ? implode(', ', $arItem['PROPERTY_METRO_VALUE']) : (strlen($arItem['PROPERTY_METRO_VALUE']) ? $arItem['PROPERTY_METRO_VALUE'] : ''));
						if($value){
							$html .= '<div class="metro"><i></i>'.$value.'</div>';
						}
					}
				}

				// element schedule
				if(in_array('SCHEDULE', $arParams['LIST_PROPERTY_CODE']) && ($arItem['PROPERTY_SCHEDULE_VALUE'] || strlen($arItem['PROPERTY_SCHEDULE_VALUE']))){
					$html .= '<div class="schedule">'.$arItem['~PROPERTY_SCHEDULE_VALUE']['TEXT'].'</div>';
				}

				// element phone
				if(in_array('PHONE', $arParams['LIST_PROPERTY_CODE']) && ($arItem['PROPERTY_PHONE_VALUE'] || (!is_array($arItem['PROPERTY_PHONE_VALUE']) && strlen($arItem['PROPERTY_PHONE_VALUE'])))){
					if(is_array($arItem['PROPERTY_PHONE_VALUE'])){
						$values = array();
						foreach($arItem['PROPERTY_PHONE_VALUE'] as $value){
							$values[] = '<a href="tel:'.str_replace(array(' ', ',', '-', '(', ')'), '', $value).'">'.$value.'</a>';
						}
						$html .= '<div class="phone">'.implode('<br>', $values).'</div>';
					}
					elseif(strlen($arItem['PROPERTY_PHONE_VALUE'])){
						$html .= '<div class="phone"><a href="tel:'.str_replace(array(' ', ',', '-', '(', ')'), '', $arItem['PROPERTY_PHONE_VALUE']).'">'.$arItem['PROPERTY_PHONE_VALUE'].'</a></div>';
					}
				}

				// element email
				if(in_array('EMAIL', $arParams['LIST_PROPERTY_CODE']) && ($arItem['PROPERTY_EMAIL_VALUE'] || (!is_array($arItem['PROPERTY_EMAIL_VALUE']) && strlen($arItem['PROPERTY_EMAIL_VALUE'])))){
					if(is_array($arItem['PROPERTY_EMAIL_VALUE'])){
						$values = array();
						foreach($arItem['PROPERTY_EMAIL_VALUE'] as $value){
							$values[] = '<a href="mailto:'.$value.'">'.$value.'</a>';
						}
						$html .= '<div class="email">'.implode(', ', $values).'</div>';
					}
					elseif(strlen($arItem['PROPERTY_EMAIL_VALUE'])){
						$html .= '<div class="email"><a href="mailto:'.$arItem['PROPERTY_EMAIL_VALUE'].'">'.$arItem['PROPERTY_EMAIL_VALUE'].'</a></div>';
					}
				}
			$html .= '</div>';

			// detail page link
			if($bDetailLink){
				$html .= '<a class="button" href="'.$arItem['DETAIL_PAGE_URL'].'"><span>'.GetMessage('T_SHOPS_DETAIL').'</span></a>';
			}

			// add placemark to map
			if($arItem['GPS_S'] && $arItem['GPS_N']){
				$mapLAT += $arItem['GPS_S'];
				$mapLON += $arItem['GPS_N'];
				$arPlacemarks[] = array(
					"ID" => $arItem["ID"],
					"LAT" => $arItem['GPS_S'],
					"LON" => $arItem['GPS_N'],
					"TEXT" => $arItem['NAME'],
					"HTML" => $html,
				);
			}
		}

		// map?>
		<div class="contacts_map">
			<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID('shops-map-block');?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:map.google.view",
					"map",
					array(
						"INIT_MAP_TYPE" => "ROADMAP",
						"MAP_DATA" => serialize(array("google_lat" => $mapLAT, "google_lon" => $mapLON, "google_scale" => 15, "PLACEMARKS" => $arPlacemarks)),
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
						"API_KEY" => $arParams["GOOGLE_API_KEY"],
						"COMPONENT_TEMPLATE" => "map",
						"COMPOSITE_FRAME_MODE" => "A",
						"COMPOSITE_FRAME_TYPE" => "AUTO"
					),
					false,
					array(
						"HIDE_ICONS" => "Y"
					)
				);?>
			<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID('shops-map-block', '');?>
		</div>
	<?}
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"shops",
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
		"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
		"SORT_BY1"	=>	$arParams["SORT_BY1"],
		"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
		"SORT_BY2"	=>	$arParams["SORT_BY2"],
		"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
		"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
		"SET_TITLE"	=>	"N",
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
		"CACHE_TYPE"	=>	'A', // for map!
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER"	=>	$arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
		"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
		"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
		"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
		"DISPLAY_NAME"	=>	"Y",
		"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
	),
	$component
);?>