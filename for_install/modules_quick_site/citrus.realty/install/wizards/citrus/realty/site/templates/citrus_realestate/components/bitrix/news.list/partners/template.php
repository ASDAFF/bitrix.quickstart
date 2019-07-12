<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-partners-list">
	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<div class="b-partners-list-pager b-partners-list-pager-top">
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?endif;?>

	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="b-partners-list-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
				<span class="b-partners-list-item-name"><?=$arItem["NAME"]?></span>
				<br />
			<?endif;?>

			<?$arPicture = false;
			$bDetailPicture = false;
			if ($arParams["DISPLAY_PICTURE"] != "N")
			{
				if (is_array($arItem["DETAIL_PICTURE"]) && count($arItem["DETAIL_PICTURE"]) > 0)
				{
					$arPicture = $arItem["DETAIL_PICTURE"];
					$bDetailPicture = true;
				}
				elseif (is_array($arItem["PREVIEW_PICTURE"]) && count($arItem["PREVIEW_PICTURE"]) > 0)
				{
					$arPicture = $arItem["PREVIEW_PICTURE"];
				}
			}

			if ($arPicture):
				$arSmallPicture = CFile::ResizeImageGet(
					$arPicture,
					array(
						'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 250 : intval($arParams['RESIZE_IMAGE_WIDTH']),
						'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 250 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
					),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					$bInitSizes = true
				);?>
				<?if ($bDetailPicture):?><a class="colorbox" href="<?=$arPicture["SRC"]?>" title="<?=$arItem["NAME"]?>"><?endif?><img class="b-partners-preview-logo b-partners-list-preview-picture" border="0" src="<?=$arSmallPicture["src"]?>" width="<?=$arSmallPicture["width"]?>" height="<?=$arSmallPicture["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /><?if ($bDetailPicture):?></a><?endif?>
			<?endif?>

			<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
				<div class="b-partners-text b-partners-list-preview-text">
					<?=$arItem["PREVIEW_TEXT"]?>
				</div>
			<?endif;?>
		</div>
	<?endforeach;?>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<div class="b-partners-list-pager b-partners-list-pager-bottom">
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?endif;?>

</div>
