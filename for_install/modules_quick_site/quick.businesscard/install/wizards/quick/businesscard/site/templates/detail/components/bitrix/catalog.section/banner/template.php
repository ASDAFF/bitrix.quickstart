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
<?if(!empty($arResult["ITEMS"])):?>
<div class="block">
	<div class="owl-carousel <?=$arParams["AUTOPLAY"]?> photo-block">
<?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);	
?>
	<div class="image-box" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="overlay-container">
			<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>">
			<div class="overlay">
				<div class="overlay-links <?if(empty($arItem["PROPERTIES"]["HREF"]["VALUE"])):?>single<?endif?>">
					<?if(!empty($arItem["PROPERTIES"]["HREF"]["VALUE"])):?><a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><i class="fa fa-link"></i></a><?endif?>
					<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" class="popup-img-single" title="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>"><i class="fa fa-search-plus"></i></a>
				</div>
			</div>
		</div>
	</div>
<?endforeach?>
	</div>
</div>
<?endif?>