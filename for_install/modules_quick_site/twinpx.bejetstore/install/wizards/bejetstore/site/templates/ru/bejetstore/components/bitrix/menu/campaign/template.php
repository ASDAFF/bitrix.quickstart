<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult))
	return;

?>
<div class="col-md-2 col-xs-4">
	<a href="<?=$arResult["0"]["LINK"]?>" class="bj-page-header__sales-link bj-icon-link">
		<span class="bj-icon i-flash bj-icon-link__icon"></span>
		<span class="bj-icon-link__link"><?=$arResult["0"]["TEXT"]?></span>
	</a>
</div>