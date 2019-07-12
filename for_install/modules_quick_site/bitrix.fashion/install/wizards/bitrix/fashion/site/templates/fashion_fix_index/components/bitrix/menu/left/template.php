<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<li class="block subcategories">
    <h4><?=GetMessage("CAT")?></h4>
    <ul>
<?foreach($arResult as $arItem):?>
    <?if($arItem["SELECTED"]):?>
        <li class="active"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>&nbsp;(<?=$arItem["PARAMS"]["CNT"]?>)</li>
    <?else:?>
        <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>&nbsp;(<?=$arItem["PARAMS"]["CNT"]?>)</li>
    <?endif?>
<?endforeach?>
    </ul>
</li><!-- .sub-categories -->
<?endif?>