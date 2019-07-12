<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-element">
	<p class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></p>
	<?=$arResult['DETAIL_TEXT']?>
	<p class="backlink"><a href="<?=$arResult['LIST_PAGE_URL']?>" title="<?=GetMessage('LIST_TITLE')?>"><?=GetMessage('LIST')?></a></p>
</div>