<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h2><?=$arResult['NAME']?></h2>
<div class="promoaction-list">
	<? foreach ($arResult['ITEMS'] as $arItem): ?>
		<a title="<?=$arItem['NAME']?>" class="promoaction-item <?=(++$i == 1 ? 'magenta' : 'green')?>" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>">
			<span class="promoaction-image">
				<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" />
			</span>
			<span class="promoaction-frame pngfix">
				<span class="promoaction-text">
					<strong><?=$arItem['NAME']?></strong>
				</span>
			</span>
			<? if (strlen($arItem['PROPERTIES']['DISCOUNT']['VALUE']) > 0): ?>
				<span class="promoaction-discount pngfix">
					<span><?=$arItem['PROPERTIES']['DISCOUNT']['VALUE']?></span>
				</span>
			<? endif ?>
		</a>
	<? endforeach ?>
</div>