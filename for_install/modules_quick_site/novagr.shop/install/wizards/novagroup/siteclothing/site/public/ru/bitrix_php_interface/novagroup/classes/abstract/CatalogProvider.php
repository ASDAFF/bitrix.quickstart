<?php

abstract class Novagroup_Classes_Abstract_CatalogProvider extends CCatalogProductProvider
{
	public static function GetProductData($arParams){
        $result = parent::GetProductData($arParams);

        $mxResult = CCatalogSku::GetProductInfo(
            $arParams['PRODUCT_ID']
        );
        if (is_array($mxResult)) {
            $action = new Novagroup_Classes_General_TimeToBuy($mxResult['ID'], $mxResult['IBLOCK_ID']);
            if ($action->checkAction()) {
                $getAction = $action->getAction();
                if($getAction['PROPERTY_QUANTITY_VALUE']<$arParams['QUANTITY'])
                {
                    $result['QUANTITY'] = $getAction['PROPERTY_QUANTITY_VALUE'];
                }
            }
        }
        return $result;
    }

    public static function OrderProduct($arParams){
        $mxResult = CCatalogSku::GetProductInfo(
            $arParams['PRODUCT_ID']
        );
        if (is_array($mxResult)) {
            $action = new Novagroup_Classes_General_TimeToBuy($mxResult['ID'], $mxResult['IBLOCK_ID']);
            if ($action->checkAction()) {
                $action->onSaleHandler($arParams['QUANTITY']);
            }
        }
        return parent::OrderProduct($arParams);
    }

}
?>
