<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])>0)
	ShowError($arResult["ERROR_MESSAGE"]);?>
<?if(count($arResult["STORES"]) > 0):?>
	<div class="tovar_descr">
	<div class="tovar_descr_title"><h2><?=$arResult["TITLE"]?></h2></div>
	<ul class="warehouse_list">
	<?foreach($arResult["STORES"] as $pid=>$arProperty):?>
		<li>
			<ul>
				<li class="tovar_char_row tovar_char_row_name">
				<span><a href="<?=$arProperty["URL"]?>"><?=$arProperty["TITLE"]?></a></span>
				<? if(isset($arProperty["PHONE"])):?>
				<span>&nbsp;&nbsp;<?=GetMessage('S_PHONE')?></span>
				<span><?=$arProperty["PHONE"]?></span>
				<?endif;?>
				<? if(isset($arProperty["SCHEDULE"])):?>
				<span>&nbsp;&nbsp;<?=GetMessage('S_SCHEDULE')?></span>
				<span><?=$arProperty["SCHEDULE"]?></span>
				<?endif;?>
				</li>
				<li class="tovar_char_row tovar_char_row_value">
				<?=$arProperty["AMOUNT"]?>
				</li>
			</ul>
		</li>
	<?endforeach;?>
	</ul>
</div>
<?endif;?>