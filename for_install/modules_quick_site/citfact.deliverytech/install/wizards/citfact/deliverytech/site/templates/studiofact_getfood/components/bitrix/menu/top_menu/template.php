<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? if (count($arResult) < 1) { return; } ?>
<ul><?
	foreach ($arResult as $arItem) {
		if ($arItem["DEPTH_LEVEL"] == "1" && $arItem["PERMISSION"] > "D") {
			?><li class="inline"><a<? if ($arItem["SELECTED"]) { echo ' class="selected"'; } ?> href="<?=$arItem["LINK"];?>" title="<?=$arItem["TEXT"];?>"><?=$arItem["TEXT"];?></a></li><?
		}
	}
?></ul>