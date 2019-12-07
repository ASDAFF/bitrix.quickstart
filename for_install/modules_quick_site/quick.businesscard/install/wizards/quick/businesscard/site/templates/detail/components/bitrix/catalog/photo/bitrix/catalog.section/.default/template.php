<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult["UF_SEO_DESCRIPTION"])):?>
<div class="block text-muted"><?=$arResult["UF_SEO_DESCRIPTION"]?></div>
<?endif?>
<?if(!empty($arResult["ITEMS"])):
	
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	

switch($arParams["LINE_ELEMENT_COUNT"])
{
	case 1: $span = 12; break;
	case 2: $span = 6; break;	
	case 3: $span = 4; break;
	case 4: $span = 3; break; 
	case 5: case 6: 
	case 7: $span = 2; break;			
    default: $span = 4;
}
?>
<div class="row">
<?foreach($arResult["ITEMS"] as $cell=>$arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
<script>
jQuery(function(){
	$(".popup-img-<?=$arItem["ID"]?>").magnificPopup({
		type:"image",
		gallery: {
			enabled: true,
			tCounter : "%curr% <?=GetMessage("QUICK_BUSINESSCARD_OF")?> %total%"
		}		
	});
});
</script>
	<div class="col-md-<?=$span?> col-sm-6">
		<div class="image-box photo-block mb-25" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="overlay-container">
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>">
				<div class="overlay">
					<div class="overlay-links">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-link"></i></a>
						<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PROPERTIES"]["PREVIEW_PICTURE_DESCRIPTION"]["VALUE"]?>" class="popup-img-<?=$arItem["ID"]?>"><i class="fa fa-search-plus"></i></a>
						<?if(!empty($arItem["PROPERTIES"]["MORE_PHOTO"]["VALUE"])):?>
							<?foreach($arItem["PROPERTIES"]["MORE_PHOTO"]["ITEMS"] as $key=>$arPhoto):?>
								<a href="<?=$arPhoto["DETAIL"]["SRC"]?>" title="<?=$arPhoto["DESCRIPTION"]?>" class="hidden popup-img-<?=$arItem["ID"]?>"></a>
							<?endforeach?>
						<?endif?>
					</div>
					<span><?=$arItem["NAME"]?></span>
				</div>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<div class="row shop-footer mt-20">
	<div class="col-md-9 pull-right">
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif?>
	</div>
</div>
<?endif?>