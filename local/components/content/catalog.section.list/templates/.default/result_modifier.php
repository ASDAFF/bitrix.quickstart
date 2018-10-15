<?
$arParams['PICTURE_WIDTH'] = intval($arParams['PICTURE_WIDTH']);
$arParams['PICTURE_HEIGHT'] = intval($arParams['PICTURE_HEIGHT']);

$arParams['PICTURE_WIDTH'] = ($arParams['PICTURE_WIDTH'] > 0 ) ? $arParams['PICTURE_WIDTH'] : 100;
$arParams['PICTURE_HEIGHT'] = ($arParams['PICTURE_HEIGHT'] > 0 ) ? $arParams['PICTURE_HEIGHT'] : 100;

foreach ($arResult['SECTIONS'] as $key => $arSection)
{
	if(is_array($arSection['PICTURE'])):
		$picture = CFile::ResizeImageGet($arSection['PICTURE'], array('width'=>$arParams['PICTURE_WIDTH'], 'height'=>$arParams['PICTURE_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
		$arResult['SECTIONS'][$key]['PICTURE'] = $picture;
	endif;
}
?>