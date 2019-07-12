<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="fluid_container" style="width: 733px; height: 530px;margin-bottom:0px;">
    <div class="camera_gold_skin" id="camera_wrap_0">
        <? foreach($arResult["ITEMS"] as $arItem): ?>
            <? if (!empty($arItem["LINK"])): ?>
                <div data-thumb="<?=$arItem["DETAIL_PICTURE"]["SRC"]; ?>" data-link="<?=$arItem["LINK"]?>" data-src="<?=$arItem["DETAIL_PICTURE"]["SRC"]; ?>">
                    <? if(!empty($arItem["DETAIL_TEXT"])): ?>
                        <?= $arItem["DETAIL_TEXT"]; ?>
                    <? endif; ?>
                </div>
            <? else:?>
                <div data-thumb="<?=$arItem["DETAIL_PICTURE"]["SRC"]; ?>" data-link="" data-src="<?=$arItem["DETAIL_PICTURE"]["SRC"]; ?>">
                    <? if(!empty($arItem["DETAIL_TEXT"])): ?>
                        <?= $arItem["DETAIL_TEXT"]; ?>
                    <? endif; ?>
                </div>
            <? endif; ?>
        <?endforeach; ?>
    </div>
</div>
<script>
    jQuery(function(){
        jQuery('#camera_wrap_0').camera({
            height: '530px',
            fx: 'stampede',
            navigation: false,
            playPause: false,
            thumbnails: false,
            barPosition: 'bottom',
            loader: false,
            time: 2000,
            transPeriod:500
        });
    });
</script>
