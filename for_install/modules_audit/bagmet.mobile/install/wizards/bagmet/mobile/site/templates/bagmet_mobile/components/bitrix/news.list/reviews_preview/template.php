<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (count($arResult["ITEMS"]) < 1)
	return;
?>
<div class="reviews_preview">
	<div class="news_block_title">
		<h3><a href="<?=SITE_DIR?>reviews/"><?=GetMessage("NEWS_TITLE")?></a></h3>
	</div>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<div class="reviews_preview_item">
			<?if (/*$arParams["DISPLAY_PICTURE"] == "Y" &&*/ is_array($arItem["PREVIEW_PICTURE"])):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"></a>
			<?endif?>
			<div>
				<?if ($arParams["DISPLAY_NAME"] == "Y" && strlen($arItem["NAME"])> 0):?>
					<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
				<?endif?>

				<?if (/*$arParams["DISPLAY_PREVIEW_TEXT"] == "Y" &&*/ strlen($arItem["PREVIEW_TEXT"])> 0):?>
					<p><?=$arItem["PREVIEW_TEXT"]?></p>
				<?endif?>
			</div>
			<div class="reviews_preview_item_date_line">
				<p class="title-date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></p>
			</div>
			<div class="splitter"></div>
		</div>
	<?endforeach;?>
<!--<br/>
<a href="<?=SITE_DIR?>news/" class="bt2 allnews"><?=GetMessage("SDNW_ALLNEWS")?></a>-->
	<div class="splitter"></div>
</div>

<script>
	var getMaxHeightNews = function ($elms) {
		  var maxHeight = 0;
		  $elms.each(function () {
			var height = $(this).outerHeight(true);
			if (height > maxHeight) {
			  maxHeight = height;
			}
		  });
		  return maxHeight;
	};
	$('.reviews_preview_item').height( getMaxHeightNews($('.reviews_preview_item')) );
</script>