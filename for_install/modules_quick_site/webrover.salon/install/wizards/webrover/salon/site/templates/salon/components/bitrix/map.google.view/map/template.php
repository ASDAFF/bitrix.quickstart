<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTransParams = array(
	'INIT_MAP_TYPE' => $arParams['INIT_MAP_TYPE'],
	'INIT_MAP_LON' => $arResult['POSITION']['google_lon'],
	'INIT_MAP_LAT' => $arResult['POSITION']['google_lat'],
	'INIT_MAP_SCALE' => $arResult['POSITION']['google_scale'],
	'MAP_WIDTH' => $arParams['MAP_WIDTH'],
	'MAP_HEIGHT' => $arParams['MAP_HEIGHT'],
	'CONTROLS' => $arParams['CONTROLS'],
	'OPTIONS' => $arParams['OPTIONS'],
	'MAP_ID' => $arParams['MAP_ID'],
);

if ($arParams['DEV_MODE'] == 'Y')
{
	$arTransParams['DEV_MODE'] = 'Y';
	if ($arParams['WAIT_FOR_EVENT'])
		$arTransParams['WAIT_FOR_EVENT'] = $arParams['WAIT_FOR_EVENT'];
}
?>
<div class="bx-yandex-view-layout">
	<div class="bx-yandex-view-map">
<?
//echo '<pre>'; print_r($arResult['POSITION']); echo '</pre>';

$APPLICATION->IncludeComponent('bitrix:map.google.system', '.default', $arTransParams, false, array('HIDE_ICONS' => 'Y'));
?>
	</div>
</div>
<?if (is_array($arResult['POSITION']['PLACEMARKS']) && ($cnt = count($arResult['POSITION']['PLACEMARKS']))):?>
<script type="text/javascript">
function BX_SetPlacemarks_<?echo $arParams['MAP_ID']?>()
{
<?
	for($i = 0; $i < $cnt; $i++):
?>
	BX_GMapAddPlacemark(<?echo CUtil::PhpToJsObject($arResult['POSITION']['PLACEMARKS'][$i])?>, '<?echo $arParams['MAP_ID']?>');
<?
	endfor;
?>
}

function BXShowMap_<?echo $arParams['MAP_ID']?>() {BXWaitForMap_view('<?echo $arParams['MAP_ID']?>');}

BX.ready(BXShowMap_<?echo $arParams['MAP_ID']?>);
</script>
<?endif;?>