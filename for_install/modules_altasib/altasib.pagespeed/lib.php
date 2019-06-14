<?php
$moduleId = "altasib.pagespeed";
$pathJS = '/bitrix/js/' . $moduleId;
//$pathCSS = '/bitrix/css/' . $moduleId;
CJSCore::RegisterExt("altasib_pagespeed_lazy_load", array(
    'js' => $pathJS.'/lazyLoad-min.js',
    //'css' => $pathCSS.'/lazyLoad.css'
));