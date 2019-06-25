<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?global $APPLICATION, $USER;?>
<?$this->setFrameMode(true);?>
<?if (!empty($arParams['ORDERS_LIST_HEADER'])) $APPLICATION->SetTitle($arParams['ORDERS_LIST_HEADER']);?>
<?if ($USER->IsAuthorized()):?>
    <?$arFilter = array_merge($arParams['FILTER'], array("USER" => $USER->GetID()))?>
    <?$APPLICATION->IncludeComponent("intec:startshop.orders.list", ".default", array(
            "CURRENCY" => $arParams['CURRENCY'],
            "SORT" => array('DATE_CREATE' => 'DESC'),
            "FILTER" => $arFilter,
            "DETAIL_PAGE_URL" => $APPLICATION->GetCurPageParam($arParams['REQUEST_VARIABLE_ORDER_ID']."=#ID#", array($arParams['ORDER_ID_VARIABLE'])),
            "USE_ADAPTABILITY" => $arParams['USE_ADAPTABILITY']
        ),
        $component
    );?>
<?elseif(!empty($arParams['URL_AUTHORIZE'])):?>
    <?LocalRedirect($arParams['URL_AUTHORIZE']);?>
    <?die();?>
<?endif;?>
