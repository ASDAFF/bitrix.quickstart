<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php require_once('header.php'); 

$APPLICATION->IncludeComponent("lssoft:cs.registration",$sTemplateName,array(
		'_SITE_DIR' => $arParams['~_SITE_DIR'],
		'INVITE_NEED_LOGIN' => $arParams['INVITE_NEED_LOGIN'],
	),
	$component
);

require_once('footer.php');