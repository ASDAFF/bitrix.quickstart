<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<? if ($arResult["ITEMS"]): ?>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?

			$bgcolor = '';
			if (!empty($arItem['PROPERTIES']["BGCOLOR"]))
			{
				$bgcolor = $arItem['PROPERTIES']["BGCOLOR"]['VALUE'];
			}
			elseif (!empty($arParams["DISPLAY_BLOCK_HTML_BGR"]))
			{
				$bgcolor = $arParams["DISPLAY_BLOCK_HTML_BGR"];
			}

		?>

		<div id="<?= $arParams["DISPLAY_BLOCK_HTML_ID"]; ?>"<?= (!empty($bgcolor) ? ' style="background: ' . $bgcolor . ';" ' : '');?>>
			<a href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="" /></a>
		</div>

	<? endforeach; ?>
<? endif; ?>