<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-news-list">

	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<div class="b-news-list-pager b-news-list-pager-top">
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?endif;?>

	<?

	foreach ($arResult['GROUPED'] as $month=>$arItems)
	{
		if ($month)
		{
			$monthFormatted = CIBlockFormatProperties::DateFormat("f Y", MakeTimeStamp('01.' . $month, "DD.MM.YYYY"));
			?><h3><?= $monthFormatted ?></h3><?
		}
		foreach ($arItems as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="b-news-list-item" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
				<? if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]): ?>
					<span class="b-news-date b-news-list-date"><? echo ToLower($arItem["DISPLAY_ACTIVE_FROM"]) ?></span>
				<? endif ?>

				<? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
					<? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
						<a class="b-news-list-item-name"
						   href="<? echo $arItem["DETAIL_PAGE_URL"] ?>"<?= (strpos($arItem["DETAIL_PAGE_URL"], '#') !== false ? ' onclick="return false;"' : '') ?> title="<?=$arItem["NAME"]?>"><? echo $arItem["NAME"] ?></a>
					<? else: ?>
						<span class="b-news-list-item-name"><? echo $arItem["NAME"] ?></span>
					<?endif; ?>
				<? endif; ?>

				<?if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])):
					$arSmallPicture = CFile::ResizeImageGet(
						$arItem["PREVIEW_PICTURE"]["ID"],
						array(
							'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_WIDTH']),
							'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
						),
						BX_RESIZE_IMAGE_EXACT,
						$bInitSizes = true
					);
					?>
					<? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
					<a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><img
							class="b-news-preview-picture b-news-list-preview-picture" border="0"
							src="<?= $arSmallPicture["src"] ?>" width="<?= $arSmallPicture["width"] ?>"
							height="<?= $arSmallPicture["height"] ?>" alt="<?= $arItem["NAME"] ?>"
							title="<?= $arItem["NAME"] ?>"/></a>
				<? else: ?>
					<img class="b-news-preview-picture b-news-list-preview-picture" border="0"
						 src="<?= $arSmallPicture["src"] ?>" width="<?= $arSmallPicture["width"] ?>"
						 height="<?= $arSmallPicture["height"] ?>" alt="<?= $arItem["NAME"] ?>"
						 title="<?= $arItem["NAME"] ?>"/>
				<?endif; ?>
				<? endif ?>

				<? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
					<div class="b-news-text b-news-list-preview-text">
						<? echo $arItem["PREVIEW_TEXT"]; ?>
					</div>
				<? endif; ?>

				<?
				if (count($arItem["DISPLAY_PROPERTIES"]) > 0)
				{
					?>
					<dl class='b-news-props'>
						<?
						foreach ($arItem["DISPLAY_PROPERTIES"] as $pid => $arProperty)
						{
							if ($arProperty["PROPERTY_TYPE"] == 'F')
							{
								if (!is_array($arProperty['VALUE']))
								{
									$arProperty['VALUE'] = array($arProperty['VALUE']);
									$arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
								}
								$arProperty["DISPLAY_VALUE"] = Array();
								foreach ($arProperty["VALUE"] as $idx => $value)
								{
									$path = CFile::GetPath($value);
									$desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
									if (strlen($path) > 0)
									{
										$ext = pathinfo($path, PATHINFO_EXTENSION);
										$fileinfo = '';
										if ($arFile = CFile::GetByID($value)->Fetch())
											$fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE'] / 1024) . GetMessage('FILE_SIZE_Kb') . ')';
										$arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\" target=\"_blank\">" . $desc . "</a>" . $fileinfo;
									}
								}
								$val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
							} else
							{
								if (!is_array($arProperty["DISPLAY_VALUE"]))
									$arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);
								$ar = '';
								foreach ($arProperty["DISPLAY_VALUE"] as $idx => $value)
									$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')' : '');

								$val = implode(' / ', $ar);
							}


							if ($arProperty["PROPERTY_TYPE"] != 'F')
							{
								?>
								<dt><?= $arProperty["NAME"] ?></dt>
								<dd><?= $val ?></dd>
							<?
							} else
							{
								?>
								<dd class="fileprop"><?= $val ?></dd><?
							}
						}
						?>
					</dl>
				<?
				}
				?>
			</div>
		<?endforeach;
	}
	?>

	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<div class="b-news-list-pager b-news-list-pager-bottom">
			<?=$arResult["NAV_STRING"]?>
		</div>
	<?endif;?>

</div>
