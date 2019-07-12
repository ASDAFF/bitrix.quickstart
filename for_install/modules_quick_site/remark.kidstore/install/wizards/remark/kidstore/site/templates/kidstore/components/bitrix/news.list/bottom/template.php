<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (count($arResult["ITEMS"]) < 1)
	return;
?>

<div class="bx_inc_news_footer">
	<h4 style="font-weight: normal;"><?=GetMessage("NEWS_TITLE")?></h4>
	<ul class="bx_inc_news_footer_newslist">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<li>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></a><br/>
				<?=(strlen($arItem["NAME"])> 0 ? $arItem["NAME"] : $arItem["PREVIEW_TEXT"])?>
			</li>
		<?endforeach;?>
	</ul>
	<br/>
	<a href="<?=SITE_DIR?>news/" class="bx_bt_white bx_big bx_shadow"><?=GetMessage("SDNW_ALLNEWS")?></a>
</div>