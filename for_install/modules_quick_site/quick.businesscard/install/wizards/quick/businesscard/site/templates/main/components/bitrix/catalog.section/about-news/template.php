<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<?foreach($arResult["ITEMS"] as $cell=>$arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
<div class="<?=$arParams["ICONS_VIEW"]?> object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="200<?//=$cell*200?>">
	<?if(!empty($arItem["PROPERTIES"]["PREVIEW_VIDEO"]["VALUE"]) && !empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="icon-container default-bg">
		<i class="fa fa-film"></i>
	</div></a>	
	<?elseif(!empty($arItem["PREVIEW_PICTURE"])):?>
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="icon-container no-border image-block">
		<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
	</div></a>
	<?elseif(!empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?>
	<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="icon-container default-bg">
		<i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?>"></i>
	</div></a>
	<?endif?>
	<div class="<?if(!empty($arItem["PREVIEW_PICTURE"]) || !empty($arItem["PROPERTIES"]["ICON"]["VALUE"]) || (!empty($arItem["PROPERTIES"]["PREVIEW_VIDEO"]["VALUE"]) && !empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"]))):?>body<?endif?>">
		<span class="post-info"><?=$arItem["~DISPLAY_ACTIVE_FROM"]["FULL"]?></span>
		<h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h2>
		<?if(!empty($arItem["PREVIEW_TEXT"])):?><p><?=$arItem["PREVIEW_TEXT"]?></p><?endif?>
		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="link"><span><?=GetMessage("QUICK_BUSINESSCARD_NEWS_DETAIL")?></span></a>
	</div>
</div>
<?endforeach?>
<?endif?>