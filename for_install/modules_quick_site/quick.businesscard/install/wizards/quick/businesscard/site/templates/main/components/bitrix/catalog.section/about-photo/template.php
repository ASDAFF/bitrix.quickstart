<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
<div class="owl-carousel content-slider-with-controls photo-block">    
<?foreach($arResult["ITEMS"] as $cell=>$arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="overlay-container margin-top-clear" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
		<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_TEXT"]?>">
		<a href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="about-photo overlay" title="<?=$arItem["PREVIEW_TEXT"]?>"><i class="fa fa-search-plus"></i></a>
	</div>
<?endforeach?>
</div>
<?endif?>