<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//echo '<pre>'.print_r($arResult,true).'</pre>';
?>
<div class="ff">
<?
if (IntVal($arResult["COUNT"])>0)
{
?>
	<!--a href="<?=$arParams["PATH_TO_BASKET"]?>">
	   <?echo str_replace('#NUM#', intval($arResult["COUNT"]), GetMessage('YOUR_CART'))?><br/>
	   <?echo str_replace('#NUM#', $arResult["SUM"], GetMessage('TSBS_SUM'))?>
	</a-->
	
	<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?=GetMessage('TSBS')?>: <?=GetMessage('YOUR_TOV')?> <strong><?=$arResult["COUNT"]?></strong> <?=str_replace('#NUM#', $arResult["SUM"], GetMessage('TSBS_SUM'))?></a>
	
<?
}
else
{
?>
	<p class="order"><a href="<?=$arParams["PATH_TO_BASKET"]?>"><?echo GetMessage('YOUR_CART_EMPTY')?></a></p>
<?
}
?>
</div>
								
											


