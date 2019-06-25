<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arCurrencies = array();
    $dbCurrencies = CStartShopCurrency::GetList();

    while ($arCurrency = $dbCurrencies->Fetch())
        $arCurrencies[$arCurrency['CODE']] = '['.$arCurrency['CODE'].'] '.$arCurrency['LANG'][LANGUAGE_ID]['NAME'];

    unset($dbCurrencies, $arCurrency);

    $arComponentParameters = array(
        "GROUPS" => array(
            "URL" => array("NAME" => GetMessage("SB_GROUP_URL")),
            "TITLE" => array("NAME" => GetMessage("SB_GROUP_TITLE"))
        ),
        "PARAMETERS" => array(
            "URL_BASKET_EMPTY" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SB_URL_BASKET_EMPTY"),
                "TYPE" => "STRING",
            ),
            "URL_ORDER_CREATED" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SB_URL_ORDER_CREATED"),
                "TYPE" => "STRING",
            ),
            "URL_ORDER_CREATED_TO_USER" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SB_URL_ORDER_CREATED_TO_USER"),
                "TYPE" => "STRING",
            ),
            "CURRENCY" => array(
                "PARENT" => "BASE",
                "NAME" => GetMessage("SB_CURRENCY"),
                "TYPE" => "LIST",
                "VALUES" => $arCurrencies
            ),
            "REQUEST_VARIABLE_ACTION" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_ACTION"),
                "TYPE" => "STRING",
                "DEFAULT" => "action"
            ),
            "REQUEST_VARIABLE_ITEM" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_ITEM"),
                "TYPE" => "STRING",
                "DEFAULT" => "item"
            ),
            "REQUEST_VARIABLE_QUANTITY" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_QUANTITY"),
                "TYPE" => "STRING",
                "DEFAULT" => "quantity"
            ),
            "REQUEST_VARIABLE_PAGE" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_PAGE"),
                "TYPE" => "STRING",
                "DEFAULT" => "page"
            ),
            "REQUEST_VARIABLE_PAYMENT" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_PAYMENT"),
                "TYPE" => "STRING",
                "DEFAULT" => "payment"
            ),
            "REQUEST_VARIABLE_VALUE_RESULT" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_VALUE_RESULT"),
                "TYPE" => "STRING",
                "DEFAULT" => "result"
            ),
            "REQUEST_VARIABLE_VALUE_SUCCESS" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_VALUE_SUCCESS"),
                "TYPE" => "STRING",
                "DEFAULT" => "success"
            ),
            "REQUEST_VARIABLE_VALUE_FAIL" => array(
                "NAME" => GetMessage("SB_REQUEST_VARIABLE_VALUE_FAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => "fail"
            ),
            "TITLE_BASKET" => array(
                "PARENT" => "TITLE",
                "NAME" => GetMessage("SB_TITLE_BASKET"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SB_TITLE_BASKET_DEFAULT")
            ),
            "TITLE_ORDER" => array(
                "PARENT" => "TITLE",
                "NAME" => GetMessage("SB_TITLE_ORDER"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SB_TITLE_ORDER_DEFAULT")
            ),
            "TITLE_PAYMENT" => array(
                "PARENT" => "TITLE",
                "NAME" => GetMessage("SB_TITLE_PAYMENT"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SB_TITLE_PAYMENT_DEFAULT")
            )
        )
    );
?>