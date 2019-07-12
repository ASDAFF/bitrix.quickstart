<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["~ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<div class="tab-content">
<?foreach($arResult["~ITEMS"] as $key=>$arSection): $count++;?>
<div class="tab-pane fade <?if($count == 1):?>in active<?endif?>" id="tab<?=$arSection["ID"]?>">
	<div class="panel-group panel-transparent" id="accordion-faq-<?=$arSection["ID"]?>">
	<?foreach($arSection["ITEMS"] as $cell=>$arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	?>
		<div class="panel panel-default" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion-faq-<?=$arSection["ID"]?>" href="#collapse<?=$arItem["ID"]?>" class="collapsed">
						<?if(!empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?><i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?> pr-10"></i><?endif?> <?=$arItem["NAME"]?>
					</a>
				</h4>
			</div>
			<div id="collapse<?=$arItem["ID"]?>" class="panel-collapse collapse">
				<div class="panel-body">
					<?=$arItem["PREVIEW_TEXT"]?>
				</div>
			</div>
		</div>	
	<?endforeach?>
	</div>
</div>	
<?endforeach?>
</div>
<?endif?>