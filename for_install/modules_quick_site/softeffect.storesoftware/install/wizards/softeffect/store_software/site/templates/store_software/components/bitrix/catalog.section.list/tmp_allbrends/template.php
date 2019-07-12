<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<style>
.catalog-section-list {
width:600px;
margin-left:26px;
}

.catalog-section-list ul {
list-style: none outside none;
margin: 2px 4px 0 3px;
padding: 0;
float:left;
}

.bb11 {margin-left:15px;


}
.catalog-section-list ul li {
 background: url("/images/bullets/arrow_red.gif") no-repeat scroll left center transparent;
 padding-left: 12px;
}



</style>

<div  class="catalog-section-list">
<ul>
<? 
$CURRENT_DEPTH=$arResult["SECTION"]["DEPTH_LEVEL"]+1;
$i=0;
foreach($arResult["SECTIONS"] as $arSection):

$arFilter = Array( 
"IBLOCK_ID"=>7, 
"SECTION_ID"=>$arSection["ID"]
); 
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));


if($CURRENT_DEPTH<$arSection["DEPTH_LEVEL"])
		echo "<ul>";
	elseif($CURRENT_DEPTH>$arSection["DEPTH_LEVEL"])
		echo str_repeat("</ul>", $CURRENT_DEPTH - $arSection["DEPTH_LEVEL"]);
	$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
?>
	<li id="<?=$this->GetEditAreaId($arSection['ID']);?>"><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?><?if(CIBlockSection::GetCount($arFilter)!=0){?>&nbsp;(<?=CIBlockSection::GetCount($arFilter)?>)<?} else{ ?>&nbsp;<?}; ?> </a></li>
	<?$i++;
	if($i==9){echo'</ul>'; echo"<ul class='bb11'>"; $i=0; continue;};
	?>
	<?
	endforeach?>
</ul>

<?
echo '<pre>';
//print_r($arResult);
echo '<pre>';
?>

</div>
