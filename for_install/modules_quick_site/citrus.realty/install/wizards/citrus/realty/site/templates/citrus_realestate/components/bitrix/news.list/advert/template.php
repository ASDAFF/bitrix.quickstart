<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

?>
<div class="list-items">

	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<div class="b-news-list-pager b-news-list-pager-top">
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?endif;?>

	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

		$preview = \Citrus\Realty\Helper::resizeOfferImage($arItem, intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_WIDTH']), intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_HEIGHT']));
		?>
		<div class="list-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
				<div class="list-item-date"><?echo ToLower($arItem["DISPLAY_ACTIVE_FROM"])?></div>
			<?endif?>

			<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
				<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
					<div class="list-item-name"><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a></div>
				<?else:?>
					<div class="list-item-name"><?echo $arItem["NAME"]?></div>
				<?endif;?>
			<?endif;?>

			<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($preview)):?>
				<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="b-news-preview-picture b-news-list-preview-picture" border="0" src="<?=$preview["src"]?>" width="<?=$preview["width"]?>" height="<?=$preview["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>
				<?else:?>
					<img class="b-news-preview-picture b-news-list-preview-picture" border="0" src="<?=$preview["src"]?>" width="<?=$preview["width"]?>" height="<?=$preview["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
				<?endif;?>
			<?endif?>

			<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
				<div class="list-item-text">
					<?
					$textParser = new CTextParser();
					echo $textParser->html_cut($arItem["PREVIEW_TEXT"], 128);
					?>
				</div>
			<?endif;?>
			<div class="list-item-more"><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("LIST_ITEM_MORE");?></a></div>
			<?
			if (count($arResult["DISPLAY_PROPERTIES"]) > 0)
			{
				?>
				<dl class='b-news-props'>
				<?
				foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty)
				{
					if ($arProperty["PROPERTY_TYPE"] == 'F')
					{
						if (!is_array($arProperty['VALUE'])) {
							$arProperty['VALUE'] = array($arProperty['VALUE']);
							$arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
						}
						$arProperty["DISPLAY_VALUE"] = Array();
						foreach ($arProperty["VALUE"] as $idx=>$value) {
							$path = CFile::GetPath($value);
							$desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
							if (strlen($path) > 0)
							{
								$ext = pathinfo($path, PATHINFO_EXTENSION);
								$fileinfo = '';
								if ($arFile = CFile::GetByID($value)->Fetch())
									$fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE']/1024) . GetMessage('FILE_SIZE_Kb') . ')';
								$arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
							}
						}
						$val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
					}
					else
					{
						if (!is_array($arProperty["DISPLAY_VALUE"]))
							$arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);
						$ar = '';
						foreach ($arProperty["DISPLAY_VALUE"] as $idx=>$value)
							$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')': '');

						$val = implode(' / ', $ar);
					}					
					

					if ($arProperty["PROPERTY_TYPE"] != 'F')
					{
						?>
						<dt><?=$arProperty["NAME"]?></dt>
						<dd><?=$val?></dd>
						<?
					}
					else
					{
						?><dd class="fileprop"><?=$val?></dd><?
					}		}
				?>
				</dl>
				<?
			}
			?>
			<?/*foreach($arItem["FIELDS"] as $code=>$value):?>
				<small>
				<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
				</small><br />
			<?endforeach;?>
			<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<small>
				<?=$arProperty["NAME"]?>:&nbsp;
				<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
					<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
				<?else:?>
					<?=$arProperty["DISPLAY_VALUE"];?>
				<?endif?>
				</small><br />
			<?endforeach;*/?>
		</div>
	<?endforeach;?>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<div class="b-news-list-pager b-news-list-pager-bottom">
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?endif;?>

</div>
