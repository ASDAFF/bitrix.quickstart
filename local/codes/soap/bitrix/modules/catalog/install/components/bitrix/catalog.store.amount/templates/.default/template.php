<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])>0)
	ShowError($arResult["ERROR_MESSAGE"]);?>
<?if(count($arResult["STORES"]) > 0):?>
<div class="catalog-detail-properties">
	<h4><?=$arResult["TITLE"]?></h4>
	<div class="catalog-detail-line"></div>
	<?foreach($arResult["STORES"] as $pid=>$arProperty):?>
	<?$greyFont=($arProperty["NUM_AMOUNT"]==0)?true:false;?>
	<div class="catalog-detail-property">
		<span><a href="<?=$arProperty["URL"]?>"><?=$arProperty["TITLE"]?></a></span>
		<? if(isset($arProperty["PHONE"])):?>
		<span>&nbsp;&nbsp;<?=GetMessage('S_PHONE')?></span>
		<span><?=$arProperty["PHONE"]?></span>
		<?endif;?>
		<? if(isset($arProperty["SCHEDULE"])):?>
		<span>&nbsp;&nbsp;<?=GetMessage('S_SCHEDULE')?></span>
		<span><?=$arProperty["SCHEDULE"]?></span>
		<?endif;?>
		<b><?if ($greyFont) echo"<grey>";?><?=$arProperty["AMOUNT"]?><?if ($greyFont) echo"</grey>";?></b>
	</div>
	<?endforeach;?>
</div>
<?endif;?>