<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<div class="shop_search">
		<h2 class="margin-0-20-15-20"><?=GetMessage("IBLOCK_FILTER_TITLE")?>:</h2>
		<form id="shop_search_form" class="shop_search_form" name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
			<?foreach($arResult["ITEMS"] as $arItem):
				if(array_key_exists("HIDDEN", $arItem)):
					echo $arItem["INPUT"];
				endif;
			endforeach;?>
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<?if(!array_key_exists("HIDDEN", $arItem)):?>
			<p><?=$arItem["NAME"]?></p>
			<?=$arItem["INPUT"]?>
				<?endif?>
			<?endforeach;?>
			<div class="shop_search_form_buttons"><input id="filter_submit" type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" /><input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" /></div>
		</form>
	</div>
