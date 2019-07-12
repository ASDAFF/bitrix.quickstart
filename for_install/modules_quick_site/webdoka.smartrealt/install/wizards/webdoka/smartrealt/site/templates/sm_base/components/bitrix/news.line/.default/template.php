<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       # 
* # mailto:info@smartrealt.com      #
* ###################################
*/

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$bFirst = true;     
?>
<?php if (count($arResult["ITEMS"]) > 0) { ?>
<div class="news-line">
        <h2><?php echo GetMessage('TITLE');?></h2>
        <?php foreach($arResult["ITEMS"] as $arItem) { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <div class="block <?=$bFirst?'f-block':''?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <span class="date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
            <a class="title" href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
            <div class="text">
                <?php if (is_array($arItem['PREVIEW_PICTURE'])) { ?>
                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="">
                <?php } ?>
                <?echo $arItem["PREVIEW_TEXT"]?>
            </div>
        </div>
        <?php $bFirst = false; } ?>
        <div class="clear"></div>
        <a class="all" href="<?php echo $arResult['LIST_PAGE_URL'];?>"><?php echo GetMessage('ALL_NEWS');?></a>
    </div>
<?php } ?>