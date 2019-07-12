<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true); ?>
<? if (count($arResult["STORES"]) < 1) {
	return;
} ?>
<div class="storage_items row">
	<? $i = 0; foreach ($arResult["STORES"] as $pid => $arProperty): ?>
		<? if ($i%2 == 0 && $i > 0) { echo '</div><div class="storage_items row">'; } $i++; ?>
		<div class="storage_item col-md-6">
			<a href="<?=$arProperty["URL"];?>"><?=$arProperty["TITLE"];?></a>
			<? if (isset($arProperty["PHONE"])): ?>
				<span class="tel"><?=GetMessage("S_PHONE");?> <?=$arProperty["PHONE"];?></span>
			<?endif;?>
			<? if (isset($arProperty["SCHEDULE"])): ?>
				<span class="schedule"><?=GetMessage("S_SCHEDULE");?> <?=$arProperty["SCHEDULE"];?></span>
			<? endif; ?>
			<span class="balance"><?=GetMessage("S_AMOUNT");?> <?=$arProperty["AMOUNT"];?></span>
		</div>
	<? endforeach; ?>
</div>