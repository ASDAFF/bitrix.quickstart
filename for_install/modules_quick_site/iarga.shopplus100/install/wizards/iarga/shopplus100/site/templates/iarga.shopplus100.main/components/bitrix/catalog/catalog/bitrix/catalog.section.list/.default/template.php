<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<nav class="menu-catalog">
<?
foreach($arResult["SECTIONS"] as $arSection):
	$arSection["NAME"] = str_replace(' ','&nbsp;',trim($arSection["NAME"]));
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
		
	<a id="<?=$this->GetEditAreaId($arSection['ID']);?>" href="<?=$arSection["SECTION_PAGE_URL"]?>" <?=($arSection['OPENED'])?'class="active"':''?> ><?=$arSection["NAME"]?><?if($arParams["COUNT_ELEMENTS"] && $arSection["ELEMENT_CNT"]>0):?>&nbsp;(<?=$arSection["ELEMENT_CNT"]?>)<?endif;?></a>

<?endforeach;?>
</nav><!--.menu-catalog-end-->