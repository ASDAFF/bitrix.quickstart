<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('iblock')) return;?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    global $APPLICATION;
    global $USER;

    CStartShopTheme::ApplyTheme(SITE_ID);

    $arDefaultParams = array(
        'REQUEST_VARIABLE_ACTION' => 'action'
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    $sAction = $_REQUEST[$arParams['REQUEST_VARIABLE_ACTION']];
    $arResult['COUNT'] = CStartShopBasket::GetItemsCount(SITE_ID);
    $arResult['SUM'] = CStartShopBasket::GetItemsAmount(SITE_ID, $arParams['CURRENCY']);
    $arResult['ITEMS'] = array();
    $arResult['PROPERTIES'] = array();
    $arResult['CURRENCY'] = CStartShopCurrency::GetByCode($arParams['CURRENCY'])->Fetch();
    $arResult['ERRORS'] = array();
    $arResult['ORDER'] = false;

    if (empty($arResult['CURRENCY']))
        $arResult['CURRENCY'] = CStartShopCurrency::GetBase()->Fetch();

    $arResult['ITEMS'] = CStartShopUtil::DBResultToArray(CStartShopBasket::GetList(
        array('NAME' => 'ASC'),
        array(),
        array(),
        array(),
        $arParams['CURRENCY'],
        SITE_ID
    ), 'ID');

    if (CStartShopVariables::Get('DELIVERY_USE', 'N', SITE_ID) == 'Y')
        $arResult['DELIVERIES'] = CStartShopUtil::DBResultToArray(CStartShopDelivery::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID, 'ACTIVE' => 'Y')), 'ID');

    if (CStartShopVariables::Get('PAYMENT_USE', 'N', SITE_ID) == 'Y')
        $arResult['PAYMENTS'] = CStartShopUtil::DBResultToArray(CStartShopPayment::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y')), 'ID');

    $arProperties = CStartShopUtil::DBResultToArray(CStartShopOrderProperty::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID, 'ACTIVE' => 'Y')), 'ID');

    if (!empty($arProperties)) {
        foreach ($arProperties as $iKey => $arProperty)
            if ($arProperty['TYPE'] == 'L' && $arProperty['SUBTYPE'] == 'IBLOCK_ELEMENT') {
                $arPropertyValues = array("0" => array("ID" => "0", "NAME" => GetMessage('SO_SELECT_EMPTY')));

                if (!empty($arProperty['DATA']['IBLOCK_ID'])) {
                    $arPropertyValuesTemp = CStartShopUtil::DBResultToArray(CIBlockElement::GetList(array('SORT' => 'ASC'), array('IBLOCK_ID' => $arProperty['DATA']['IBLOCK_ID'])), 'ID');

                    foreach ($arPropertyValuesTemp as $iPropertyValueTempID => $arPropertyValueTemp)
                        $arPropertyValues[$iPropertyValueTempID] = $arPropertyValueTemp;

                    unset($arPropertyValuesTemp, $iPropertyValueTempID, $arPropertyValueTemp);
                }

                $arProperties[$iKey]['VALUES'] = $arPropertyValues;
                unset($arPropertyValues);
            }

        $arPropertiesCommon = $arProperties;

        if (!empty($arResult['DELIVERIES']))
            foreach ($arResult['DELIVERIES'] as $iDeliveryID => $arDelivery) {
                $arResult['DELIVERIES'][$iDeliveryID]['PRICE'] = CStartShopCurrency::FormatAsArray(CStartShopCurrency::Convert($arDelivery['PRICE'], null, $arParams['CURRENCY']), $arParams['CURRENCY']);
                $arDeliveryProperties = array();

                if (!empty($arDelivery['PROPERTIES']))
                    foreach ($arDelivery['PROPERTIES'] as $iDeliveryProperty) {
                        if (!empty($arProperties[$iDeliveryProperty]))
                            $arDeliveryProperties[$iDeliveryProperty] = $arProperties[$iDeliveryProperty];

                        unset($arPropertiesCommon[$iDeliveryProperty]);
                    }

                $arResult['DELIVERIES'][$iDeliveryID]['PROPERTIES'] = $arDeliveryProperties;
                unset($arDeliveryProperties);
            }

        $arResult['PROPERTIES'] = $arPropertiesCommon;
        unset($arPropertiesCommon, $arProperties);
    }

    if (empty($arResult['ITEMS']) && !empty($arParams['URL_BASKET_EMPTY'])) {
        LocalRedirect($arParams['URL_BASKET_EMPTY']);
        die();
    }

    if ($sAction == "order" && !empty($arResult['ITEMS'])) {
        $arDelivery = $arResult['DELIVERIES'][$_POST['DELIVERY']];
        $arPayment = $arResult['PAYMENTS'][$_POST['PAYMENT']];

        $arEmptyProperties = array();
        $arHandleProperties = $arResult['PROPERTIES'];
        $arUserFields = array();

        if (is_array($arDelivery['PROPERTIES']))
            $arHandleProperties = array_merge(
                $arHandleProperties,
                $arDelivery['PROPERTIES']
            );

        foreach ($arHandleProperties as $iPropertyID => $arProperty) {
            $sPropertyValue = $_POST['PROPERTY_'.$arProperty['ID']];

            $bValidProperty = true;

            if (!empty($arProperty['USER_FIELD']))
                if (!empty($sPropertyValue))
                    $arUserFields[$arProperty['USER_FIELD']] = $sPropertyValue;

            if ($arProperty['REQUIRED'] == "Y" && empty($sPropertyValue))
                $bValidProperty = false;

            if ($arProperty['TYPE'] == "S" && empty($arProperty['SUBTYPE']))
                if ($arProperty['DATA']['LENGTH'] > 0)
                    if (strlen($sPropertyValue) > $arProperty['DATA']['LENGTH'])
                        $bValidProperty = false;

            if ($arProperty['TYPE'] == "S" && (empty($arProperty['SUBTYPE']) || $arProperty['SUBTYPE'] == "TEXT"))
                if (!empty($arProperty['DATA']['EXPRESSION']))
                    if (!preg_match($arProperty['DATA']['EXPRESSION'], $sPropertyValue))
                        $bValidProperty = false;

            if (!$bValidProperty)
                $arEmptyProperties[$arProperty['ID']] = $arProperty;

            unset($bValidProperty);
        }

        if (empty($arResult['CURRENCY']))
            $arResult['ERRORS'][] = array("CODE" => "CURRENCY_EMPTY");

        if (empty($arDelivery) && !empty($arResult['DELIVERIES']))
            $arResult['ERRORS'][] = array("CODE" => "DELIVERY_EMPTY");

        if (empty($arPayment) && !empty($arResult['PAYMENTS']))
            $arResult['ERRORS'][] =  array("CODE" => "PAYMENT_EMPTY");

        if (!empty($arEmptyProperties))
            $arResult['ERRORS'][] = array("CODE" => "PROPERTIES_EMPTY", "PROPERTIES" => $arEmptyProperties);

        if (empty($arResult['ERRORS'])) {
            $arBaseCurrency = CStartShopCurrency::GetBase()->Fetch();
            $bCreatedToUser = false;

            $arFields = array(
                "SID" => SITE_ID,
                "CURRENCY" => $arBaseCurrency['CODE'],
            );

            if ($USER->IsAuthorized()) {
                $arFields["USER"] = $USER->GetID();
            } else {
                $oUser = new CUser();
                $bCreateUser = false;
                $bCreateUserAllowed = CStartShopVariables::Get("ORDER_REGISTER_NEW_USER", null, SITE_ID) == "Y";

                if (!empty($arUserFields['EMAIL'])) {
                    $arUser = $oUser->GetList($by = "timestamp_x", $order = "desc", array(
                        "EMAIL" => $arUserFields['EMAIL']
                    ))->Fetch();

                    if (empty($arUser))
                        $arUser = $oUser->GetList($by = "timestamp_x", $order = "desc", array(
                            "LOGIN" => $arUserFields['EMAIL']
                        ))->Fetch();

                    if (!empty($arUser)) {
                        $arFields["USER"] = $arUser["ID"];
                        $bCreatedToUser = true;
                    } else {
                        $arUserFields["LOGIN"] = $arUserFields["EMAIL"];
                        $bCreateUser = true;
                    }
                } else {
                    $arUserFields["LOGIN"] = "user_".(microtime(true) * 10000);
                    $arUserFields["EMAIL"] = $arUserFields["LOGIN"]."@".$_SERVER['SERVER_NAME'];
                    $bCreateUser = true;
                }

                if ($bCreateUserAllowed && $bCreateUser) {
                    $arUserFields["PASSWORD"] = randString(7);
                    $arUserFields["PASSWORD_CONFIRM"] = $arUserFields["PASSWORD"];

                    $iGroupID = CStartShopVariables::Get("ORDER_REGISTER_NEW_USER_GROUP", null, SITE_ID);

                    if (!empty($iGroupID))
                        $arUserFields["GROUP_ID"] = array($iGroupID);

                    $iUserID = $oUser->Add($arUserFields);

                    if (!empty($iUserID)) {
                        $arFields["USER"] = $iUserID;
                        $oUser->Authorize($iUserID);
                    }

                    unset($iUserID);
                }

                unset($oUser);
                unset($bCreateUser);
            }

            if (!empty($arDelivery))
                $arFields['DELIVERY'] = $arDelivery['ID'];

            if (!empty($arPayment))
                $arFields['PAYMENT'] = $arPayment['ID'];

            $arStatus = CStartShopOrderStatus::GetDefault(SITE_ID)->Fetch();

            if (empty($arStatus))
                $arStatus = CStartShopOrderStatus::GetList(array('SORT' => 'ASC'), array('SID' => SITE_ID))->Fetch();

            if (!empty($arStatus))
                $arFields['STATUS'] = $arStatus['ID'];

            $arFields['PROPERTIES'] = array();
            $arFields['ITEMS'] = array();

            foreach ($arHandleProperties as $arProperty)
                $arFields['PROPERTIES'][$arProperty['ID']] = $_POST['PROPERTY_'.$arProperty['ID']];

            foreach ($arResult['ITEMS'] as $arItem)
                $arFields['ITEMS'][$arItem['ID']] = array(
                    "NAME" => $arItem["NAME"],
                    "QUANTITY" => $arItem['STARTSHOP']['BASKET']['QUANTITY'],
                    "PRICE" => CStartShopCurrency::Convert($arItem['STARTSHOP']['PRICES']['MINIMAL']['VALUE'], $arItem['STARTSHOP']['CURRENCY'], $arBaseCurrency['CODE'])
                );

            $iOrderID = CStartShopOrder::Add($arFields);

            if ($iOrderID) {
                CStartShopBasket::Clear(SITE_ID);
                $arResult['ITEMS'] = array();
                $arResult['ORDER'] = $iOrderID;

                $arOrder = CStartShopOrder::GetByID($iOrderID)->Fetch();

                if (CStartShopVariables::Get('MAIL_USE', 'N', SITE_ID) == "Y") {
                    if (CStartShopVariables::Get('MAIL_ADMIN_ORDER_CREATE', 'N', SITE_ID) == "Y") {
                        $sEvent = CStartShopVariables::Get('MAIL_ADMIN_ORDER_CREATE_EVENT', '', SITE_ID);
                        $sMail = CStartShopVariables::Get('MAIL_MAIL', '', SITE_ID);
                        $oEvent = new CEvent();

                        if (!empty($sEvent) && !empty($sMail))
                            $oEvent->SendImmediate($sEvent, SITE_ID, array(
                                "ORDER_ID" => $arOrder['ID'],
                                "ORDER_AMOUNT" => CStartShopCurrency::FormatAsString(CStartShopCurrency::Convert($arOrder['AMOUNT'] , $arOrder['CURRENCY'])),
                                "STARTSHOP_SHOP_EMAIL" => $sMail,
                            ), "N", "");

                        unset($oEvent);
                    }

                    if (CStartShopVariables::Get('MAIL_CLIENT_ORDER_CREATE', 'N', SITE_ID) == "Y") {
                        $sEvent = CStartShopVariables::Get('MAIL_CLIENT_ORDER_CREATE_EVENT', '', SITE_ID);
                        $sMailShop = CStartShopVariables::Get('MAIL_MAIL', '', SITE_ID);
                        $sMail = $arOrder['PROPERTIES'][CStartShopVariables::Get('MAIL_ORDER_PROPERTY', '', SITE_ID)];
                        $oEvent = new CEvent();

                        if (!empty($sEvent) && !empty($sMail))
                            $oEvent->Send($sEvent, SITE_ID, array(
                                "ORDER_ID" => $arOrder['ID'],
                                "ORDER_AMOUNT" => CStartShopCurrency::FormatAsString(CStartShopCurrency::Convert($arOrder['AMOUNT'] , $arOrder['CURRENCY'])),
                                "STARTSHOP_CLIENT_EMAIL" => $sMail,
                                "STARTSHOP_SHOP_EMAIL" => $sMailShop
                            ), "N", "");

                        unset($oEvent);
                    }
                }

                if (!$bCreatedToUser) {
                    if (!empty($arParams['URL_ORDER_CREATED'])) {
                        LocalRedirect(CStartShopUtil::ReplaceMacros($arParams['URL_ORDER_CREATED'], array('ID' => $iOrderID)));
                        die();
                    }
                } else {
                    if (!empty($arParams['URL_ORDER_CREATED_TO_USER'])) {
                        LocalRedirect(CStartShopUtil::ReplaceMacros($arParams['URL_ORDER_CREATED_TO_USER'], array('ID' => $iOrderID)));
                        die();
                    }
                }
            }
        }

        unset(
            $arEmptyProperties,
            $arCheckableProperties,
            $arDelivery,
            $arPayment,
            $arEmptyProperties
        );
    }

    $this->IncludeComponentTemplate();
?>
