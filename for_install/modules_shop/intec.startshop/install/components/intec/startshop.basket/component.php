<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    global $APPLICATION;

    $arDefaultParams = array(
        'REQUEST_VARIABLE_PAGE' => 'page',
        'REQUEST_VARIABLE_ACTION' => 'action',
        'REQUEST_VARIABLE_ITEM' => 'item',
        'REQUEST_VARIABLE_QUANTITY' => 'quantity',
        'URL_BASKET_EMPTY' => ''
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    $arRequestParametersRemove = array(
        $arParams['REQUEST_VARIABLE_PAGE'],
        $arParams['REQUEST_VARIABLE_ACTION'],
        $arParams['REQUEST_VARIABLE_ITEM'],
        $arParams['REQUEST_VARIABLE_QUANTITY']
    );

    $arResult['URL_BASKET'] = $APPLICATION->GetCurPageParam(
        $arParams['REQUEST_VARIABLE_PAGE'].'=basket',
        $arRequestParametersRemove
    );

    $arResult['URL_ORDER'] = $APPLICATION->GetCurPageParam(
        $arParams['REQUEST_VARIABLE_PAGE'].'=order',
        $arRequestParametersRemove
    );

    if (empty($arParams['URL_BASKET_EMPTY'])) {
        $arResult['URL_BASKET_EMPTY'] = $arResult['URL_BASKET'];
    } else {
        $arResult['URL_BASKET_EMPTY'] = $arParams['URL_BASKET_EMPTY'];
    }

    $arPages = array(
        'basket',
        'order',
        'payment'
    );

    $sPage = $_GET[$arParams['REQUEST_VARIABLE_PAGE']];

    if (!in_array($sPage, $arPages))
        $sPage = current($arPages);

    if ($sPage == 'basket') {
        if (!empty($arParams['TITLE_BASKET'])) {
            $APPLICATION->SetTitle($arParams['TITLE_BASKET']);
            $APPLICATION->AddChainItem($arParams['TITLE_BASKET']);
        }

        $this->IncludeComponentTemplate('basket');
    } else if ($sPage == 'order') {
        if (!empty($arParams['TITLE_ORDER'])) {
            $APPLICATION->SetTitle($arParams['TITLE_ORDER']);
            $APPLICATION->AddChainItem($arParams['TITLE_ORDER']);
        }

        $this->IncludeComponentTemplate('order');
    } else if ($sPage == 'payment') {
        if (!empty($arParams['TITLE_PAYMENT'])) {
            $APPLICATION->SetTitle($arParams['TITLE_PAYMENT']);
            $APPLICATION->AddChainItem($arParams['TITLE_PAYMENT']);
        }

        $this->IncludeComponentTemplate('payment');
    }
?>
