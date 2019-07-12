<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

if($arParams["LOAD_JS"] == "Y")
{
    $path = str_replace("\\", "/", dirname(__FILE__));
    $path = str_replace($_SERVER["DOCUMENT_ROOT"], "", $path);
    
    $APPLICATION->AddHeadString('<link href="'.$path.'/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />');
    $APPLICATION->AddHeadString('<script type="text/javascript">var tplImage = "'.GetMessage("V1RT_PERSONAL_IMAGE").'";</script>');
    
    $APPLICATION->AddHeadScript($path."/fancybox/jquery.easing-1.3.pack.js");
    $APPLICATION->AddHeadScript($path."/fancybox/jquery.mousewheel-3.0.4.pack.js");
    $APPLICATION->AddHeadScript($path."/fancybox/jquery.fancybox-1.3.4.pack.js");
    $APPLICATION->AddHeadScript($path."/fancybox/script.js");
}

if($arParams["RESIZE_MODE_W"] != "" && $arParams["RESIZE_MODE_H"] != "" && $arParams["RESIZE_MODE"] == "F")
    $APPLICATION->AddHeadString('<style type="text/css">.photo-line a {display:block;width:'.$arParams["RESIZE_MODE_W"].'px;height:'.$arParams["RESIZE_MODE_H"].'px;overflow:hidden !important;}</style>');
elseif($arParams["RESIZE_MODE_W"] != "" && $arParams["RESIZE_MODE_H"] != "" && $arParams["RESIZE_MODE"] == "P")
    $APPLICATION->AddHeadString('<style type="text/css">.photo-line a {display:block;width:'.$arResult["MIN_W"].'px;height:'.$arResult["MIN_H"].'px;overflow:hidden !important;}</style>');
?>