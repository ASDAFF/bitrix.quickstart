<?php
/**
 * Created by JetBrains PhpStorm.
 * Project: www-demo1251
 * User: anton (aqw.novij@gmail.com)
 * Date: 05.08.13
 * Time: 12:17
 */
$HTTP_HOST = ($APPLICATION->IsHTTPS()) ? "https://" : "http://";
$HTTP_HOST .= $_SERVER["HTTP_HOST"];

$parseHost = parse_url($HTTP_HOST);
if($parseHost['scheme']=='http' and $parseHost['port']=='80') $HTTP_HOST = 'http://'.$parseHost['host'];
if($parseHost['scheme']=='https' and $parseHost['port']=='443') $HTTP_HOST = 'https://'.$parseHost['host'];

global $APPLICATION;
// получим полный URI текущий страницы
$CURRENT_PAGE = $HTTP_HOST;
$CURRENT_PAGE .= $APPLICATION->GetCurUri();
$CURRENT_PAGE = '<strong>'.$CURRENT_PAGE.'</strong>';
ob_start();
$APPLICATION->IncludeComponent("novagroup:map","",Array('SET_TITLE'=>"N"),false);
$SITE_MAP = "<h2>".GetMessage('TITLE')."</h2>".ob_get_contents();
ob_end_clean();

$arResult["DETAIL_TEXT"] = str_replace(
    array("#CURRENT_URL#", "#SITE_MAP#"),
    array($CURRENT_PAGE, $SITE_MAP),
    $arResult["DETAIL_TEXT"]
);