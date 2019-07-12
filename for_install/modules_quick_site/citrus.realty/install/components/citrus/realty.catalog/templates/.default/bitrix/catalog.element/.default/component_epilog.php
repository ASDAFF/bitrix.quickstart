<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

CUtil::InitJSCore(Array("carousel", "realtyAddress", "fancybox"));
\Citrus\Realty\Helper::setLastSection($arResult["IBLOCK_SECTION_ID"]);

if ($arResult["CANONICAL"])
	$APPLICATION->AddHeadString('<link rel="canonical" href="' . $arResult["CANONICAL"] . '">', true);
