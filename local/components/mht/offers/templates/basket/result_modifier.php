<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/slick.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/slick_init.js");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/slick.css");

?>