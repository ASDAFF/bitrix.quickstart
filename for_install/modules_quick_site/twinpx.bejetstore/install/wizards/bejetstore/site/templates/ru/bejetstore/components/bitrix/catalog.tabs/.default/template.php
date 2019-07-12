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
$bFirst = true;
?>
<div class="bj-catalogue-tabs">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs nav-justified" role="tablist">
	<?
	foreach ($arParams["DATA"] as $tabId => $arTab)
	{
		if (isset($arTab["NAME"]) && isset($arTab["CONTENT"]))
		{?>
		<li<?=($bFirst ? ' class="active"' : '')?>><a href="<?=$arTab["LINK"]?>" role="tab" data-toggle="tab" class="bj-icon-link">
		<span class="bj-icon <?=$arTab["CLASS"]?> bj-icon-link__icon"></span>
		<span class="bj-icon-link__link"><?=$arTab["NAME"]?></span>
	  	</a></li>
		<?
		}
		$bFirst = false;
	}
	?>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
	<?
	foreach ($arParams["DATA"] as $tabId => $arTab)
	{
		if (isset($arTab["NAME"]) && isset($arTab["CONTENT"]))
		{?>
		<?=$arTab["CONTENT"]?>
		<?}
	}
	?>
	</div>
</div>