<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="docs">
 <ul class="blockInner">
 <?//echo '<pre>';print_r($arResult["ITEMS"]);echo '</pre>';?>
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
        
        <li class="<?=$arElement["FILE_TYPE"]?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>"><a href="<?echo $arElement["FILE_SRC"]?>"><?echo $arElement["NAME"]?></a> <?echo $arElement["FILE_TYPE"]?> , <?echo $arElement["FILE_SIZE"]?></li>
        

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>


</ul>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
