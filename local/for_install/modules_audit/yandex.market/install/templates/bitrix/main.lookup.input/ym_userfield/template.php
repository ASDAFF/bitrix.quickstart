<?
/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global array $arParams
 * @global array $arResult
 * @global CBitrixComponentTemplate $this
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->AddHeadScript('/bitrix/components/bitrix/main.lookup.input/templates/iblockedit/script2.js');
$APPLICATION->SetAdditionalCSS('/bitrix/components/bitrix/main.lookup.input/templates/iblockedit/style.css');

$control_id = $arParams['CONTROL_ID'];
$textarea_id = (!empty($arParams['INPUT_NAME_STRING']) ? $arParams['INPUT_NAME_STRING'] : 'visual_'.$control_id);
$boolStringValue = (isset($arParams['INPUT_VALUE_STRING']) && $arParams['INPUT_VALUE_STRING'] != '');
$INPUT_VALUE = array();

$mliFieldClass = '';

if ($arParams['MAIN_UI_FILTER'] == 'Y')
{
	$mliLayoutClass = 'mli-layout-ui-filter';
	$mliFieldClass = 'mli-field-ui-filter';
}

if ($boolStringValue)
{
	$arTokens = preg_split('/(?<=])[\n;,]+/', $arParams['~INPUT_VALUE_STRING']);
	foreach($arTokens as $key => $token)
	{
		if(preg_match("/^(.*) \\[(\\d+)\\]/", $token, $match))
		{
			$match[2] = intval($match[2]);
			if (0 < $match[2])
				$INPUT_VALUE[] = array(
					"ID" => $match[2],
					"NAME" => $match[1],
				);
		}
	}
}
?><div class="mli-layout <?=$mliLayoutClass?>" id="layout_<?=$control_id?>">
<div style="display:none" id="value_container_<?=$control_id?>">
<?if ($INPUT_VALUE):?>
	<?foreach ($INPUT_VALUE as $value):?>
		<input type="hidden" name="<?echo $arParams['~INPUT_NAME']; ?>" value="<?echo $value["ID"]?>">
	<?endforeach;?>
<?else:?>
	<input type="hidden" name="<?echo $arParams['~INPUT_NAME']; ?>" value="">
<?endif;?>
</div>
<?
if($arParams["MULTIPLE"]=="Y" && $arParams['MAIN_UI_FILTER'] !== 'Y')
{
	?><textarea name="<?=$textarea_id?>" id="<?=$textarea_id?>" class="mli-field"><? echo ($boolStringValue ? htmlspecialcharsbx($arParams['INPUT_VALUE_STRING']) : '');?></textarea><?
}
else
{
	?><input autocomplete="off" type="text" name="<?=$textarea_id?>" id="<?=$textarea_id?>" value="<? echo ($boolStringValue ? htmlspecialcharsbx($arParams['INPUT_VALUE_STRING']) : '');?>" class="mli-field <?=$mliFieldClass?>" /><?
}
?></div><?

$arSelectorParams = array(
	'AJAX_PAGE' => $this->GetFolder()."/ajax.php",
	'AJAX_PARAMS' => [
		'lang' => LANGUAGE_ID,
		'site' => SITE_ID,
		'provider' => $arParams['PROVIDER']
	],
	'CONTROL_ID' => $control_id,
	'LAYOUT_ID' => 'layout_'.$control_id,
	'INPUT_NAME' => $arParams['~INPUT_NAME'],
	'PROACTIVE' => 'MESSAGE',
	'VALUE' => $INPUT_VALUE,
	'VISUAL' => array(
		'ID' => $textarea_id,
		'MAIN_UI_FILTER' => $arParams['MAIN_UI_FILTER'],
		'MULTIPLE' => $arParams['MULTIPLE'],
		'START_TEXT' => $arParams['START_TEXT'],
		'SEARCH_POSITION' => ($arParams['FILTER'] == 'Y' ? 'absolute' : ''),
		'SEARCH_ZINDEX' => 4000,
	),
);

?>
<script type="text/javascript">
BX.ready(BX.defer(function() {
	window.jsMLI_<?=$control_id?> = new JCMainLookupAdminSelector(<? echo CUtil::PhpToJSObject($arSelectorParams); ?>);
	<?
	if (array_key_exists('RESET', $arParams) && 'Y' == $arParams['RESET'])
	{
		?>window.jsMLI_<?=$control_id?>.Reset(true, false);<?
	}
	?>
}));
</script>