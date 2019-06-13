<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<div id="ymap<?=$arParams['MAP_ID']?>"
	class="ymap"
	data-map-id="<?=$arParams['MAP_ID']?>"
    data-map-center="<?=$arParams['MAP_CENTER']?>"
    data-map-zoom="<?=$arParams['MAP_ZOOM']?>"
    data-map-points=<?=json_encode($arParams['MAP_POINTS'])?>
    data-map-points-text=<?=json_encode($arParams['MAP_POINTS_TEXT'])?>
    data-map-view=<?=json_encode($arParams['MAP_VIEW'])?>
    data-map-controls=<?=json_encode($arParams['MAP_CONTROLS'])?>
	style="width: <?=$arParams['MAP_WIDTH']?>; height: <?=$arParams['MAP_HEIGHT']?>;"
></div>