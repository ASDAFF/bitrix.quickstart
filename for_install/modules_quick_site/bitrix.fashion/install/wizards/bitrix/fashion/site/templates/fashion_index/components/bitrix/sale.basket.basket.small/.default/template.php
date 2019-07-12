<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo GetMessage("TEST_END");
	return;
}?>
<div class="cart">
<?if (intval($arResult['ITEMS']) > 0){?>
    <?if ($arResult["READY"]=="Y"){?>
        <?$q = $t = 0;
        foreach ($arResult["ITEMS"] as $v){
            if ($v["DELAY"]=="N" && $v["CAN_BUY"]=="Y"){
                $t += $v["QUANTITY"] * $v["PRICE"];
                $q += $v["QUANTITY"];
            }
        }?>
    <?if (strlen($arParams["PATH_TO_BASKET"])>0):?><a href="<?=$arParams["PATH_TO_BASKET"]?>"><?endif;?><?=CSiteFashionStore::declOfNum($q, array(GetMessage("Q1"), GetMessage("Q2"), GetMessage("Q3")))?> <?=GetMessage("ON")?> <?=SaleFormatCurrency($t, $arResult["ITEMS"][0]["CURRENCY"])?><?if (strlen($arParams["PATH_TO_BASKET"])>0):?></a><?endif;?>
    <?}?>
<?} else {?>
    <span><?=GetMessage("EMPTY")?></span>
<?}?>
</div>