<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?if (!CModule::IncludeModule('iblock')) return;?>
<?
    global $USER;

    CStartShopTheme::ApplyTheme(SITE_ID);

    $arDefaultParams = array(
        'ORDER_ID' => '',
        'FILTER' => array(),
        '404_SET_STATUS' => 'N',
        '404_REDIRECT' => 'N',
        '404_PAGE' => '/404.php'
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    if (empty($arParams['404_PAGE']))
        $arParams['404_PAGE'] = "/404.php";

    if (!is_array($arParams['~FILTER']))
        $arParams['~FILTER'] = array();

    $arParams['~FILTER']['ID'] = intval($arParams['ORDER_ID']);

    $arOrder = CStartShopOrder::GetList(array(), $arParams['~FILTER'])->Fetch();

    $arItemsID = array();

    if (!empty($arOrder)) {
        $arOrder = array_merge($arOrder, CStartShopUtil::ArrayPrefix($arOrder, '~', null, STARTSHOP_UTIL_ARRAY_PREFIX_USE_KEY));
        $arResult = $arOrder;

        $arProperties = CStartShopUtil::DBResultToArray(CStartShopOrderProperty::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID)), 'ID');

        /* Обработка значений свойств */
        foreach ($arProperties as $iPropertyID => $arProperty) {
            $sValue = $arResult['PROPERTIES'][$iPropertyID];

            if ($arProperty['TYPE'] == 'L' && $arProperty['SUBTYPE'] == 'IBLOCK_ELEMENT')
                if (!empty($sValue)) {
                    $arIBlockElement = CIBlockElement::GetByID($sValue)->Fetch();
                    $sValue = $arIBlockElement['NAME'];
                }

            $arProperties[$iPropertyID]['VALUE'] = $sValue;
        }

        if (!empty($arResult['STATUS']))
            $arResult['STATUS'] = CStartShopOrderStatus::GetByID($arResult['STATUS'])->Fetch();

        if (!empty($arResult['DELIVERY'])) {
            $arResult['DELIVERY'] = CStartShopDelivery::GetByID($arResult['DELIVERY'])->Fetch();

            if (!empty($arResult['DELIVERY'])) {
                $arPropertiesDelivery = array();

                foreach ($arResult['DELIVERY']['PROPERTIES'] as $iPropertyDeliveryID)
                    if (!empty($arProperties[$iPropertyDeliveryID])){
                        $arPropertiesDelivery[$iPropertyDeliveryID] = $arProperties[$iPropertyDeliveryID];
                        unset($arProperties[$iPropertyDeliveryID]);
                    }

                unset($arPropertyDelivery);
                $arResult['DELIVERY']['PROPERTIES'] = $arPropertiesDelivery;
                unset($arPropertiesDelivery);

                $arResult['DELIVERY']['PRICE'] = CStartShopCurrency::ConvertAndFormatAsArray($arResult['DELIVERY']['PRICE'], false, $arParams['CURRENCY']);
            }
        }

        $arResult['PROPERTIES'] = $arProperties;

        if (!empty($arResult['PAYMENT']))
            $arResult['PAYMENT'] = CStartShopPayment::GetByID($arResult['PAYMENT'])->Fetch();

        foreach ($arResult['ITEMS'] as $iItemID => $arItem) {
            $arItemsID[] = $arItem['ITEM'];
            $arResult['ITEMS'][$iItemID]['PRICE'] = CStartShopCurrency::ConvertAndFormatAsArray($arItem['PRICE'], $arResult['CURRENCY'], $arParams['CURRENCY']);
            $arResult['ITEMS'][$iItemID]['AMOUNT'] = CStartShopCurrency::ConvertAndFormatAsArray($arItem['PRICE'] * $arItem['QUANTITY'], $arResult['CURRENCY'], $arParams['CURRENCY']);
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

        foreach ($arResult['ITEMS'] as $iItemID => $arItem) {
            $arResult['ITEMS'][$iItemID]['ELEMENT'] = $arItems[$arItem['ITEM']];
        }

        $arResult['CURRENCY'] = $arParams['CURRENCY'];
        $arResult['AMOUNT'] = CStartShopCurrency::ConvertAndFormatAsArray($arResult['~AMOUNT'], $arResult['~CURRENCY'], $arResult['CURRENCY']);
    } else {
        if ($arParams['404_SET_STATUS'] == 'Y')
            CHTTP::SetStatus(404);

        if ($arParams['404_REDIRECT'] == 'Y') {
            LocalRedirect($arParams['404_PAGE']);
            die();
        }
    }

    $this->IncludeComponentTemplate();
?>