<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!empty($arResult['ITEMS'])) {
    $brand = 'BRAND';//name property

    foreach ($arResult['ITEMS'] as $arItemID => $arItem) {
        if ($arItem['CODE'] == $brand) {
            foreach ($arItem['VALUES'] as $key => $item) {
                if ($item['VALUE'] == $arParams['INNET_BRAND_VALUE']){

                    $arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', '!NAME' => false, 'PROPERTY_' . $brand => $item['VALUE']);
                    $res = CIBlockElement::GetList(array(), $arFilter, false, array('nTopCount' => 1), array('PROPERTY_' . $brand));
                    if ($arFields = $res->Fetch()) {
                        $arResult['BRAND_FILTER'] = array('NAME' => $arFields['PROPERTY_BRAND_VALUE'], 'LINK' => SITE_DIR . 'catalog/' . '?' . $item['CONTROL_NAME'] . '=Y&set_filter=y');
                    }
                    break;
                }
            }
        }
    }
}

?>