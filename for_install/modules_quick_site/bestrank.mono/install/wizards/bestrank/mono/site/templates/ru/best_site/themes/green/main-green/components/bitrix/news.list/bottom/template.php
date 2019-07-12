<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (count($arResult["ITEMS"]) < 1)
	return;
?>
<h4><?=GetMessage("NEWS_TITLE")?></h4>
<ul class="lsnn"> 
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<li>
			<p class="title-date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></p>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="title-link"><?=(strlen($arItem["NAME"])> 0 ? $arItem["NAME"] : $arItem["PREVIEW_TEXT"])?></a>
		</li>
	<?endforeach;?>
</ul>
<br/>
<a href="<?=SITE_DIR?>news/" class="bt2 allnews"><?=GetMessage("SDNW_ALLNEWS")?></a>