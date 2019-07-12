<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="vacancy-cont">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$res_preview = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 183, "height" => 183), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
        <div class="item">
            <div class="name"><?=$arItem['NAME'];?></div>
            <div class="earn">
                <?foreach($arItem['DISPLAY_PROPERTIES'] as $arProperty){?>
                    <?=$arProperty['NAME']?>: <?=$arProperty['VALUE']?> <br />
                <?}?>
            </div>
            <div class="desc"><?=$arItem['PREVIEW_TEXT'];?></div>
            <?/*<a class="btn-answer" href="#"><?=GetMessage('INNET_SEND_CV')?></a>*/?>
            <div class="clear"></div>
        </div>
    <?}?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div class="clear"></div><br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

<div class="clear"></div>