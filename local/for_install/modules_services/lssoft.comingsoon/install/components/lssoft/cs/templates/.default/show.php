<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php require_once('header.php'); 

$APPLICATION->IncludeComponent("lssoft:cs.show",$sTemplateName,array(
		'TITLE' => $arParams['TITLE'],
		'DESCRIPTION' => $arParams['DESCRIPTION'],
		'LIKE' => $arParams['~LIKE'],
		'INVITE_ENABLED' => $arParams['INVITE_ENABLED'],
	),
	$component
);

require_once('footer.php');