<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?global $APPLICATION, $USER;?>
<?$this->setFrameMode(true);?>
    <?
        $arFilter = $arParams['FILTER'];

        if (!is_array($arFilter))
            $arFilter = array();

        if ($USER->IsAuthorized()) {
            $arFilter['USER'] = $USER->GetID();
        } else {
            $arFilter['USER'] = null;
        }
    ?>
    <?$APPLICATION->IncludeComponent("intec:startshop.orders.detail", ".default", array(
        "FILTER" => $arFilter,
        "SORT" => $arParams['SORT'],
        "LIST_PAGE_URL" => $APPLICATION->GetCurPageParam("", array($arParams['ORDER_ID_VARIABLE'])),
        "ORDER_ID" => $_REQUEST[$arParams['REQUEST_VARIABLE_ORDER_ID']],
        "CURRENCY" => $arParams['CURRENCY'],
        "USE_ADAPTABILITY" => $arParams['USE_ADAPTABILITY'],
        "404_SET_STATUS" => $arParams['404_SET_STATUS'],
        "404_REDIRECT" => $arParams['404_REDIRECT'],
        "404_PAGE" => $arParams['404_PAGE']
    ),
    $component
);?>