<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<?foreach($arResult["ITEMS"] as $key=>$arItem):	
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
<div class="panel panel-default" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span <?if(empty($arItem["PROPERTIES"]["COLOR"]["VALUE"])):?>class="collapsed"<?endif?>><?if(!empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?><i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?> pr-10"></i><?endif?> <?=$arItem["NAME"]?></span>
		</h4>
	</div>
	<div class="panel-collapse">
		<div class="panel-body">
			<?=$arItem["PREVIEW_TEXT"]?>
		</div>
	</div>
</div>
<?endforeach?>
<?endif?>