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
<div class="owl-carousel <?=$arParams["AUTOPLAY"]?>">
<?foreach($arResult["ITEMS"] as $cell=>$arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);	
?>
	<div class="image-box <?if($cell<4 && $arParams["AUTOPLAY"] == "carousel"):?>object-non-visible<?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" <?if($cell<4 && $arParams["AUTOPLAY"] == "carousel"):?>data-animation-effect="fadeInLeft" data-effect-delay="<?=(300-$cell*100)?>"<?endif?>>
		<div class="overlay-container pic">
			<?if(!empty($arItem["PROPERTIES"]["PREVIEW_VIDEO"]["VALUE"]) && !empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
			<div class="embed-responsive embed-responsive-4by3">
				<iframe class="embed-responsive-item" src="<?=$arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" allowfullscreen></iframe>
			</div>			
			<?elseif(is_array($arItem["PREVIEW_PICTURE"])):?>
			<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
			<div class="overlay">
				<div class="overlay-links">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-link"></i></a>
					<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["NAME"]?>" class="popup-img-single"><i class="fa fa-search-plus"></i></a>
				</div>
			</div>
			<?else:?>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-image pic"></i></a>
			<?endif?>
		</div>
		<div class="image-box-body">
			<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
			<div class="preview-text"><?=$arItem["PREVIEW_TEXT"]?></div>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="link"><span><?=GetMessage("QUICK_BUSINESSCARD_WORKS_DETAIL")?></span></a>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>