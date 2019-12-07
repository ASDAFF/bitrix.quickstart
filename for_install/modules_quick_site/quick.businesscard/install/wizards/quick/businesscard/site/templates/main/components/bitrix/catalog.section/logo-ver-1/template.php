<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<div class="owl-carousel clients">
<?foreach($arResult["ITEMS"] as $cell=>$arItem):	
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="client" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if(!empty($arItem["PROPERTIES"]["HREF"]["VALUE"])):?>
			<a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>" target="_blank"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"></a>
		<?else:?>
			<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
		<?endif?>
	</div>
<?endforeach?>
</div>
<?endif?>