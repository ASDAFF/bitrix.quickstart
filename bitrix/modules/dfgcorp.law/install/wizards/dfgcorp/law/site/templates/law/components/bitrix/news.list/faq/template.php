<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="faq-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="faq-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="question">
			<?echo $arItem["PREVIEW_TEXT"];?>
		</div>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<div class="faq-author-name"><?echo $arItem["NAME"]?></div>
		<?endif;?>
		<div class="answer">
			<?echo $arItem["DETAIL_TEXT"];?>
		</div>
	</div>
	<div class="line"></div>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
<script>
jQuery(window).load(function(){
	jQuery('.faq-item').click(function(){
		if(jQuery(this).find('.answer:first').css('display')=='none'){
			jQuery(this).find('.answer:first').css('display', 'block');
		}else{
			jQuery(this).find('.answer:first').css('display', 'none');
		}
	});
});
</script>