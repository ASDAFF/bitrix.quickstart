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
<div class="offerContainer">
    <?php if ($arParams['SHOW_TITLE'] == 'Y' && strlen($arParams['TITLE']) > 0) { ?>
        <h2 class="offer"><?php echo $arParams['TITLE']?></h2>
    <?php } ?>
    
    <?php foreach ($arResult['arItems'] as $arItem) { ?>
    <div class="offer">
        <div class="info">
            <?php
                $arPhoto = array();
                if (intval($arItem['PhotoFileId']) > 0)
                    $arPhoto = CFile::ResizeImageGet($arItem['PhotoFileId'], array('width'=>80, 'height'=>60), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                
                if (strlen($arPhoto['src']) > 0) {
            ?>
            <img src="<?php echo $arPhoto['src']?>" alt="<?php echo $arItem['SectionFullName'];?> <?php echo $arItem['Address'];?>">
            <?php } ?>
            <a class="type type1"><?php echo $arItem['SectionFullName'];?></a><br>
            <a href="<?php echo $arItem['DetailUrl']?>"><?php echo $arItem['Address'];?></a>

            <?php if (strlen($arItem['MetroStationName']) > 0) { ?>
                </br><span class="metroStation"><span class="label"><?php echo GetMessage('SMARTREALT_METRO') ?></span> <?php echo $arItem['MetroStationName'] ?></span>
            <?php } else if (strlen($arItem['CityAreaName']) > 0) { ?>
                </br><span class="cityArea"><?php echo GetMessage('SMARTREALT_CITY_AREA') ?> <?php echo $arItem['CityAreaName'] ?></span>
            <?php } ?>
        </div>
        <?php if (intval($arItem['RoomQuantity']) > 0) { ?>
        <p class="param"><label for=""><?php echo GetMessage('SMARTREALT_ROOMS');?></label><?php echo $arItem['RoomQuantity'];?></p>
        <?php } ?>
        <?php if (intval($arItem['GeneralArea']) > 0) { ?>
            <p class="param"><label for=""><?php echo GetMessage('SMARTREALT_AREA');?>:</label><?php
                echo SmartRealt_CatalogElement::GetAreaString($arItem);
            ?>
            <?php if (!empty($arItem['Floor'])) { ?>
            <p class="param"><label for=""><?php echo GetMessage('SMARTREALT_FLOOR');?>:</label><?=$arItem['Floor']?>/<?=$arItem['FloorQuantity']?></p>
            <?php } ?>
        <?php } ?>
        <p class="param"><label for=""><?php echo GetMessage('SMARTREALT_PRICE');?>:</label><?php echo $arItem['Price'];?></p>
    </div>
    <?php } ?>
    <?php if (strlen($arParams['CATALOG_TOP_LIST_URL']) > 0) { ?> 
    <p class="all-offers"><a href="<?php echo $arParams['CATALOG_TOP_LIST_URL'] ?>"><?php echo GetMessage('SMARTREALT_ALL_OFFERS');?></a> &#187;</p>
    <?php } ?>
</div>
