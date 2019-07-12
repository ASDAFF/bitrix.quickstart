<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="brand-list">
    <ul>
        <? foreach($arResult["ITEMS"] as $arItem): ?>
            <li class="item brands-catalog" itemscope itemtype="http://schema.org/Product">
                <div class="image">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"];?>">
                        <? if(!empty($arItem["DETAIL_PICTURE"])): ?>
                            <img src="<?=$arItem["DETAIL_PICTURE"]['src']; ?>" width="<?=$arItem["DETAIL_PICTURE"]['width']; ?>" height="<?=$arItem["DETAIL_PICTURE"]['height']; ?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"];?>" />
                            <p style="font-size:20px;"><?=$arItem["NAME"];?></p>
                        <? else: ?>
                            <img src="<?=SITE_TEMPLATE_PATH?>/i/no-image.jpg" />
                            <p style="font-size:20px;"><?=$arItem["NAME"];?></p>
                        <? endif; ?>
                    </a>
                </div>
            </li>
        <? endforeach; ?>
    </ul>
</div>