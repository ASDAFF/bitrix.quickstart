<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); $this->setFrameMode(true);?>
<?
//var_dump($arResult); 
    echo CFile::ShowImage(
        $arResult["PHOTO"], 
        (int)$arParams["PHOTO_WIDTH"], 
        (int)$arParams["PHOTO_HEIGHT"], 
        "border=0 alt='".htmlspecialchars($arResult["ELEMENT"]['NAME'],null,SITE_CHARSET)."'",
        "",
        false,
        "",
        (int)$arParams["PHOTO_WIDTH"], 
        (int)$arParams["PHOTO_HEIGHT"] 
    );
?>
<??>