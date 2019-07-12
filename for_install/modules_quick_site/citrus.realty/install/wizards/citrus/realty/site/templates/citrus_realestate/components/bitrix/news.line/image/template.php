<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-photo-line">
	<?$i=0;?>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$bIncludeAreas = $APPLICATION->GetShowIncludeAreas();
		if ($bIncludeAreas)
		{
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		}
		?>
		<div class="content-lic <?if ($i%2==0) echo "first";?>">
		<?$i++;?>
		<span class="b-photo-line-item"<?=($bIncludeAreas ? 'id="' . $this->GetEditAreaId($arItem['ID']) . '"' : '')?>>
			<?/*<span class="b-photo-line-date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>*/?>
			<?
			$arPicture = false;
			$bDetailPicture = false;
			if (is_array($arItem["DETAIL_PICTURE"]) && count($arItem["DETAIL_PICTURE"]) > 0)
			{
				$arPicture = $arItem["DETAIL_PICTURE"];
				$bDetailPicture = true;
			}
			elseif (is_array($arItem["PREVIEW_PICTURE"]) && count($arItem["PREVIEW_PICTURE"]) > 0)
			{
				$arPicture = $arItem["PREVIEW_PICTURE"];
			}
			
			$arClass = Array();
			$href = $arItem["DETAIL_PAGE_URL"];
			$arClass[] = "popup";
			$href = $arPicture["SRC"];
			$class = count($arClass) > 0 ? ' class="' . implode(' ', $arClass) . '"' : '';
			
			?><a<?=$class?> href="<?=$href?>" title="<?=$arItem['NAME']?>" id="<?=$arItem['ID']?>" rel="image"><?
				
				if ($arPicture)
				{
					$arSmallPicture = CFile::ResizeImageGet(
						$arPicture,
						array(
							'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_WIDTH']), 
							'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
						),
						BX_RESIZE_IMAGE_EXACT,
						true
					);
					
					?><img class="b-photo-picture b-photo-line-picture" border="0" src="<?=$arSmallPicture["src"]?>" width="<?=$arSmallPicture["width"]?>" height="<?=$arSmallPicture["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /><?
				}
				if (is_array($arPicture))
					$imageWidth = intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_WIDTH']);
			
			?><span class="b-photo-line-name"<?=(is_array($arPicture) ? ' style="max-width: ' . $imageWidth . 'px;"' : '')?>><?=$arItem["NAME"]?></span></a>
			<?
				if (is_set($arItem, 'PREVIEW_TEXT') && strlen($arItem['PREVIEW_TEXT']) > 0)
					echo '<span class="b-photo-line-text">' . $arItem['PREVIEW_TEXT'] . '</span>';
			?>
		</span>
		</div>
	<?endforeach;?>
</div>