<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $arDefaultParams = array(
        'USE_ADAPTABILITY' => 'N',
        'USE_ITEMS_PICTURES' => 'Y',
        'USE_BUTTON_BASKET' => 'N',
        'URL_BASKET' => '',
    );

    $arParams = array_merge($arDefaultParams, $arParams);

    $arParams['USE_BUTTON_BASKET'] = ($arParams['USE_BUTTON_BASKET'] == 'Y' && !empty($arParams['URL_BASKET'])) ? 'Y' : 'N';

    if ($arParams['USE_ITEMS_PICTURES'] == 'Y')
        foreach ($arResult['ITEMS'] as &$arItem) {
            $arItem['PICTURE'] = CStartShopToolsIBlock::GetItemPicture($arItem, 200, 200, true);

            if (empty($arItem['PICTURE']))
                $arItem['PICTURE']['SRC'] = $this->GetFolder().'/images/image.empty.png';
        }
?>
