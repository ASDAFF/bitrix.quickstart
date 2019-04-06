<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	/** @var CBitrixComponent $this */
	/** @var array $arParams */
	/** @var array $arResult */
	/** @var string $componentPath */
	/** @var string $componentName */
	/** @var string $componentTemplate */
	/** @global CDatabase $DB */
	/** @global CUser $USER */
	/** @global CMain $APPLICATION */

	if(!CModule::IncludeModule("iblock")) return;
	
	if (!empty($arParams['MAP_WIDTH'])) {
		if ((stristr($arParams['MAP_WIDTH'], '%') === FALSE) && 
		(stristr($arParams['MAP_WIDTH'], 'px') === FALSE) &&
		(stristr($arParams['MAP_WIDTH'], 'em') === FALSE) &&
		(stristr($arParams['MAP_WIDTH'], 'rem') === FALSE)) {
			$arParams['MAP_WIDTH'] = $arParams['MAP_WIDTH']."px";
		}
	}
	
	if (!empty($arParams['MAP_HEIGHT'])) {
		if ((stristr($arParams['MAP_HEIGHT'], '%') === FALSE) && 
		(stristr($arParams['MAP_HEIGHT'], 'px') === FALSE) &&
		(stristr($arParams['MAP_HEIGHT'], 'em') === FALSE) &&
		(stristr($arParams['MAP_HEIGHT'], 'rem') === FALSE)) {
			$arParams['MAP_HEIGHT'] = $arParams['MAP_HEIGHT']."px";
		}
	}
	
	if (empty($arParams['MAP_CENTER'])) {
		$arParams['MAP_CENTER'] = "56.8378,60.6034";
	}
	
	if (empty($arParams['MAP_ZOOM'])) {
		$arParams['MAP_ZOOM'] = "12";
	}
	
	if(!empty($arParams['MAP_VIEW'])) {
		switch ($arParams['MAP_VIEW']) {
			case "VIEW_SCHEME":
				$arParams['MAP_VIEW']='yandex#map';
				break;
			case "VIEW_SATELLITE":
				$arParams['MAP_VIEW']='yandex#satellite';
				break;
			case "VIEW_HYBRID":
				$arParams['MAP_VIEW']='yandex#hybrid';
				break;
		}
	}
	
	if(!empty($arParams['MAP_CONTROLS'])) {
		$i = 0;
		foreach($arParams['MAP_CONTROLS'] as $arElement) {
			switch ($arElement) {
				case "CONTROLS_NONE":
					$arParams['MAP_CONTROLS'][$i]='none';
					break;
				case "CONTROLS_VIEW_SELECTOR":
					$arParams['MAP_CONTROLS'][$i]='typeSelector';
					break;
				case "CONTROLS_ZOOM_CONTROL":
					$arParams['MAP_CONTROLS'][$i]='zoomControl';
					break;
				case "CONTROLS_SMALL_ZOOM_CONTROL":
					$arParams['MAP_CONTROLS'][$i]='smallZoomControl';
					break;
				case "CONTROLS_SCALE_LINE":
					$arParams['MAP_CONTROLS'][$i]='scaleLine';
					break;
				case "CONTROLS_MINI_MAP":
					$arParams['MAP_CONTROLS'][$i]='miniMap';
					break;
				case "CONTROLS_SEARCH":
					$arParams['MAP_CONTROLS'][$i]='searchControl';
					break;
				case "CONTROLS_TRAFFIC":
					$arParams['MAP_CONTROLS'][$i]='trafficControl';
					break;
			}
			$i++;
		}
	}
	
	if (!empty($arParams['MAP_POINTS'])) {
		$arParams['MAP_POINTS']=str_replace(' ', '', $arParams['MAP_POINTS']);	
		$arParams['MAP_POINTS']=explode(";",$arParams['MAP_POINTS']);
	}
	
	if (!empty($arParams['MAP_POINTS_TEXT'])) {
		$arParams['MAP_POINTS_TEXT']=str_replace('  ', ' ', $arParams['MAP_POINTS_TEXT']);
		$arParams['MAP_POINTS_TEXT']=str_replace('; ', ';', $arParams['MAP_POINTS_TEXT']);
		$arParams['MAP_POINTS_TEXT']=explode(";",$arParams['MAP_POINTS_TEXT']);
	}
	
	$component_dir=substr(__DIR__, strpos(__DIR__, "/bitrix"), strlen(__DIR__));

	$APPLICATION->AddHeadScript("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"); 
	$APPLICATION->AddHeadScript("//api-maps.yandex.ru/2.1/?lang=ru_RU");
	$APPLICATION->AddHeadScript($component_dir."/js/connect_map.js");

	$this->IncludeComponentTemplate();
?>