<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
<?foreach($arResult["ITEMS"] as $key=>$arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
<div class="section <?=$arItem["PROPERTIES"]["BG"]["VALUE"]?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
	<div class="row">
		<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
		<div class="col-xs-2 col-xs-offset-1">
			<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>" class="popup-img-single"><div class="<?=$arParams["CIRCLE_IMG"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"></div></a>
		</div>
		<div class="col-xs-8">
		<?else:?>
		<div class="col-xs-10 col-xs-offset-1">
		<?endif?>
			<p><?=$arItem["PREVIEW_TEXT"]?></p>
			<?if(!empty($arItem["PROPERTIES"]["FIO"]["VALUE"])):?><div class="testimonial-info-1 text-right">- <?=$arItem["PROPERTIES"]["FIO"]["VALUE"]?></div><?endif?>
			<?if(!empty($arItem["PROPERTIES"]["POSITION"]["VALUE"])):?><div class="testimonial-info-2 text-right"><?=$arItem["PROPERTIES"]["POSITION"]["VALUE"]?></div><?endif?>
		</div>
	</div>
</div>
<?endforeach?>
<div class="row shop-footer mt-20">
	<div class="col-md-9 pull-right">
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif?>
	</div>
</div>
<?endif?>