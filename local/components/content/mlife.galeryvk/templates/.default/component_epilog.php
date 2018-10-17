<?
if($arParams['JQUERY']==1) {
$APPLICATION->AddHeadScript($templateFolder."/js/jquery.1.9.js");
}
if($arParams['FANCY']==1) {
$APPLICATION->SetAdditionalCSS($templateFolder."/fancybox/jquery.fancybox-1.3.4.css");
$APPLICATION->SetAdditionalCSS($templateFolder."/fancybox/jquery.fancybox.css");
$APPLICATION->AddHeadScript($templateFolder."/fancybox/lib/jquery.mousewheel-3.0.6.pack.js");
$APPLICATION->AddHeadScript($templateFolder."/fancybox/jquery.fancybox.js");
}
if($arParams['FANCY_TRUMB']==1){
$APPLICATION->AddHeadScript($templateFolder."/fancybox/helpers/jquery.fancybox-thumbs.js");
$APPLICATION->SetAdditionalCSS($templateFolder."/fancybox/helpers/jquery.fancybox-thumbs.css");
}
?>