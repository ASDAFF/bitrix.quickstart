<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
    var ShsProductview = new Object;
    ShsProductview.timezaderzh = <?=$arParams[TIME_ZADERZH]?>;
    ShsProductview.timeshow = <?=$arParams[TIME_SHOW]?>;
</script>
<div id="shs-productview" style="background:<?=$arParams["COLOR"]?>">
    <img src="<?=$templateFolder."/images/close.png"?>" alt="" />
    <?if($arResult["COUNT"]>0):?>
        <?if($arResult["COUNT"]==1):?>
        <p><?=GetMessage("SHS_TEXT_BEGIN")." ".$arResult["COUNT"]." ".CShsProductview::GetPeople($arResult["COUNT"])." ".GetMessage("SHS_TEXT1_END")?></p>
        <?else:?>
        <p><?=GetMessage("SHS_TEXT_BEGIN")." ".$arResult["COUNT"]." ".CShsProductview::GetPeople($arResult["COUNT"])." ".GetMessage("SHS_TEXT_END")?></p>
        <?endif;?>
    <?endif;?>
</div>