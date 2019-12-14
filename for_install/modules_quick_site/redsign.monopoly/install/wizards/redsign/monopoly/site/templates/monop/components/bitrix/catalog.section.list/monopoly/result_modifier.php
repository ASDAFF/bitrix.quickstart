<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($arResult['SECTION']["ID"] > 0)
{
	$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arResult['SECTION']["ID"]);
	$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
}
else
{
	$arResult["IPROPERTY_VALUES"] = array();
}

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
$arTitleOptions = null;

if($arParams["SET_TITLE"])
{
	if ($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
		$APPLICATION->SetTitle($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arTitleOptions);
	elseif(isset($arResult['SECTION']["NAME"]))
		$APPLICATION->SetTitle($arResult['SECTION']["NAME"], $arTitleOptions);
}

if ($arParams["SET_BROWSER_TITLE"] === 'Y')
{
	$browserTitle = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult, $arParams["BROWSER_TITLE"]
		,$arResult["IPROPERTY_VALUES"], "SECTION_META_TITLE"
	);
	if (is_array($browserTitle))
		$APPLICATION->SetPageProperty("title", implode(" ", $browserTitle), $arTitleOptions);
	elseif ($browserTitle != "")
		$APPLICATION->SetPageProperty("title", $browserTitle, $arTitleOptions);
}

if ($arParams["SET_META_KEYWORDS"] === 'Y')
{
	$metaKeywords = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult, $arParams["META_KEYWORDS"]
		,$arResult["IPROPERTY_VALUES"], "SECTION_META_KEYWORDS"
	);
	if (is_array($metaKeywords))
		$APPLICATION->SetPageProperty("keywords", implode(" ", $metaKeywords), $arTitleOptions);
	elseif ($metaKeywords != "")
		$APPLICATION->SetPageProperty("keywords", $metaKeywords, $arTitleOptions);
}

if ($arParams["SET_META_DESCRIPTION"] === 'Y')
{
	$metaDescription = \Bitrix\Main\Type\Collection::firstNotEmpty(
		$arResult, $arParams["META_DESCRIPTION"]
		,$arResult["IPROPERTY_VALUES"], "SECTION_META_DESCRIPTION"
	);
	if (is_array($metaDescription))
		$APPLICATION->SetPageProperty("description", implode(" ", $metaDescription), $arTitleOptions);
	elseif ($metaDescription != "")
		$APPLICATION->SetPageProperty("description", $metaDescription, $arTitleOptions);
}