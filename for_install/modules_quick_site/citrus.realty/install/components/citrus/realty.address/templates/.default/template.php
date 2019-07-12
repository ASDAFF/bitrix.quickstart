<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*
	 'smallMapDefaultSet';
	'mediumMapDefaultSet' (�� ���������);
	'largeMapDefaultSet'.

	"rulerControl" - ������� � ���������� ������� control.RulerControl;
	"searchControl" - ������ ������ control.SearchControl;
	"trafficControl" - ������ ������ control.TrafficControl;
	"typeSelector" - ������ ������������ ���� ����� control.TypeSelector;
	"zoomControl" - �������� �������� control.ZoomControl;
	"geolocationControl" - ������� ���������� ����������� control.GeolocationControl;
	"routeEditor" - �������� ��������� control.RouteEditor.
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
