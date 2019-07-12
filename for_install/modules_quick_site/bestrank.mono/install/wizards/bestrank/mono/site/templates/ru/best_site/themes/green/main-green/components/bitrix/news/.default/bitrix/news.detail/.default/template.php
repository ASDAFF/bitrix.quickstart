<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<script type="text/javascript">
$(document).ready(function() {
	$('.catalog-detail-images').fancybox({
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'speedIn': 600,
		'speedOut': 200,
		'overlayShow': false,
		'cyclic' : true,
		'padding': 20,
		'titlePosition': 'over',
		'onComplete': function() {
		$("#fancybox-title").css({ 'top': '100%', 'bottom': 'auto' });
		}
	});
});
</script>

<div class="post">
    <?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
    <h2><?=$arResult["NAME"]?></h2>
    <?endif;?>
    <?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
    <div class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div><br>
    <?endif;?>
	<?if($arParams["DISPLAY_PICTURE"]!="N"):?>
		<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
			<a href="<?=(is_array($arResult["DETAIL_PICTURE"]) ? $arResult["DETAIL_PICTURE"]["SRC"] : $arResult["PREVIEW_PICTURE"]["SRC"])?>" class="catalog-detail-images">
				<img class="detail_picture" border="0" src="<?=(is_array($arResult["PREVIEW_PICTURE"]) ? $arResult["PREVIEW_PICTURE"]["SRC"] : $arResult["DETAIL_PICTURE"]["SRC"])?>" width="<?=(is_array($arResult["PREVIEW_PICTURE"]) ? $arResult["PREVIEW_PICTURE"]["WIDTH"] : $arResult["DETAIL_PICTURE"]["WIDTH"])?>" height="<?=(is_array($arResult["PREVIEW_PICTURE"]) ? $arResult["PREVIEW_PICTURE"]["HEIGHT"] : $arResult["DETAIL_PICTURE"]["HEIGHT"])?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
			</a>
		<?endif?>
	<?endif?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
 	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
		<?echo $arResult["DETAIL_TEXT"];?>
 	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<?if($arParams["DISPLAY_PICTURE"]!="N" && (is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"]))):?>
		<div style="clear:both;"></div>
	<?endif?>
</div>