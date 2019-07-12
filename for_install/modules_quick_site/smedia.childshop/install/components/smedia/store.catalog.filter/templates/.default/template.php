<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//echo "<pre>".print_r($arResult,true)."</pre>"?>
<div class="filter">
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">

<?
foreach($arResult["ITEMS"] as $arItem):
	if(array_key_exists("HIDDEN", $arItem)):
		echo $arItem["INPUT"];
	endif;
endforeach;?>
<div class="fieldsConteiner">
	<?$i=1;
	foreach($arResult["ITEMS"] as $arItem):?>
		<?if(!array_key_exists("HIDDEN", $arItem)):?>
		    <?if($arItem["TYPE"]=="textbox"):?>
			    <div class="<?=$arItem["TYPE"]?> input-text">
					<label class="" for="filter_<?=$arItem["INPUT_NAME"]?>"><?=$arItem["NAME"]?></label>
					<?=$arItem["INPUT"]?>
				</div>
			<?else:?>
				<div class="<?=$arItem["TYPE"]?>" id="id_<?=$arItem["INPUT_NAME"]?>">						
					<?if($arItem["TYPE"]=="dropdown"):?>
						<div class="stylized_select">
							<?=$arItem["INPUT"]?>
							<div class="input_wrapper">
					                        <input type="text" name="noname" value="<?=$arItem["USER_VALUE"]?>" />
					                </div>
					       </div>
					 <?else:?>
					 	<?=$arItem["INPUT"]?>
					<?endif?>
				</div>
			<?endif?>
		<?if($i==3):?>
			<div style="clear:both;"></div>
		<?endif;
		$i++;
		endif;?>
	<?endforeach;?>
	<div style="clear:both;"></div>
</div>
	<div class="button"><input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" /></div>
	<input type="hidden" name="set_filter" value="Y" />
	<div class="button"><input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" /></div>
<div style="clear:both;"></div>
</form>
</div>