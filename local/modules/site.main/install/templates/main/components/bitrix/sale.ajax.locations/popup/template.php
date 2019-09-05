<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!$arParams['LOCATION_VALUE']
	&& $arParams['PROP_VALUE']
	&& $arParams['LOCATION_VALUE'] != $arParams['PROP_VALUE']
) {
	$arParams['LOCATION_VALUE'] = $arParams['PROP_VALUE'];
	$arResult['LOCATION_STRING'] = $arParams['PROP_VALUE_FORMATED'];
}
?>

<div class="sale-ajax-locations sale-ajax-locations-popup dropdown" data-ajax-gate="<?=$templateFolder?>/ajax.php" data-params='<?=json_encode(array('SITE_ID' => $arParams['SITE_ID']))?>'>
	<?if ($arParams['AJAX_CALL'] != 'Y') {
		?>
		<link rel="stylesheet" href="<?=$templateFolder?>/template.css?<?=time()?>"/>
		<script src="<?=$templateFolder?>/template.js?<?=time()?>"></script>
		<?
	}?>
	<input
		class="form-control location-name"
		type="text"
		name="<?=$arParams['CITY_INPUT_NAME']?>_string"
		id="<?=$arParams['DOM_ID']?>"
		value="<?=$arResult['LOCATION_STRING']?>"
		data-initial-value="<?=$arResult['LOCATION_STRING']?>"
		autocomplete="off"
		<?=$arParams['REQUIRED'] ? ' required=""' : ''?>
		<?=$arResult['SINGLE_CITY'] == 'Y' ? ' disabled=""' : ''?>
	/>
	<ul class="dropdown-menu"></ul>
	<input class="location-id" type="hidden" name="<?=$arParams['CITY_INPUT_NAME']?>" value="<?=$arParams['LOCATION_VALUE']?>" data-initial-value="<?=$arParams['LOCATION_VALUE']?>"/>
</div>