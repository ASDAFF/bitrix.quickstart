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
            "URL" => array("NAME" => GetMessage("SBB_GROUP_URL"))
        ),
        "PARAMETERS" => array(
            "URL_BASKET_EMPTY" => array(
                "PARENT" => "URL",
                "NAME" => GetMessage("SBB_URL_BASKET_EMPTY"),
                "TYPE" => "STRING",
            ),
            "CURRENCY" => array(
                "PARENT" => "BASE",
                "NAME" => GetMessage("SBB_CURRENCY"),
                "TYPE" => "LIST",
                "VALUES" => $arCurrencies
            ),
            "REQUEST_VARIABLE_ACTION" => array(
                "PARENT" => "ADDITIONAL",
                "NAME" => GetMessage("SBB_REQUEST_VARIABLE_ACTION"),
                "TYPE" => "STRING",
                "DEFAULT" => "action"
            ),
            "REQUEST_VARIABLE_ITEM" => array(
                "PARENT" => "ADDITIONAL",
                "NAME" => GetMessage("SBB_REQUEST_VARIABLE_ITEM"),
                "TYPE" => "STRING",
                "DEFAULT" => "item"
            ),
            "REQUEST_VARIABLE_QUANTITY" => array(
                "PARENT" => "ADDITIONAL",
                "NAME" => GetMessage("SBB_REQUEST_VARIABLE_QUANTITY"),
                "TYPE" => "STRING",
                "DEFAULT" => "quantity"
            )
        )
    );
?>