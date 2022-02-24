<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

use Bitrix\Sale\Internals\BasketPropertyTable, Kit\Origami\Helper\Config;

if (Config::get("IMAGE_FOR_OFFER") == 'PRODUCT' && $arResult["BASKET_ITEMS"] && $arResult['JS_DATA']['GRID']['ROWS'])
{
    $Basket = new \Kit\Origami\Image\Basket();
    $Basket->setMediumHeight(80);
    $Basket->setMediumWidth(80);
    $arProductID = [];

    foreach($arResult["BASKET_ITEMS"] as $arItem)
    {
        $arProductID[] = $arItem['PRODUCT_ID'];
    }

    $images = $Basket->getImages($arProductID);

    foreach($arResult['JS_DATA']['GRID']['ROWS'] as &$arRow)
    {
        $arRow['data'] = $Basket->changeImages($arRow['data'], $images[$arRow['data']['PRODUCT_ID']]);
        $arRow['data']['PREVIEW_PICTURE_SRC_2X'] = $arRow['data']['PREVIEW_PICTURE_SRC'];
    }
}