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

$arViewModeList = $arResult['VIEW_MODE_LIST'];

$arViewStyles = array(
	'LIST' => array(
		'CONT' => 'bx_sitemap',
		'TITLE' => 'bx_sitemap_title',
		'LIST' => 'bx_sitemap_ul',
	),
	'LINE' => array(
		'CONT' => 'bx_catalog_line',
		'TITLE' => 'bx_catalog_line_category_title',
		'LIST' => 'bx_catalog_line_ul',
		'EMPTY_IMG' => $this->GetFolder().'/images/line-empty.png'
	),
	'TEXT' => array(
		'CONT' => 'bx_catalog_text',
		'TITLE' => 'bx_catalog_text_category_title',
		'LIST' => 'bx_catalog_text_ul'
	),
	'TILE' => array(
		'CONT' => 'bx_catalog_tile',
		'TITLE' => 'bx_catalog_tile_category_title',
		'LIST' => 'bx_catalog_tile_ul',
		'EMPTY_IMG' => $this->GetFolder().'/images/tile-empty.png'
	)
);
$arCurView = $arViewStyles[$arParams['VIEW_MODE']];

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

if ('Y' == $arParams['SHOW_PARENT_NAME'] && 0 < $arResult['SECTION']['ID'])
{
	$this->AddEditAction($arResult['SECTION']['ID'], $arResult['SECTION']['EDIT_LINK'], $strSectionEdit);
	$this->AddDeleteAction($arResult['SECTION']['ID'], $arResult['SECTION']['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
}
if (0 < $arResult["SECTIONS_COUNT"])
{
?>
<div class="bj-group-menu bj-side-block">
<?foreach ($arResult['SECTIONS'] as &$arSection){?>
<?if(!empty($arSection)):
if($arResult["SECTION"]["ID"] == $arSection["ID"] || in_array($arResult["SECTION"]["ID"], $arSection["SUB_ID"])){$bOpen = true;}else{$bOpen = false;}?>
	<?if(!empty($arSection["SUBSECTIONS"])):?>
	<div class="bj-hidden<?=($bOpen ? " i-open" : "")?>">
	<div class="h3 bj-hidden-link<?=($bOpen ? " i-up" : "")?>"><?=$arSection["NAME"]?></div>
	<menu class="bj-hidden__hidden">
	<?foreach ($arSection["SUBSECTIONS"] as $key => $arSub) {?>
	<li><a href="<?=$arSub["SECTION_PAGE_URL"]?>">
	<?if($APPLICATION->GetCurDir() == $arSub["SECTION_PAGE_URL"]):?>
	<b><?=$arSub["NAME"]?></b>
	<?else:?>
	<?=$arSub["NAME"]?>
	<?endif;?></a></li>
	<?}?>
	</menu>
	</div>
	<?else:?>
	<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="h3"><?=$arSection["NAME"]?></a>
	<?endif;?>
<?endif;?>
<?}?>
</div>
<?}?>