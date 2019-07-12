<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="content">
    <div class="partners">
        <?foreach ($arResult['ITEMS'] as $arItem){?>
            <div class="item rL hid">
                <div class="image_block rL hid"><a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME'];?>"></a></div>
                <div class="text_block rL hid">
                    <a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?=$arItem['NAME'];?></a>
                    <p><?=$arItem['PREVIEW_TEXT'];?></p>
                    <a class="more" target="_blank" href="<?=$arItem['PROPERTIES']['PARTNER_LINK']['VALUE']?>"><?=$arItem['PROPERTIES']['PARTNER_LINK']['NAME']?></a>
                </div>
				<div class="clearfix"></div>
            </div>
        <?}?>

        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <div class="clear"></div><br /><?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
</div>