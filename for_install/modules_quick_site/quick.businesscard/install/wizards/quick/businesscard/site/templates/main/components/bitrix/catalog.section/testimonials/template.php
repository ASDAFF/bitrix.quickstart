<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<div class="owl-carousel <?=$arParams["AUTOPLAY"]?>">
<?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="testimonial" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
					<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-12">
						<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>" class="popup-img-single"><div class="<?=$arParams["CIRCLE_IMG"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"></div></a>
					</div>
					<div class="col-md-8 col-sm-8 col-xs-12">
				<?else:?>
					<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1">
				<?endif?>
						<p><?=$arItem["PREVIEW_TEXT"]?></p>
						<?if(!empty($arItem["PROPERTIES"]["FIO"]["VALUE"])):?><div class="testimonial-info-1 text-right">- <?=$arItem["PROPERTIES"]["FIO"]["VALUE"]?></div><?endif?>
						<?if(!empty($arItem["PROPERTIES"]["POSITION"]["VALUE"])):?><div class="testimonial-info-2 text-right"><?=$arItem["PROPERTIES"]["POSITION"]["VALUE"]?></div><?endif?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>