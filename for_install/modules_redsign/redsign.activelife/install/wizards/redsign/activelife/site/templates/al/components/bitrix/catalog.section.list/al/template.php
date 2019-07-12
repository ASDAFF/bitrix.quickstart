<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
		'LIST' => 'menu_vml',
	),
);
$arCurView = $arViewStyles[$arParams['VIEW_MODE']];

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

if ($arResult["SECTIONS_COUNT"] > 0)
{
?>
<ul class="menu_vml">
<?
$intCurrentDepth = 1;
$boolFirst = true;
foreach ($arResult['SECTIONS'] as &$arSection)
{
    $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
    $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

    if ($intCurrentDepth < $arSection['RELATIVE_DEPTH_LEVEL'])
    {
        if (0 < $intCurrentDepth)
            echo '<ul class="menu_vml__sub">';
    }
    elseif ($intCurrentDepth == $arSection['RELATIVE_DEPTH_LEVEL'])
    {
        if (!$boolFirst)
            echo '</li>';
    }
    else
    {
        while ($intCurrentDepth > $arSection['RELATIVE_DEPTH_LEVEL'])
        {
            echo '</li></ul>';
            $intCurrentDepth--;
        }
        echo '</li>';
    }

    ?>
    <li class="menu_vml__item" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
        <a class="clearfix" href="<?=$arSection["SECTION_PAGE_URL"]?>">
            <?php if($arSection['HAVE_SUBSECTIONS']): ?>
                <svg class="menu_vml__toggle icon-svg"><use xlink:href="#svg-right-round"></use></svg>
            <?php endif; ?>
            <span class="menu_vml__name"><?=$arSection["NAME"];?></span>
            <?php if ($arParams["COUNT_ELEMENTS"]): ?>
                <span class="menu_vml__cnt">(<?=$arSection["ELEMENT_CNT"]?>)</span>
            <?php endif ?>
        </a>
        <?

    $intCurrentDepth = $arSection['RELATIVE_DEPTH_LEVEL'];
    $boolFirst = false;
}
unset($arSection);
while ($intCurrentDepth > 1)
{
    echo '</li></ul>';
    $intCurrentDepth--;
}
if ($intCurrentDepth > 0)
{
    echo '</li>';
}
?>
</ul>
<?
}
if (0 < $arResult['SECTION']['ID'])
{
    
    $this->SetViewTarget('catalog_section-title');
    echo (
        isset($arResult['SECTION']['IPROPERTY_VALUES']['SECTION_PAGE_TITLE']) && $arResult['SECTION']['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'] != ''
        ? $arResult['SECTION']['IPROPERTY_VALUES']['SECTION_PAGE_TITLE']
        : $arResult['SECTION']['NAME']
    );
    $this->EndViewTarget();
}

if ($arResult['SECTION']['DESCRIPTION'] != '') {
	$this->SetViewTarget('catalog_section_description');
		?><div class="catalog__descr"><?=$arResult['SECTION']['DESCRIPTION']?></div><?
	$this->EndViewTarget();
}
if ($arParams['SHOW_SECTION_PICTURE'] == 'Y' && isset($arResult['SECTION']['PICTURE']['RESIZE'])) {
	$this->SetViewTarget('catalog_section_pic');
		?><img class="catalog__pic" src="<?=$arResult['SECTION']['PICTURE']['RESIZE']['src']?>" alt="<?=$arResult['SECTION']['PICTURE']['ALT']?>" title="<?=$arResult['SECTION']['PICTURE']['TITLE']?>" /><?
	$this->EndViewTarget();
}