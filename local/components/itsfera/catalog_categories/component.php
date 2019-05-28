<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$obCache = new CPHPCache;
$CACHE_ID = "itsfera_catalog_categories";
$arParams['CACHE_TIME']=0;
if ($obCache->StartDataCache($arParams['CACHE_TIME'], $CACHE_ID)): //3 часа


	$arResult = getCatalogTree();

	$iParentDepth = $iLastDepth = 1;
	$iParentKey = $iLastKey = 0;
	$arParentKeys = []; //массив родительских ключей по глубине
	$arMovedItems = [];
	foreach ($arResult as $key => $arItem) {

		$arLastItem = $arResult[$key - 1];
		if ($key > 0 && $arItem[3]['DEPTH_LEVEL'] > $arLastItem[3]['DEPTH_LEVEL']) {
			$arParentKeys[$arItem[3]['DEPTH_LEVEL']] = $key - 1;
		}

		if (isset($arParentKeys[$arItem[3]['DEPTH_LEVEL']])) {
			$iParentKey = $arParentKeys[$arItem[3]['DEPTH_LEVEL']];
			$arResult[$iParentKey]['CHILDREN'][] = &$arResult[$key];
			$arMovedItems[] = $key;
		}
	}
	foreach ($arResult as $key => $arItem) {
		if (in_array($key, $arMovedItems)) {
			unset($arResult[$key]);
		}
	}
	$arResult = array_values($arResult);
	$obCache->EndDataCache($arResult);
else:
	$arResult = $obCache->GetVars();
endif;


$this->IncludeComponentTemplate();
?>