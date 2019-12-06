<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>
<?if(!empty($arResult["ITEMS"])):?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
?>
	<div class="list-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
		<div class="row">
			<?if(!empty($arItem["PROPERTIES"]["PREVIEW_VIDEO"]["VALUE"]) && !empty($arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"])):?>
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container">					
					<div class="embed-responsive embed-responsive-4by3">
						<iframe class="embed-responsive-item" src="<?=$arItem["PROPERTIES"]["HREF_VIDEO"]["VALUE"]?>?rel=0&showinfo=0&color=white&html5=1" allowfullscreen></iframe>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="body">
					<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
					<div class="tags mb-10">
						<span class="badge transparent-bg"><?if(!empty($arItem["DISPLAY_ACTIVE_FROM"])):?><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_FROM")?> <?=$arItem["DISPLAY_ACTIVE_FROM"]?><?endif?> <?if(!empty($arItem["DISPLAY_ACTIVE_TO"])):?><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_TO")?> <?=$arItem["DISPLAY_ACTIVE_TO"]?><?endif?></span>
					</div>
					<?endif?>
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="pr-5"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a> <?if(!empty($arItem["IBLOCK_SECTION"]["NAME"])):?><span class="small pl-20"><i class="pr-5 fa fa-tags"></i> <?=$arItem["IBLOCK_SECTION"]["NAME"]?></span><?endif?>
				</div>
			</div>
			<?elseif(is_array($arItem["PREVIEW_PICTURE"]) && $arParams["DISPLAY_PICTURE"]!="N"):?>	
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container">					
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
					<div class="overlay">
						<div class="overlay-links">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-link"></i></a>
							<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" class="popup-img-single"><i class="fa fa-search-plus"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="body">
					<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
					<div class="tags mb-10">
						<span class="badge transparent-bg"><?if(!empty($arItem["DISPLAY_ACTIVE_FROM"])):?><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_FROM")?> <?=$arItem["DISPLAY_ACTIVE_FROM"]?><?endif?> <?if(!empty($arItem["DISPLAY_ACTIVE_TO"])):?><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_TO")?> <?=$arItem["DISPLAY_ACTIVE_TO"]?><?endif?></span>
					</div>
					<?endif?>
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="pr-5"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a> <?if(!empty($arItem["IBLOCK_SECTION"]["NAME"])):?><span class="small pl-20"><i class="pr-5 fa fa-tags"></i> <?=$arItem["IBLOCK_SECTION"]["NAME"]?></span><?endif?>
				</div>
			</div>
			<?else:?>
			<div class="col-md-12">
				<div class="body">
					<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
					<div class="tags mb-10">
						<span class="badge transparent-bg"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
					</div>
					<?endif?>
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<?if(!empty($arItem["IBLOCK_SECTION"]["NAME"])):?><span class="small pl-15 pull-left"><i class="pr-5 fa fa-tags"></i> <?=$arItem["IBLOCK_SECTION"]["NAME"]?></span><?endif?> <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block pr-10 text-right"><?=GetMessage("SERGELAND_EFFORTLESS_ACTIONS_DETAIL")?></a>
				</div>
			</div>			
			<?endif?>
		</div>
	</div>
<?endforeach?>
<div class="row shop-footer">
	<div class="col-md-9 pull-right">
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif?>
	</div>
</div>
<?endif?>