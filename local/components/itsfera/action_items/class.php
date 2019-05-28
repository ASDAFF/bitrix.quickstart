<?

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Sale;
use MHT\Product;
use Bitrix\Main\Data\Cache;

class CActionItems extends CBitrixComponent
{

    public function executeComponent ()
    {

        if ( ! Loader::includeModule('sale') &&
             ! Loader::includeModule('catalog') &&
             ! Loader::includeModule("iblock")) {
            return;
        }


        $fUser    = (int)CSaleBasket::GetBasketUserID(true);
        $fSite_ID = Bitrix\Main\Context::getCurrent()->getSite();

        $cache = Cache::createInstance();
        if ($cache->initCache(600, "action_items_" . $fUser)) {

            $this->arResult['ITEMS'] = $cache->getVars();

        } else if ($cache->startDataCache()) {


            //просмотренные товары
            $arFilter = array(
                'LID'      => $fSite_ID,
                'FUSER_ID' => $fUser,
            );

            $arViewedId = array();

            $db_res = CSaleViewedProduct::GetList(
                array(
                    "DATE_VISIT" => "DESC",
                ),
                $arFilter,
                false,
                array(
                    "nTopCount" => 5,
                ),
                array('ID', 'IBLOCK_ID', 'PRODUCT_ID')
            );
            while ($arItems = $db_res->Fetch()) {
                $arViewedId[] = $arItems["PRODUCT_ID"];
            }


            //товары из корзины
            $arBasketProductID = array();

            $basket = Sale\Basket::loadItemsForFUser($fUser, $fSite_ID);
            foreach ($basket as $basketItem) {
                $arBasketProductID[] = $basketItem->getProductId();
            }

            $arProductID = array_merge($arViewedId, $arBasketProductID);

            $arSectionList = array();

            $rsSections = CIBlockElement::GetElementGroups($arProductID);;
            while ($arSection = $rsSections->Fetch()) {

                $arSectionID[$arSection['ID']] = $arSection['ID'];

                $arSectionList[$arSection['ID']] = array(
                    'ID'   => $arSection['ID'],
                    'NAME' => $arSection['NAME'],
                );
            }

            $arFilter = array(
                'ACTIVE'     => 'Y',
                'SECTION_ID' => $arSectionID,
            );

            $arItems = array();

            $res = CIBlockElement::GetList(array('SORT' => 'ASC'), $arFilter);
            while ($ob = $res->GetNextElement()) {

                $f = $ob->GetFields();
                $p = $ob->GetProperties();

                //получаем скидку
                //способ тотже что и в local\classes\MHT\Product.php
                $product = new Product($f, $p);

                $intPercent = 0;

                if ($product->get('old-price')) {

                    $oldPrice = intval(str_replace(' ', '', $product->get('old-price')));
                    $newPrice = intval(str_replace(' ', '', $product->get('price')));

                    if ($newPrice < $oldPrice) {

                        $intPercent = round(($oldPrice - $newPrice) / ($oldPrice / 100));

                    }
                }

                if ($intPercent > 0) {

                    $arItems[$intPercent][] = array(
                        'ID'      => $f['ID'],
                        'NAME'    => $f['NAME'],
                        'PERCENT' => $intPercent,
                    );
                }
            }

            krsort($arItems);

            foreach ($arItems as $persentGroup) {
                foreach ($persentGroup as $arItem) {
                    if ($arItem['ID']) {
                        $this->arResult['ITEMS'][] = $arItem['ID'];
                    }
                }

            }


            $cache->endDataCache($this->arResult['ITEMS']);
        }


        $this->arResult['ITEMS'] = array_slice($this->arResult['ITEMS'], 0, 20);

        $this->IncludeComponentTemplate();
    }

}

?>