<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["~ITEMS"])):

	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	
?>
<?foreach($arResult["~ITEMS"] as $key=>$arSection):?>
	<?if(!empty($arSection["NAME"])):?><?if($key>0):?><br><?endif?><h2 class="title"><?=$arSection["NAME"]?></h2><?endif?>
	<?if(!empty($arSection["DESCRIPTION"])):?><div class="block text-muted"><?=$arSection["DESCRIPTION"]?></div><?endif?>
	<?foreach($arSection["ITEMS"] as $cell=>$arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	?>
	<div class="list-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="row">
			<?if(!empty($arItem["PROPERTIES"]["PREVIEW_VIDEO"]["VALUE"]) && !empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container">
					<div class="embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item" src="<?=$arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" allowfullscreen></iframe>
					</div>
					<div class="mb-15 hidden-sm hidden-md hidden-lg"></div>
				</div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="body">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_WORKS_DETAIL")?></a>
				</div>
			</div>
			<?elseif(is_array($arItem["PREVIEW_PICTURE"])):?>
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container">
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="">
					<div class="overlay">
						<div class="overlay-links">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-link"></i></a>
							<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" class="popup-img-single"><i class="fa fa-search-plus"></i></a>
						</div>
					</div>
				</div>
				<div class="mb-15 hidden-sm hidden-md hidden-lg"></div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="body">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_BUSINESSCARD_WORKS_DETAIL")?></a>
				</div>
			</div>			
			<?else:?>
			<div class="col-md-12">
				<div class="body">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("QUICK_BUSINESSCARD_WORKS_DETAIL")?></a>
				</div>
			</div>
			<?endif?>
		</div>
	</div>
	<?endforeach?>
	<?if($arSection["COUNT"] > $arParams["LINE_ELEMENT_COUNT"] && $arSection["PATH"]["ID"] > 0):?>
	<div class="mb-20">
		<div class="row">
			<div class="text-center">
			<a href="<?=$arSection["PATH"]["SECTION_PAGE_URL"]?>" class="btn btn-sm btn-white margin-top-clear"><?=GetMessage("QUICK_BUSINESSCARD_WORKS_SECTION_PAGE_URL")?> <i class="fa fa-arrow-circle-right pl-5"></i></a>
			</div>
		</div>
	</div>
	<?endif?>
<?endforeach?>
<?endif?>