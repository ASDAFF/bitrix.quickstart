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
            "404" => array("NAME" => GetMessage("SOD_GROUP_404"))
        ),
        "PARAMETERS" => array(
            "CURRENCY" => array(
                "NAME" => GetMessage("SOD_CURRENCY"),
                "TYPE" => "LIST",
                "VALUES" => $arCurrencies
            ),
            "LIST_PAGE_URL" => array(
                "NAME" => GetMessage("SOD_LIST_PAGE_URL"),
                "TYPE" => "STRING",
                "DEFAULT" => "/orders/"
            )
        )
    );

    $arComponentParameters['PARAMETERS']['404_SET_STATUS'] = array(
        "PARENT" => "404",
        "NAME" => GetMessage('SOD_404_SET_STATUS'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arComponentParameters['PARAMETERS']['404_REDIRECT'] = array(
        "PARENT" => "404",
        "NAME" => GetMessage('SOD_404_REDIRECT'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
    );

    $arComponentParameters['PARAMETERS']['404_PAGE'] = array(
        "PARENT" => "404",
        "NAME" => GetMessage('SOD_404_PAGE'),
        "TYPE" => "STRING",
        "DEFAULT" => "/404.php"
    );
?>