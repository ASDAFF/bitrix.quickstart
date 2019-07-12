<?php
    $sAction = 'PHOTOS';
    $arName = pathinfo(__FILE__);
    $iStep = preg_replace('/[^0-9]/', '', $arName['filename']);
    require('data_download.php');
?>
