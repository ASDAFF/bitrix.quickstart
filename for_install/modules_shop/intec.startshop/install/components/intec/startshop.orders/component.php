<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    global $APPLICATION;

    CStartShopTheme::ApplyTheme(SITE_ID);

    $arDefaultParams = array(
        'SORT' => array(),
        'FILTER' => array(),
        'REQUEST_VARIABLE_ORDER_ID' => 'ORDER_ID'
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    $sPage = 'list';

    if (is_numeric($_REQUEST[$arParams['REQUEST_VARIABLE_ORDER_ID']])) {
        $sPage = 'detail';

        if (!empty($arParams['TITLE_ORDERS_DETAIL'])) {
            $APPLICATION->AddChainItem($arParams['TITLE_ORDERS_DETAIL']);
            $APPLICATION->SetTitle($arParams['TITLE_ORDERS_DETAIL']);
        }
    } else {
        if (!empty($arParams['TITLE_ORDERS_LIST'])) {
            $APPLICATION->AddChainItem($arParams['TITLE_ORDERS_LIST']);
            $APPLICATION->SetTitle($arParams['TITLE_ORDERS_LIST']);
        }
    }

    $this->IncludeComponentTemplate($sPage);
?>