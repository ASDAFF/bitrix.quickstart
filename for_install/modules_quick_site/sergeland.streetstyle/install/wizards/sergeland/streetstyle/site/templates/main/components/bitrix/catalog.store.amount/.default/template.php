<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])>0)
	ShowError($arResult["ERROR_MESSAGE"]);
?>
<?if(isset($arResult["IS_SKU"]) && $arResult["IS_SKU"] == 1):?>
<div class="bx_storege" id="catalog_store_amount_div">
	<?if(count($arResult["SKU"]) > 0):?>
		<ul>
		<?foreach($arResult["SKU"] as $pid => $arProperty):?>
			<li>
				<h4><?=$arProperty["TITLE"]?></h4>
				<?if(isset($arProperty["PHONE"])):?>
					<span class="tel"><?=GetMessage('S_PHONE')?>&nbsp;<?=$arProperty["PHONE"]?></span><br />
				<?endif;?>
				<?if(isset($arProperty["SCHEDULE"])):?>
					<span class="schedule"><?=GetMessage('S_SCHEDULE')?>&nbsp;<?=$arProperty["SCHEDULE"]?></span><br />
				<?endif;?>
				<span class="balance"><b><?=$arProperty["AMOUNT"]?></b></span>
			</li>
		<?endforeach;?>
		</ul>
	<?endif;?>
</div>
<?else:?>
<div class="bx_storege" id="catalog_store_amount_div">
	<?if(count($arResult["STORES"]) > 0):?>
		<ul>
		<?foreach($arResult["STORES"] as $pid => $arProperty):?>
			<li>
				<h4><?=$arProperty["TITLE"]?></h4>
				<?if(isset($arProperty["PHONE"])):?>
					<span class="tel"><?=GetMessage('S_PHONE')?>&nbsp;<?=$arProperty["PHONE"]?></span><br />
				<?endif;?>
				<?if(isset($arProperty["SCHEDULE"])):?>
					<span class="schedule"><?=GetMessage('S_SCHEDULE')?>&nbsp;<?=$arProperty["SCHEDULE"]?></span><br />
				<?endif;?>
				<span class="balance"><b><?=$arProperty["AMOUNT"]?></b></span>
			</li>
		<?endforeach;?>
		</ul>
	<?endif;?>
</div>
<?endif;?>