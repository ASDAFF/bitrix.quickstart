<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arDefaultParams = array(
        'REQUEST_VARIABLE_ACTION' => 'action',
        'REQUEST_VARIABLE_PAYMENT' => 'payment',
        'REQUEST_VARIABLE_VALUE_RESULT' => 'result',
        'REQUEST_VARIABLE_VALUE_SUCCESS' => 'success',
        'REQUEST_VARIABLE_VALUE_FAIL' => 'fail'
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    $sStatus = "FAIL";

    $sPayment = null;
    $sAction = null;
    $arActions = array(
        "RESULT" => $arParams['REQUEST_VARIABLE_VALUE_RESULT'],
        "SUCCESS" => $arParams['REQUEST_VARIABLE_VALUE_SUCCESS'],
        "FAIL" => $arParams['REQUEST_VARIABLE_VALUE_FAIL'],
    );

    $arOrder = false;

    $sPayment = strval($_REQUEST[$arParams['REQUEST_VARIABLE_PAYMENT']]);
    $sAction = strval($_REQUEST[$arParams['REQUEST_VARIABLE_ACTION']]);

    if (!empty($sPayment) && !empty($sAction) && in_array($sAction, $arActions)) {
        $arPayment = CStartShopPayment::GetList(array(), array('CODE' => $sPayment))->Fetch();

        if (!empty($arPayment)) {
            if ($sAction == $arActions["RESULT"]) {
                $iOrderID = CStartShopPayment::IncludeResultHandler($arPayment['ID']);
                $arOrder = CStartShopOrder::GetByID($iOrderID)->Fetch();

                if (!empty($arOrder)) {
                    if ($arOrder['PAYED'] != 'Y') {
                        $iStatus = intval(CStartShopVariables::Get("PAYMENT_ORDER_STATUS"));

                        $arUpdateFields = array('PAYED' => 'Y');

                        if (!empty($iStatus))
                            $arUpdateFields['STATUS'] = $iStatus;

                        CStartShopOrder::Update($iOrderID, $arUpdateFields);
                        CStartShopEvents::Call('OnOrderPayedSuccess', array('ORDER' => $arOrder));

                        if (CStartShopVariables::Get('MAIL_USE', 'N', SITE_ID) == "Y" && CStartShopVariables::Get('MAIL_ADMIN_ORDER_PAY', 'N', SITE_ID) == "Y") {
                            $sEvent = CStartShopVariables::Get('MAIL_ADMIN_ORDER_PAY_EVENT', '', SITE_ID);
                            $sMail = CStartShopVariables::Get('MAIL_MAIL', '', SITE_ID);
                            $oEvent = new CEvent();

                            if (!empty($sEvent) && !empty($sMail))
                                $oEvent->SendImmediate($sEvent, SITE_ID, array(
                                    "ORDER_ID" => $arOrder['ID'],
                                    "ORDER_AMOUNT" => CStartShopCurrency::ConvertAndFormatAsString($arOrder['AMOUNT'] , $arOrder['CURRENCY']),
                                    "STARTSHOP_SHOP_EMAIL" => $sMail,
                                ), "N", "");

                            unset($oEvent);
                        }

                        $sStatus = "SUCCESS";
                    }
                } else {
                    CStartShopEvents::Call('OnOrderPayedFail', array('ORDER_ID' => $iOrderID));
                }
            } else if ($sAction == $arActions["SUCCESS"]) {
                $iOrderID = CStartShopPayment::IncludeSuccessHandler($arPayment['ID']);
                $arOrder = CStartShopOrder::GetByID($iOrderID)->Fetch();

                if (!empty($arOrder)) {
                    if ($arOrder['PAYED'] == 'Y')
                        $sStatus = "SUCCESS";
                }
            } else {
                $iOrderID = CStartShopPayment::IncludeFailHandler($arPayment['ID']);
                $arOrder = CStartShopOrder::GetByID($iOrderID)->Fetch();
            }
        }
    }

    $arResult['STATUS'] = $sStatus;
    $arResult['ORDER'] = $arOrder;

    $this->IncludeComponentTemplate();
?>