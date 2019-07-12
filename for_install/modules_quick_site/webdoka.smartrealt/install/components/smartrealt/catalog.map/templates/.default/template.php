<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<?php if (!$arResult['bNotFound']) { ?>
<div class="mapContainer">
    <h2><?php echo GetMessage('SMARTREALT_MAP_TITLE');?></h2>
    <?php if ($arParams['IS_YANDEX']) { ?>
    <style type="text/css">
        .mapContainer div.infoWindow div.params
        {
            width: auto;
        }
    </style>
    <?php } ?>
    <div id="<?php echo $arParams['MAP_ID']?>" class="map" style="width: <?php echo $arParams['MAP_WIDTH']?>px; height: <?php echo $arParams['MAP_HEIGHT']?>px;"><div class="mapLoad"><?php echo GetMessage('SMARTREALT_MAP_LOAD');?></div></div>
    <?php if ($arParams['IS_YANDEX']) { ?>
    <script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&mode=debug" type="text/javascript"></script>
    <?php } else { ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>                                                                                           
    <?php } ?>
    <script type="text/javascript">
    //<!--
        oSmartRealtMap = new SmartRealtMap();     
        oSmartRealtMap.SetMapId('<?php echo $arParams['MAP_ID'];?>');
        oSmartRealtMap.SetMapType('<?php echo $arParams['MAP_TYPE'];?>');
        oSmartRealtMap.SetWidth('<?php echo $arParams['MAP_WIDTH'];?>');
        oSmartRealtMap.SetHeight('<?php echo $arParams['MAP_HEIGHT'];?>');
        oSmartRealtMap.SetMarkers(<?php echo json_encode($arResult['arItems']);?>);
        oSmartRealtMap.LoadMap();   
    //-->
    </script>
</div>
<?php } ?>