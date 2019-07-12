<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if (count($arResult["ITEMS"]) < 1)
	return;

$bOpen = false;
?>
<div class="bj-block-group">
<?foreach($arResult["ITEMS"] as $key => $arItem):?>
<?if($key % 3 == 0):$bOpen = true;?><div class="row"><?endif;?>
<div class="col-sm-4 col-xs-12 bj-news-block">
	<?if(!$arItem["PREVIEW_PICTURE"]["SRC"])
	{
	$arItem["PREVIEW_PICTURE"]=$arItem["DETAIL_PICTURE"];
	}?>
	<?if(!empty($arItem["PREVIEW_PICTURE"])):?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" class="img-responsive bj-block__img"></a>
	<hr><?endif;?>
	<h3><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
	<p><?=$arItem["PREVIEW_TEXT"]?></p>
</div>
<hr class="clearfix visible-xs-block">
<?if($key % 3 == 2):$bOpen = false;?></div><?endif;?>
<?endforeach;?>
<?if($bOpen):?></div><?endif;?>
</div>
<hr>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<?=$arResult["NAV_STRING"]?>
<?endif;?>