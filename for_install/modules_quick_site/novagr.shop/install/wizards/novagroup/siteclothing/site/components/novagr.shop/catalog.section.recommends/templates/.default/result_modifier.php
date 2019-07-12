<?php
/**
 * Created by JetBrains PhpStorm.
 * Project: www-demo1251-utf8
 * User: anton (aqw.novij@gmail.com)
 * Date: 06.10.13
 * Time: 20:00
 */

$arParams["MAP_DATA"] = parse_str($arParams["MAP_DATA"], $output);
if (is_array($output) and isset($output['MAP_DATA'])) {
    $arData = (is_array($output['MAP_DATA'])) ? $output['MAP_DATA'] : array();
}

$videoData = array();
if (is_array($arData['URL']) and is_array($arData['TITLE'])){
    ksort($arData['URL']);
    ksort($arData['TITLE']);
    foreach ($arData['URL'] as $KEY => $URL){
        $TITLE = (isset($arData['TITLE'][$KEY])) ? $arData['TITLE'][$KEY] : "";
        if (trim($URL) <> "" || trim($TITLE) <> ""){
            $videoData[] = array(
                "IMAGE_SRC" => $URL,
                "PRODUCT_URL" => $TITLE,
            );
        }
    }
}
if (count($videoData) > 0) {
    $arResult["ITEMS"] = $videoData;
}