<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="sort-conteiner">
	<label class="label">
		<?=GetMessage("IBLOCK_FILTER_TITLE")?>
	</label>
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" class="liberiya_filter" action="<?//=rawurldecode($arResult["FORM_ACTION"])?><?=$APPLICATION->GetCurPageParam("", array("digital_only", "new_only", "paper_only")); ?>" enctype="multipart/form">
	<?foreach($arResult["ITEMS"] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem["INPUT"];
		endif;
	endforeach;?>

	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?if(!array_key_exists("HIDDEN", $arItem)):?>
				<?=$arItem["INPUT"]?>

		<?endif?>
	<?endforeach;?>
		<ul class="check-list" id="<?=$arParams['BLOCK_HTML_ID']?>">
			<li><label class="checkbox-label" onclick="app.checkbox(this)"><span class="checkbox"><input type="checkbox" value="1" name="new_only" <?if(isset($_REQUEST['new_only'])):?>checked<?endif;?> /></span> <?=GetMessage("FILTER_NEWS_ONLY")?></label></li>
			<li><label class="checkbox-label" onclick="app.checkbox(this)"><span class="checkbox"><input type="checkbox" value="on" name="digital_only" <?if(isset($_REQUEST['digital_only'])):?>checked<?endif;?> /></span> <?=GetMessage("FILTER_DIGITAL_ONLY")?></label></li>
			<li><label class="checkbox-label" onclick="app.checkbox(this)"><span class="checkbox"><input type="checkbox" value="1" name="paper_only" <?if(isset($_REQUEST['paper_only'])):?>checked<?endif;?> /></span> <?=GetMessage("FILTER_PAPER_ONLY")?></label></li>
		</ul>		
			
			<input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;	
</form>
<div class="pager-conteiner">
	<div class="pager">	
		<?$APPLICATION->ShowViewContent("list_pager");?>	
	</div>
</div>			
<div class="cb"></div>
</div>	
