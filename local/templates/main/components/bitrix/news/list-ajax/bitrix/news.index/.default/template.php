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
<div class="news-index">
<?foreach($arResult["IBLOCKS"] as $arIBlock):?>
	<?if(count($arIBlock["ITEMS"])>0):?>
		<b><?=$arIBlock["NAME"]?></b>
		<ul>
		<?foreach($arIBlock["ITEMS"] as $arItem):?>
			<li><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></li>
		<?endforeach;?>
		</ul>
	<?endif?>
<?endforeach;?>
</div>
