<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
$maxW = 150;
$maxH = 120;
 
foreach($arResult["ITEMS"] as &$arElement){
    if($arElement['PREVIEW_PICTURE']){
        $file = CFile::ResizeImageGet($arElement['PREVIEW_PICTURE'],
                array('width'=>$maxW, 'height'=>$maxH),
                BX_RESIZE_IMAGE_PROPORTIONAL,
                true);            
        $arElement['PREVIEW_PICTURE'] = array();
        $arElement['PREVIEW_PICTURE']['SRC'] = $file['src'];
        $arElement['PREVIEW_PICTURE']['WIDTH'] = $file['width']; 
        $arElement['PREVIEW_PICTURE']['HEIGHT'] = $file['height']; 
    }  
}  

$tm = array();

foreach($arResult['ITEMS'] as $key => $item)
    if(in_array($item['ID'], $tm))
        unset($arResult['ITEMS'][$key]);
    else
        $tm[] = $item['ID'];
