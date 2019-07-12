<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<div class="panel-group panel-dark" id="accordion">
<?foreach($arResult["ITEMS"] as $cell=>$arItem):	
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="panel panel-default" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$cell?>" <?if($cell>0):?>class="collapsed"<?endif?>>
					<i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?>"></i> <?=$arItem["NAME"]?>
				</a>
			</h4>
		</div>
		<div id="collapse<?=$cell?>" class="panel-collapse collapse <?if($cell<1):?>in<?endif?>">
			<div class="panel-body">
				<?=$arItem["PREVIEW_TEXT"]?>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>