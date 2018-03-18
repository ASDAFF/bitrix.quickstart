<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class = "col_title">
<h2 class = "color1"><?if(strlen($arParams["BLOCK_TITLE"])>0) echo $arParams["BLOCK_TITLE"]; else echo GetMessage("DEFAULT_BLOCK_TITLE");?></h2>
</div>
<div class = "news_list">
<?if($arParams["DISPLAY_TOP_PAGER"]){?>
	<div class = "pager"><?=$arResult["NAV_STRING"]?></div>
<?}?>
<?foreach($arResult["ITEMS"] as $arItem){?>
<div class = "news_block">
<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]){?>
<div class = "news_date_time color3"><?echo $arItem["DISPLAY_ACTIVE_FROM"];?></div>
<?}?>
<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]){?>
<div class = "news_title">
<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])){?>
<a href = "<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
<?}else{?>
<?echo $arItem["NAME"]?>
<?}?>
</div>
<?}?>

<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])){?>
<div class = "news_image">
<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])){?>
<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>
<?}else{?>
<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
<?}?>
</div>
<?}?>

<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]){?>
<div class = "news_text"><?echo $arItem["PREVIEW_TEXT"];?></div>
<?}?>
<?if(count($arItem["DISPLAY_PROPERTIES"])>0){?>
<div class = "news_props">
<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty){?>
<small><i>
<?=$arProperty["NAME"]?>:&nbsp;
<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
<?else:?>
<?=$arProperty["DISPLAY_VALUE"];?>
<?endif?>
</i></small><br />
<?}?>
</div>
<?}?>

<div class = "news_border"></div>
</div>
<?}?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]){?>
	<div class = "pager"><?=$arResult["NAV_STRING"]?></div>
<?}?>
<div class = "all_news"><a href = ""><?echo GetMessage("ALL_NEWS_LINK");?></a></div>
<div class = "news_border"></div>
</div>
