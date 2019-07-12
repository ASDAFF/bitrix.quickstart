<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->RestartBuffer();

unset($arResult["COMBO"]);
unset($arResult["ITEMS"]);
unset($arResult["PRICES"]);
unset($arResult["HIDDEN"]);

if (is_array($GLOBALS['arrFilter']) && !isset($GLOBALS['arrFilter']['=PROPERTY_5']) && count($GLOBALS['arrFilter']) == 1)
{
	foreach ($_REQUEST AS $key => $value)
	{
		if (strpos($key, 'arrFilter_5_') !== false)
		{
			$arResult['ELEMENT_COUNT'] = 0;
			
			continue;
		}
	}
}

echo json_encode($arResult);

?>