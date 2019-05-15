<?
/*
 * Получаем аватарку пользователя
 * */
foreach ($arResult['USERS'] as $key => $arUser){
    if(!empty($arUser['PERSONAL_PHOTO'])){
        $arResult['USERS'][$key]['RESIZE_IMG'] = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width'=>120, 'height'=>120), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    }
}
?>