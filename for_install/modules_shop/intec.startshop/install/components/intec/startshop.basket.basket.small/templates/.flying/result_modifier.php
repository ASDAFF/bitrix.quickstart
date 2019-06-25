<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $arDefaultParams = array(
        'REQUEST_VARIABLE_BASKET_OPENED' => 'fly_basket_opened'
    );

    $arParams = array_merge($arDefaultParams, $arParams);

	foreach ($arResult['ITEMS'] as $iItemID => $arItem) {
        $arResult['ITEMS'][$iItemID]['ACTIONS']['DELETE'] = CStartShopUtil::UrlParametersSet($arItem['ACTIONS']['DELETE'], array(
            $arParams['REQUEST_VARIABLE_BASKET_OPENED'] => 'Y'
        ));

        $arResult['ITEMS'][$iItemID]['ACTIONS']['SET_QUANTITY'] = CStartShopUtil::UrlParametersSet($arItem['ACTIONS']['SET_QUANTITY'], array(
            $arParams['REQUEST_VARIABLE_BASKET_OPENED'] => 'Y'
        ));
	}
?>