<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if ($arParams['BXCERT_IMG_TYPE'] && ($arParams['BXCERT_IMG_WIDTH'] || $arParams['BXCERT_IMG_HEIGHT'])) {
    if (is_array($arResult["ITEMS"])) {
        foreach ($arResult["ITEMS"] as $i => $arItem) {
            if (is_array($arItem[$arParams['BXCERT_IMG_TYPE']])) {
                $ratio = $arItem[$arParams['BXCERT_IMG_TYPE']]["WIDTH"] / $arItem[$arParams['BXCERT_IMG_TYPE']]["HEIGHT"];
                if ($arParams['BXCERT_IMG_WIDTH'] && !$arParams['BXCERT_IMG_HEIGHT']) {
                    $arParams['BXCERT_IMG_HEIGHT'] = round($arParams['BXCERT_IMG_WIDTH'] / $ratio);
                } elseif (!$arParams['BXCERT_IMG_WIDTH'] && $arParams['BXCERT_IMG_HEIGHT']) {
                    $arParams['BXCERT_IMG_WIDTH'] = $arParams['BXCERT_IMG_HEIGHT'] * $ratio;
                }
                $img = CFile::ResizeImageGet($arItem[$arParams['BXCERT_IMG_TYPE']], array("width" => $arParams['BXCERT_IMG_WIDTH'], "height" => $arParams['BXCERT_IMG_HEIGHT']), BX_RESIZE_IMAGE_EXACT);
                $arResult["ITEMS"][$i][$arParams['BXCERT_IMG_TYPE']]["SRC"] = $img["src"];
                $arResult["ITEMS"][$i][$arParams['BXCERT_IMG_TYPE']]["WIDTH"] = $arParams['BXCERT_IMG_WIDTH'];
                $arResult["ITEMS"][$i][$arParams['BXCERT_IMG_TYPE']]["HEIGHT"] = $arParams['BXCERT_IMG_HEIGHT'];
            }
        }
    } elseif (is_array($arResult[$arParams['BXCERT_IMG_TYPE']])) {
        $ratio = $arResult[$arParams['BXCERT_IMG_TYPE']]["WIDTH"] / $arResult[$arParams['BXCERT_IMG_TYPE']]["HEIGHT"];
        if ($arParams['BXCERT_IMG_WIDTH'] && !$arParams['BXCERT_IMG_HEIGHT']) {
            $arParams['BXCERT_IMG_HEIGHT'] = round($arParams['BXCERT_IMG_WIDTH'] / $ratio);
        } elseif (!$arParams['BXCERT_IMG_WIDTH'] && $arParams['BXCERT_IMG_HEIGHT']) {
            $arParams['BXCERT_IMG_WIDTH'] = $arParams['BXCERT_IMG_HEIGHT'] * $ratio;
        }
        $img = CFile::ResizeImageGet($arResult[$arParams['BXCERT_IMG_TYPE']], array("width" => $arParams['BXCERT_IMG_WIDTH'], "height" => $arParams['BXCERT_IMG_HEIGHT']), BX_RESIZE_IMAGE_EXACT);
        $arResult[$arParams['BXCERT_IMG_TYPE']]["SRC"] = $img["src"];
        $arResult[$arParams['BXCERT_IMG_TYPE']]["WIDTH"] = $arParams['BXCERT_IMG_WIDTH'];
        $arResult[$arParams['BXCERT_IMG_TYPE']]["HEIGHT"] = $arParams['BXCERT_IMG_HEIGHT'];
    }
}
?>