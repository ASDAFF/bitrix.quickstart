<?

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Sale;
use MHT\Product;
use Bitrix\Main\Data\Cache;

class CSuperDiscountItems extends CBitrixComponent
{

    public function executeComponent ()
    {
        // сразу ставим куку что пользователь здесь был
        setcookie("ISeeSuperDiskout",1);

        if ( ! Loader::includeModule('sale') &&
             ! Loader::includeModule('catalog') &&
             ! Loader::includeModule("iblock")) {
            return;
        }


//        $cache = Cache::createInstance();
//        if ($cache->initCache(600, "action_items_" . $fUser)) {
//
//            $this->arResult['ITEMS'] = $cache->getVars();
//
//        } else if ($cache->startDataCache()) {

            $arSelect = Array("ID", "NAME", "PROPERTY_PRODUCT_ID");

            $arFilter = array(
                'ACTIVE'     => 'Y',
                'ACTIVE_DATE'=>'Y',
                'IBLOCK_ID' =>  getIBlockIdByCode('super_discount'),
            );

            $res = CIBlockElement::GetList(array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $f = $ob->getFields();
                $this->arResult['ITEMS'][] = $f['PROPERTY_PRODUCT_ID_VALUE'];
            }


//            $cache->endDataCache($this->arResult['ITEMS']);
//        }

        $this->IncludeComponentTemplate();
    }

}

?>