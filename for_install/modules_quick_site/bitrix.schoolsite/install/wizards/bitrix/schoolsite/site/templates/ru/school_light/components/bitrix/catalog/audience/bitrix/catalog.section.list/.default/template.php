<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section-list">
<?
echo "<div class='itemRow'>";
$i = 0;
foreach($arResult["SECTIONS"] as $arSection):
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
	if($i == 2){  
    echo "</div><div class='itemRow'>";
    $i = 0;
  }  
?>
	<div class="item" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
  <b><?=$arSection["NAME"]?><?if($arParams["COUNT_ELEMENTS"]):?>&nbsp;(<?=$arSection["ELEMENT_CNT"]?>)<?endif;?></b>
  <?if(is_array($arSection["ITEMS"]) && count($arSection["ITEMS"])):?>
   <ul class="subjects-list">
    <?foreach($arSection["ITEMS"] as $arElement):?>
     <li><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></li>
    <?endforeach?>
   </ul>
  <?endif?>
 </div>  
 <?$i++;?>
<?endforeach?>
</div>
