<?php
/**
 * Created by JetBrains PhpStorm.
 * Project: www-demo1251-utf8
 * User: anton (aqw.novij@gmail.com)
 * Date: 06.10.13
 * Time: 20:00
 */

if (!is_array($arParams["IBLOCKS"]))
    $arParams["IBLOCKS"] = array($arParams["IBLOCKS"]);

foreach ($arParams["IBLOCKS"] as $k => $v) {
    if (!$v)
        unset($arParams["IBLOCKS"][$k]);
}

if (count($arParams["IBLOCKS"]) > 0) {
    $videoData = array();
    foreach ($arParams["IBLOCKS"] as $url) {
        $CAqwVideo = new CAqwVideo();
        $getService = $CAqwVideo->getServiceByUrl($url);
        if ($getService !== false) {
            $videoData[] = $getService->getDataByParams($arParams);
        }
    }
    if (count($videoData) > 0) {
        $arResult["ITEMS"] = $videoData;
    }
};