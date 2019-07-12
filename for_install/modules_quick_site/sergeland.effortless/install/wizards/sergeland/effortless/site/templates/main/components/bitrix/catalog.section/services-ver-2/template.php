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
<div class="row grid-space-20">
<?$n=0;
foreach($arResult["ITEMS"] as $cell=>$arItem):	
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="col-sm-6" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
		<div class="<?=$arParams["ICONS_VIEW"]?> object-non-visible <?if($cell%2 == 0):?>right<?endif?>" data-animation-effect="fadeInUpSmall" data-effect-delay="<?=200*$n?>">
			<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
			<a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><div class="icon-container default-bg image-block">
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
			</div></a>
			<?elseif(!empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?>
			<a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><div class="icon-container default-bg">
				<i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?>"></i>
			</div></a>
			<?endif?>
			<div class="<?if(!empty($arItem["PREVIEW_PICTURE"]) || !empty($arItem["PROPERTIES"]["ICON"]["VALUE"])):?>body<?endif?>">
				<?if(!empty($arItem["PROPERTIES"]["SHOW_NAME"]["VALUE"])):?><h2><a class="link-dark" href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>"><?=$arItem["NAME"]?></a></h2><?endif?>
				<?if(!empty($arItem["PREVIEW_TEXT"])):?><p class="mt-15"><?=$arItem["PREVIEW_TEXT"]?></p><?endif?>
				<?if(!empty($arItem["PROPERTIES"]["SHOW_BUTTON"]["VALUE"])):?><a href="<?=$arItem["PROPERTIES"]["HREF"]["VALUE"]?>" class="link"><span><?=$arItem["PROPERTIES"]["TEXT_BUTTON"]["VALUE"]?></span></a><?endif?>
			</div>
		</div>
	</div>
<?$cell++; if($cell%2==0) $n++;?>
<?endforeach?>
</div>
<?endif?>