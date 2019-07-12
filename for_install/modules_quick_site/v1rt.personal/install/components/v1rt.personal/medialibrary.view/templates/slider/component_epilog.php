<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

if($arParams["LOAD_JS"] == "Y")
{
    $path = str_replace("\\", "/", dirname(__FILE__));
    $path = str_replace($_SERVER["DOCUMENT_ROOT"], "", $path);
    
    $arParams["NIVO_CONTROLNAV"]    = ($arParams["NIVO_CONTROLNAV"] == "Y") ? true : false;
    $arParams["NIVO_PAUSEOFHOVER"]  = ($arParams["NIVO_PAUSEOFHOVER"] == "Y") ? true : false;
    $arParams["NIVO_DIRNAV"]        = ($arParams["NIVO_DIRNAV"] == "Y") ? true : false;
    
    $APPLICATION->AddHeadScript($path."/nivoslider/jquery.nivo.slider.pack.js");    
    $APPLICATION->AddHeadString('<link href="'.$path.'/nivoslider/nivo-slider.css" rel="stylesheet" type="text/css" />');
    $APPLICATION->AddHeadString('<script type="text/javascript">$(window).load(function(){$("#slider").nivoSlider({effect:"'.$arParams["NIVO_EFFECT"].'", animSpeed:"'.$arParams["NIVO_ANIMSPEED"].'", pauseTime:"'.$arParams["NIVO_PAUSETIME"].'", directionNav:"'.$arParams["NIVO_DIRNAV"].'", controlNav:"'.$arParams["NIVO_CONTROLNAV"].'", pauseOnHover:"'.$arParams["NIVO_PAUSEOFHOVER"].'"});});</script>');
}

if($arParams["RESIZE_MODE_W"] != "" && $arParams["RESIZE_MODE_H"] != "" && $arParams["RESIZE_MODE"] == "F")
    $APPLICATION->AddHeadString('<style type="text/css">#slider {display:block;width:'.$arParams["RESIZE_MODE_W"].'px;height:'.$arParams["RESIZE_MODE_H"].'px;overflow:hidden !important;}</style>');
?>