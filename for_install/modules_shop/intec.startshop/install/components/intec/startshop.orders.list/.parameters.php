<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arCurrencies = array();
    $dbCurrencies = CStartShopCurrency::GetList();

    while ($arCurrency = $dbCurrencies->Fetch())
        $arCurrencies[$arCurrency['CODE']] = '['.$arCurrency['CODE'].'] '.$arCurrency['LANG'][LANGUAGE_ID]['NAME'];

    unset($dbCurrencies, $arCurrency);

    $arComponentParameters = array(
        "PARAMETERS" => array(
            "CURRENCY" => array(
                "NAME" => GetMessage("SOL_CURRENCY"),
                "TYPE" => "LIST",
                "VALUES" => $arCurrencies
            ),
            "DETAIL_PAGE_URL" => array(
                "NAME" => GetMessage("SOL_DETAIL_PAGE_URL"),
                "TYPE" => "STRING",
                "DEFAULT" => "/?ORDER_ID=#ID#"
            )
        )
    )
?>