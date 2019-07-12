<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["LIST_PAGE_URL"] = str_replace('//', '/', CComponentEngine::MakePathFromTemplate($arResult["LIST_PAGE_URL"]));

?>
<h3><a href="<?=$arResult["LIST_PAGE_URL"]?>"><?=GetMessage("CITRUS_REALTY_CONTACTS")?></a></h3>
<a href="<?=$arResult["LIST_PAGE_URL"]?>" class="info-contacts-maps"></a>
<div class="news-items">
	<?
	foreach ($arResult["ITEMS"] as $arItem)
	{
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="news-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<div class="news-item-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
			<div class="news-item-text">
			<?
			if (count($arItem["DISPLAY_PROPERTIES"]) > 0)
			{
				?>
				<dl>
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
									$arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
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

							$val = implode(', ', $ar);
						}


						if ($arProperty["PROPERTY_TYPE"] != 'F')
						{
							?>
							<dt><?= $arProperty["NAME"] ?>:</dt>
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
		</div>
		<?
	}
	?>
	<div class="news-item-more">
		<a href="<?=$arResult["LIST_PAGE_URL"]?>"><?=GetMessage("CITRUS_REALTY_MAP")?></a>
	</div>
</div>
