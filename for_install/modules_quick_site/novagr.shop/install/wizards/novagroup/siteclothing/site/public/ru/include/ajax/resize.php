<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

/*
examples

http://demo1251.local/include/ajax/resize.php?file[]=/upload/iblock/bc2/bc2682e97408a7b9bcded6a93418810c.jpg&file[]=/upload/iblock/010/010dd68beeaf401b2240464973e1181b.jpg

http://demo1251.local/include/ajax/resize.php?file[]=1342&file[]=2719&w=200&h=500

*/

if (!empty($_REQUEST["w"]))
    $width = $_REQUEST["w"];
else
    $width = 177;

if (!empty($_REQUEST["h"]))
    $height = $_REQUEST["h"];
else
    $height = 236;


$arResult["files"] = array();
if (is_array($_REQUEST['file'])) {
    foreach ($_REQUEST['file'] as $file) {
        $arFileTmp = Novagroup_Classes_General_Main::reSizeImgAndCache(
            $file,
            array('W'=>$width, 'H'=>$height)
        );
        if (!empty($arFileTmp["src"])) $arResult["files"][] =  $arFileTmp["src"];
        //deb($arFileTmp);
    }

    $arResultJson = json_encode($arResult);
    die($arResultJson);
}
?>