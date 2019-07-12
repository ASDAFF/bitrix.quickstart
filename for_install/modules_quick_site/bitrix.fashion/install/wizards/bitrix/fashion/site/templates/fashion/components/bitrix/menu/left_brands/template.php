<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?if (!empty($arResult)):?>
<li class="block subcategories">
    <h4><?=GetMessage("CAT")?></h4>
    <ul>
<?foreach($arResult as $arItem):?>
    <?if($arItem["SELECTED"]):?>
        <li class="active"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
        <? if(!empty($arItem["SUB_SECTIONS"])): ?>
                <? foreach($arItem["SUB_SECTIONS"] as $section): ?>
                    <li class="lvl2<?if($section["ACTIVE"]): ?> active<? endif; ?>"><a href="<?=$section["SECTION_PAGE_URL"];?>"><?=$section["NAME"];?></a>&nbsp;(<?=$section["COUNT"];?>)</li>
                    <? if(!empty($section["SUB_SECTIONS"]) && $section["ACTIVE"]): ?>
						<? foreach($section["SUB_SECTIONS"] as $sect): ?>
							<li class="lvl3<?if($sect["ACTIVE"]): ?> active<? endif; ?>"><a href="<?=$sect["SECTION_PAGE_URL"];?>"><?=$sect["NAME"];?></a>&nbsp;(<?=$sect["COUNT"];?>)</li>
						<? endforeach; ?>
                    <? endif; ?>
                <? endforeach; ?>
        <? endif; ?>
    <?else:?>
        <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>&nbsp;</li>
    <?endif?>
<?endforeach?>
    </ul>
</li><!-- .sub-categories -->
<?endif?>