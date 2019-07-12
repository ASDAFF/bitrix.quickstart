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
$this->setFrameMode(true);?>
<div class="col-sm-4 col-xs-12 bj-news-block">
	<h2><?=GetMessage("READ_ALSO")?></h2>
	<hr>
	<a href="<?=$arResult["DETAIL_PAGE_URL"]?>"><img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" class="img-responsive bj-block__img"></a>
	<hr>
	<div class="bj-date"><?echo $arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<h2><a href="<?=$arResult["DETAIL_PAGE_URL"]?>"><?=$arResult["NAME"]?></a></h2>
	<p><?=$arResult["PREVIEW_TEXT"]?></p>
</div>