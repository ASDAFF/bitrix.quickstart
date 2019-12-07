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

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

if($arResult["SECTIONS_COUNT"] > 0):?>
<ul class="nav nav-tabs" role="tablist">
	<?foreach($arResult['SECTIONS'] as &$arSection): $count++;			
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
	?>
		<li <?if($count == 1):?>class="active"<?endif?> id="<?=$this->GetEditAreaId($arSection['ID'])?>"><a href="#tab<?=$arSection['ID']?>" role="tab" data-toggle="tab"><?if(!empty($arSection["UF_ICON"])):?><i class="fa <?=$arSection["UF_ICON"]?> pr-10"></i><?endif?><?=$arSection['NAME']?><?if($arParams["COUNT_ELEMENTS"]):?><span>(<?=$arSection['ELEMENT_CNT']?> <?=$arSection["NUMBER"]["TEXT"]?>)</span><?endif?></a></li>
	<?endforeach?>
</ul>
<?endif?>