<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->createFrame()->begin();?>
<?if($arResult["COUNT"] <= 0)
	return;
?>
<div class="row bj-index-banners">
	<?foreach ($arResult["banners"] as $key => $arBanner) {?>
	<?if($key != 0 && $arResult["COUNT"] > 1):?><hr class="visible-xs-block"><?endif;?>
	<?=$arBanner?>
	<?}?>
</div>