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

$arParams['PICTURE_WIDTH'] = intval($arParams['PICTURE_WIDTH']);
$arParams['PICTURE_HEIGHT'] = intval($arParams['PICTURE_HEIGHT']);

$arParams['PICTURE_WIDTH'] = ($arParams['PICTURE_WIDTH'] > 0 ) ? $arParams['PICTURE_WIDTH'] : 100;
$arParams['PICTURE_HEIGHT'] = ($arParams['PICTURE_HEIGHT'] > 0 ) ? $arParams['PICTURE_HEIGHT'] : 100;
?>
<?
if (0 < $arResult["SECTIONS_COUNT"])
{
?>
<div class="section-list-box">
<ul class="section-list">
<?
			foreach ($arResult['SECTIONS'] as &$arSection)
			{
				$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
				$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
				?>
				<li id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="lvl-<?=$arSection['DEPTH_LEVEL']?>">
					<a href="<? echo $arSection["SECTION_PAGE_URL"]; ?>">
				<?if($arSection["PICTURE"]):?>
					<img src="<?=$arSection['PICTURE']['src']?>" width="<?=$arSection['PICTURE']['width']?>" height="<?=$arSection['PICTURE']['height']?>"/>
				<?else:?>
					<span class="no-photo" style="width:<?=$arParams['PICTURE_WIDTH'];?>px;height: <?=$arParams['PICTURE_HEIGHT'];?>px;"></span>
				<?endif;?>
				<span class="title">
					<?=$arSection["NAME"];?>
				</span>
				<?
				if ($arParams["COUNT_ELEMENTS"])
				{
					?> <span class="col">- <? echo $arSection["ELEMENT_CNT"]; ?></span><?
				}
				?></a>
				</li>
			<?
			}
			unset($arSection);?>
</ul>
</div>
<?
}
?>