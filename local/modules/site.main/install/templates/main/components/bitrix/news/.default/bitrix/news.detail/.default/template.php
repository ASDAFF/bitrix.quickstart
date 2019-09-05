<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<article class="news-detail news-detail-default">
	<?if ($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]) {
		?><h2><?=$arResult["NAME"]?></h2><?
	}?>
	<div class="row">
		<div class="col-md-4">
			<?if (is_array($arResult["DETAIL_PICTURE"])) {
				?><img class="image" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>"/><?
			}?>
		</div>
		<div class="col-md-8">
			<?
			if ($arResult["DISPLAY_ACTIVE_FROM"]) {
				?><span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span><?
			}
			
			if ($arResult["FIELDS"]["PREVIEW_TEXT"]) {
				?><div class="preview"><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></div><?
			}
			
			if ($arResult["NAV_RESULT"]) {
				if ($arParams["DISPLAY_TOP_PAGER"]) {
					print $arResult["NAV_STRING"];
				}
				?><div class="detail"><?=$arResult["NAV_TEXT"]?></div><?
				if ($arParams["DISPLAY_BOTTOM_PAGER"]) {
					print $arResult["NAV_STRING"];
				}
			} elseif (strlen($arResult["DETAIL_TEXT"])) {
				?><div class="detail"><?=$arResult["DETAIL_TEXT"]?></div><?
			} else {
				?><div class="detail"><?=$arResult["PREVIEW_TEXT"]?></div><?
			}
			
			if ($arResult["FIELDS"] || $arResult["DISPLAY_PROPERTIES"]) {
				?>
				<dl class="propperties">
					<?
					foreach ($arResult["FIELDS"] as $code=>$value) {
						?>
						<dt><?=GetMessage("IBLOCK_FIELD_" . $code)?></dt>
						<dd><?=$value?></dd>
						<?
					}
					
					foreach ($arResult["DISPLAY_PROPERTIES"] as $arProperty) {
						?>
						<dt><?=$arProperty["NAME"]?></dt>
						<dd><?=implode(', ', (array) $arProperty["DISPLAY_VALUE"])?></dd>
						<?
					}
					?>
				</dl>
				<?
			}
			?>
		</div>
	</div>
</article>