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
<?if(!empty($arResult["UF_SEO_DESCRIPTION"])):?>
<div class="block text-muted"><?=$arResult["UF_SEO_DESCRIPTION"]?></div>
<?endif?>
<?if(!empty($arResult["ITEMS"])):?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
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
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_EFFORTLESS_WORKS_DETAIL")?></a>
				</div>
			</div>
			<?elseif(is_array($arItem["PREVIEW_PICTURE"])):?>
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
				<div class="mb-15 hidden-sm hidden-md hidden-lg"></div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="body">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=GetMessage("QUICK_EFFORTLESS_WORKS_DETAIL")?></a>
				</div>
			</div>			
			<?else:?>
			<div class="col-md-12">
				<div class="body">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn-block text-right"><?=GetMessage("QUICK_EFFORTLESS_WORKS_DETAIL")?></a>
				</div>
			</div>
			<?endif?>
		</div>
	</div>
<?endforeach;?>
<div class="row shop-footer">
	<div class="col-md-3 col-xs-12 col-sm-12 pull-right">
		<form>
			<div class="form-group">
				<select class="form-control">
					<option value=""><?=GetMessage("QUICK_EFFORTLESS_WORKS_SORT")?></option>
					<option <?if(array_key_exists("date", $_REQUEST) && array_key_exists("desc", $_REQUEST)):?>selected<?endif?> value="?date&desc"><?=GetMessage("QUICK_EFFORTLESS_WORKS_DATE_DESC")?></option>
					<option <?if(array_key_exists("date", $_REQUEST) && array_key_exists("asc",  $_REQUEST)):?>selected<?endif?> value="?date&asc"><?=GetMessage("QUICK_EFFORTLESS_WORKS_DATE_ASC")?></option>
					<option <?if(array_key_exists("name", $_REQUEST) && array_key_exists("asc",  $_REQUEST)):?>selected<?endif?> value="?name&asc"><?=GetMessage("QUICK_EFFORTLESS_WORKS_NAME_ASC")?></option>
					<option <?if(array_key_exists("name", $_REQUEST) && array_key_exists("desc", $_REQUEST)):?>selected<?endif?> value="?name&desc"><?=GetMessage("QUICK_EFFORTLESS_WORKS_NAME_DESC")?></option>
				</select>
			</div>
		</form>
	</div>
	<div class="col-md-9">
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif?>
	</div>
</div>
<?endif?>