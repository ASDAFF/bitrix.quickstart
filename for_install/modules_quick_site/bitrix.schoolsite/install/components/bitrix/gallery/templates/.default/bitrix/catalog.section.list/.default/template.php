<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section-list">
<table width='100%'>
<?
$TOP_DEPTH = $arResult["SECTION"]["DEPTH_LEVEL"];
$CURRENT_DEPTH = $TOP_DEPTH;
$count=0;
foreach($arResult["SECTIONS"] as $arSection):
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
	if($CURRENT_DEPTH < $arSection["DEPTH_LEVEL"])
		echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"]-$TOP_DEPTH),"<tr>";
	elseif($CURRENT_DEPTH == $arSection["DEPTH_LEVEL"])
		echo "</td>";
	else
	{
		while($CURRENT_DEPTH > $arSection["DEPTH_LEVEL"])
		{	
			echo "</td>";
			echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</table>","\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH-1);
			$CURRENT_DEPTH--;
		}
		echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</td>";
	}

	echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"]-$TOP_DEPTH);$count++;
    
    if($count==4):$count=0;?></tr><tr><?endif;?>
	<td>
	<?if($arSection["PICTURE"]["SRC"]):
        $img = CFile::ResizeImageGet($arSection["PICTURE"]["ID"], array('width'=>120, 'height'=>120), BX_RESIZE_IMAGE_EXACT, true);?>
        <a href="<?=$arSection["SECTION_PAGE_URL"]?>">
            <img src="<?=$img["src"]?>">
        </a>
    <?endif;?>
    <br />
    <a href="<?=$arSection["SECTION_PAGE_URL"]?>">
        <b><?=$arSection["NAME"]?></b>
    </a>
    <br />
    <?if($arParams["COUNT_ELEMENTS"]):?>&nbsp;<?=$arSection["ELEMENT_CNT"]; echo GetMessage('CT_BCSL_ELEMENT_FOTO'); endif;

	$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
endforeach;

while($CURRENT_DEPTH > $TOP_DEPTH)
{
	echo "</td>";
	echo "\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH),"</tr>","\n",str_repeat("\t", $CURRENT_DEPTH-$TOP_DEPTH-1);
	$CURRENT_DEPTH--;
}
?>
</table>
</div>