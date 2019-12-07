<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["~ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));	

switch($arParams["LINE_ELEMENT_COUNT"])
{
	case 1: $span = 12; break;
	case 2: $span = 6; break;
	case 3: $span = 4; break;
	case 4: $span = 3; break; 
	case 5: case 6: case 7: $span = 2; break;
	default: $span = 4;
}	
?>
<?foreach($arResult["~ITEMS"] as $key=>$arSection): $count = count($arSection["ITEMS"]);?>
	<?if(!empty($arSection["NAME"])):?><?if($key>0):?><br><?endif?><h2 class="title"><?=$arSection["NAME"]?></h2><?endif?>
	<?if(!empty($arSection["DESCRIPTION"])):?><div class="block text-muted"><?=$arSection["DESCRIPTION"]?></div><?endif?>
	<?foreach($arSection["ITEMS"] as $cell=>$arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	?>
	<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
	<div class="row grid-space-20">
	<?endif?>
	<div class="col-md-<?=$span?> mb-20">
		<div class="image-box team-member" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="overlay-container pic">
			<?if(!empty($arItem["PREVIEW_PICTURE"]["SRC"])):?>
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="overlay small">
					<i class="fa fa-plus"></i>
				</a>
			<?else:?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-spinner pic"></i></a>
			<?endif?>
			</div>
			<div class="image-box-body">
				<h3 class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<?if(!empty($arItem["PROPERTIES"]["POSITION"]["VALUE"])):?><span class="badge default-bg"><?=$arItem["PROPERTIES"]["POSITION"]["VALUE"]?></span><?endif?>
				<ul class="list-unstyled team">
					<?if(!empty($arItem["PROPERTIES"]["EMAIL"]["VALUE"])):?><li><i class="fa fa-envelope-o"></i> <a href="mailto:<?=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?>"><?=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?></a></li><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["PHONE"]["VALUE"])):?><li><i class="fa fa-phone"></i> <?=$arItem["PROPERTIES"]["PHONE"]["VALUE"]?></li><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["SKYPE"]["VALUE"])):?><li><i class="fa fa-skype"></i> <?=$arItem["PROPERTIES"]["SKYPE"]["VALUE"]?></li><?endif?>
				</ul>
			</div>
		</div>
	</div>
	<?$cell++;
	if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0 || $count == $cell):?>
	</div>
	<?endif?>
	<?endforeach?>
<?endforeach?>
<?endif?>