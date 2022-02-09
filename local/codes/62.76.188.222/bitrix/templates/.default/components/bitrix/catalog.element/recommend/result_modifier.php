<? 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)  die();

if ($arResult['PREVIEW_PICTURE']) { 
    $file = CFile::ResizeImageGet($arResult['PREVIEW_PICTURE'], 
              array('width' => 155,
                    'height' => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
    $arResult['PREVIEW_PICTURE'] = array();
    $arResult['PREVIEW_PICTURE']['SRC'] = $file["src"];
    $arResult['PREVIEW_PICTURE']['WIDTH'] = $file['width'];
    $arResult['PREVIEW_PICTURE']['HEIGHT'] = $file['height'];
} 