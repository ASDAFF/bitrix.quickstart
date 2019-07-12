<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["LIST_PAGE_URL"] = str_replace('//', '/', CComponentEngine::MakePathFromTemplate($arResult["LIST_PAGE_URL"]));

?>
<div class="contacts-wrapper">
	<?
	$jsParams = array("id" => "contacts-map", "items" => array());
	foreach ($arResult["ITEMS"] as $key=>$arItem)
	{
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

		?>
		<div class="contacts-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<a href="#<?=$arItem["CODE"]?>" class="contacts-item-name dotted" id="<?=$arItem["CODE"]?>""><?=$arItem["NAME"]?></a>
			<?
			if ($arItem["PREVIEW_TEXT"])
				echo '<div class="contacts-item-previewtext">' . $arItem["PREVIEW_TEXT"] . '</div>';
			?>
			<div class="contacts-item-text">
			<?
			if (count($arItem["DISPLAY_PROPERTIES"]) > 0)
			{
				$schedule = $phones = '';
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
						$ar = $ar2 = array();
						foreach ($arProperty["DISPLAY_VALUE"] as $idx => $value)
						{
							$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')' : '');
							$ar2[] = (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] . ': ' : '') . $value;
						}

						$val = implode(', ', $ar);
						if ($arProperty["CODE"] == 'schedule')
							$schedule = implode('<br>', $ar2);
						elseif ($arProperty["CODE"] == 'phones')
							$phones = $val;
					}


					?><div class="contacts-item-prop"><?= $val ?></div><?
				}

				// nook todo office name click to open balloon
				if (is_array($arItem["PROPERTIES"]["address"]) && $arItem["PROPERTIES"]["address"]['VALUE'])
				{
					$jsParams["items"][$key]["address"] = $arItem["PROPERTIES"]["address"]["VALUE"];
					$jsParams["items"][$key]["code"] = $arItem["CODE"];
					$jsParams["items"][$key]["header"] = $arItem["NAME"];
					$jsParams["items"][$key]["body"] = $arItem["PROPERTIES"]["address"]["VALUE"] . '<br>' . $phones;
					$jsParams["items"][$key]["footer"] = $schedule;
				}
			}
			?>
			</div>
		</div>
		<?
	}
	?>
	<div id="contacts-map" style="width: 100%; height: 400px;"></div>
	<script>
		$().citrusRealtyOfficeMap(<?=CUtil::PhpToJSObject($jsParams)?>);
	</script>
</div>
