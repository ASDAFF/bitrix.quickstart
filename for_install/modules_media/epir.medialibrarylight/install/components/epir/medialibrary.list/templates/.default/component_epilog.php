<?
if($arParams['INCLUDE_JQUERY'] == 'Y'){
    $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$templateFolder.'/js/jquery-1.7.1.min.js"></script>',true);
}
if($arParams['INCLUDE_FANCYBOX'] == 'Y'){
    $APPLICATION->AddHeadString('<link rel="stylesheet" href="'.$templateFolder.'/js/fancybox/jquery.fancybox.css?v=2.1.4">',true);
    $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$templateFolder.'/js/fancybox/jquery.fancybox.pack.js?v=2.1.4"></script>',true);
}
if($arParams['INCLUDE_LAZY'] == 'Y'){
    $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$templateFolder.'/js/jquery.lazyload/jquery.lazyload.min.js"></script>',true);
}
?>