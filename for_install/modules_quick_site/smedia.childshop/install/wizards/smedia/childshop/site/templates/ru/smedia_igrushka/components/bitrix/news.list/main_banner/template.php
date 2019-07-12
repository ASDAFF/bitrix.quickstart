<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="main_banners">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="main_banner" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arItem["DISPLAY_PROPERTIES"]["LINK"]['VALUE']):?>
			<a href="<?echo $arItem["DISPLAY_PROPERTIES"]["LINK"]['VALUE']?>" title="<?echo $arItem["NAME"]?>">
		<?endif?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>			
				<img class="banner" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>"/>
		<?endif?>
		<?if($arItem["DISPLAY_PROPERTIES"]["LINK"]['VALUE']):?>
			</a>
		<?endif?>		
	</div>
<?endforeach;?>
</div>
