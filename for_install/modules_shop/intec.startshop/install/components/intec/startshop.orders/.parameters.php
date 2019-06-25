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
            "URL" => array("NAME" => GetMessage("SO_GROUP_URL")),
            "TITLE" => array("NAME" => GetMessage("SO_GROUP_TITLE")),
            "404" => array("NAME" => GetMessage("SO_GROUP_404"))
        ),
        "PARAMETERS" => array(
            "CURRENCY" => array(
                "PARENT" => "BASE",
                "NAME" => GetMessage("SO_CURRENCY"),
                "TYPE" => "LIST",
                "VALUES" => $arCurrencies
            ),
            "REQUEST_VARIABLE_ORDER_ID" => array(
                "NAME" => GetMessage("SO_REQUEST_VARIABLE_ORDER_ID"),
                "TYPE" => "STRING",
                "DEFAULT" => "ORDER_ID"
            ),
            "TITLE_ORDERS_LIST" => array(
                "PARENT" => "TITLE",
                "NAME" => GetMessage("SO_TITLE_ORDERS_LIST"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SO_TITLE_ORDERS_LIST_DEFAULT")
            ),
            "TITLE_ORDERS_DETAIL" => array(
                "PARENT" => "TITLE",
                "NAME" => GetMessage("SO_TITLE_ORDERS_DETAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => GetMessage("SO_TITLE_ORDERS_DETAIL_DEFAULT")
            ),
        )
    );

    $arComponentParameters['PARAMETERS']['404_SET_STATUS'] = array(
        "PARENT" => "404",
        "NAME" => GetMessage('SO_404_SET_STATUS'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arComponentParameters['PARAMETERS']['404_REDIRECT'] = array(
        "PARENT" => "404",
        "NAME" => GetMessage('SO_404_REDIRECT'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arComponentParameters['PARAMETERS']['404_PAGE'] = array(
        "PARENT" => "404",
        "NAME" => GetMessage('SO_404_PAGE'),
        "TYPE" => "STRING",
        "DEFAULT" => "/404.php"
    );
?>