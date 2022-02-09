<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ($arParams['AJAX_POST']=='Y' && in_array($arParams['ACTION'], array('REPLY', 'VIEW')))
	ob_start();
?>
