<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<h1 style="color:#fff; font-size:32px; margin-left:40px;"><?=GetMessage("CATALOG_BLOCK_TITLE")?></h1>

<? 
$cols="3"; //cols count 
$k="0"; 
$j="0";
?>
<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr> 
<?foreach($arResult["SECTIONS"] as $arSection): $j++; if($j>$arParams["SECTION_COUNT"])break; $k++;
$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
    <?if($k>$cols){$k=1;?></tr><tr><?}?> 
    <td id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="section" align="center" valign="top" width="<?=count(100/$cols)?>%"> 
        <div class="section-bg">
			<div class="section-img"><a href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection["NAME"]?>"><img src="<?=$arSection['PICTURE']['SRC']?>" border="0" title="<?=$arSection["NAME"]?>" alt="<?=$arSection["NAME"]?>"></a></div>
		</div>
		<h1 style="font-size:22px;"><a href="<?=$arSection['SECTION_PAGE_URL']?>" style="color:#fff; text-decoration:none;" title="<?=$arSection["NAME"]?>"><?=$arSection["NAME"]?></a></h1>
    </td>
<?endforeach;?>
 
<?if($k!=$cols){for($i=1; $i<=$cols-$k; $i++){?> 
<td>&nbsp;</td> 
<?}}?>
</tr>
<tr><td colspan="<?=$cols?>" align="center">
	<a href="/catalog/"><img src="<?=$templateFolder?>/images/catalog-all.png"></a>
</td></tr>
</table>
