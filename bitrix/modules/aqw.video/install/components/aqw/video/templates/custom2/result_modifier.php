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
    ksort($arData['PREVIEW_URL']);
    ksort($arData['TITLE_URL']);
    foreach ($arData['URL'] as $KEY => $URL){
        $TITLE = (isset($arData['TITLE'][$KEY])) ? $arData['TITLE'][$KEY] : "";
        $PREVIEW_URL = (isset($arData['PREVIEW_URL'][$KEY])) ? $arData['PREVIEW_URL'][$KEY] : "";
        $TITLE_URL = (isset($arData['TITLE_URL'][$KEY])) ? $arData['TITLE_URL'][$KEY] : "";
        if (trim($URL) <> "" || trim($TITLE) <> ""){

            $CAqwVideo = new CAqwVideo();
            $getService = $CAqwVideo->getServiceByUrl($URL);
            if ($getService !== false) {
                $videoDataItem = $getService->getDataByParams($arParams);
                $videoDataItem['title'] = $TITLE;
                $videoDataItem['preview'] = (!empty($PREVIEW_URL)) ? $PREVIEW_URL : $videoDataItem['preview'];
                $videoDataItem['title_url'] = $TITLE_URL;
                $videoData[] = $videoDataItem;
            }
        }
    }
}
if (count($videoData) > 0) {
    $arResult["ITEMS"] = $videoData;
}