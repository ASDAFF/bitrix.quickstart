<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? 
$cols="3"; //cols count 
$k="0"; 
$j="0";
?>
<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr> 
<?foreach($arResult["SECTIONS"] as $arSection): $j++; if($arParams["SECTION_COUNT"] && $j>$arParams["SECTION_COUNT"])break; $k++;
$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
    <?if($k>$cols){$k=1;?></tr><tr><?}?> 
    <td id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="section" align="center" valign="top" width="<?=count(100/$cols)?>%"> 
	<?$secimg = CFile::ResizeImageGet($arSection['PICTURE'],array("width"=>"114px","height"=>"90px"),"BX_RESIZE_IMAGE_PROPORTIONAL_ALT",true);?>
        <div class="section-bg">
			<div class="section-img"><a href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection["NAME"]?>"><img src="<?=$secimg['src']?>" border="0" title="<?=$arSection["NAME"]?>" alt="<?=$arSection["NAME"]?>" width="<?=$secimg['width']?>" height="<?=$secimg['height']?>"></a></div>
		</div>
		<h1 style="font-size:16px;"><a href="<?=$arSection['SECTION_PAGE_URL']?>" style="color:#4b433c; text-decoration:none;" title="<?=$arSection["NAME"]?>"><?=$arSection["NAME"]?></a></h1>
    </td>
<?endforeach;?>
 
<?if($k!=$cols){for($i=1; $i<=$cols-$k; $i++){?> 
<td>&nbsp;</td> 
<?}}?>
</tr>
</table>
