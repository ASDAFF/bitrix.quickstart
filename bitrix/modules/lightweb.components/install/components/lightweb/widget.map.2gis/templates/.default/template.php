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

<div 	id="<?=$arParams['MAP_ID']?>" 
		class="lwMap2Gis"
        style="width:<?=$arParams['~WIDTH']?>; height:<?=$arParams['~HEIGHT']?>"
        data-center-lat="<?=$arParams['CENTER_MAP'][0]?>"
        data-center-long="<?=$arParams['CENTER_MAP'][1]?>"
        data-zoom="<?=$arParams['ZOOM_MAP']?>"
        data-doubleClickZoom="<?=$arParams['DOUBLE_CLICK_ZOOM']?>"
        data-geoclicker="<?=$arParams['GEOCLICKER']?>"
        data-coordinates-points-lat="<?=$arParams['COORDINATES_POINTS'][0]?>"
        data-coordinates-points-long="<?=$arParams['COORDINATES_POINTS'][1]?>"
        data-iconpoints="<?=$arParams['ICON_POINTS']['FILE']?>"
        data-iconpoints-width="<?=$arParams['ICON_POINTS']['WIDTH']?>"
        data-iconpoints-height="<?=$arParams['ICON_POINTS']['HEIGHT']?>"
        data-post-points="<?=$arParams['POST_POINTS']?>"></div>