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
?>
<?
if(strlen($arResult["DETAIL_TEXT"])){$PUBLIC_TEXT=$arResult["DETAIL_TEXT"];}else{$PUBLIC_TEXT=$arResult["PREVIEW_TEXT"];}
if($arResult["DETAIL_PICTURE"]["SRC"])
	$PUBLIC_SRC=$arResult["DETAIL_PICTURE"]["SRC"];
else
	$PUBLIC_SRC=$arResult["PREVIEW_PICTURE"]["SRC"];
?>
<?//print_R($arResult)?>
<div class="row">
	<div class="col-sm-6">
		<div class="bj-theme-photo">
			<?if($PUBLIC_SRC):?><img src="<?=$PUBLIC_SRC;?>" class="img-responsive" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"><?endif?>
		</div>
	</div>
	<hr class="visible-xs-block">
	<div class="col-sm-6">
		<div class="bj-text-more-wrapper">
			<div class="bj-text-more-container">
			<?echo $PUBLIC_TEXT;?>
			</div>
		</div>
		<div class="bj-text-more"><a href><?=GetMessage("MORE")?></a></div>
	</div>
</div><hr>
