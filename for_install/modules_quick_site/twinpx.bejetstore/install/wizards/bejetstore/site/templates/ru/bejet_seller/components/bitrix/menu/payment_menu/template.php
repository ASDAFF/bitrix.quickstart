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
<menu class="col-sm-2 col-sm-offset-2 bj-page-footer__payment">
<?foreach($arResult as $itemIdex => $arItem):?>
	<li><a href="<?=$arItem["LINK"]?>" target="_blank" class="bj-icon-link">
		<span class="bj-icon bj-icon-link__icon"<?=(!empty($arItem["PARAMS"]["IMG"]) ? ' style="background-image: url(/upload/'.$arItem["PARAMS"]["IMG"]["SUBDIR"].'/'.$arItem["PARAMS"]["IMG"]["FILE_NAME"].')"' : '')?>></span>
		<span class="bj-icon-link__link"><?=$arItem["TEXT"]?></span>
	</a></li>
<?endforeach;?>
</menu>