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

<nav class="bj-top-nav hidden-sm hidden-xs">
<?foreach($arResult as $itemIdex => $arItem):?>
	<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
<?endforeach;?>
</nav>
