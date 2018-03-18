<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<div class="LeftMenu1">


    <div class="lm2">
        <h1 class="f16"><?=GetMessage("MYCOMPANY_REMO_PEREYTI")?></h1>
        <div class="bord"></div>
        <ul class="ar">
            <?
            foreach($arResult as $arItem):
                if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                    continue;
                ?>
                <?if($arItem["SELECTED"]):?>
		<li><a href="<?=$arItem["LINK"]?>" class="selected" id="itemselectedleft"><?=$arItem["TEXT"]?></a>
                <?else:?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                <?endif?>

                <?endforeach?>

        </ul>
    </div>

</div>





<?endif?>