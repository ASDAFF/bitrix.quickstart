<?
if($arParams['INCLUDE_JQUERY'] == 'Y'){
    $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$templateFolder.'/js/jquery-1.7.1.min.js"></script>',true);
}
if($arParams['INCLUDE_FANCYBOX'] == 'Y'){
    $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$templateFolder.'/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>',true);
    $APPLICATION->AddHeadString('<link rel="stylesheet" href="'.$templateFolder.'/js/fancybox/jquery.fancybox-1.3.4.css">',true);
}
if($arParams['INCLUDE_LAZY'] == 'Y'){
    $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$templateFolder.'/js/jquery.lazyload/jquery.lazyload.min.js"></script>',true);
}
?>