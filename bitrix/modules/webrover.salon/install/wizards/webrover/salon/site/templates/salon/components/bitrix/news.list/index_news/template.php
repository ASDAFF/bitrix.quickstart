<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-list">
	<h2><?=$arResult['NAME']?></h2>
	<? foreach ($arResult['ITEMS'] as $arItem): ?>
		<dl class="news-item">
			<dt><a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a></dt>
			<dd><?=$arItem['PREVIEW_TEXT']?></dd>
		</dl>
	<? endforeach ?>
	<div class="cl"></div>
</div>