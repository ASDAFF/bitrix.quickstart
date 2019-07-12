<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="banners_column">
	<div class="small_banners_block">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$img = (($arItem["PREVIEW_PICTURE"] || $arItem["DETAIL_PICTURE"]) ? CFile::ResizeImageGet(($arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : $arItem["DETAIL_PICTURE"]), array("width" => 180, "height" => 260), BX_RESIZE_IMAGE_EXACT , true) : false);
			?>
			<?if($img):?>
				<div class="advt_banner" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<?if(strlen($arItem["PROPERTIES"]["URL_STRING"]["VALUE"])):?>
						<a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" target="<?=$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]?>">
					<?endif;?>
						<img border="0" width="<?=$img["width"]?>" height="<?=$img["height"]?>" src="<?=$img["src"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
					<?if(strlen($arItem["PROPERTIES"]["URL_STRING"]["VALUE"])):?>
						</a>
					<?endif;?>
				</div>
			<?endif;?>
		<?endforeach;?>
	</div>
</div>