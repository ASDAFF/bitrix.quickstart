<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<dl class="block-3">
    <?foreach($arResult["ITEMS"] as $arItem):?>
    	<?
    	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    	?>
        <dt><?if($arItem["DISPLAY_ACTIVE_FROM"] != ""):?><?=$arItem["DISPLAY_ACTIVE_FROM"]?><?endif;?><span>[<?=$arItem["NAME"]?>]</span></dt>
        <dd id="<?=$this->GetEditAreaId($arItem['ID']);?>"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["PREVIEW_TEXT"]?></a></dd>
    <?endforeach;?>
</dl>