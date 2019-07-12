<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])>0)
	ShowError($arResult["ERROR_MESSAGE"]);?>
<?if (count($arResult["STORES"]) > 0):?>
<h3><?=$arResult["TITLE"]?></h3>
<div class="catalog-detail-property options"><ul class="lsnn">
<?foreach($arResult["STORES"] as $pid=>$arProperty):?>
	<?$greyFont=($arProperty["NUM_AMOUNT"]==0)?true:false;?>
	<li>
		<a href="<?=$arProperty["URL"]?>"><span><?=$arProperty["TITLE"]?></span></a>
		<? if(isset($arProperty["PHONE"])):?>
		<span>&nbsp;&nbsp;<?=GetMessage('S_PHONE')?></span>
		<span><?=$arProperty["PHONE"]?></span>
		<?endif;?>
		<? if(isset($arProperty["SCHEDULE"])):?>
		<span>&nbsp;&nbsp;<?=GetMessage('S_SCHEDULE')?></span>
		<span><?=$arProperty["SCHEDULE"]?></span>
		<?endif;?>
		<b><span><?if ($greyFont) echo"<grey>";?><?=$arProperty["AMOUNT"]?><?if ($greyFont) echo"</grey>";?></span></b>
	</li>
<?endforeach;?>
</ul></div>
<?endif?>
