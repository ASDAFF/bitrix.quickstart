<?
######################################################
# Name: energosoft.twitter                           #
# File: component.php                                #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;
if(!CModule::IncludeModule("energosoft.twitter")) return;

$arParams["ES_TWITTER"] = urlencode(trim($arParams["ES_TWITTER"]));
$arResult["DATA_RELATED"] = $arParams["ES_DATA_RELATED"];
if($arParams["ES_DATA_RELATED"]!="" && $arParams["ES_DATA_RELATED_DESCRIPTION"]!="") $arResult["DATA_RELATED"] = $arParams["ES_DATA_RELATED"].":".$arParams["ES_DATA_RELATED_DESCRIPTION"];
$this->IncludeComponentTemplate();
$APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>", true);
?>