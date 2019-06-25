<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?if (!CModule::IncludeModule('iblock')) return;?>
<?
    CStartShopTheme::ApplyTheme(SITE_ID);

    $arDefaultParams = array(
        "FILTER" => array(),
        "SORT" => array(),
        "DETAIL_PAGE_URL" => "/ORDER_ID=#ID#"
    );

    $arParams = array_merge($arDefaultParams, $arParams);
    $arResult['ORDERS'] = array();

    $dbOrders = CStartShopOrder::GetList($arParams['SORT'], $arParams['FILTER']);
    $arOrderProperties = CStartShopUtil::DBResultToArray(CStartShopOrderProperty::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID)), 'ID');
    $arDeliveries = CStartShopUtil::DBResultToArray(CStartShopDelivery::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID)), 'ID');
    $arPayments = CStartShopUtil::DBResultToArray(CStartShopPayment::GetList(array('SORT' => 'ASC')), 'ID');
    $arOrderStatuses = CStartShopUtil::DBResultToArray(CStartShopOrderStatus::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID)), 'ID');

    $arOrderPropertiesID = array_keys($arOrderProperties);
    $arDeliveryPropertiesID = array();

    foreach ($arDeliveries as $arDelivery)
        foreach ($arDelivery['PROPERTIES'] as $iDeliveryPropertyID)
            if (!in_array($iDeliveryPropertyID, $arDeliveryPropertiesID))
                $arDeliveryPropertiesID[] = $iDeliveryPropertyID;

    $arOrderPropertiesID = array_diff($arOrderPropertiesID, $arDeliveryPropertiesID);
    $arItemsID = array();

    while ($arOrder = $dbOrders->Fetch()) {
        $arOrderDefault = $arOrder;

        foreach ($arOrder['ITEMS'] as $iItemID => $arItem) {
            if (!in_array($iItemID, $arItemsID))
                $arItemsID[] = $arItem['ITEM'];

            $arOrder['ITEMS'][$iItemID]['PRICE'] = CStartShopCurrency::ConvertAndFormatAsArray($arItem['PRICE'], $arResult['CURRENCY'], $arParams['CURRENCY']);
            $arOrder['ITEMS'][$iItemID]['AMOUNT'] = CStartShopCurrency::ConvertAndFormatAsArray($arItem['PRICE'] * $arItem['QUANTITY'], $arResult['CURRENCY'], $arParams['CURRENCY']);
        }

        $arProperties = array();

        foreach ($arOrderProperties as $iPropertyID => $arProperty) {
            $arProperties[$iPropertyID] = $arProperty;
            $sValue = $arOrder['PROPERTIES'][$iPropertyID];

            if ($arProperty['TYPE'] == 'L' && $arProperty['SUBTYPE'] == 'IBLOCK_ELEMENT')
                if (!empty($sValue)) {
                    $arIBlockElement = CIBlockElement::GetByID($sValue)->Fetch();
                    $sValue = $arIBlockElement['NAME'];
                }

            $arProperties[$iPropertyID]['VALUE'] = $sValue;
        }

        if (!empty($arOrder['DELIVERY'])) {
            $arOrder['DELIVERY'] = $arDeliveries[$arOrder['DELIVERY']];

            if (!empty($arOrder['DELIVERY'])) {
                $arDeliveryProperties = array();

                foreach ($arOrder['DELIVERY']['PROPERTIES'] as $iDeliveryPropertyID)
                    if (!empty($arProperties[$iDeliveryPropertyID]))
                        $arDeliveryProperties[$iDeliveryPropertyID] = $arProperties[$iDeliveryPropertyID];

                $arOrder['DELIVERY']['PROPERTIES'] = $arDeliveryProperties;
                unset($arDeliveryProperties, $iDeliveryPropertyID);
            }
        }

        if (!empty($arOrder['PAYMENT']))
            $arOrder['PAYMENT'] = $arPayments[$arOrder['PAYMENT']];

        $arOrder['PROPERTIES'] = array();
        foreach ($arOrderPropertiesID as $iOrderPropertyID)
            if (!empty($arProperties[$iOrderPropertyID]))
                $arOrder['PROPERTIES'][$iOrderPropertyID] = $arProperties[$iOrderPropertyID];

        $arOrder['STATUS'] = $arOrderStatuses[$arOrder['STATUS']];
        $arOrder['CURRENCY'] = $arParams['CURRENCY'];
        $arOrder['AMOUNT'] = CStartShopCurrency::ConvertAndFormatAsArray($arOrderDefault['AMOUNT'], $arOrderDefault['CURRENCY'], $arOrder['CURRENCY']);
        $arOrder['ACTIONS']['VIEW'] = CStartShopUtil::ReplaceMacros($arParams['DETAIL_PAGE_URL'], $arOrder);
        $arResult['ORDERS'][$arOrder['ID']] = $arOrder;
    }

    $arItems = array();

    if (!empty($arItemsID))
        $arItems = CStartShopUtil::DBResultToArray(CStartShopCatalogProduct::GetList(
            array('NAME' => 'ASC'),
            array('ID' => $arItemsID),
            array(),
            array(),
            $arParams['CURRENCY'],
            SITE_ID
        ), 'ID');

    foreach ($arResult['ORDERS'] as $iOrderID => $arOrder)
        foreach ($arOrder['ITEMS'] as $iOrderItemID => $arOrderItem)
            $arResult['ORDERS'][$iOrderID]['ITEMS'][$iOrderItemID]['ELEMENT'] = $arItems[$arOrderItem['ITEM']];

    $this->IncludeComponentTemplate();
?>