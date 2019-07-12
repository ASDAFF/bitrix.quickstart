<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
if (!empty($arResult['DETAIL_PICTURE']['ID'])) {
    $arPic[] = array(
        'RESIZE_SRC' => CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array("width" => 208, "height" => 160), BX_RESIZE_IMAGE_EXACT, true),
        'SRC' => $arResult['DETAIL_PICTURE']['SRC']
    );
} else if (!empty($arResult['PREVIEW_PICTURE']['ID'])) {
    $arPic[] = array(
        'RESIZE_SRC' => CFile::ResizeImageGet($arResult['PREVIEW_PICTURE']['ID'], array("width" => 208, "height" => 160), BX_RESIZE_IMAGE_EXACT, true),
        'SRC' => $arResult['PREVIEW_PICTURE']['SRC']
    );
}

if (!empty($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $val) {
        $arPic[] = array(
            'RESIZE_SRC' => CFile::ResizeImageGet($val, array("width" => 215, "height" => 178), BX_RESIZE_IMAGE_EXACT, true),
            'SRC' => CFile::GetPath($val)
        );
    }
}

if (!empty($arResult['PROPERTIES']['MORE_PHOTO_BOTTOM']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['MORE_PHOTO_BOTTOM']['VALUE'] as $val) {
        $arPicBottom[] = array(
            'RESIZE_SRC' => CFile::ResizeImageGet($val, array("width" => 260, "height" => 200), BX_RESIZE_IMAGE_EXACT, true),
            'SRC' => CFile::GetPath($val)
        );
    }
}

$arResult['IMAGES_SLIDER'] = $arPic;
$arResult['MORE_PHOTO_BOTTOM'] = $arPicBottom;
?>