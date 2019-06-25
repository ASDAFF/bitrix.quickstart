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
            "URL" => array("NAME" => GetMessage("SO_GROUP_URL"))
        ),
        "PARAMETERS" => array(
            "URL_BASKET_EMPTY" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SO_URL_BASKET_EMPTY"),
                "TYPE" => "STRING",
            ),
            "URL_ORDER_CREATED" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SO_URL_ORDER_CREATED"),
                "TYPE" => "STRING",
            ),
            "URL_ORDER_CREATED_TO_USER" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SO_URL_ORDER_CREATED_TO_USER"),
                "TYPE" => "STRING",
            ),
            "CURRENCY" => array(
                "PARENT" => "BASE",
                "NAME" => GetMessage("SO_CURRENCY"),
                "TYPE" => "LIST",
                "VALUES" => $arCurrencies
            ),
            "REQUEST_VARIABLE_ACTION" => array(
                "PARENT" => "ADDITIONAL",
                "NAME" => GetMessage("SO_REQUEST_VARIABLE_ACTION"),
                "TYPE" => "STRING",
                "DEFAULT" => "action"
            ),
        )
    );
?>