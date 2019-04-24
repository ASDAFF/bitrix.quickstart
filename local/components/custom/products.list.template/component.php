<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

$arResult = $arParams['RESULT'];
$arParams = $arParams['PARAMS'];

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/custom/catalog.smart.filter/class.php";

$smartFilter = new ItmitCatalogSmartFilter();

foreach ($arResult["ITEMS"] as &$item) {
    foreach ($item['PROPERTIES']['TAGS']['VALUE'] as &$tag) {
        $tag = [
            'title' => $tag,
            'href' => rawurlencode($smartFilter->translit($tag))
        ];
    }
}

$this->includeComponentTemplate();
