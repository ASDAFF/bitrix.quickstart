<?php

/**
 * find and set title
 */
if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]) <> "") {
    $browserTitle = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"];
} else {
    $browserTitle = $arResult['BRAND']['NAME'];
}
Novagroup_Classes_General_Main::setTitle($browserTitle);

/**
 * find and set keywords
 */
if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]) <> "") {
    $metaKeywords = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"];
} else {
    $metaKeywords = "";
}
Novagroup_Classes_General_Main::setKeywords($metaKeywords);

/**
 * find and set description
 */
if (trim($arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]) <> "") {
    $metaDescription = $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"];
} else {
    $metaDescription = "";
}
Novagroup_Classes_General_Main::setDescription($metaDescription);
