<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*
	 'smallMapDefaultSet';
	'mediumMapDefaultSet' (по умолчанию);
	'largeMapDefaultSet'.

	"rulerControl" - линейка и масштабный отрезок control.RulerControl;
	"searchControl" - панель поиска control.SearchControl;
	"trafficControl" - панель пробок control.TrafficControl;
	"typeSelector" - панель переключения типа карты control.TypeSelector;
	"zoomControl" - ползунок масштаба control.ZoomControl;
	"geolocationControl" - элемент управления геолокацией control.GeolocationControl;
	"routeEditor" - редактор маршрутов control.RouteEditor.
 */


$mapId = $arParams['MAP_ID'];
$params = array(
	'id' => $mapId,
	'address' => $arParams['ADDRESS'],
	'header' => $arParams["~NAME"],
	'body' => empty($arParams["BODY"]) ? false : $arParams["~BODY"],
	'footer' => empty($arParams["FOOTER"]) ? false : $arParams["~FOOTER"],
	'controls' => array('smallMapDefaultSet'),
	'openBallon' => $arParams["OPEN_BALOON"],
);

$APPLICATION->AddHeadScript('//api-maps.yandex.ru/2.1/?lang=ru_RU');

?><div id="<?=$mapId?>" style="width: <?=$arParams['MAP_WIDTH']?>;height: <?=$arParams['MAP_HEIGHT']?>"></div><?

?>
<script type="text/javascript">
$().citrusRealtyAddress(<?=CUtil::PhpToJSObject($params)?>);
</script>
