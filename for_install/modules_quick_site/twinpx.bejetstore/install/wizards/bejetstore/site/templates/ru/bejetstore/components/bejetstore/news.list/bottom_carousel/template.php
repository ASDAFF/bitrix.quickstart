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

$bFirst = true;
?>
<div class="bj-hr-heading">
	<div class="bj-hr-heading__content"><span><?=GetMessage("NEWS")?></span></div>
</div>

<hr class="i-size-L">

<div class="panel visible-xs-block">
	<div id="news-carousel" class="carousel slide bj-news-carousel" data-ride="carousel">
		<!-- Wrapper for slides -->
		<div class="carousel-inner">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<div class="item<?=($bFirst ? ' active' : '')?> bj-news-line">
				<span class="bj-news-line__date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="bj-news-line__text"><?=(strlen($arItem["NAME"])> 0 ? $arItem["NAME"] : $arItem["PREVIEW_TEXT"])?></a>
			</div><?$bFirst = false;?>
		<?endforeach;?>
		</div>
		<!-- Controls -->
		<a class="left carousel-control" href="#news-carousel" role="button" data-slide="prev">
		<span><</span>
		</a>
		<a class="right carousel-control" href="#news-carousel" role="button" data-slide="next">
		<span>></span>
		</a>
	</div>
</div>