<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<style>
.catalog-section-list2 {
width:600px;
margin-left:26px;
margin-top:20px;
}

.catalog-section-list2 ul {
list-style: none outside none;
margin: 2px 4px 0 3px;
padding: 0;
}


.catalog-section-list2 ul li {
float:left;
margin-right:29px;
}
</style>
<div class="catalog-section-list2">
<ul>
<?
$i=0;
$CURRENT_DEPTH=$arResult["SECTION"]["DEPTH_LEVEL"]+1;
foreach($arResult["SECTIONS"] as $arSection):
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
	if($CURRENT_DEPTH<$arSection["DEPTH_LEVEL"])
		echo "<ul>";
	elseif($CURRENT_DEPTH>$arSection["DEPTH_LEVEL"])
		echo str_repeat("</ul>", $CURRENT_DEPTH - $arSection["DEPTH_LEVEL"]);
	$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
?><?  if($arResult["SECTIONS"][$i]["UF_LOGO_ALLBRANDS"]!=''){



  ?><li id="<?=$this->GetEditAreaId($arSection['ID']);?>"><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><img  src="<?=CFile::GetPath($arResult["SECTIONS"][$i]["UF_LOGO_ALLBRANDS"]);?>" width="168" height="57" /></a></li>
  
  
  <?} else{?>
	
	
	<li id="<?=$this->GetEditAreaId($arSection['ID']);?>"><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><img  src="<?=$arResult["SECTIONS"][$i]["PICTURE"]["SRC"];?>" width="168" height="57" /></a></li>
	<?};?>
<? $i++;endforeach?>
</ul>
</div>

<?

echo '<pre>';
//print_r($arResult);
echo '</pre>';
?>