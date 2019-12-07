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
	<div class="list-item">
		<div class="row">
			<div class="col-sm-6 col-md-4">
				<div class="overlay-container">
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>">
					<a href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="popup-img-single overlay" title="<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>"><i class="fa fa-search-plus"></i></a>
				</div>
				<div class="mb-15 hidden-sm hidden-md hidden-lg"></div>
			</div>
			<div class="col-sm-6 col-md-8">
				<div class="body">
					<h3><a href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="popup-img-single" title="<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>"><?=$arItem["NAME"]?></a></h3>
					<p class="mb-10"><?=$arItem["PREVIEW_TEXT"]?></p>
				</div>
			</div>
		</div>
	</div>
	<?endforeach?>
<?endforeach?>
<?endif?>