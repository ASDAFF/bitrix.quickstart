<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $APPLICATION;

$dir = str_replace('\\', '/', __DIR__);
include($dir."/lang/".LANGUAGE_ID."/template.php");
$MESS["CT_BETS_USER_NAME"] = $arResult["USERS_BETS"][0]["USER_NAME"];
$APPLICATION->AddHeadString('<script type="text/javascript">BX.message('.CUtil::PhpToJsObject($MESS).');</script>');
$APPLICATION->AddHeadString('<script type="text/javascript">var productId = '.intval($arResult["AUCTION"]["PRODUCT_ID"]).';var auctionId = '.intval($arResult["AUCTION"]["ID"]).';</script>');
?>