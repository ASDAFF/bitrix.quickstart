<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

use Kit\Origami\Helper\Config;

if (Config::get("IMAGE_FOR_OFFER") == 'PRODUCT' && $arResult["BASKET_ITEMS"] && $arResult['GRID']['ROWS'])
{
    $Basket = new \Kit\Origami\Image\Basket();
    $Basket->setMediumHeight(190);
    $Basket->setMediumWidth(190);
    $arProductID = [];

    foreach($arResult["BASKET_ITEMS"] as $arItem)
    {
        $arProductID[] = $arItem['PRODUCT_ID'];
    }

    $images = $Basket->getImages($arProductID);

    foreach($arResult['GRID']['ROWS'] as &$arRow)
    {
        $arRow['data'] = $Basket->changeImages($arRow['data'], $images[$arRow['data']['PRODUCT_ID']]);
    }
}