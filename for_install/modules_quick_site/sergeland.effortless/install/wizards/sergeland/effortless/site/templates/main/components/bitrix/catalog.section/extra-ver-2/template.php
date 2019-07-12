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
$this->setFrameMode(true);

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

if (!empty($arResult["ITEMS"])):
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>
<?$count = count($arResult["ITEMS"]);
foreach($arResult["ITEMS"] as $cell=>$arItem):	
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0): $k=0;?>
<div class="main">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
<?endif?>
					<div class="col-md-<?=$span?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<div class="box-style-1 gray-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="<?=($k*200)?>">
							<?if(!empty($arItem["PREVIEW_PICTURE"])):?>						
								<a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"></a>
							<?elseif(!empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?>
								<a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?>"></i></a>
							<?endif?>
							<?if(!empty($arItem["PROPERTIES"]["SHOW_NAME"]["VALUE"])):?><h2><a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><?=$arItem["NAME"]?></a></h2><?endif?>
							<?if(!empty($arItem["PREVIEW_TEXT"])):?><p class="mt-15"><?=$arItem["PREVIEW_TEXT"]?></p><?endif?>
							<?if(!empty($arItem["PROPERTIES"]["SHOW_BUTTON"]["VALUE"])):?><a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>" class="btn-default btn"><?=$arItem["PROPERTIES"]["TEXT_BUTTON"]["VALUE"]?></a><?endif?>
						</div>
					</div>			
<?$cell++; $k++;
if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0 || $count == $cell):?>
				</div>
			</div>
		</div>
	</div>
</div>
<?endif?>
<?endforeach?>
<?endif?>