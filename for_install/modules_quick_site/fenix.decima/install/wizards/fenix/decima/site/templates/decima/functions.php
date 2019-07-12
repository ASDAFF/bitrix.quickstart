<?
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
    if (!function_exists('resize'))
    {
        function resize($img, $PROP, $width, $height){
            if($PROP=='Y')
                $file = CFile::ResizeImageGet($img, array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_PROPORTIONAL, true, false, false, 100);  
            else $file = CFile::ResizeImageGet($img, array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_EXACT, true, false, false, 100);  
            $result=Array('ID'=>$img, 'SRC'=>$file['src'], 'WIDTH'=>$file['width'], 'HEIGHT'=>$file['height']);  

            return $result;
        }
    }

    if (!function_exists('margin'))
    {
        function margin($height_fact, $height, $width_fact, $width){
            
            $result='';
            $result.=($height_fact<$height)?abs(($height-$height_fact)/2):'0';$result.='px ';
            $result.=($width_fact<$width)?abs($width-(($width-$width_fact)/2)-$width_fact):'0';$result.='px '; 
            $result.=($height_fact<$height)?abs(($height-$height_fact)/2):'0';$result.='px ';
            $result.=($width_fact<$width)?abs(($width-$width_fact)/2):'0';$result.='px; ';
            if($width_fact>$height_fact)$result.='width:'.$width.'px; height:'.$height.'px;';
            elseif($height_fact>$width_fact)$result.='width:'.$width.'px; height:'.$height.'px;';
            /*style="margin:<?=($height_fact<$arParams['DISPLAY_IMG_HEIGHT'])?abs(($arParams['DISPLAY_IMG_HEIGHT']-$arItem["PICTURE"]["HEIGHT"])/2):'0'?>px <?=abs($arParams['DISPLAY_IMG_WIDTH']-(($arParams['DISPLAY_IMG_WIDTH']-$arItem["PICTURE"]["WIDTH"])/2)-$arItem["PICTURE"]["WIDTH"])?>px  <?=($arItem["PICTURE"]["HEIGHT"]<$arParams['DISPLAY_IMG_HEIGHT'])?abs(($arParams['DISPLAY_IMG_HEIGHT']-$arItem["PICTURE"]["HEIGHT"])/2):'0'?>px <?=($arItem["PICTURE"]["WIDTH"]<$arParams['DISPLAY_IMG_WIDTH'])?abs(($arParams['DISPLAY_IMG_WIDTH']-$arItem["PICTURE"]["WIDTH"])/2):'0'?>px; <?if($arItem["PICTURE"]["WIDTH"]>$arItem["PICTURE"]["HEIGHT"] && $arItem["PICTURE"]["WIDTH"]>$arParams['DISPLAY_IMG_WIDTH'])echo 'width="'.$arParams['DISPLAY_IMG_WIDTH'].'"';elseif($arItem["PICTURE"]["HEIGHT"]>$arItem["PICTURE"]["WIDTH"] && $arItem["PICTURE"]["HEIGHT"]>$arParams['DISPLAY_IMG_HEIGHT'])echo 'height="'.$arParams['DISPLAY_IMG_HEIGHT'].'"'?>"
            */
            return $result;
        }
    }
    
?>