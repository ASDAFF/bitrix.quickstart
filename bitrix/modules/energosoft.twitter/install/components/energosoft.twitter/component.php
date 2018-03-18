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
$arParams["ES_TWITTER_AUTOREFRESH"] = intval($arParams["ES_TWITTER_AUTOREFRESH"]);
$arParams["ES_WITDH"] = intval($arParams["ES_WITDH"]);
$arParams["ES_HEIGHT"] = intval($arParams["ES_HEIGHT"]);

$arResult["ID"] = uniqid("_");
$this->IncludeComponentTemplate();

if($arParams["ES_INCLUDE_JQUERY"]=="Y") $APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery-1.6.4.min.js\"></script>", true);
if($arParams["ES_INCLUDE_JQUERY_MOUSEWHEEL"]=="Y") $APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery.mousewheel.min.js\"></script>", true);
if($arParams["ES_INCLUDE_JQUERY_JSCROLLPANE"]=="Y") $APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery.jscrollpane.min.js\"></script>", true);
if($arParams["ES_INCLUDE_JQUERY_TIMEAGO"]=="Y") $APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery.timeago.js\"></script>", true);
if($arParams["ES_INCLUDE_JQUERY_TIMEAGO_RU"]=="Y") $APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery.timeago.ru.js\"></script>", true);
$APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"/bitrix/js/energosoft/jquery.energosoft.twitter.js\"></script>", true);
$APPLICATION->AddHeadString("<script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>", true);
?>