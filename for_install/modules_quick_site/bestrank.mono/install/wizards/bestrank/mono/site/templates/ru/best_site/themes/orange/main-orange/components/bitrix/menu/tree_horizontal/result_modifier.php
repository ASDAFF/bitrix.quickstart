<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult)):

	$top_key = -1;
	foreach($arResult as $key => $arItem):
		if ($arItem["DEPTH_LEVEL"] == 1):
			$top_key++;
			$arFormatted["TOP"][$top_key] = $arItem;
		elseif ($arItem['PERMISSION'] > 'D'):
			$arFormatted["TOP"][$top_key]["ITEMS"][] = $arItem;
		endif;
	endforeach;

	foreach($arFormatted["TOP"] as $key => $arTopItem):
		if (count($arTopItem["ITEMS"]) > 12)
			$arFormatted["TOP"][$key]["LARGE"] = true;
		else
			$arFormatted["TOP"][$key]["LARGE"] = false;
	endforeach;
	
endif;

$arResult = $arFormatted["TOP"];

//echo '<pre>'; print_r($arResult); echo '</pre>';
?>