<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="basket">
<?if (strpos ($arParams["PATH_TO_BASKET"],'/')===0) $arParams["PATH_TO_BASKET"] = substr ($arParams["PATH_TO_BASKET"],1)?>
<p class="link"><a href="<?=SITE_DIR?><?=$arParams["PATH_TO_BASKET"]?>"><?=GetMessage('BASKET_TITLE')?></a></p>
<?
if (IntVal($arResult["COUNT"])>0)
{?>
	<p><?=GetMessage('TSBS_SUM')?><strong><?=$arResult["SUM"]?></strong></p>
	
<?}
else
{?>
	<p><?echo GetMessage('BASKET_EMPTY')?></p>
<?}?>
</div>