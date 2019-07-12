<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) { $title = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]; }
else if (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) { $title = $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]; }
else { $title = $arResult["NAME"]; }
$APPLICATION->SetTitle($title);
$APPLICATION->SetPageProperty("title", $title);
?>