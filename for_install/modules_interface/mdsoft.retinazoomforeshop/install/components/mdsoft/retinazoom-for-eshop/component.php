<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


if ($arParams["INCLUDE_JQUERY"] == "Y")
	$APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/modules/mdsoft.retinazoom/install/components/mdsoft/retinazoom/js/jquery-1.7.2.min.js\"></script>", true);

if ($this->StartResultCache()){
	$this->IncludeComponentTemplate();

	$arResult["IMAGE_URL"] = $arParams["ELEMENT_URL"];
	$arResult["IMAGE_WIDTH"] = $arParams["IMAGE_WIDTH"];
	$arResult["IMAGE_HEIGHT"] = $arParams["IMAGE_HEIGHT"];
	$arResult["IMAGE_ALT"] = $arParams["IMAGE_ALT"];
	$arResult["IMAGE_TITLE"] = $arParams["IMAGE_TITLE"];

	$arResult["BOX_SIZE"] = $arParams["BOX_SIZE"];
	$arResult["ZOMM"] = $arParams["ZOOM"];
}


echo '<script type="text/javascript" src="' . substr(__DIR__, strrpos(__DIR__, "/bitrix/components/"), strlen(__DIR__)) . '/script.js"></script>';
echo '<link href="' . substr(__DIR__, strrpos(__DIR__, "/bitrix/components/"), strlen(__DIR__)) . '/style.css";  type="text/css" rel="stylesheet" />';


?>