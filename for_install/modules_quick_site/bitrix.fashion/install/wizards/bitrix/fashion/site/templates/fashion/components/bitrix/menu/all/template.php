<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div id="categories" class="categories-full">
    <div class="wrapper">
        <div class="container">
            <div class="cut">
            <ul class="menu">
<?$previousLevel = 0;
$childrenItems = 0;

$MAX_CHILD_ITEMS = 7;
$rootLnk = '';

foreach($arResult as $arItem):?>
	<? if(!empty($arItem["PARAMS"]["DEPTH_LEVEL"])): ?>
		<? $arItem["DEPTH_LEVEL"] = $arItem["PARAMS"]["DEPTH_LEVEL"]; ?>
	<? endif; ?>
	<? if(!empty($arItem["PARAMS"]["IS_PARENT"])): ?>
		<? $arItem["IS_PARENT"] = $arItem["PARAMS"]["IS_PARENT"]; ?>
	<? endif; ?>
    <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
        <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
    <?endif?>
    <?if ($arItem["IS_PARENT"]):?>
        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
            <li class="parent"><a<?if ($arItem["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                <ul class="sub-menu">
            <?$childrenItems = 0;$rootLnk = $arItem["LINK"];$end = 0;?>
        <?endif?>
    <?else:?>
        <?if ($arItem["PERMISSION"] > "D"):?>
            <?$childrenItems++?>
            <?if ($childrenItems > $MAX_CHILD_ITEMS && !$end) {?>
                <li class="more"><a href="<?=$rootLnk?>"><?=GetMessage("MORE")?>&hellip;</a></li>
            <?}?>
            <?if ($childrenItems > $MAX_CHILD_ITEMS) {
                $end = 1;
                continue;
            }?>

            <li><a<?if ($arItem["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>

        <?else:?>
            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                <li><a href="" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
            <?else:?>
                <li><a href="" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
            <?endif?>
        <?endif?>
    <?endif?>
    <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
<?endforeach?>
<?if ($previousLevel > 1)://close last item tags?>
    <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>
            </ul>
            </div>
        </div><!-- .container -->
        <div class="nav prev"><a href="#" title="<?=GetMessage("BTN_PREV")?>"><?=GetMessage("BTN_PREV")?></a></div>
        <div class="nav next"><a href="#" title="<?=GetMessage("BTN_NEXT")?>"><?=GetMessage("BTN_NEXT")?></a></div>
    </div><!-- .wrapper -->
</div><!-- .container-full -->
<?endif?>