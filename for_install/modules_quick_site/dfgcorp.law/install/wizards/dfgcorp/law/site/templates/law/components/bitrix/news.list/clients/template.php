<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h3><?=(strlen($arParams["BLOCK_TITLE"])>0 ? $arParams["BLOCK_TITLE"] : GetMessage("CT_IBLOCK_TITLE_DEFAULT"))?></h3>
<div class="client-list">
<div class="client-logos">
<?
	$arParams["MAX_WIDTH"] = intval($arParams["MAX_WIDTH"]) > 0 ? intval($arParams["MAX_WIDTH"]) : 150;
	$arParams["MAX_HEIGHT"] = intval($arParams["MAX_HEIGHT"]) > 0 ? intval($arParams["MAX_HEIGHT"]) : 75;
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
<?
	if($arItem["PREVIEW_PICTURE"]["WIDTH"] / 
		$arItem["PREVIEW_PICTURE"]["HEIGHT"] < $arParams["MAX_WIDTH"] / $arParams["MAX_HEIGHT"]){
		$arParams["MAX_HEIGHT"] = $arItem["PREVIEW_PICTURE"]["HEIGHT"] * ($arParams["MAX_WIDTH"] / $arItem["PREVIEW_PICTURE"]["WIDTH"] );
	}else{
		$arParams["MAX_WIDTH"] = $arItem["PREVIEW_PICTURE"]["WIDTH"] * ($arParams["MAX_HEIGHT"] / $arItem["PREVIEW_PICTURE"]["HEIGHT"]);
	}
?>
	<div class="client-logo" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<img height="<?=$arParams["MAX_HEIGHT"]?>" width="<?=$arParams["MAX_WIDTH"]?>" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"  alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
	</div>
<?endforeach;?>
</div>
<div class="clear"></div>
</div>