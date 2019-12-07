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
			<?if(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container pic">
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="overlay small">
						<i class="fa fa-plus"></i>
					</a>
				</div>
				<div class="mb-20 hidden-sm hidden-md hidden-lg"></div>
			</div>
			<?endif?>
			<div class="<?if(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>col-sm-6 col-md-8<?else:?>col-md-12<?endif?>">
				<div class="body">
					<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
					<ul class="list-unstyled team">
						<?if(!empty($arItem["PROPERTIES"]["HREF"]["VALUE"])):?><li><i class="fa fa-external-link"></i> <a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>" target="_blank"><?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?></a></li><?endif?>
						<?if(!empty($arItem["PROPERTIES"]["PHONE"]["VALUE"])):?><li><i class="fa fa-phone"></i> <?=$arItem["PROPERTIES"]["PHONE"]["VALUE"]?></li><?endif?>
						<?if(!empty($arItem["PROPERTIES"]["ADDRESS"]["VALUE"])):?><li><i class="fa fa-globe"></i> <?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"]?></li><?endif?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?endforeach?>
<?endforeach?>
<?endif?>