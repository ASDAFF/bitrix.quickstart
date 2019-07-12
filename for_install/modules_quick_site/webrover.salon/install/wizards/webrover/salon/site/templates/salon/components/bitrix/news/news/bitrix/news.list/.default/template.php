<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-list">
	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?><br />
	<?endif;?>
<?/*	<h1>Новости</h1>*/?>
	<? foreach ($arResult['ITEMS'] as $arItem): ?>
		<div class="news-item">
			<div class="date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
			<div class="title"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="Подробнее"><?=$arItem['NAME']?></a></div>
			<div class="anons">
				<?=$arItem['PREVIEW_TEXT']?>
			</div>
		</div>
	<? endforeach ?>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
</div>
